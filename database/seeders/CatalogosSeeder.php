<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Funcionario;
use App\Models\CategoriaPersonal;
use App\Models\ClasificacionInformacion;
use App\Models\EstadoAgenda;
use App\Models\ObligacionContrato;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        // Categorías de Personal
        $instructor = CategoriaPersonal::create([
            'nombre' => 'INSTRUCTOR',
            'descripcion' => 'Personal encargado de la formación'
        ]);

        $apoyo = CategoriaPersonal::create([
            'nombre' => 'APOYOS ADM Y GEST',
            'descripcion' => 'Personal de apoyo administrativo y gestión'
        ]);

        // Obligaciones para Instructores (Ejemplos basados en conocimiento previo o genéricos)
        $obligacionesInstructor = [
            'Impartir formación profesional integral',
            'Evaluar el aprendizaje de los aprendices',
            'Participar en procesos de investigación',
            'Elaborar guías de aprendizaje',
        ];

        foreach ($obligacionesInstructor as $nombre) {
            ObligacionContrato::create([
                'categoria_personal_id' => $instructor->id,
                'nombre' => $nombre
            ]);
        }

        // Obligaciones para Apoyo (Ejemplos)
        $obligacionesApoyo = [
            'Apoyar la gestión administrativa del centro',
            'Gestionar bases de datos y archivos',
            'Brindar soporte en procesos de contratación',
        ];

        foreach ($obligacionesApoyo as $nombre) {
            ObligacionContrato::create([
                'categoria_personal_id' => $apoyo->id,
                'nombre' => $nombre
            ]);
        }

        // Clasificación de Información
        ClasificacionInformacion::create(['nombre' => 'PÚBLICA']);
        ClasificacionInformacion::create(['nombre' => 'INTERNA']);
        ClasificacionInformacion::create(['nombre' => 'CONFIDENCIAL']);

        // Estados de Agenda
        EstadoAgenda::create(['nombre' => 'BORRADOR', 'descripcion' => 'Agenda en creación']);
        EstadoAgenda::create(['nombre' => 'ENVIADA', 'descripcion' => 'Enviada a coordinación']);
        EstadoAgenda::create(['nombre' => 'APROBADA_SUPERVISOR', 'descripcion' => 'Aprobada por el supervisor']);
        EstadoAgenda::create(['nombre' => 'APROBADA_VIATICOS', 'descripcion' => 'Aprobada por Viáticos']);
        EstadoAgenda::create(['nombre' => 'APROBADA_ORDENADOR', 'descripcion' => 'Aprobación final del ordenador']);
        EstadoAgenda::create(['nombre' => 'APROBADA', 'descripcion' => 'Proceso completo']);
        EstadoAgenda::create(['nombre' => 'CORRECCIÓN', 'descripcion' => 'Requiere ajustes por el usuario']);

        // Funcionarios (Ejemplos)
        Funcionario::create([
            'nombre' => 'LUIS FERNANDO MALDONADO',
            'cargo' => 'SUBDIRECTOR DE CENTRO',
            'tipo' => 'ORDENADOR'
        ]);

        Funcionario::create([
            'nombre' => 'MARIA HELENA JIMENEZ',
            'cargo' => 'COORDINADORA ACADÉMICA',
            'tipo' => 'SUPERVISOR'
        ]);
    }
}
