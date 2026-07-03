<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeUnit;
use App\Models\DevelopmentAxis;
use App\Models\DevelopmentTheme;
use App\Models\SubstantiveActivity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AreaActivityController extends Controller
{
    public function index($areaId)
    {
        $area = AdministrativeUnit::with('department')->findOrFail($areaId);
        
        $activities = SubstantiveActivity::with(['monthlySchedule', 'theme.axis'])
            ->where('administrative_unit_id', $areaId)
            ->get();
            
        $themes = DevelopmentTheme::with('axis')->get()->groupBy('development_axis_id')->map(function ($items, $axisId) {
            $axisName = $items->first()->axis ? $items->first()->axis->name : 'Sin Eje';
            return [
                'axis_name' => $axisName,
                'themes' => $items->map(function ($theme) {
                    return [
                        'id' => $theme->id,
                        'name' => $theme->name,
                    ];
                })->values()
            ];
        })->values();

        return Inertia::render('Settings/Areas/Activities/Index', [
            'area' => $area,
            'activities' => $activities,
            'themes' => $themes,
        ]);
    }

    public function store(Request $request, $areaId)
    {
        $area = AdministrativeUnit::findOrFail($areaId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'measurement_unit' => 'required|string|max:255',
            'development_theme_id' => 'required|exists:development_themes,id',
            'schedule' => 'nullable|array',
            'schedule.jan_programmed' => 'nullable|numeric|min:0',
            'schedule.feb_programmed' => 'nullable|numeric|min:0',
            'schedule.mar_programmed' => 'nullable|numeric|min:0',
            'schedule.apr_programmed' => 'nullable|numeric|min:0',
            'schedule.may_programmed' => 'nullable|numeric|min:0',
            'schedule.jun_programmed' => 'nullable|numeric|min:0',
            'schedule.jul_programmed' => 'nullable|numeric|min:0',
            'schedule.aug_programmed' => 'nullable|numeric|min:0',
            'schedule.sep_programmed' => 'nullable|numeric|min:0',
            'schedule.oct_programmed' => 'nullable|numeric|min:0',
            'schedule.nov_programmed' => 'nullable|numeric|min:0',
            'schedule.dec_programmed' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $areaId) {
            $activity = SubstantiveActivity::create([
                'administrative_unit_id' => $areaId,
                'name' => $validated['name'],
                'measurement_unit' => $validated['measurement_unit'],
                'development_theme_id' => $validated['development_theme_id'],
            ]);

            $scheduleData = $validated['schedule'] ?? [];
            $scheduleData['substantive_activity_id'] = $activity->id;
            
            // Default empty fields to 0
            $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            foreach ($months as $month) {
                if (!isset($scheduleData["{$month}_programmed"])) {
                    $scheduleData["{$month}_programmed"] = 0;
                }
            }

            $activity->monthlySchedule()->create($scheduleData);
        });

        return redirect()->back()->with('message', 'Actividad creada correctamente.');
    }

    public function update(Request $request, $areaId, $activityId)
    {
        $activity = SubstantiveActivity::where('administrative_unit_id', $areaId)->findOrFail($activityId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'measurement_unit' => 'required|string|max:255',
            'development_theme_id' => 'required|exists:development_themes,id',
            'schedule' => 'nullable|array',
            'schedule.jan_programmed' => 'nullable|numeric|min:0',
            'schedule.feb_programmed' => 'nullable|numeric|min:0',
            'schedule.mar_programmed' => 'nullable|numeric|min:0',
            'schedule.apr_programmed' => 'nullable|numeric|min:0',
            'schedule.may_programmed' => 'nullable|numeric|min:0',
            'schedule.jun_programmed' => 'nullable|numeric|min:0',
            'schedule.jul_programmed' => 'nullable|numeric|min:0',
            'schedule.aug_programmed' => 'nullable|numeric|min:0',
            'schedule.sep_programmed' => 'nullable|numeric|min:0',
            'schedule.oct_programmed' => 'nullable|numeric|min:0',
            'schedule.nov_programmed' => 'nullable|numeric|min:0',
            'schedule.dec_programmed' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($activity, $validated) {
            $activity->update([
                'name' => $validated['name'],
                'measurement_unit' => $validated['measurement_unit'],
                'development_theme_id' => $validated['development_theme_id'],
            ]);

            if (isset($validated['schedule'])) {
                $schedule = $activity->monthlySchedule;
                if ($schedule) {
                    $schedule->update($validated['schedule']);
                } else {
                    $scheduleData = $validated['schedule'];
                    $scheduleData['substantive_activity_id'] = $activity->id;
                    $activity->monthlySchedule()->create($scheduleData);
                }
            }
        });

        return redirect()->back()->with('message', 'Actividad actualizada correctamente.');
    }

    public function destroy($areaId, $activityId)
    {
        $activity = SubstantiveActivity::where('administrative_unit_id', $areaId)->findOrFail($activityId);
        
        DB::transaction(function () use ($activity) {
            if ($activity->monthlySchedule) {
                $activity->monthlySchedule->delete();
            }
            $activity->delete();
        });

        return redirect()->back()->with('message', 'Actividad eliminada correctamente.');
    }
}
