<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubstantiveActivity;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Downloads/Index');
    }

    public function exportActivities(Request $request)
    {
        $quarter = (int) $request->input('quarter', 1);
        
        $monthsMap = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        $quarterMonths = $monthsMap[$quarter] ?? [1, 2, 3];

        $activities = SubstantiveActivity::with([
            'administrativeUnit.generalSector',
            'administrativeUnit.auxiliarySector',
            'administrativeUnit.budgetProject',
            'monthlySchedule',
            'progressReports' => function ($query) use ($quarterMonths) {
                $query->whereIn('month', $quarterMonths);
            }
        ])->get();

        $response = new StreamedResponse(function () use ($activities, $quarter) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel compatibility
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Clave Dependencia General',
                'Clave Dependencia Auxiliar',
                'Proyecto',
                'Nombre Actividad',
                'Unidad de Medida',
                'Programado anual',
                'Programado Trimestre',
                'Alcanzado Trimestre'
            ]);

            foreach ($activities as $activity) {
                $generalSector = $activity->administrativeUnit->generalSector->code ?? '';
                $auxiliarySector = $activity->administrativeUnit->auxiliarySector->code ?? '';
                $project = $activity->administrativeUnit->budgetProject->code ?? '';
                $name = $activity->name;
                $measurementUnit = $activity->measurement_unit;
                $annualTarget = $activity->annual_target;

                $programmedQuarter = 0;
                if ($activity->monthlySchedule) {
                    $s = $activity->monthlySchedule;
                    if ($quarter === 1) {
                        $programmedQuarter = $s->jan_programmed + $s->feb_programmed + $s->mar_programmed;
                    } elseif ($quarter === 2) {
                        $programmedQuarter = $s->apr_programmed + $s->may_programmed + $s->jun_programmed;
                    } elseif ($quarter === 3) {
                        $programmedQuarter = $s->jul_programmed + $s->aug_programmed + $s->sep_programmed;
                    } elseif ($quarter === 4) {
                        $programmedQuarter = $s->oct_programmed + $s->nov_programmed + $s->dec_programmed;
                    }
                }

                $achievedQuarter = $activity->progressReports->sum('reported_value');

                fputcsv($handle, [
                    $generalSector,
                    $auxiliarySector,
                    $project,
                    $name,
                    $measurementUnit,
                    $annualTarget,
                    $programmedQuarter,
                    $achievedQuarter
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="actividades_trimestre_' . $quarter . '.csv"');

        return $response;
    }
}
