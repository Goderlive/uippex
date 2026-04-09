<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class LandlordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $tenant = Tenant::updateOrCreate(
                ['id' => 'calimaya'],
                ['plan' => 'premium']
            );

            $tenant->domains()->updateOrCreate(
                ['domain' => 'calimaya.uippex.com']
            );

            $this->command->info('Tenant Calimaya created successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error creating tenant: ' . $e->getMessage());
            Log::error('Tenant creation failed', ['error' => $e->getMessage()]);
        }
    }
}
