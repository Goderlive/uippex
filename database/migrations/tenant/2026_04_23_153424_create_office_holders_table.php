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
        Schema::create('office_holders', function (Blueprint $table) {
            $table->id();
            $table->morphs('holdable');
            $table->string('academic_degree', 50)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('position_title', 150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_holders');
    }
};
