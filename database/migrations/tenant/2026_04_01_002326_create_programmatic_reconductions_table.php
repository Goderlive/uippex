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
        Schema::create('programmatic_reconductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('administrative_unit_id')->constrained('administrative_units')->onDelete('cascade');
            $table->string('document_number');
            $table->date('requested_date');
            $table->unsignedTinyInteger('quarter'); // 1, 2, 3, 4
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->tinyInteger('status')->default(0); // 0: Borrador, 1: Enviado/En Revisión, 2: Autorizado/Aplicado, 3: Rechazado
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programmatic_reconductions');
    }
};
