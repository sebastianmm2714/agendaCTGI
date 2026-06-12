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
        // Modificar columna role en users usando DB::statement para evitar dependencias extras
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('contratista', 'administrador', 'viaticos', 'supervisor_contrato', 'ordenador_gasto', 'funcionario', 'legalizacion') NOT NULL DEFAULT 'contratista'");

        // Añadir columnas de estado y observaciones de legalización
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_estado')) {
                $table->string('legalizacion_estado')->nullable()->after('legalizado_at');
            }
            if (!Schema::hasColumn('agendas_desplazamiento', 'legalizacion_observaciones')) {
                $table->text('legalizacion_observaciones')->nullable()->after('legalizacion_estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir columnas en agendas_desplazamiento
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_estado')) { $cols[] = 'legalizacion_estado'; }
            if (Schema::hasColumn('agendas_desplazamiento', 'legalizacion_observaciones')) { $cols[] = 'legalizacion_observaciones'; }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        // Revertir rol en users
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('contratista', 'administrador', 'viaticos', 'supervisor_contrato', 'ordenador_gasto', 'funcionario') NOT NULL DEFAULT 'contratista'");
    }
};

