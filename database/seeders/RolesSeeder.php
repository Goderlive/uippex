<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiamos la cache de permisos de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super-Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'PMD-Planeación']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Enlace-Dependencia']);
    }
}
