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
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->string('email', 150)->nullable()->after('nombre');
            $table->string('tipo_documento', 20)->nullable()->after('email');
            $table->string('numero_documento', 50)->nullable()->after('tipo_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropColumn(['email', 'tipo_documento', 'numero_documento']);
        });
    }
};
