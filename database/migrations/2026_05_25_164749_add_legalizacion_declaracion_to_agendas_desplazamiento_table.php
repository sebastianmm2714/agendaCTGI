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
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_declaracion')) {
                $table->string('legalizacion_declaracion')->nullable()->after('legalizacion_planillas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_declaracion')) {
                $table->dropColumn('legalizacion_declaracion');
            }
        });
    }
};
