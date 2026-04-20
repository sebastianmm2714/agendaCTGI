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
    @php $currentStatus = request('status'); @endphp
    <div class="row g-4 mb-5 animate__animated animate__fadeInUp">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.dashboard', ['status' => 'all']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 filter-card 
                    {{ $currentStatus == 'all' ? 'filter-card-active' : ($currentStatus && $currentStatus != 'all' ? 'filter-card-inactive' : '') }}" 
                    style="transition: all 0.3s ease; border-bottom: 4px solid var(--bs-primary) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                                <i class="fas fa-file-invoice fa-lg"></i>
                            </div>
                            <span class="h3 fw-bold mb-0 text-dark">{{ $stats['total_agendas'] }}</span>
                        </div>
                        <h6 class="fw-bold text-muted text-uppercase small mb-1">Total Agendas</h6>
                        <p class="text-dark fw-bold mb-0 opacity-75">Registradas en el sistema</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.dashboard', ['status' => 'proceso']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 filter-card 
                    {{ $currentStatus == 'proceso' ? 'filter-card-active' : ($currentStatus ? 'filter-card-inactive' : '') }}" 
                    style="transition: all 0.3s ease; border-bottom: 4px solid var(--bs-success) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                                <i class="fas fa-paper-plane fa-lg"></i>
                            </div>
                            <span class="h3 fw-bold mb-0 text-dark">{{ $stats['enviadas'] }}</span>
                        </div>
                        <h6 class="fw-bold text-muted text-uppercase small mb-1">En Proceso</h6>
                        <p class="text-success fw-bold mb-0 opacity-75">Agendas enviadas/liquidadas</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.dashboard', ['status' => 'DEVUELTA']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 filter-card 
                    {{ $currentStatus == 'DEVUELTA' ? 'filter-card-active' : ($currentStatus ? 'filter-card-inactive' : '') }}" 
                    style="transition: all 0.3s ease; border-bottom: 4px solid var(--bs-danger) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger">
                                <i class="fas fa-undo fa-lg"></i>
                            </div>
                            <span class="h3 fw-bold mb-0 text-dark">{{ $stats['devueltas'] }}</span>
                        </div>
                        <h6 class="fw-bold text-muted text-uppercase small mb-1">Devueltas</h6>
                        <p class="text-danger fw-bold mb-0 opacity-75">Requieren corrección</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.dashboard', ['status' => 'APROBADA']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 filter-card 
                    {{ $currentStatus == 'APROBADA' ? 'filter-card-active' : ($currentStatus ? 'filter-card-inactive' : '') }}" 
                    style="transition: all 0.3s ease; border-bottom: 4px solid var(--bs-info) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                                <i class="fas fa-check-double fa-lg"></i>
                            </div>
                            <span class="h3 fw-bold mb-0 text-dark">{{ $stats['finalizadas'] }}</span>
                        </div>
                        <h6 class="fw-bold text-muted text-uppercase small mb-1">Finalizadas</h6>
                        <p class="text-info fw-bold mb-0 opacity-75">Proceso completo</p>
                    </div>
                </div>
            </a>
        </div>
    </div>



    <div class="row g-4 mb-5">
        {{-- Reporte Detallado --}}
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-bottom py-4 px-4">
                    <form action="{{ route('admin.dashboard') }}" method="GET" id="searchForm">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-dark p-2 rounded-3 me-3">
                                        <i class="fas fa-list-ul text-white"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-dark">Seguimiento de Agendas</h5>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search') }}" 
                                        class="form-control border-start-0 rounded-end-pill" 
                                        placeholder="Buscar por ID, nombre o documento...">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-center gap-2 justify-content-end">
                                <label class="small text-muted fw-bold mb-0">Ver:</label>
                                <select name="per_page" class="form-select rounded-pill w-auto" onchange="this.form.submit()">
                                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                <button type="submit" class="btn btn-success rounded-pill px-3 fw-bold shadow-sm">
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </form>
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
                @if($agendas->total() > $agendas->perPage())
                <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center">
                    <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                        {{ $agendas->links() }}
                    </div>
                </div>
                @endif
                @if($agendas->total() == 0)
                <div class="p-5 text-center">
                    <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No se encontraron resultados para la búsqueda actual.</p>
                </div>
                @endif
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
    // Las tarjetas ahora son enlaces directos, por lo que el filtrado JS ya no es necesario para el dashboard global
    // pero mantenemos el efecto visual si es deseado o para futuras interacciones locales.
});
</script>
@endpush

<style>
    /* Ocultar texto por defecto del paginador */
    .custom-pagination nav > div:first-child,
    .custom-pagination nav div.d-none.flex-sm-fill > div:first-child,
    .custom-pagination nav p.text-muted {
        display: none !important;
    }
    
    .custom-pagination nav > div.d-none.flex-sm-fill > div {
        display: flex !important;
        justify-content: center !important;
    }

    /* Diseño circular Sena */
    .custom-pagination .pagination {
        margin-bottom: 0 !important;
        gap: 0.3rem;
    }
    .custom-pagination .page-item .page-link {
        border-radius: 50% !important;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        color: #4b5563;
        font-weight: 600;
        background: transparent;
        transition: all 0.2s ease;
    }
    .custom-pagination .page-item:not(.active) .page-link:hover {
        background: #f3f4f6;
        color: #39a900;
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #39a900 !important;
        color: white !important;
        box-shadow: 0 4px 6px -1px rgba(57, 169, 0, 0.4);
    }
    
    .custom-pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background: transparent;
    }

    .filter-card {
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .filter-card-inactive {
        opacity: 0.35;
        filter: grayscale(0.85);
        transform: scale(0.94);
    }

    .filter-card-active {
        opacity: 1 !important;
        filter: grayscale(0) !important;
        transform: scale(1.04);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12) !important;
        z-index: 5;
    }

    .filter-card:hover:not(.filter-card-inactive) {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
</style>


@endsection
