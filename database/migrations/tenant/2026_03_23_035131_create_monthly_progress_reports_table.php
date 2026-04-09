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
        Schema::create('monthly_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('substantive_activity_id')->constrained('substantive_activities')->onDelete('cascade');
            $table->unsignedTinyInteger('month'); // 1 to 12
            $table->decimal('reported_value', 15, 2);
            $table->string('evidence_path')->nullable();
            $table->tinyInteger('status')->default(0); // 0=En Revisión, 1=Validado
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['substantive_activity_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_progress_reports');
    }
};
