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
        Schema::create('carga_masiva_historials', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_archivo');
            $table->string('ruta_reporte');
            $table->integer('total_registros')->default(0);
            $table->integer('total_exito')->default(0);
            $table->integer('total_errores')->default(0);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carga_masiva_historials');
    }
};
