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

        if ($user->role === 'legalizacion') {
            $filtro  = $request->get('ver');
            $search  = $request->get('search');
            $perPage = (int) $request->get('per_page', 10);

            $baseStatsQuery = AgendaDesplazamiento::query()->has('legalizacion');

            $stats = [
                'pendientes'  => (clone $baseStatsQuery)->whereHas('legalizacion', function($q) { $q->where('estado', 'APROBADA_SUPERVISOR'); })->count(),
                'enviadas'    => (clone $baseStatsQuery)->whereHas('legalizacion', function($q) { $q->where('estado', 'APROBADA_LEGALIZACION'); })->count(),
                'finalizadas' => (clone $baseStatsQuery)->whereHas('legalizacion', function($q) { $q->where('estado', 'APROBADA_ORDENADOR'); })->count(),
                'devueltas'   => (clone $baseStatsQuery)->whereHas('legalizacion', function($q) { $q->whereIn('estado', ['DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']); })->count(),
            ];

            $query = AgendaDesplazamiento::query()->has('legalizacion');

            if ($request->filled('search')) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($qu) use ($search) {
                        $qu->where('name', 'like', "%$search%")
                           ->orWhere('numero_documento', 'like', "%$search%");
                    })
                    ->orWhere('ruta', 'like', "%$search%")
                    ->orWhere('destinos', 'like', "%$search%")
                    ->orWhereRaw("DATE_FORMAT(fecha_inicio, '%d/%m/%Y') LIKE ?", ["%$search%"])
                    ->orWhereRaw("DATE_FORMAT(fecha_fin, '%d/%m/%Y') LIKE ?", ["%$search%"]);
                });
            }

            $agendas = $query->with(['user', 'estado', 'clasificacion', 'actividades', 'obligaciones', 'legalizacion'])
                ->when($filtro === 'pendientes', function ($q) {
                    return $q->whereHas('legalizacion', function($sq) { $sq->where('estado', 'APROBADA_SUPERVISOR'); });
                })
                ->when($filtro === 'enviadas', function ($q) {
                    return $q->whereHas('legalizacion', function($sq) { $sq->where('estado', 'APROBADA_LEGALIZACION'); });
                })
                ->when($filtro === 'finalizadas', function ($q) {
                    return $q->whereHas('legalizacion', function($sq) { $sq->where('estado', 'APROBADA_ORDENADOR'); });
                })
                ->when($filtro === 'devueltas', function ($q) {
                    return $q->whereHas('legalizacion', function($sq) { $sq->whereIn('estado', ['DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']); });
                })
                ->orderBy('updated_at', 'desc')
                ->paginate($perPage)->appends($request->all());

            session(['back_url_reportar_dia' => $request->fullUrl()]);
            $supervisores = collect();

            return view('inicio', compact('stats', 'agendas', 'filtro', 'supervisores'));
        }

        $filtro  = $request->get('ver');
        $search  = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);

        // 1. Base de la consulta
        $query = AgendaDesplazamiento::query();

        // 2. Aplicar privacidad de registros (Multi-tenancy por rol)
        if ($user->role === 'contratista' || $user->role === 'funcionario') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'supervisor_contrato') {
            $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
            $query->where('supervisor_id', $funcionario ? $funcionario->id : 0);
        } elseif ($user->role === 'ordenador_gasto') {
            $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
            $query->where('ordenador_id', $funcionario ? $funcionario->id : 0);
        }

        // 3. BÚSQUEDA INTELIGENTE (Si aplica)
        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%$search%")
                       ->orWhere('numero_documento', 'like', "%$search%");
                })
                ->orWhere('ruta', 'like', "%$search%")
                ->orWhere('destinos', 'like', "%$search%")
                ->orWhereRaw("DATE_FORMAT(fecha_inicio, '%d/%m/%Y') LIKE ?", ["%$search%"])
                ->orWhereRaw("DATE_FORMAT(fecha_fin, '%d/%m/%Y') LIKE ?", ["%$search%"]);
            });
        }

        // 4. Definir qué es "Pendiente" para cada rol
        $estadoPendienteNombre = match ($user->role) {
                'administrador', 'contratista', 'funcionario' => 'BORRADOR',
                'supervisor_contrato' => 'ENVIADA',
                'viaticos' => 'APROBADA_SUPERVISOR',
                'ordenador_gasto' => 'APROBADA_VIATICOS',
                default => 'ENVIADA',
            };

        $estadoPendiente = \App\Models\EstadoAgenda::where('nombre', $estadoPendienteNombre)->first();

        // 5. ESTADÍSTICAS DINÁMICAS (Sin aplicar filtro de búsqueda para los contadores de las tarjetas)
        $baseStatsQuery = AgendaDesplazamiento::query();
        if ($user->role === 'contratista' || $user->role === 'funcionario') {
            $baseStatsQuery->where('user_id', $user->id);
        } elseif ($user->role === 'supervisor_contrato') {
            $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
            $baseStatsQuery->where('supervisor_id', $funcionario ? $funcionario->id : 0);
        } elseif ($user->role === 'ordenador_gasto') {
            $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
            $baseStatsQuery->where('ordenador_id', $funcionario ? $funcionario->id : 0);
        }

        // Devueltas: Tienen observaciones y no están finalizadas (o su legalización ha sido devuelta)
        $queryDevueltasStats = (clone $baseStatsQuery)->where(function($q) use ($user) {
            $q->where(function($q2) {
                $q2->whereNotNull('observaciones_finanzas')
                   ->whereHas('estado', function($e) {
                       $e->whereNotIn('nombre', ['APROBADA_VIATICOS', 'APROBADA']);
                   });
            });
            if ($user->role !== 'viaticos') {
                $q->orWhereHas('legalizacion', function($sq) {
                    $sq->whereIn('estado', ['DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']);
                });
            }
        });
        $idDevueltasStats = $queryDevueltasStats->pluck('id')->toArray();

        // Enviadas / En Proceso: Ya no son borradores, pero aún no están finalizadas totalmente (Excepto APROBADA)
        $queryEnviadasStats = match ($user->role) {
                'administrador', 'contratista', 'funcionario' => (clone $baseStatsQuery)->whereHas('estado', function($q){ $q->whereNotIn('nombre', ['BORRADOR', 'APROBADA']); }),
                'supervisor_contrato' => (clone $baseStatsQuery)->whereHas('estado', function($q){ $q->whereIn('nombre', ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS']); }),
                'viaticos' => (clone $baseStatsQuery)->whereHas('estado', function($q){ $q->whereIn('nombre', ['APROBADA_VIATICOS']); }),
                'ordenador_gasto' => (clone $baseStatsQuery)->whereHas('estado', function($q){ $q->where('nombre', 'APROBADA_VIATICOS'); }),
                default => (clone $baseStatsQuery)->whereHas('estado', function($q){ $q->where('nombre', 'ENVIADA'); }),
            };
        $queryEnviadasStats->whereNotIn('id', $idDevueltasStats);

        // Finalizadas / Aprobadas: Estado final APROBADA (o APROBADA_VIATICOS para contratistas/funcionarios si así lo defines, pero mejor APROBADA como cierre total)
        $queryFinalizadasStats = (clone $baseStatsQuery)->whereHas('estado', function($q){ $q->where('nombre', 'APROBADA'); });

        $stats = [
            'pendientes' => (clone $baseStatsQuery)->where('estado_id', $estadoPendiente?->id)->whereNotIn('id', $idDevueltasStats)->count(),
            'enviadas' => $queryEnviadasStats->count(),
            'finalizadas' => $queryFinalizadasStats->count(),
            'devueltas' => $queryDevueltasStats->count(),
        ];

        // Sumar legalizaciones pendientes al contador de la tarjeta según el rol
        if ($user->role === 'supervisor_contrato') {
            $stats['pendientes'] += (clone $baseStatsQuery)->whereHas('legalizacion', function($sq) { $sq->where('estado', 'ENVIADA'); })->count();
        } elseif ($user->role === 'ordenador_gasto') {
            $stats['pendientes'] += (clone $baseStatsQuery)->whereHas('legalizacion', function($sq) { $sq->where('estado', 'APROBADA_LEGALIZACION'); })->count();
        }

        // 6. Listado para la tabla (Aplicando búsqueda y filtros)
        $idDevueltasSearch = (clone $query)->where(function($q) use ($user) {
            $q->where(function($q2) {
                $q2->whereNotNull('observaciones_finanzas')
                   ->whereHas('estado', function($e) {
                       $e->whereNotIn('nombre', ['APROBADA_VIATICOS', 'APROBADA']);
                   });
            });
            if ($user->role !== 'viaticos') {
                $q->orWhereHas('legalizacion', function($sq) {
                    $sq->whereIn('estado', ['DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']);
                });
            }
        })->pluck('id')->toArray();

        $queryEnviadasSearch = match ($user->role) {
            'administrador', 'contratista', 'funcionario' => (clone $query)->whereHas('estado', function($q){ $q->whereNotIn('nombre', ['BORRADOR', 'APROBADA']); }),
            'supervisor_contrato' => (clone $query)->whereHas('estado', function($q){ $q->whereIn('nombre', ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS']); }),
            'viaticos' => (clone $query)->whereHas('estado', function($q){ $q->whereIn('nombre', ['APROBADA_VIATICOS']); }),
            'ordenador_gasto' => (clone $query)->whereHas('estado', function($q){ $q->where('nombre', 'APROBADA_VIATICOS'); }),
            default => (clone $query)->whereHas('estado', function($q){ $q->where('nombre', 'ENVIADA'); }),
        };
        $queryEnviadasSearch->whereNotIn('id', $idDevueltasSearch);

        $queryFinalizadasSearch = (clone $query)->whereHas('estado', function($q){ $q->where('nombre', 'APROBADA'); });

        $agendas = $query->with(['user', 'estado', 'clasificacion', 'actividades', 'obligaciones', 'legalizacion'])
            ->when($filtro == 'pendientes', function ($q) use ($estadoPendiente, $idDevueltasSearch, $user) {
                if ($user->role === 'supervisor_contrato') {
                    return $q->where(function($sq) use ($estadoPendiente, $idDevueltasSearch) {
                        $sq->where(function($sq1) use ($estadoPendiente, $idDevueltasSearch) {
                            $sq1->where('estado_id', $estadoPendiente?->id)->whereNotIn('id', $idDevueltasSearch);
                        })
                        ->orWhereHas('legalizacion', function($lsq) { $lsq->where('estado', 'ENVIADA'); });
                    });
                } elseif ($user->role === 'ordenador_gasto') {
                    return $q->where(function($sq) use ($estadoPendiente, $idDevueltasSearch) {
                        $sq->where(function($sq1) use ($estadoPendiente, $idDevueltasSearch) {
                            $sq1->where('estado_id', $estadoPendiente?->id)->whereNotIn('id', $idDevueltasSearch);
                        })
                        ->orWhereHas('legalizacion', function($lsq) { $lsq->where('estado', 'APROBADA_LEGALIZACION'); });
                    });
                }
                return $q->where('estado_id', $estadoPendiente?->id)->whereNotIn('id', $idDevueltasSearch);
            })
            ->when($filtro == 'enviadas', function ($q) use ($queryEnviadasSearch) {
                return $q->whereIn('id', $queryEnviadasSearch->pluck('id'));
            })
            ->when($filtro == 'finalizadas', function ($q) use ($queryFinalizadasSearch) {
                return $q->whereIn('id', $queryFinalizadasSearch->pluck('id'));
            })
            ->when($filtro == 'devueltas', function ($q) use ($idDevueltasSearch) {
                return $q->whereIn('id', $idDevueltasSearch);
            })
            ->when($filtro == 'borradores' && (in_array($user->role, ['viaticos', 'supervisor_contrato', 'ordenador_gasto'])), function ($q) {
                return $q->whereHas('estado', function($e){ $e->where('nombre', 'BORRADOR'); });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)->appends($request->all());

        // 7. Contador de borradores para limpieza (Viáticos y Supervisores)
        if (in_array($user->role, ['viaticos', 'supervisor_contrato', 'ordenador_gasto'])) {
            $stats['borradores'] = (clone $baseStatsQuery)->whereHas('estado', function($e){ $e->where('nombre', 'BORRADOR'); })->count();
        }

        session(['back_url_reportar_dia' => $request->fullUrl()]);
        
        $supervisores = collect();
        if ($user->role === 'viaticos') {
            $supervisores = \App\Models\LiderDeProceso::whereHas('agendas', function($q) {
                $q->whereHas('estado', function($e) {
                    $e->whereIn('nombre', ['APROBADA_VIATICOS', 'APROBADA']);
                });
            })->orderBy('nombre', 'asc')->get();
        }

        return view('inicio', compact('stats', 'agendas', 'filtro', 'supervisores'));
    }
}