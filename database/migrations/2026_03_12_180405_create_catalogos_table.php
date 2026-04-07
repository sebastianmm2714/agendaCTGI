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
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('cargo', 100);
            $table->enum('tipo', ['SUPERVISOR', 'ORDENADOR', 'VIATICOS']);
            $table->string('firma', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('categorias_personal', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion', 255);
            $table->timestamps();
        });

        Schema::create('clasificacion_informacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->timestamps();
        });

        Schema::create('estados_agenda', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_agenda');
        Schema::dropIfExists('clasificacion_informacion');
        Schema::dropIfExists('categorias_personal');
        Schema::dropIfExists('funcionarios');
    }
};
