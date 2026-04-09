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
        Schema::create('substantive_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('administrative_unit_id')->constrained('administrative_units');
            $table->foreignId('development_theme_id')->constrained('development_themes');
            $table->string('name');
            $table->string('measurement_unit');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substantive_activities');
    }
};
