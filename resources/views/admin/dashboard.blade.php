@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    {{-- Encabezado --}}
    <div class="d-flex align-items-center justify-content-between mb-4 animate__animated animate__fadeIn">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Panel de Control Administrativo</h2>
            <p class="text-muted mb-0">Gestión global de agendas, catálogos y personal</p>
        </div>
        <div class="bg-success bg-opacity-10 p-3 rounded-4">
            <i class="fas fa-user-shield fa-2x text-success"></i>
        </div>
    </div>

    {{-- Tarjetas de Estadísticas --}}
    <div class="row g-4 mb-5 animate__animated animate__fadeInUp">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 filter-card" data-filter="all" style="cursor: pointer; transition: transform 0.2s;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-file-invoice fa-lg text-primary"></i>
                        </div>
                        <span class="h3 fw-bold mb-0 text-dark">{{ $stats['total_agendas'] }}</span>
                    </div>
                    <h6 class="fw-bold text-muted text-uppercase small mb-1">Total Agendas</h6>
                    <p class="text-dark fw-bold mb-0">Registradas en el sistema</p>
                </div>
                <div class="bg-primary opacity-10" style="height: 4px;"></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 filter-card" data-filter="proceso" style="cursor: pointer; transition: transform 0.2s;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-paper-plane fa-lg text-success"></i>
                        </div>
                        <span class="h3 fw-bold mb-0 text-dark">{{ $stats['enviadas'] }}</span>
                    </div>
                    <h6 class="fw-bold text-muted text-uppercase small mb-1">En Proceso</h6>
                    <p class="text-success fw-bold mb-0">Agendas enviadas/liquidadas</p>
                </div>
                <div class="bg-success opacity-10" style="height: 4px;"></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 filter-card" data-filter="DEVUELTA" style="cursor: pointer; transition: transform 0.2s;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-undo fa-lg text-danger"></i>
                        </div>
                        <span class="h3 fw-bold mb-0 text-dark">{{ $stats['devueltas'] }}</span>
                    </div>
                    <h6 class="fw-bold text-muted text-uppercase small mb-1">Devueltas</h6>
                    <p class="text-danger fw-bold mb-0">Requieren corrección</p>
                </div>
                <div class="bg-danger opacity-10" style="height: 4px;"></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 filter-card" data-filter="APROBADA" style="cursor: pointer; transition: transform 0.2s;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-check-double fa-lg text-info"></i>
                        </div>
                        <span class="h3 fw-bold mb-0 text-dark">{{ $stats['finalizadas'] }}</span>
                    </div>
                    <h6 class="fw-bold text-muted text-uppercase small mb-1">Finalizadas</h6>
                    <p class="text-info fw-bold mb-0">Proceso completo</p>
                </div>
                <div class="bg-info opacity-10" style="height: 4px;"></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        {{-- Reporte Detallado --}}
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center justify-content-between border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-dark p-2 rounded-3 me-3">
                            <i class="fas fa-list-ul text-white"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Seguimiento de Agendas (Últimas 10)</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 custom-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small text-uppercase fw-bold">ID / Fecha</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Contratista</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Estado Actual</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Progreso</th>
                                    <th class="py-3 text-center text-muted small text-uppercase fw-bold">PDF</th>
                                    <th class="pe-4 py-3 text-end text-muted small text-uppercase fw-bold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agendas as $agenda)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">#{{ $agenda->id }}</div>
                                        <div class="small text-muted">{{ $agenda->updated_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-bold text-uppercase small">{{ $agenda->user->name }}</div>
                                        <div class="text-muted small">ID: {{ $agenda->user->numero_documento }}</div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2 fw-normal" 
                                              style="background-color: #f0f7f0; color: #39a900; border: 1px solid #e1f0d7;">
                                            {{ $agenda->estado->nombre }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $porcentaje = match($agenda->estado->nombre) {
                                                'BORRADOR' => 10,
                                                'ENVIADA' => 30,
                                                'APROBADA_SUPERVISOR' => 50,
                                                'APROBADA_VIATICOS' => 75,
                                                'APROBADA_ORDENADOR' => 90,
                                                'APROBADA' => 100,
                                                default => 20,
                                            };
                                            $color = match(true) {
                                                $porcentaje <= 30 => 'danger',
                                                $porcentaje < 100 => 'warning',
                                                $porcentaje == 100 => 'success',
                                            };
                                            // Si tiene observaciones de corrección, forzamos rojo sin importar el porcentaje
                                            if ($agenda->observaciones_finanzas && $agenda->estado->nombre == 'CORRECCIÓN') {
                                                $color = 'danger';
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px; border-radius: 3px;">
                                                <div class="progress-bar bg-{{ $color }}" style="width: {{ $porcentaje }}%"></div>
                                            </div>
                                            <span class="small fw-bold text-{{ $color }}">{{ $porcentaje }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank" class="btn btn-link text-danger p-0">
                                            <i class="fas fa-file-pdf fa-2x"></i>
                                        </a>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border"
                                                data-bs-toggle="modal" data-bs-target="#modalPreview{{ $agenda->id }}">
                                            <i class="fas fa-eye me-1"></i> Detalles
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modales de Vista Previa --}}
@foreach($agendas as $agenda)
<div class="modal fade" id="modalPreview{{ $agenda->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 bg-light p-4">
                <h5 class="fw-bold mb-0">Detalles de Agenda #{{ $agenda->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase">Información del Usuario</label>
                        <p class="mb-1"><i class="fas fa-user text-success me-2"></i> {{ $agenda->user->name }}</p>
                        <p class="mb-1"><i class="fas fa-id-card text-success me-2"></i> {{ $agenda->user->numero_documento }}</p>
                        <p class="mb-0"><i class="fas fa-briefcase text-success me-2"></i> {{ $agenda->user->categoria->nombre ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase">Estado del Proceso</label>
                        <div class="p-3 rounded-3 mt-1 {{ $agenda->observaciones_finanzas ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }}">
                            <p class="fw-bold mb-1"><i class="fas fa-circle me-2" style="font-size: 0.6rem;"></i> {{ $agenda->estado->nombre }}</p>
                            @if($agenda->observaciones_finanzas)
                                <p class="small mb-0 fst-italic">"{{ $agenda->observaciones_finanzas }}"</p>
                            @else
                                <p class="small mb-0">No hay observaciones pendientes.</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="bg-light p-3 rounded-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf fa-3x text-danger me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Documento Final</h6>
                            <small class="text-muted">Vista previa de la agenda generada</small>
                        </div>
                    </div>
                    <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank" class="btn btn-danger rounded-pill px-4 fw-bold">
                        Ver PDF Completo
                    </a>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary w-100 rounded-pill py-2 fw-bold" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .custom-table tbody tr { transition: all 0.2s ease; }
    .custom-table tbody tr:hover { background-color: #f8fafc; }
    .progress-bar { transition: width 0.6s ease; }
    .filter-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    $('.filter-card').on('click', function() {
        let filter = $(this).data('filter');
        $('.filter-card').css('opacity', '0.6');
        $(this).css('opacity', '1');

        if(filter === 'all') {
            $('.custom-table tbody tr').show();
        } else if (filter === 'proceso') {
            $('.custom-table tbody tr').each(function() {
                let state = $(this).find('td:eq(2) .badge').text().trim().toUpperCase();
                if(state !== 'BORRADOR' && !state.includes('DEVUELTA') && !state.includes('CORRECCIÓN') && state !== 'APROBADA') {
                    $(this).show();
                } else if(state === 'APROBADA_SUPERVISOR' || state === 'APROBADA_ORDENADOR' || state === 'APROBADA_VIATICOS') {
                    $(this).show(); // Aprobaciones parciales cuentan como en proceso
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('.custom-table tbody tr').each(function() {
                let state = $(this).find('td:eq(2) .badge').text().trim().toUpperCase();
                
                if (filter === 'APROBADA') {
                    // Solo mostrar APROBADA exacta (Finalizada), no APROBADA_SUPERVISOR etc.
                    if (state === 'APROBADA') $(this).show();
                    else $(this).hide();
                } else if (filter === 'DEVUELTA') {
                    // Mostrar CORRECCIÓN o cualquier cosa que el backend considere devuelta
                    if (state.includes('CORRECCIÓN') || state.includes('DEVUELTA')) $(this).show();
                    else $(this).hide();
                } else {
                    if(state.includes(filter)) $(this).show();
                    else $(this).hide();
                }
            });
        }
    });
});
</script>
@endpush
@endsection
