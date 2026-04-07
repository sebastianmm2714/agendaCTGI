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
        Schema::create('obligaciones_contrato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_personal_id')->constrained('categorias_personal')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->timestamps();
        });

        Schema::create('agendas_desplazamiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('clasificacion_id')->constrained('clasificacion_informacion');
            $table->foreignId('estado_id')->constrained('estados_agenda');

            $table->date('fecha_elaboracion');
            $table->string('ruta', 255);
            $table->string('entidad_empresa', 150);
            $table->string('contacto', 150);
            $table->string('objetivo_desplazamiento', 150);
            
            $table->string('regional', 150)->nullable();
            $table->string('centro', 150)->nullable();
            $table->json('destinos')->nullable();

            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->decimal('valor_viaticos', 12, 2)->nullable();
            $table->text('observaciones_finanzas')->nullable();
            $table->string('cdp', 50)->nullable();

            $table->decimal('valor_terminal_aereo', 12, 2)->nullable();
            $table->decimal('valor_terminal_terrestre', 12, 2)->nullable();
            $table->decimal('valor_intermunicipal', 12, 2)->nullable();

            $table->timestamps();
        });

        Schema::create('agenda_obligaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas_desplazamiento')->cascadeOnDelete();
            $table->foreignId('obligacion_id')->constrained('obligaciones_contrato')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas_desplazamiento')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('ruta_ida', 255)->nullable();
            $table->string('ruta_regreso', 255)->nullable();
            $table->json('transporte_ida')->nullable();
            $table->json('transporte_regreso')->nullable();
            $table->text('actividad');
            $table->decimal('valor_aereo', 12, 2)->nullable();
            $table->decimal('valor_terrestre', 12, 2)->nullable();
            $table->decimal('valor_intermunicipal', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
        Schema::dropIfExists('agenda_obligaciones');
        Schema::dropIfExists('agendas_desplazamiento');
        Schema::dropIfExists('obligaciones_contrato');
    }
};
