<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\AdministrativeUnit;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the dependencies.
     */
    public function index()
    {
        $activeYearId = FiscalYear::where('is_active', true)->value('id');
        
        $departments = Department::with('administrativeUnits')
            ->where('fiscal_year_id', $activeYearId)
            ->get();
            
        $allDepartments = Department::where('fiscal_year_id', $activeYearId)->get();

        return Inertia::render('Settings/Departments/Index', [
            'departments' => $departments,
            'allDepartments' => $allDepartments,
        ]);
    }

    /**
     * Store a newly created dependency.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $activeYearId = FiscalYear::where('is_active', true)->value('id');

        if (!$activeYearId) {
            return redirect()->back()->with('error', 'No hay un año fiscal activo.');
        }

        Department::create([
            'name' => $request->name,
            'fiscal_year_id' => $activeYearId,
        ]);

        return redirect()->back()->with('message', 'Dependencia creada exitosamente.');
    }

    /**
     * Update the specified dependency.
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('message', 'Dependencia actualizada.');
    }

    /**
     * Remove the specified dependency.
     */
    public function destroy(Department $department)
    {
        if ($department->administrativeUnits()->count() > 0) {
            return redirect()->back()->with('error', 'No puedes eliminar una dependencia que tiene áreas asignadas.');
        }

        $department->delete();

        return redirect()->back()->with('message', 'Dependencia eliminada.');
    }
    
    /**
     * Update Area Name
     */
    public function updateArea(Request $request, AdministrativeUnit $area)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $area->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('message', 'Área actualizada.');
    }
    
    /**
     * Move Area to another Dependency
     */
    public function moveArea(Request $request, AdministrativeUnit $area)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        $area->update([
            'department_id' => $request->department_id,
        ]);

        return redirect()->back()->with('message', 'Área movida a otra dependencia.');
    }
}
