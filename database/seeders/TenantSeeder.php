<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Stancl\Tenancy\Database\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::create(['id' => 'toluca']);
        
        $tenant->domains()->create(['domain' => 'toluca.uippe.mx']);
        $tenant->domains()->create(['domain' => 'toluca.localhost']);
    }
}
