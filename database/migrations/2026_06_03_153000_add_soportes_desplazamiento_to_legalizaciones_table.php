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
            $table->text('soportes_desplazamiento')->nullable()->after('conclusiones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legalizaciones', function (Blueprint $table) {
            $table->dropColumn('soportes_desplazamiento');
        });
    }
};
