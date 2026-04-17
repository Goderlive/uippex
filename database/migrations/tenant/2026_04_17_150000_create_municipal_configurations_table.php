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
        Schema::create('municipal_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->string('shield_path')->nullable();
            $table->string('official_name', 100)->default('H. Ayuntamiento');
            $table->string('administration_period', 50)->nullable();
            $table->string('primary_color', 7)->default('#333333');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipal_configurations');
    }
};
