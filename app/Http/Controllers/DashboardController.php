<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Si es administrador, redirigir a su panel específico
        if ($user->role === 'administrador') {
            return redirect()->route('admin.dashboard');
        }

        $filtro = $request->get('ver');

        // 1. Base de la consulta
        $query = AgendaDesplazamiento::query();

        // 2. Definir qué es "Pendiente" para cada rol
        $estadoPendienteNombre = match ($user->role) {
                'administrador', 'contratista' => 'BORRADOR',
                'supervisor_contrato' => 'ENVIADA',
                'viaticos' => 'APROBADA_SUPERVISOR',
                'ordenador_gasto' => 'APROBADA_VIATICOS',
                default => 'ENVIADA',
            };

        $estadoPendiente = \App\Models\EstadoAgenda::where('nombre', $estadoPendienteNombre)->first();

        // 3. Aplicar privacidad de registros
        if ($user->role === 'administrador' || $user->role === 'contratista') {
            $query->where('user_id', $user->id);
        }

        // 4. ESTADÍSTICAS DINÁMICAS
        // Devueltas: Tienen observaciones y no están finalizadas
        $queryDevueltas = (clone $query)->whereNotNull('observaciones_finanzas')
            ->whereHas('estado', function($q) {
                $q->whereNotIn('nombre', ['APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA']);
            });
        $idDevueltas = $queryDevueltas->pluck('id')->toArray();

        // Enviadas: Ya no son borradores, no son "pendientes de accion" y no están devueltas
        $queryEnviadas = match ($user->role) {
                'administrador', 'contratista' => (clone $query)->whereHas('estado', function($q){ $q->whereNotIn('nombre', ['BORRADOR', 'APROBADA']); }),
                'supervisor_contrato' => (clone $query)->whereHas('estado', function($q){ $q->whereIn('nombre', ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA']); }),
                'viaticos' => (clone $query)->whereHas('estado', function($q){ $q->whereIn('nombre', ['APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA']); }),
                'ordenador_gasto' => (clone $query)->whereHas('estado', function($q){ $q->where('nombre', 'APROBADA'); }),
                default => (clone $query)->whereHas('estado', function($q){ $q->where('nombre', 'APROBADA_VIATICOS'); }),
            };

        // Excluir devueltas de "Enviadas" para evitar duplicidad visual
        $queryEnviadas->whereNotIn('id', $idDevueltas);

        $stats = [
            'pendientes' => (clone $query)->where('estado_id', $estadoPendiente?->id)->whereNotIn('id', $idDevueltas)->count(),
            'enviadas' => $queryEnviadas->count(),
            'devueltas' => $queryDevueltas->count(),
        ];

        // 5. Listado para la tabla
        $agendas = $query->with(['user', 'estado', 'clasificacion'])
            ->when($filtro == 'pendientes', function ($q) use ($estadoPendiente, $idDevueltas) {
                return $q->where('estado_id', $estadoPendiente?->id)->whereNotIn('id', $idDevueltas);
            })
            ->when($filtro == 'enviadas', function ($q) use ($queryEnviadas) {
                return $q->whereIn('id', $queryEnviadas->pluck('id'));
            })
            ->when($filtro == 'devueltas', function ($q) use ($idDevueltas) {
                return $q->whereIn('id', $idDevueltas);
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('inicio', compact('stats', 'agendas', 'filtro'));
    }
}