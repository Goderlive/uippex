<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\AdministrativeUnit;
use App\Models\OfficeHolder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HolderDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Department::with(['holder', 'administrativeUnits.holder']);

        // Filter by user's department unless they are Super-Admin or PMD-Planeación
        if (!$user->hasRole(['Super-Admin', 'PMD-Planeación'])) {
            if ($user->department_id) {
                $query->where('id', $user->department_id);
            }
        }

        $departments = $query->get();

        return Inertia::render('Directory/Index', [
            'departments' => $departments
        ]);
    }

    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'id_modelo' => 'required|integer',
            'type_model' => 'required|in:Department,AdministrativeUnit',
            'academic_degree' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'position_title' => 'required|string|max:150',
        ]);

        $modelClass = $validated['type_model'] === 'Department' ? Department::class : AdministrativeUnit::class;
        $target = $modelClass::findOrFail($validated['id_modelo']);

        $target->holder()->updateOrCreate(
            [
                'holdable_id' => $target->id,
                'holdable_type' => $modelClass,
            ],
            [
                'academic_degree' => $validated['academic_degree'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'position_title' => $validated['position_title'],
            ]
        );

        return redirect()->back()->with('success', 'Titular asignado correctamente.');
    }
}
