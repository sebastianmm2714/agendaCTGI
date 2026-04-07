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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('tipo_documento', 20)->nullable();
            $table->string('numero_documento', 30)->nullable();
            $table->string('numero_contrato', 50)->nullable();
            $table->year('anio_contrato')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->text('objeto_contractual')->nullable();
            $table->string('firma', 255)->nullable();
            $table->decimal('salario_honorarios', 15, 2)->nullable();
            
            $table->foreignId('categoria_personal_id')->nullable()->constrained('categorias_personal')->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('funcionarios')->nullOnDelete();
            $table->foreignId('ordenador_id')->nullable()->constrained('funcionarios')->nullOnDelete();

            $table->enum('role', [
                'administrador', 
                'contratista', 
                'funcionario',
                'supervisor_contrato', 
                'ordenador_gasto', 
                'viaticos'
            ])->default('contratista');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
