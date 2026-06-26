<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bloque A: Schema Add
        Schema::table('administrative_units', function (Blueprint $table) {
            $table->unsignedBigInteger('general_sector_id')->nullable();
            $table->unsignedBigInteger('auxiliary_sector_id')->nullable();
            $table->unsignedBigInteger('budget_project_id')->nullable();
        });

        // Bloque B: Data Patching (Cross-Database)
        DB::table('administrative_units')->orderBy('id')->chunk(100, function ($units) {
            foreach ($units as $unit) {
                // Fetch IDs from Landlord models (defaulting to year 2026)
                $generalSectorId = \App\Models\GeneralSector::where('code', $unit->general_sector_code)
                    ->where('year', 2026)
                    ->value('id');
                
                $auxiliarySectorId = \App\Models\AuxiliarySector::where('code', $unit->auxiliary_sector_code)
                    ->where('year', 2026)
                    ->value('id');
                    
                $budgetProjectId = \App\Models\BudgetProject::where('code', $unit->budget_project_code)
                    ->where('year', 2026)
                    ->value('id');

                DB::table('administrative_units')
                    ->where('id', $unit->id)
                    ->update([
                        'general_sector_id' => $generalSectorId,
                        'auxiliary_sector_id' => $auxiliarySectorId,
                        'budget_project_id' => $budgetProjectId,
                    ]);
            }
        });

        // Bloque C: Schema Drop
        Schema::table('administrative_units', function (Blueprint $table) {
            $table->dropColumn([
                'general_sector_code',
                'auxiliary_sector_code',
                'budget_project_code'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('administrative_units', function (Blueprint $table) {
            $table->string('general_sector_code')->nullable();
            $table->string('auxiliary_sector_code')->nullable();
            $table->string('budget_project_code')->nullable();
        });

        DB::table('administrative_units')->orderBy('id')->chunk(100, function ($units) {
            foreach ($units as $unit) {
                $generalSectorCode = \App\Models\GeneralSector::where('id', $unit->general_sector_id)->value('code');
                $auxiliarySectorCode = \App\Models\AuxiliarySector::where('id', $unit->auxiliary_sector_id)->value('code');
                $budgetProjectCode = \App\Models\BudgetProject::where('id', $unit->budget_project_id)->value('code');

                DB::table('administrative_units')
                    ->where('id', $unit->id)
                    ->update([
                        'general_sector_code' => $generalSectorCode,
                        'auxiliary_sector_code' => $auxiliarySectorCode,
                        'budget_project_code' => $budgetProjectCode,
                    ]);
            }
        });

        Schema::table('administrative_units', function (Blueprint $table) {
            $table->dropColumn([
                'general_sector_id',
                'auxiliary_sector_id',
                'budget_project_id'
            ]);
        });
    }
};
