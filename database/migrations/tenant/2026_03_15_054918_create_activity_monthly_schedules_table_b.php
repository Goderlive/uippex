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
        Schema::create('activity_monthly_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('substantive_activity_id')->constrained('substantive_activities');
            
            // Programmed Months
            $table->decimal('jan_programmed', 15, 2)->default(0.00);
            $table->decimal('feb_programmed', 15, 2)->default(0.00);
            $table->decimal('mar_programmed', 15, 2)->default(0.00);
            $table->decimal('apr_programmed', 15, 2)->default(0.00);
            $table->decimal('may_programmed', 15, 2)->default(0.00);
            $table->decimal('jun_programmed', 15, 2)->default(0.00);
            $table->decimal('jul_programmed', 15, 2)->default(0.00);
            $table->decimal('aug_programmed', 15, 2)->default(0.00);
            $table->decimal('sep_programmed', 15, 2)->default(0.00);
            $table->decimal('oct_programmed', 15, 2)->default(0.00);
            $table->decimal('nov_programmed', 15, 2)->default(0.00);
            $table->decimal('dec_programmed', 15, 2)->default(0.00);

            // Executed Months
            $table->decimal('jan_executed', 15, 2)->default(0.00);
            $table->decimal('feb_executed', 15, 2)->default(0.00);
            $table->decimal('mar_executed', 15, 2)->default(0.00);
            $table->decimal('apr_executed', 15, 2)->default(0.00);
            $table->decimal('may_executed', 15, 2)->default(0.00);
            $table->decimal('jun_executed', 15, 2)->default(0.00);
            $table->decimal('jul_executed', 15, 2)->default(0.00);
            $table->decimal('aug_executed', 15, 2)->default(0.00);
            $table->decimal('sep_executed', 15, 2)->default(0.00);
            $table->decimal('oct_executed', 15, 2)->default(0.00);
            $table->decimal('nov_executed', 15, 2)->default(0.00);
            $table->decimal('dec_executed', 15, 2)->default(0.00);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_monthly_schedules');
    }
};
