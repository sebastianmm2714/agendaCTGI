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
            if (!Schema::hasColumn('agendas_desplazamiento', 'orden_viaje')) {
                $table->string('orden_viaje', 100)->nullable()->after('cdp');
            }
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_fotos')) {
                $table->json('legalizacion_fotos')->nullable()->after('orden_viaje');
            }
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_planillas')) {
                $table->json('legalizacion_planillas')->nullable()->after('legalizacion_fotos');
            }
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizado_at')) {
                $table->timestamp('legalizado_at')->nullable()->after('legalizacion_planillas');
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
            if (Schema::hasColumn('agendas_desplazamiento', 'orden_viaje')) { $cols[] = 'orden_viaje'; }
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_fotos')) { $cols[] = 'legalizacion_fotos'; }
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_planillas')) { $cols[] = 'legalizacion_planillas'; }
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizado_at')) { $cols[] = 'legalizado_at'; }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
