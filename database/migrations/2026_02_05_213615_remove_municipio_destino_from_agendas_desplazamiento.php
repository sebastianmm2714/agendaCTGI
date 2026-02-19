<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->dropColumn('municipio_destino');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('agendas_desplazamiento', function (Blueprint $table) {
            $table->string('municipio_destino');
        });
    }
};
