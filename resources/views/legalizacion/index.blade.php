@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    {{-- Alertas de sesión --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 py-3 px-4 d-flex align-items-center gap-3 animate__animated animate__fadeIn">
            <i class="fas fa-check-circle fa-lg text-success"></i>
            <span class="fw-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 py-3 px-4 d-flex align-items-center gap-3 animate__animated animate__fadeIn">
            <i class="fas fa-exclamation-triangle fa-lg text-warning"></i>
            <span class="fw-medium">{{ session('warning') }}</span>
        </div>
    @endif

    {{-- LAS 3 TARJETAS FILTRADORAS --}}
    <div class="row g-2 g-md-3 mb-4">

        {{-- Tarjeta Pendientes --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 transition-all position-relative {{ $ver == 'pendientes' ? 'bg-primary text-white shadow-lg' : 'bg-white text-primary border-start border-primary border-4' }}"
                style="min-height: 110px; cursor: pointer;">
                <a href="{{ route('legalizacion.index', ['ver' => 'pendientes']) }}" class="stretched-link"></a>
                <div class="d-flex justify-content-between align-items-start h-100">
                    <div class="d-flex flex-column justify-content-between h-100">
                        <h6 class="fw-bold text-uppercase mb-1 small {{ $ver == 'pendientes' ? 'text-white' : 'text-muted' }}"
                            style="font-size: 0.65rem; letter-spacing: 0.5px;">
                            Por Revisar
                        </h6>
                        <h2 class="fw-bold mb-0" style="font-size: 1.6rem; line-height: 1;">{{ $stats['pendientes'] }}</h2>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x opacity-25"></i>
                </div>
            </div>
        </div>

        {{-- Tarjeta Aprobadas --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 transition-all position-relative {{ $ver == 'aprobadas' ? 'bg-success text-white shadow-lg' : 'bg-white text-success border-start border-success border-4' }}"
                style="min-height: 110px; cursor: pointer;">
                <a href="{{ route('legalizacion.index', ['ver' => 'aprobadas']) }}" class="stretched-link"></a>
                <div class="d-flex justify-content-between align-items-start h-100">
                    <div class="d-flex flex-column justify-content-between h-100">
                        <h6 class="fw-bold text-uppercase mb-1 small {{ $ver == 'aprobadas' ? 'text-white' : 'text-muted' }}"
                            style="font-size: 0.65rem; letter-spacing: 0.5px;">
                            Aprobadas
                        </h6>
                        <h2 class="fw-bold mb-0" style="font-size: 1.6rem; line-height: 1;">{{ $stats['aprobadas'] }}</h2>
                    </div>
                    <i class="fas fa-check-double fa-2x opacity-25"></i>
                </div>
            </div>
        </div>

        {{-- Tarjeta Devueltas --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 transition-all position-relative {{ $ver == 'devueltas' ? 'bg-danger text-white shadow-lg' : 'bg-white text-danger border-start border-danger border-4' }}"
                style="min-height: 110px; cursor: pointer;">
                <a href="{{ route('legalizacion.index', ['ver' => 'devueltas']) }}" class="stretched-link"></a>
                <div class="d-flex justify-content-between align-items-start h-100">
                    <div class="d-flex flex-column justify-content-between h-100">
                        <h6 class="fw-bold text-uppercase mb-1 small {{ $ver == 'devueltas' ? 'text-white' : 'text-muted' }}"
                            style="font-size: 0.65rem; letter-spacing: 0.5px;">
                            Devueltas
                        </h6>
                        <h2 class="fw-bold mb-0" style="font-size: 1.6rem; line-height: 1;">{{ $stats['devueltas'] }}</h2>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- BUSCADOR Y FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
        <div class="card-body px-4 py-3 bg-light rounded-4">
            <form action="{{ route('legalizacion.index') }}" method="GET" class="row g-3 align-items-center" id="searchForm">
                <input type="hidden" name="ver" value="{{ $ver }}">
                {{-- Buscador --}}
                <div class="col-12 col-md-7">
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-white border-end-0 px-3">
                            <i class="fas fa-search text-success"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0 fw-medium"
                            placeholder="Buscar por contratista, documento, ruta u orden de viaje..."
                            value="{{ request('search') }}"
                            style="font-size: 0.95rem;">
                        @if(request('search'))
                            <a href="{{ route('legalizacion.index', ['ver' => $ver]) }}"
                                class="btn btn-white border-start-0 text-muted" title="Limpiar búsqueda">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
                {{-- Selector per_page --}}
                <div class="col-6 col-md-2">
                    <select name="per_page" class="form-select rounded-pill shadow-sm bg-white fw-bold text-muted"
                        onchange="this.form.submit()" title="Registros por página"
                        style="font-size:0.85rem; height: 40px; border: 1px solid #dee2e6;">
                        @foreach([5, 10, 25, 50] as $opt)
                            <option value="{{ $opt }}" {{ request('per_page', 10) == $opt ? 'selected' : '' }}>
                                {{ $opt }} REG.
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Buscar --}}
                <div class="col-6 col-md-3">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm"
                        style="height: 40px;">
                        <i class="fas fa-filter me-1"></i> Filtrar Listado
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- LISTADO DINÁMICO --}}
    <div class="card border-0 shadow-sm rounded-4 animate__animated animate__fadeInUp">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    @if($ver == 'pendientes')
                        <i class="fas fa-file-invoice-dollar text-primary me-2"></i> Legalizaciones por Revisar
                    @elseif($ver == 'aprobadas')
                        <i class="fas fa-check-double text-success me-2"></i> Legalizaciones Aprobadas
                    @elseif($ver == 'devueltas')
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i> Legalizaciones Devueltas
                    @endif
                </h4>
                <p class="text-muted mb-0 small">
                    Se muestran {{ $agendas->count() }} de {{ $agendas->total() }} registros
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('legalizacion.index', ['ver' => $ver]) }}" class="btn btn-light btn-sm rounded-pill px-3 border text-muted">
                    <i class="fas fa-sync-alt me-1"></i> Actualizar
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive px-2">
                <table class="table table-hover table-borderless align-middle mb-0"
                    style="border-collapse: separate; border-spacing: 0 8px;">
                    <thead>
                        <tr class="border-bottom border-light">
                            <th class="ps-4 pb-3 text-uppercase fw-bold text-muted text-nowrap"
                                style="min-width: 150px; font-size: 0.7rem; letter-spacing: 0.5px;">Contratista</th>
                            <th class="pb-3 text-uppercase fw-bold text-muted text-nowrap"
                                style="min-width: 100px; font-size: 0.7rem; letter-spacing: 0.5px;">Orden Viaje</th>
                            <th class="pb-3 text-uppercase fw-bold text-muted text-nowrap"
                                style="min-width: 150px; font-size: 0.7rem; letter-spacing: 0.5px;">Ruta / Destino</th>
                            <th class="pb-3 text-uppercase fw-bold text-muted text-nowrap"
                                style="min-width: 100px; font-size: 0.7rem; letter-spacing: 0.5px;">Fechas</th>
                            <th class="pb-3 text-uppercase fw-bold text-muted text-center text-nowrap"
                                style="min-width: 110px; font-size: 0.7rem; letter-spacing: 0.5px;">
                                @if($ver == 'devueltas') Motivo @else Estado @endif
                            </th>
                            <th class="pb-3 text-center text-uppercase fw-bold text-muted text-nowrap"
                                style="min-width: 130px; font-size: 0.7rem; letter-spacing: 0.5px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendas as $agenda)
                            <tr class="align-middle bg-white rounded-3 shadow-sm transition-all hover-translate-y">

                                {{-- Contratista --}}
                                <td class="ps-4 py-3 rounded-start-3">
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                        {{ $agenda->user->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.3px;">
                                        ID: <span class="fw-bold">#{{ $agenda->id }}</span>
                                        @if($agenda->user->categoria)
                                            | {{ $agenda->user->categoria->nombre }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Orden de Viaje --}}
                                <td class="py-3">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-3 py-2 rounded-pill border"
                                          style="font-size: 0.72rem;">
                                        {{ $agenda->orden_viaje ?? 'S/N' }}
                                    </span>
                                </td>

                                {{-- Ruta / Destino --}}
                                <td class="py-3">
                                    <div class="fw-bold text-dark text-uppercase" style="font-size: 0.85rem;">
                                        <i class="fas fa-route text-success me-2 opacity-75"></i>
                                        {{ Str::limit($agenda->ruta, 40) }}
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                        @if($agenda->destinos)
                                            {{ implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $agenda->destinos)))) }}
                                        @else
                                            {{ $agenda->ciudad_destino ?: 'Sin destino' }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Fechas --}}
                                <td class="py-3">
                                    <div class="d-flex flex-column gap-1">
                                        <span class="fw-bold text-dark" style="font-size: 0.8rem;">
                                            <i class="fas fa-calendar-alt text-muted me-1 opacity-50"></i>
                                            {{ $agenda->fecha_inicio?->format('d/m/Y') }}
                                        </span>
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-arrow-right text-muted me-1 opacity-50"></i>
                                            {{ $agenda->fecha_fin?->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Estado / Motivo --}}
                                <td class="text-center py-3">
                                    @if($ver == 'devueltas')
                                        <div class="d-inline-block p-2 rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25"
                                            style="cursor: pointer; min-width: 100px;">
                                            <div class="text-danger fw-bold small text-uppercase mb-1"
                                                style="font-size: 0.7rem;">
                                                <i class="fas fa-undo-alt me-1"></i> Devuelta
                                            </div>
                                            <div class="text-dark fw-medium" style="font-size: 0.65rem; line-height: 1.1;">
                                                {{ Str::limit($agenda->legalizacion_observaciones, 30) }}
                                            </div>
                                        </div>
                                    @elseif($ver == 'aprobadas')
                                        @if($agenda->legalizacion_estado == 'APROBADA_ORDENADOR')
                                            <span class="badge rounded-pill px-3 py-2 text-uppercase fw-bold shadow-sm"
                                                style="background-color: #22c55e; color: white; font-size: 0.68rem; letter-spacing: 0.5px;">
                                                <i class="fas fa-check-double me-1"></i> Aprobada Final
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-3 py-2 text-uppercase fw-bold shadow-sm"
                                                style="background-color: #8b5cf6; color: white; font-size: 0.68rem; letter-spacing: 0.5px;">
                                                <i class="fas fa-check me-1"></i> Aprobada Leg.
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge rounded-pill px-3 py-2 text-uppercase fw-bold shadow-sm"
                                            style="background-color: #0ea5e9; color: white; font-size: 0.68rem; letter-spacing: 0.5px;">
                                            <i class="fas fa-clock me-1"></i> Pendiente
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="text-center py-3 rounded-end-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($ver == 'pendientes')
                                            <a href="{{ route('legalizacion.gestionar', ['id' => $agenda->id, 'tab' => 'pendientes']) }}"
                                                class="btn btn-primary btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                title="Gestionar legalización #{{ $agenda->id }}">
                                                <i class="fas fa-tasks"></i>
                                                <span class="d-none d-md-inline ms-1">Gestionar</span>
                                            </a>
                                        @elseif($ver == 'aprobadas')
                                            <a href="{{ route('legalizacion.ver', $agenda->id) }}" target="_blank"
                                                class="btn btn-outline-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                title="Ver PDF legalización #{{ $agenda->id }}">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="d-none d-md-inline ms-1">Ver PDF</span>
                                            </a>
                                            <a href="{{ route('legalizacion.gestionar', ['id' => $agenda->id, 'tab' => 'aprobadas']) }}"
                                                class="btn btn-outline-secondary btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                title="Ver detalle #{{ $agenda->id }}">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-md-inline ms-1">Ver</span>
                                            </a>
                                        @elseif($ver == 'devueltas')
                                            <a href="{{ route('legalizacion.ver', $agenda->id) }}" target="_blank"
                                                class="btn btn-outline-danger btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm bg-white"
                                                title="Ver PDF legalización #{{ $agenda->id }}">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="d-none d-md-inline ms-1">Ver PDF</span>
                                            </a>
                                            <a href="{{ route('legalizacion.gestionar', ['id' => $agenda->id, 'tab' => 'devueltas']) }}"
                                                class="btn btn-warning btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm text-dark"
                                                title="Ver detalle devolución #{{ $agenda->id }}">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-md-inline ms-1">Detalle</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block text-light"></i>
                                        <p class="mb-0 fw-medium">No hay legalizaciones en este estado.</p>
                                        @if(request('search'))
                                            <p class="small mt-1">Intenta con otros términos de búsqueda.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginación --}}
        @if($agendas->hasPages())
            <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center">
                <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                    {{ $agendas->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

<style>
    .transition-all {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .transition-all:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    .card.bg-primary.text-white i,
    .card.bg-success.text-white i,
    .card.bg-danger.text-white i {
        color: rgba(255, 255, 255, 0.3) !important;
    }
    .hover-translate-y:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important;
    }

    /* Paginación circular SENA */
    .custom-pagination nav > div:first-child,
    .custom-pagination nav div.d-none.flex-sm-fill > div:first-child,
    .custom-pagination nav p.text-muted {
        display: none !important;
    }
    .custom-pagination nav > div.d-none.flex-sm-fill > div {
        display: flex !important;
        justify-content: center !important;
    }
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
</style>
@endsection
