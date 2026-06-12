<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_codigo_regional')) {
                $table->string('legalizacion_codigo_regional')->nullable()->after('orden_viaje');
            }
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_codigo_centro')) {
                $table->string('legalizacion_codigo_centro')->nullable()->after('legalizacion_codigo_regional');
            }
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_gastos_transporte')) {
                $table->json('legalizacion_gastos_transporte')->nullable()->after('legalizacion_codigo_centro');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_codigo_regional')) { $cols[] = 'legalizacion_codigo_regional'; }
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_codigo_centro')) { $cols[] = 'legalizacion_codigo_centro'; }
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_gastos_transporte')) { $cols[] = 'legalizacion_gastos_transporte'; }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
