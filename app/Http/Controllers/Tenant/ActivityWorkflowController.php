<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\AdministrativeUnit;
use App\Models\SubstantiveActivity;
use App\Models\MonthlyProgressReport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ActivityWorkflowController extends Controller
{
    /**
     * Gateway hierarchy check.
     */
    public function index()
    {
        $user = Auth::user();

        // Admin Roles: See all Departments
        if ($user->hasAnyRole(['Super-Admin', 'PMD-Planeación'])) {
            return Inertia::render('Activities/AdminDepartments', [
                'departments' => Department::all(),
            ]);
        }

        // Enlace-Dependencia Role: See only their areas
        if ($user->department_id) {
            $areas = AdministrativeUnit::where('department_id', $user->department_id)->get();
            $dept = Department::find($user->department_id);
            
            // Phase 14.2: RAMT Multi-Área Departamental Math
            $ramtCompliance = [1 => false, 2 => false, 3 => false, 4 => false];
            $totalDepartmentActivities = SubstantiveActivity::whereIn('administrative_unit_id', $areas->pluck('id'))->count();
            $expectedQuarterTotal = $totalDepartmentActivities * 3;

            if ($expectedQuarterTotal > 0) {
                $quartersMap = [1 => [1, 2, 3], 2 => [4, 5, 6], 3 => [7, 8, 9], 4 => [10, 11, 12]];
                foreach ($quartersMap as $q => $monthsArr) {
                    $validatedCount = \App\Models\MonthlyProgressReport::whereHas('substantiveActivity', function($qBase) use ($areas) {
                        $qBase->whereIn('administrative_unit_id', $areas->pluck('id'));
                    })
                    ->whereIn('month', $monthsArr)
                    ->where('status', 1)
                    ->count();

                    $ramtCompliance[$q] = ($validatedCount === $expectedQuarterTotal);
                }
            }
            
            return Inertia::render('Activities/AreaList', [
                'areas' => $areas,
                'current_department_name' => $dept ? $dept->name : 'Área Asignada',
                'ramt_quarters_compliance' => $ramtCompliance,
                'is_enlace' => $user->hasRole('Enlace-Dependencia'),
            ]);
        }

        // Fallback for users without identity context
        return redirect()->route('dashboard')->with('error', 'Tu usuario no tiene una dependencia o rol operativo asignado.');
    }

    /**
     * Show areas for a specific department (Admin view or authorized Enlace).
     */
    public function showDepartment(Department $department)
    {
        $user = Auth::user();

        // Security: Prevent Enlaces from seeing other departments via URL tampering
        if (!$user->hasAnyRole(['Super-Admin', 'PMD-Planeación'])) {
            if ((int)$user->department_id !== (int)$department->id) {
                abort(403, 'Acceso denegado: No puedes consultar dependencias ajenas.');
            }
        }

        return Inertia::render('Activities/AreaList', [
            'areas' => $department->administrativeUnits,
            'current_department_name' => $department->name,
            'ramt_quarters_compliance' => null, // Admins don't generate RAMT
            'is_enlace' => false,
        ]);
    }

    /**
     * Core Work Interface: Show activities and capture monthly progress.
     */
    public function showArea(AdministrativeUnit $administrativeUnit, Request $request)
    {
        $user = Auth::user();

        // Security Check
        if (!$user->hasAnyRole(['Super-Admin', 'PMD-Planeación'])) {
            if ((int)$user->department_id !== (int)$administrativeUnit->department_id) {
                abort(403, 'Acceso denegado: Esta unidad no pertenece a tu dependencia.');
            }
        }

        // Month Selection Logic
        $month = (int) $request->input('month', date('n'));
        if ($month < 1 || $month > 12) $month = (int) date('n');

        $monthColMap = [
            1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr', 5 => 'may', 6 => 'jun',
            7 => 'jul', 8 => 'aug', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dec'
        ];
        $targetCol = $monthColMap[$month] . '_programmed';

        // Load activities with their annual schedule and the specific month's progress
        $activities = SubstantiveActivity::where('administrative_unit_id', $administrativeUnit->id)
            ->with(['monthlySchedule', 'progressReports' => function($q) use ($month) {
                $q->where('month', $month);
            }])
            ->get()
            ->map(function($activity) use ($targetCol, $month) {
                // Computed properties for the UI
                $activity->month_target = $activity->monthlySchedule ? (float)$activity->monthlySchedule->$targetCol : 0;
                $activity->current_report = $activity->progressReports->first();

                // Attach Public Asset URL if evidence exists
                if ($activity->current_report && $activity->current_report->evidence_path) {
                    $activity->current_report->evidence_url = tenant_asset($activity->current_report->evidence_path);
                }

                return $activity;
            });

        return Inertia::render('Activities/AreaActivitiesShow', [
            'unit' => $administrativeUnit,
            'activities' => $activities,
            'current_month' => $month,
            'can_validate' => $user->hasAnyRole(['Super-Admin', 'PMD-Planeación']),
        ]);
    }

    /**
     * Store or update monthly progress for an activity.
     */
    public function storeProgress(Request $request, SubstantiveActivity $activity)
    {
        $user = Auth::user();

        // Security
        if (!$user->hasAnyRole(['Super-Admin', 'PMD-Planeación'])) {
            if ((int)$user->department_id !== (int)$activity->administrativeUnit->department_id) {
                abort(403, 'No tienes permiso para reportar el avance de esta actividad.');
            }
        }

        $month = $request->month;

        // DIAMOND WALL RLS: If already validated, it's immutable for Enlace
        $existing = MonthlyProgressReport::where('substantive_activity_id', $activity->id)
            ->where('month', $month)
            ->first();

        if ($existing && $existing->status === 1) {
            abort(403, 'Este registro ya ha sido validado por Planeación Central y se encuentra cerrado.');
        }

        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'reported_value' => 'required|numeric|min:0',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
        ]);

        $evidencePath = null;

        // Reset status to 0 (En Revisión) on submission/update
        $data = [
            'reported_value' => $request->reported_value,
            'status' => 0, 
        ];

        if ($request->hasFile('evidence')) {
            $data['evidence_path'] = $request->file('evidence')->store("evidencias/{$month}", 'public');
        }

        MonthlyProgressReport::updateOrCreate(
            [
                'substantive_activity_id' => $activity->id,
                'month' => $month,
            ],
            $data
        );

        return redirect()->back()->with('message', 'Avance reportado correctamente para validación.');
    }

    /**
     * Admin Audit: Validate or return a report with observations.
     */
    public function validateProgress(Request $request, MonthlyProgressReport $report)
    {
        $user = Auth::user();

        // RBAC Check
        if (!$user->hasAnyRole(['Super-Admin', 'PMD-Planeación'])) {
            abort(403, 'No tienes permisos de validación.');
        }

        $request->validate([
            'action' => 'required|string|in:approve,reject',
            'observations' => 'nullable|string|max:1000',
        ]);

        if ($request->action === 'approve') {
            $report->status = 1; // Validado
        } else {
            $report->status = 0; // Rechazado / Pendiente de nuevo
        }

        $report->validated_by = $user->id;
        $report->observations = $request->observations;
        $report->save();

        $msg = $request->action === 'approve' 
            ? "Registro de {$report->substantiveActivity->name} validado con éxito."
            : "Registro rechazado. Se han enviado las observaciones al enlace.";

        return redirect()->back()->with('message', $msg);
    }

    /**
     * Phase 14.2: PDF Download RAMT (Departamental)
     */
    public function downloadRamt(int $quarter)
    {
        $user = Auth::user();

        if (!$user->hasRole('Enlace-Dependencia')) {
            abort(403, 'Acceso denegado: Operación exclusiva para Enlaces de Dependencia.');
        }

        // Validate quarter limits
        if ($quarter < 1 || $quarter > 4) {
            abort(400, 'Cuartil inválido.');
        }

        $quartersMap = [1 => [1, 2, 3], 2 => [4, 5, 6], 3 => [7, 8, 9], 4 => [10, 11, 12]];
        $monthPrefixes = [
            1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr', 5 => 'may', 6 => 'jun',
            7 => 'jul', 8 => 'aug', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dec'
        ];
        $monthsArr = $quartersMap[$quarter];

        $department = Department::with([
            'holder',
            'administrativeUnits.substantiveActivities.monthlySchedule',
            'administrativeUnits.substantiveActivities.progressReports' => function($query) use ($monthsArr) {
                $query->whereIn('month', $monthsArr)->where('status', 1);
            }
        ])->find($user->department_id);
        
        if (!$department) {
            abort(403, 'Usuario sin departamento asignado.');
        }

        // Check if Department has activities mapped
        $areasIdArray = $department->administrativeUnits->pluck('id');
        $totalActivities = \App\Models\SubstantiveActivity::whereIn('administrative_unit_id', $areasIdArray)->count();
        if ($totalActivities === 0) {
            abort(403, 'No hay metas programadas para generar el RAMT departamental.');
        }

        // Global accumulators
        $globalProg = 0;
        $globalRep = 0;

        foreach ($department->administrativeUnits as $area) {
            foreach ($area->substantiveActivities as $activity) {
                $actProg = 0;
                $actRep = 0;

                if ($activity->monthlySchedule) {
                    foreach ($monthsArr as $m) {
                        $col = $monthPrefixes[$m] . '_programmed';
                        $actProg += (float) $activity->monthlySchedule->$col;
                    }
                }

                foreach ($activity->progressReports as $report) {
                    $actRep += (float) $report->reported_value;
                }

                $globalProg += $actProg;
                $globalRep += $actRep;

                if ($actProg == 0) {
                    $actPercent = 100.00;
                } else {
                    $actPercent = ($actRep / $actProg) * 100;
                }

                $activity->setAttribute('trimestral_compliance_percent', number_format(min($actPercent, 100), 2));
            }
        }

        if ($globalProg == 0) {
            $departmentGlobalPercentage = "100.00";
        } else {
            $departmentGlobalPercentage = number_format(min(($globalRep / $globalProg) * 100, 100), 2);
        }

        $config = \App\Models\MunicipalConfiguration::getSettings();

        // Load PDF using the department relations directly
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ramt_acuse', [
            'department' => $department,
            'user' => $user,
            'quarter' => $quarter,
            'config' => $config,
            'department_global_percentage' => $departmentGlobalPercentage
        ])->setPaper('a4', 'portrait');

        return $pdf->download("RAMT_Acuse_Tri{$quarter}_".$department->name.".pdf");
    }
}
