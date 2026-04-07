@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Panel de Autorización - Coordinación</h2>
            <p class="text-muted">Gestione las firmas y revisiones de su equipo de trabajo</p>
        </div>
        <span class="badge bg-dark px-3 py-2 fs-6 shadow-sm">Total: {{ $agendas->count() }}</span>
    </div>

    {{-- TARJETAS FILTRADORAS --}}
    <div class="row mb-4 nav" id="pills-tab" role="tablist">
        {{-- CARD: POR FIRMAR --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10 text-primary p-3 h-100 cursor-pointer active" 
                 id="tab-pendientes" data-bs-toggle="pill" data-bs-target="#pendientes" type="button" role="tab" style="cursor: pointer; transition: 0.3s;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Por Firmar / Revisar</p>
                        <h2 class="mb-0 fw-bold">{{ $agendas->filter(fn($a) => $a->estado->nombre == 'ENVIADA')->count() }}</h2>
                    </div>
                    <i class="fas fa-signature fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- CARD: ENVIADAS A VIÁTICOS --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10 text-success p-3 h-100 cursor-pointer" 
                 id="tab-enviadas" data-bs-toggle="pill" data-bs-target="#enviadas" type="button" role="tab" style="cursor: pointer; transition: 0.3s;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Enviadas a Viáticos</p>
                        <h2 class="mb-0 fw-bold">{{ $agendas->filter(fn($a) => in_array($a->estado->nombre, ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA']))->count() }}</h2>
                    </div>
                    <i class="fas fa-paper-plane fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- CARD: DEVUELTAS POR VIÁTICOS --}}
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
        
        {{-- VISTA: PENDIENTES (Nuevas solicitudes) --}}
        <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
            @include('coordinador.partials.table', [
                'lista' => $agendas->filter(fn($a) => $a->estado->nombre == 'ENVIADA'), 
                'tipo' => 'pendientes'
            ])
        </div>

        {{-- VISTA: ENVIADAS (Ya firmadas por el coordinador) --}}
        <div class="tab-pane fade" id="enviadas" role="tabpanel">
            @include('coordinador.partials.table', [
                'lista' => $agendas->filter(fn($a) => in_array($a->estado->nombre, ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA'])), 
                'tipo' => 'enviadas'
            ])
        </div>

        {{-- VISTA: DEVUELTAS (Las que Viáticos rechazó) --}}
        <div class="tab-pane fade" id="devueltas" role="tabpanel">
            @include('coordinador.partials.table', [
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