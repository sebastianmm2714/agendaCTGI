@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Gestión Técnica de Viáticos</h2>
        <p class="text-muted">Valida los soportes y aprueba las agendas autorizadas por coordinación.</p>
    </div>

    {{-- Resumen de Estados (Filtros Visuales) --}}
    @php
        $devueltasIds = $agendas->filter(fn($a) => !empty($a->observaciones_finanzas) && !in_array($a->estado->nombre, ['APROBADA_VIATICOS', 'APROBADA']))->pluck('id')->toArray();
        $countPendientes = $agendas->filter(fn($a) => $a->estado->nombre == 'APROBADA_SUPERVISOR' && !in_array($a->id, $devueltasIds))->count();
        $countAprobadas = $agendas->filter(fn($a) => in_array($a->estado->nombre, ['APROBADA_VIATICOS', 'APROBADA']))->count();
        $countDevueltas = count($devueltasIds);
    @endphp
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary bg-opacity-10">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-primary text-uppercase">Pendiente por revisar</p>
                        <h2 class="fw-bold mb-0 text-dark">{{ $countPendientes }}</h2>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-success bg-opacity-10">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-success text-uppercase">Aprobadas</p>
                        <h2 class="fw-bold mb-0 text-dark">{{ $countAprobadas }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-danger bg-opacity-10">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-danger text-uppercase">Devueltas</p>
                        <h2 class="fw-bold mb-0 text-dark">{{ $countDevueltas }}</h2>
                    </div>
                    <i class="fas fa-undo-alt fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Gestión --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Contratista</th>
                            <th>Destino</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Fecha Registro</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendas as $agenda)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $agenda->user->name ?? $agenda->nombre_completo }}</div>
                                <div class="small text-muted">ID: #{{ $agenda->id }}</div>
                            </td>
                            <td>
                                @if($agenda->destinos)
                                    {{ implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $agenda->destinos)))) }}
                                @else
                                    {{ $agenda->ciudad_destino ?: 'N/A' }}
                                @endif
                            </td>
                            <td>{{ $agenda->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>{{ $agenda->fecha_fin?->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>{{ $agenda->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($agenda->estado->nombre == 'APROBADA_SUPERVISOR')
                                    <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #e0f2fe; color: #0369a1; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #bae6fd;">Pendiente Revisar</span>
                                @elseif($agenda->estado->nombre == 'APROBADA_VIATICOS')
                                    <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #dcfce7; color: #166534; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #bbf7d0;">Aprobada</span>
                                @elseif($agenda->estado->nombre == 'APROBADA')
                                    <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #d1fae5; color: #065f46; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #a7f3d0;">Finalizada</span>
                                @elseif($agenda->estado->nombre == 'CORRECCIÓN' || $agenda->observaciones_finanzas)
                                    <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #fee2e2; color: #991b1b; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #fecaca;">Devuelta</span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #e0f2fe; color: #075985; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #bae6fd;">En Proceso</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('viaticos.gestionar', $agenda->id) }}" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-eye me-1"></i> Gestionar
                                    </a>
                                    @if(in_array($agenda->estado->nombre, ['APROBADA_VIATICOS', 'APROBADA']))
                                        <a href="{{ route('viaticos.export', $agenda->id) }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-file-excel me-1"></i> Excel
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- No modal needed --}}

@endsection