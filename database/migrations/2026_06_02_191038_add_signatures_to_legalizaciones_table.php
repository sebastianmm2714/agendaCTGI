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
        Schema::table('legalizaciones', function (Blueprint $table) {
            $table->string('firma_comisionado_path')->nullable()->after('observaciones');
            $table->string('firma_supervisor_path')->nullable()->after('firma_comisionado_path');
            $table->string('firma_ordenador_path')->nullable()->after('firma_supervisor_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legalizaciones', function (Blueprint $table) {
            $table->dropColumn(['firma_comisionado_path', 'firma_supervisor_path', 'firma_ordenador_path']);
        });
    }
};
