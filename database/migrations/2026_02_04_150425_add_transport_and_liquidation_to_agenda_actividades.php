<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agenda_actividades', function (Blueprint $table) {
            $table->json('transporte_ida')->nullable()->after('ruta_ida');
            $table->json('transporte_regreso')->nullable()->after('ruta_regreso');
            $table->string('valor_aereo', 50)->nullable()->after('observaciones');
            $table->string('valor_terrestre', 50)->nullable()->after('valor_aereo');
            $table->string('valor_intermunicipal', 50)->nullable()->after('valor_terrestre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_actividades', function (Blueprint $table) {
            $table->dropColumn(['transporte_ida', 'transporte_regreso', 'valor_aereo', 'valor_terrestre', 'valor_intermunicipal']);
        });
    }
};
