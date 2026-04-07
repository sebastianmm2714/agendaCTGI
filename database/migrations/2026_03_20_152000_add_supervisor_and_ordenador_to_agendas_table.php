<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('ordenador_id')->nullable()->after('supervisor_id');

            $table->foreign('supervisor_id')->references('id')->on('funcionarios')->onDelete('set null');
            $table->foreign('ordenador_id')->references('id')->on('funcionarios')->onDelete('set null');
        });

        // Poblar registros existentes con la información actual de los usuarios
        DB::statement("
            UPDATE agendas_desplazamiento a
            JOIN users u ON a.user_id = u.id
            SET a.supervisor_id = u.supervisor_id,
                a.ordenador_id = u.ordenador_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropForeign(['ordenador_id']);
            $table->dropColumn(['supervisor_id', 'ordenador_id']);
        });
    }
};
