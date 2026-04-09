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
        Schema::create('monthly_execution_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_monthly_schedule_id')->constrained('activity_monthly_schedules');
            $table->tinyInteger('month');
            $table->decimal('reported_value', 15, 2);
            $table->text('justification')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_execution_logs');
    }
};
