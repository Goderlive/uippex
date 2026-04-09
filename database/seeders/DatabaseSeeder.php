<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tenantId = tenant('id') ?? 'localhost';
        
        $this->call([
            RolesSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => "admin@{$tenantId}.uippe.mx"],
            [
                'name' => 'Administrador ' . ucfirst($tenantId),
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            ]
        );

        $admin->assignRole('Super-Admin');
    }
}
