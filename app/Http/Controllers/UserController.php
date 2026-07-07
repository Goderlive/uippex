<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Department;
use App\Models\AdministrativeUnit;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['department', 'roles'])->get();
        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $activeFiscalYear = \App\Models\FiscalYear::where('is_active', true)->first();
        $departments = $activeFiscalYear 
            ? Department::where('fiscal_year_id', $activeFiscalYear->id)->get() 
            : Department::all();
        $roles = Role::where('name', '!=', 'Super-Admin')->get();

        return Inertia::render('Users/Create', [
            'departments' => $departments,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required|string|exists:roles,name',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $masterDeptId = null;
        if ($request->department_id) {
            $masterDeptId = \App\Models\Department::find($request->department_id)?->master_department_id;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'master_department_id' => $masterDeptId,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('message', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $activeFiscalYear = \App\Models\FiscalYear::where('is_active', true)->first();
        $departments = $activeFiscalYear 
            ? Department::where('fiscal_year_id', $activeFiscalYear->id)->get() 
            : Department::all();
        $roles = Role::where('name', '!=', 'Super-Admin')->get();

        $currentDepartment = $user->getCurrentDepartment();
        $user->load('roles');

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'current_department_id' => $currentDepartment ? $currentDepartment->id : null,
            'departments' => $departments,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class.',email,'.$user->id,
            'password' => ['nullable', Rules\Password::defaults()],
            'role' => 'required|string|exists:roles,name',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $masterDeptId = null;
        if ($request->department_id) {
            $masterDeptId = \App\Models\Department::find($request->department_id)?->master_department_id;
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'master_department_id' => $masterDeptId,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('message', 'Usuario actualizado exitosamente.');
    }
}
