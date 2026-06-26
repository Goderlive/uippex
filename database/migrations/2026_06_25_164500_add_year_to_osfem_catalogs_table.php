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
        $tables = ['general_sectors', 'auxiliary_sectors', 'budget_programs', 'budget_projects'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->smallInteger('year')->default(2026)->after('code');
                
                // Drop the old index on code. Since it was created using $table->string('code')->index(),
                // the default index name is {$tableName}_code_index.
                $table->dropIndex("{$tableName}_code_index");
                
                // Add composite unique index
                $table->unique(['code', 'year']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['general_sectors', 'auxiliary_sectors', 'budget_programs', 'budget_projects'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropUnique("{$tableName}_code_year_unique");
                $table->index('code');
                $table->dropColumn('year');
            });
        }
    }
};
