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
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->enum('estado', [
                'BORRADOR',
                'ENVIADA',
                'REVISION',
                'APROBADA',
                'RECHAZADA',
                'FIRMADA_SUPERVISOR',
                'FIRMADA_ORDENADOR'
            ])->default('BORRADOR')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->enum('estado', [
                'BORRADOR',
                'ENVIADA',
                'REVISION',
                'APROBADA',
                'RECHAZADA',
                'FIRMADA_SUPERVISOR',
                'FIRMADA_ORDENADOR'
            ])->default('BORRADOR')->change();
        });
    }
};
