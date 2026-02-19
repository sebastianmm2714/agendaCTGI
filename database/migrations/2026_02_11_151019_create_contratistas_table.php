<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('contratistas', function (Blueprint $table) {
            $table->id();
            $table->string ('cedula', 20)->unique();
            $table->string ('nombre', 150);
            $table->string('numero_cuenta', 50);

            $table->foreignId('tipo_vinculacion_id')
                ->constrained('tipos_vinculacion')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('contratistas');
    }
};
