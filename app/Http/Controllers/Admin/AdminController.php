<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendaDesplazamiento;
use App\Models\EstadoAgenda;
use App\Models\CategoriaPersonal;
use App\Models\User;
use App\Models\LiderDeProceso;
use App\Models\ObligacionContrato;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Dashboard del administrador con estadísticas globales.
     */
    public function dashboard(Request $request)
    {
        $statusFilter = $request->get('status');
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);

        $stats = [
            'total_agendas' => AgendaDesplazamiento::count(),
            'enviadas' => AgendaDesplazamiento::whereHas('estado', function ($q) {
                $q->whereIn('nombre', ['ENVIADA', 'APROBADA_SUPERVISOR', 'APROBADA_VIATICOS']);
            })->count(),
            'devueltas' => AgendaDesplazamiento::whereHas('estado', function ($q) {
                $q->where('nombre', 'CORRECCIÓN');
            })->count(),
            'finalizadas' => AgendaDesplazamiento::whereHas('estado', function ($q) {
                $q->where('nombre', 'APROBADA');
            })->count(),
            'total_usuarios' => User::count(),
            'total_lideres' => LiderDeProceso::count(),
        ];

        // Query Builder for Agendas
        $query = AgendaDesplazamiento::with(['user', 'estado', 'clasificacion'])
            ->latest('updated_at');

        // Apply Status Filters
        if ($statusFilter) {
            if ($statusFilter === 'proceso') {
                $query->whereHas('estado', function ($q) {
                    $q->whereIn('nombre', ['ENVIADA', 'APROBADA_SUPERVISOR', 'APROBADA_VIATICOS']);
                });
            } elseif ($statusFilter === 'DEVUELTA') {
                $query->whereHas('estado', function ($q) {
                    $q->where('nombre', 'CORRECCIÓN');
                });
            } elseif ($statusFilter === 'APROBADA') {
                $query->whereHas('estado', function ($q) {
                    $q->where('nombre', 'APROBADA');
                });
            }
        }


        // Apply Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%$search%")
                           ->orWhere('numero_documento', 'like', "%$search%");
                    });
            });
        }

        $agendas = $query->paginate($perPage)->appends($request->all());

        return view('admin.dashboard', compact('stats', 'agendas', 'statusFilter'));
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
