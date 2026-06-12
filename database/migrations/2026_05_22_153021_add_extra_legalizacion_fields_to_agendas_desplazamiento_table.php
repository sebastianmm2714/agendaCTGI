<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_resultados')) {
                $table->json('legalizacion_resultados')->nullable()->after('legalizacion_planillas');
            }
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_compromisos')) {
                $table->json('legalizacion_compromisos')->nullable()->after('legalizacion_resultados');
            }
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_conclusiones')) {
                $table->json('legalizacion_conclusiones')->nullable()->after('legalizacion_compromisos');
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
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_resultados')) { $cols[] = 'legalizacion_resultados'; }
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_compromisos')) { $cols[] = 'legalizacion_compromisos'; }
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_conclusiones')) { $cols[] = 'legalizacion_conclusiones'; }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
