<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProgrammaticReconduction;
use App\Models\ReconductionItem;
use App\Models\SubstantiveActivity;
use App\Models\ActivityMonthlySchedule;
use Barryvdh\DomPDF\Facade\Pdf;

class ReconductionController extends Controller
{
    /**
     * Listado General de Reconducciones
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasAnyRole(['Super-Admin', 'PMD-Planeación']);

        $query = ProgrammaticReconduction::with(['administrativeUnit', 'requestedBy', 'validatedBy'])
            ->withCount('items');

        if (!$isAdmin) {
            // Enlace: Solo ve las de su área de adscripción real (Asumimos 1 Area ligada o busqueda por el usuario)
            // Ya que los roles de Enlace crean borradores para su Department/Area especifica.
            // O usaremos $user->id como requester por practicidad operativa en este demo dictamen local:
            $query->where('requested_by', $user->id);
        } else {
            // Admin: Ve todas las enviadas (status > 0)
            $query->where('status', '>', 0);
        }

        $reconductions = $query->orderBy('created_at', 'desc')->get();

        return Inertia::render('Reconductions/Index', [
            'reconductions' => $reconductions,
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * Generar Borrador de Reconducción Inicial
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Obtener el AdministrativeUnit asociado al Enlace. (En Fase 6 se amarraba Department, asumo la relacion 1ra Unit de su Dept)
        // Para simplificar, buscamos la Unit en la que le toca operar, si no tiene mandamos 403.
        // Simularemos encontrar su Unit principal.
        $areaId = \App\Models\AdministrativeUnit::where('department_id', $user->department_id)->value('id');
        
        if (!$areaId) {
            return redirect()->back()->withErrors('Tu usuario no tiene un Área (AdministrativeUnit) válida asociada para solicitar reconducciones.');
        }

        // Calculation of current Quarter (1-4)
        $month = (int) date('n');
        $quarter = ceil($month / 3);

        $docNumber = "UIPPE-R-" . date('YmdHis');

        $reconduction = ProgrammaticReconduction::create([
            'administrative_unit_id' => $areaId,
            'document_number' => $docNumber,
            'requested_date' => date('Y-m-d'),
            'quarter' => $quarter,
            'requested_by' => $user->id,
            'status' => 0, // Borrador draft
        ]);

        return redirect()->route('reconductions.edit', $reconduction->id);
    }

    /**
     * React Builder interactivo de la Reconducción.
     */
    public function edit(ProgrammaticReconduction $reconduction)
    {
        $user = Auth::user();
        $isAdmin = $user->hasAnyRole(['Super-Admin', 'PMD-Planeación']);

        // Seguridad
        if (!$isAdmin && $reconduction->requested_by !== $user->id) {
            abort(403, 'Aceso denegado al dictamen.');
        }

        $reconduction->load(['items', 'administrativeUnit']);

        // Actividades disponibles con su Programación y acumulado logrado (achieved_so_far)
        $activities = SubstantiveActivity::where('administrative_unit_id', $reconduction->administrative_unit_id)
            ->with(['monthlySchedule', 'progressReports' => function($q){
                $q->where('status', 1); // Solo reportes validados
            }])
            ->get()
            ->map(function($act) {
                // Sumar avance validado acumulado real
                $achieved_so_far = $act->progressReports->sum('reported_value');
                $act->achieved_so_far = $achieved_so_far;

                // Format the JSON Schedule from the Matrix row to objects mapping properties forms react easy map
                if ($act->monthlySchedule) {
                    $sch = $act->monthlySchedule;
                    $act->schedule_matrix = [
                        'jan' => (float)$sch->jan_programmed, 'feb' => (float)$sch->feb_programmed, 'mar' => (float)$sch->mar_programmed,
                        'apr' => (float)$sch->apr_programmed, 'may' => (float)$sch->may_programmed, 'jun' => (float)$sch->jun_programmed,
                        'jul' => (float)$sch->jul_programmed, 'aug' => (float)$sch->aug_programmed, 'sep' => (float)$sch->sep_programmed,
                        'oct' => (float)$sch->oct_programmed, 'nov' => (float)$sch->nov_programmed, 'dec' => (float)$sch->dec_programmed,
                    ];
                } else {
                    $act->schedule_matrix = null;
                }

                // Add original annual goal calculated dynamically or from model helper
                $act->current_annual_goal = array_sum($act->schedule_matrix ?? []);

                return $act;
            });

        return Inertia::render('Reconductions/Builder', [
            'reconduction' => $reconduction,
            'available_activities' => $activities,
            'can_edit' => ($reconduction->status === 0 && !$isAdmin),
            'can_validate' => ($isAdmin && $reconduction->status === 1)
        ]);
    }

