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
        $departments = Department::all();
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('message', 'Usuario creado exitosamente.');
    }
}
