<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendaDesplazamiento;
use App\Models\EstadoAgenda;
use App\Models\CategoriaPersonal;
use App\Models\User;
use App\Models\Funcionario;
use App\Models\ObligacionContrato;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Dashboard del administrador con estadísticas globales.
     */
    public function dashboard()
    {
        $stats = [
            'total_agendas' => AgendaDesplazamiento::count(),
            'enviadas' => AgendaDesplazamiento::whereHas('estado', function($q) {
                $q->whereIn('nombre', ['ENVIADA', 'APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA_ORDENADOR']);
            })->count(),
            'devueltas' => AgendaDesplazamiento::whereNotNull('observaciones_finanzas')
                ->whereHas('estado', function($q) {
                    $q->whereNotIn('nombre', ['APROBADA']);
                })->count(),
            'finalizadas' => AgendaDesplazamiento::whereHas('estado', function($q) {
                $q->where('nombre', 'APROBADA');
            })->count(),
            'total_usuarios' => User::count(),
            'total_funcionarios' => Funcionario::count(),
        ];

        // Reporte Detallado de Agendas
        $agendas = AgendaDesplazamiento::with(['user', 'estado', 'clasificacion'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'agendas'));
    }

    /**
     * Vista unificada de catálogos (Estados y Categorías).
     */
    public function catalogos()
    {
        $estados = EstadoAgenda::orderBy('nombre')->get();
        $categorias = CategoriaPersonal::orderBy('nombre')->get();
        $obligaciones = ObligacionContrato::with('categoria')->orderBy('nombre')->get();
        
        return view('admin.catalogos.index', compact('estados', 'categorias', 'obligaciones'));
    }
}