    /**
     * Guardar el Borrador React Memory UI en DB JSON (Draft Items)
     */
    public function update(Request $request, ProgrammaticReconduction $reconduction)
    {
        // RLS
        if ($reconduction->status !== 0) {
            abort(403, 'El dictamen ha sido cerrado o mandado a revisión, es inmutable.');
        }

        $request->validate([
            'items' => 'array',
        ]);

        DB::transaction(function () use ($request, $reconduction) {
            // Recrear items
            $reconduction->items()->delete();

            foreach ($request->items as $item) {
                $reconduction->items()->create([
                    'substantive_activity_id' => $item['substantive_activity_id'],
                    'modification_type' => $item['modification_type'] ?? 'increase',
                    'previous_annual_goal' => $item['previous_annual_goal'],
                    'new_annual_goal' => $item['new_annual_goal'],
                    'achieved_so_far' => $item['achieved_so_far'],
                    'previous_schedule' => $item['previous_schedule'], 
                    'new_schedule' => $item['new_schedule'],
                    'justification' => $item['justification'] ?? 'Sin justificación.',
                ]);
            }
        });

        return redirect()->back()->with('message', 'Borrador de reconducción guardado correctamente.');
    }

    /**
     * Enviar a Validación (Pasa al PMD Admin)
     */
    public function sendToValidation(ProgrammaticReconduction $reconduction)
    {
        if ($reconduction->status !== 0) abort(403);
        
        $reconduction->update(['status' => 1]); // Enviado
        return redirect()->route('reconductions.index')->with('message', 'Oficio de reconducción remitido para validación central.');
    }

    /**
     * Aprobar la Reconducción y reescribir físicamente PBR
     */
    public function approveReconduction(Request $request, ProgrammaticReconduction $reconduction)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['Super-Admin', 'PMD-Planeación'])) {
            abort(403);
        }

        if ($reconduction->status !== 1) {
            return redirect()->back()->withErrors('El dictamen no está en estado Revisor.');
        }

        DB::transaction(function () use ($reconduction, $user) {
            $reconduction->load('items');

            foreach ($reconduction->items as $item) {
                $activityId = $item->substantive_activity_id;
                $newSch = $item->new_schedule; // This is loaded as AsArrayObject!
                
                // Actualizar Matriz Real PBR (La lógica principal!)
                ActivityMonthlySchedule::where('substantive_activity_id', $activityId)
                    ->update([
                        'jan_programmed' => $newSch['jan'] ?? 0,
                        'feb_programmed' => $newSch['feb'] ?? 0,
                        'mar_programmed' => $newSch['mar'] ?? 0,
                        'apr_programmed' => $newSch['apr'] ?? 0,
                        'may_programmed' => $newSch['may'] ?? 0,
                        'jun_programmed' => $newSch['jun'] ?? 0,
                        'jul_programmed' => $newSch['jul'] ?? 0,
                        'aug_programmed' => $newSch['aug'] ?? 0,
                        'sep_programmed' => $newSch['sep'] ?? 0,
                        'oct_programmed' => $newSch['oct'] ?? 0,
                        'nov_programmed' => $newSch['nov'] ?? 0,
                        'dec_programmed' => $newSch['dec'] ?? 0,
                    ]);
            }

            $reconduction->status = 2; // APROBADO Y APLICADO
            $reconduction->validated_by = $user->id;
            $reconduction->validated_at = now()->toDateTimeString();
            $reconduction->save();
        });

        return redirect()->route('reconductions.index')->with('message', '¡Dictamen de Reconducción PBR Aprobado y Aplicado masivamente al sistema OSFEM!');
    }

    /**
     * Generar Documento PDF Oficial OSFEM
     */
    public function generatePdf(ProgrammaticReconduction $reconduction)
    {
        $reconduction->load([
            'requestedBy',
            'validatedBy',
            'administrativeUnit.department',
            'items.activity',
        ]);

        $pdf = Pdf::loadView('pdf.reconduction_dictamen', [
            'reconduction' => $reconduction,
        ])->setPaper('letter', 'landscape');

        return $pdf->stream('Dictamen_'.$reconduction->document_number.'.pdf');
    }
}
