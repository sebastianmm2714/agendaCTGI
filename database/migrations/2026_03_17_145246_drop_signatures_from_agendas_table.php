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
            $table->dropColumn(['firma_supervisor', 'firma_ordenador', 'fecha_firma_coordinador']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->string('firma_supervisor')->nullable()->after('valor_intermunicipal');
            $table->string('firma_ordenador')->nullable()->after('firma_supervisor');
            $table->timestamp('fecha_firma_coordinador')->nullable()->after('firma_ordenador');
        });
    }
};
