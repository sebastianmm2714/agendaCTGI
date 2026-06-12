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
                'estado_legalizacion',
                'observaciones_legalizacion',
                'firma_supervisor_leg_path',
                'firma_ordenador_leg_path'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->string('estado_legalizacion')->nullable();
            $table->text('observaciones_legalizacion')->nullable();
            $table->string('firma_supervisor_leg_path')->nullable();
            $table->string('firma_ordenador_leg_path')->nullable();
        });
    }
};
