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
        Schema::create('legalizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_desplazamiento_id')
                  ->constrained('agendas_desplazamiento')
                  ->onDelete('cascade');
            
            $table->string('codigo_regional')->nullable();
            $table->string('codigo_centro')->nullable();
            $table->text('gastos_transporte')->nullable();
            $table->text('fotos')->nullable();
            $table->text('planillas')->nullable();
            $table->string('declaracion_path')->nullable();
            $table->text('resultados')->nullable();
            $table->text('compromisos')->nullable();
            $table->text('conclusiones')->nullable();
            $table->timestamp('legalizado_at')->nullable();
            $table->boolean('realiza_declaracion')->default(false);
            $table->text('tiquetes')->nullable();
            $table->string('estado')->default('CREADA');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalizaciones');
    }
};
