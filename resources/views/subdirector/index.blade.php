@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Bandeja de Subdirección (Ordenador de Gasto)</h2>
            <p class="text-muted">Gestione la autorización final y firma de agendas de desplazamiento</p>
        </div>
        <span class="badge bg-dark px-3 py-2 fs-6 shadow-sm">Total: {{ $agendas->count() }}</span>
    </div>

    {{-- TARJETAS FILTRADORAS --}}
    <div class="row mb-4 nav" id="pills-tab" role="tablist">
        {{-- CARD: POR AUTORIZAR --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10 text-primary p-3 h-100 cursor-pointer active" 
                 id="tab-pendientes" data-bs-toggle="pill" data-bs-target="#pendientes" type="button" role="tab" style="cursor: pointer; transition: 0.3s;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Por Autorizar</p>
                        <h2 class="mb-0 fw-bold">{{ $agendas->filter(fn($a) => $a->estado->nombre == 'APROBADA_VIATICOS')->count() }}</h2>
                    </div>
                    <i class="fas fa-signature fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- CARD: APROBADAS --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10 text-success p-3 h-100 cursor-pointer" 
                 id="tab-aprobadas" data-bs-toggle="pill" data-bs-target="#aprobadas" type="button" role="tab" style="cursor: pointer; transition: 0.3s;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Aprobadas / Finalizadas</p>
                        <h2 class="mb-0 fw-bold">{{ $agendas->filter(fn($a) => $a->estado->nombre == 'APROBADA')->count() }}</h2>
                    </div>
                    <i class="fas fa-check-double fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- CARD: DEVUELTAS --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10 text-danger p-3 h-100 cursor-pointer" 
                 id="tab-devueltas" data-bs-toggle="pill" data-bs-target="#devueltas" type="button" role="tab" style="cursor: pointer; transition: 0.3s;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Devueltas / Corrección</p>
                        <h2 class="mb-0 fw-bold">{{ $agendas->filter(fn($a) => $a->estado->nombre == 'CORRECCIÓN')->count() }}</h2>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTENIDO DE LAS TABLAS --}}
    <div class="tab-content" id="pills-tabContent">
        
        {{-- VISTA: PENDIENTES --}}
        <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
            @include('subdirector.partials.table', [
                'lista' => $agendas->filter(fn($a) => $a->estado->nombre == 'APROBADA_VIATICOS'), 
                'tipo' => 'pendientes'
            ])
        </div>

        {{-- VISTA: APROBADAS --}}
        <div class="tab-pane fade" id="aprobadas" role="tabpanel">
            @include('subdirector.partials.table', [
                'lista' => $agendas->filter(fn($a) => $a->estado->nombre == 'APROBADA'), 
                'tipo' => 'aprobadas'
            ])
        </div>

        {{-- VISTA: DEVUELTAS --}}
        <div class="tab-pane fade" id="devueltas" role="tabpanel">
            @include('subdirector.partials.table', [
                'lista' => $agendas->filter(fn($a) => $a->estado->nombre == 'CORRECCIÓN'), 
                'tipo' => 'devueltas'
            ])
        </div>

    </div>
</div>

<style>
    .card.active {
        box-shadow: 0 0 0 3px currentColor !important;
        transform: scale(1.02);
    }
    .cursor-pointer:hover {
        transform: translateY(-5px);
        filter: brightness(0.95);
    }
</style>
@endsection