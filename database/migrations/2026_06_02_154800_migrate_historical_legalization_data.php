<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $agendas = DB::table('agendas_desplazamiento')->get();

        foreach ($agendas as $agenda) {
            $hasData = !empty($agenda->legalizacion_codigo_regional) ||
                       !empty($agenda->legalizacion_codigo_centro) ||
                       !empty($agenda->legalizacion_gastos_transporte) ||
                       !empty($agenda->legalizacion_fotos) ||
                       !empty($agenda->legalizacion_planillas) ||
                       !empty($agenda->legalizacion_declaracion) ||
                       !empty($agenda->legalizacion_resultados) ||
                       !empty($agenda->legalizacion_compromisos) ||
                       !empty($agenda->legalizacion_conclusiones) ||
                       !empty($agenda->legalizado_at) ||
                       !empty($agenda->realiza_declaracion) ||
                       !empty($agenda->legalizacion_tiquetes) ||
                       !empty($agenda->legalizacion_estado) ||
                       !empty($agenda->legalizacion_observaciones);

            if ($hasData) {
                $exists = DB::table('legalizaciones')
                    ->where('agenda_desplazamiento_id', $agenda->id)
                    ->exists();

                if (!$exists) {
                    DB::table('legalizaciones')->insert([
                        'agenda_desplazamiento_id' => $agenda->id,
                        'codigo_regional' => $agenda->legalizacion_codigo_regional,
                        'codigo_centro' => $agenda->legalizacion_codigo_centro,
                        'gastos_transporte' => $agenda->legalizacion_gastos_transporte,
                        'fotos' => $agenda->legalizacion_fotos,
                        'planillas' => $agenda->legalizacion_planillas,
                        'declaracion_path' => $agenda->legalizacion_declaracion,
                        'resultados' => $agenda->legalizacion_resultados,
                        'compromisos' => $agenda->legalizacion_compromisos,
                        'conclusiones' => $agenda->legalizacion_conclusiones,
                        'legalizado_at' => $agenda->legalizado_at,
                        'realiza_declaracion' => $agenda->realiza_declaracion ? 1 : 0,
                        'tiquetes' => $agenda->legalizacion_tiquetes,
                        'estado' => $agenda->legalizacion_estado ?? 'CREADA',
                        'observaciones' => $agenda->legalizacion_observaciones,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('legalizaciones')->truncate();
    }
};
