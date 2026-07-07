<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\AdministrativeUnit;
use App\Models\FiscalYear;
use App\Models\GeneralSector;
use App\Models\AuxiliarySector;
use App\Models\BudgetProject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the dependencies.
     */
    public function index()
    {
        $activeYear = FiscalYear::where('is_active', true)->first();
        $activeYearId = $activeYear ? $activeYear->id : null;
        $activeYearValue = $activeYear ? $activeYear->year : date('Y');
        
        $departments = Department::with(['administrativeUnits.generalSector', 'administrativeUnits.auxiliarySector', 'administrativeUnits.budgetProject'])
            ->where('fiscal_year_id', $activeYearId)
            ->get();
            
        $allDepartments = Department::where('fiscal_year_id', $activeYearId)->get();

        $generalSectors = GeneralSector::where('year', $activeYearValue)->get();
        $auxiliarySectors = AuxiliarySector::where('year', $activeYearValue)->get();
        $budgetProjects = BudgetProject::where('year', $activeYearValue)->get();

        return Inertia::render('Settings/Departments/Index', [
            'departments' => $departments,
            'allDepartments' => $allDepartments,
            'generalSectors' => $generalSectors,
            'auxiliarySectors' => $auxiliarySectors,
            'budgetProjects' => $budgetProjects,
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
     * Store a new Area (AdministrativeUnit)
     */
    public function storeArea(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'general_sector_id' => ['required', \Illuminate\Validation\Rule::exists(\App\Models\GeneralSector::class, 'id')],
            'auxiliary_sector_id' => ['required', \Illuminate\Validation\Rule::exists(\App\Models\AuxiliarySector::class, 'id')],
            'budget_project_id' => ['required', \Illuminate\Validation\Rule::exists(\App\Models\BudgetProject::class, 'id')],
        ]);

        $activeYearId = FiscalYear::where('is_active', true)->value('id');

        if (!$activeYearId) {
            return redirect()->back()->with('error', 'No hay un año fiscal activo.');
        }

        AdministrativeUnit::create([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'general_sector_id' => $request->general_sector_id,
            'auxiliary_sector_id' => $request->auxiliary_sector_id,
            'budget_project_id' => $request->budget_project_id,
            'fiscal_year_id' => $activeYearId,
        ]);

        return redirect()->back()->with('message', 'Área creada exitosamente.');
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
