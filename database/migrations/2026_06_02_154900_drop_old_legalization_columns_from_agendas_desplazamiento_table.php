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
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->dropColumn([
                'legalizacion_codigo_regional',
                'legalizacion_codigo_centro',
                'legalizacion_gastos_transporte',
                'legalizacion_fotos',
                'legalizacion_planillas',
                'legalizacion_declaracion',
                'legalizacion_resultados',
                'legalizacion_compromisos',
                'legalizacion_conclusiones',
                'legalizado_at',
                'realiza_declaracion',
                'legalizacion_tiquetes',
                'legalizacion_estado',
                'legalizacion_observaciones',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->string('legalizacion_codigo_regional')->nullable();
            $table->string('legalizacion_codigo_centro')->nullable();
            $table->text('legalizacion_gastos_transporte')->nullable();
            $table->text('legalizacion_fotos')->nullable();
            $table->text('legalizacion_planillas')->nullable();
            $table->string('legalizacion_declaracion')->nullable();
            $table->text('legalizacion_resultados')->nullable();
            $table->text('legalizacion_compromisos')->nullable();
            $table->text('legalizacion_conclusiones')->nullable();
            $table->timestamp('legalizado_at')->nullable();
            $table->boolean('realiza_declaracion')->default(false);
            $table->text('legalizacion_tiquetes')->nullable();
            $table->string('legalizacion_estado')->default('CREADA');
            $table->text('legalizacion_observaciones')->nullable();
        });
    }
};
