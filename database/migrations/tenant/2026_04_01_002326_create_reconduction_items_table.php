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
        Schema::create('reconduction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programmatic_reconduction_id')->constrained('programmatic_reconductions')->onDelete('cascade');
            $table->foreignId('substantive_activity_id')->constrained('substantive_activities');
            $table->string('modification_type', 30); // reduction, cancellation, increase, creation
            $table->decimal('previous_annual_goal', 15, 2);
            $table->decimal('new_annual_goal', 15, 2);
            $table->decimal('achieved_so_far', 15, 2);
            $table->jsonb('previous_schedule');
            $table->jsonb('new_schedule');
            $table->text('justification');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconduction_items');
    }
};
