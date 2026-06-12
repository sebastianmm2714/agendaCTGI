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
            if (!Schema::hasColumn('agendas_desplazamiento', 'realiza_declaracion')) {
                $table->boolean('realiza_declaracion')->nullable()->after('legalizado_at');
            }
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_tiquetes')) {
                $table->json('legalizacion_tiquetes')->nullable()->after('realiza_declaracion');
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
            if (Schema::hasColumn('agendas_desplazamiento', 'realiza_declaracion')) { $cols[] = 'realiza_declaracion'; }
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_tiquetes')) { $cols[] = 'legalizacion_tiquetes'; }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
