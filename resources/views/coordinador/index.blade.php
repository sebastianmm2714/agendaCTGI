@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Panel de Autorización - Coordinación</h2>
            <p class="text-muted">Gestione las firmas y revisiones de su equipo de trabajo</p>
        </div>
        <span class="badge bg-dark px-3 py-2 fs-6 shadow-sm">Total: {{ $pendientes->total() + $enviadas->total() + $devueltas->total() }}</span>
    </div>



    {{-- TARJETAS FILTRADORAS --}}
    <div class="row mb-4 nav" id="pills-tab" role="tablist">
        {{-- CARD: POR FIRMAR --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10 text-primary p-3 h-100 cursor-pointer {{ $activeTab == 'pendientes' ? 'active' : '' }}" 
                 id="tab-pendientes" data-bs-toggle="pill" data-bs-target="#pendientes" type="button" role="tab" style="transition: 0.3s;" onclick="document.getElementById('active_tab_input').value='pendientes';">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Por Firmar / Revisar</p>
                        <h2 class="mb-0 fw-bold">{{ $pendientes->total() }}</h2>
                    </div>
                    <i class="fas fa-signature fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- CARD: ENVIADAS A VIÁTICOS --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10 text-success p-3 h-100 cursor-pointer {{ $activeTab == 'enviadas' ? 'active' : '' }}" 
                 id="tab-enviadas" data-bs-toggle="pill" data-bs-target="#enviadas" type="button" role="tab" style="transition: 0.3s;" onclick="document.getElementById('active_tab_input').value='enviadas';">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Enviadas a Viáticos</p>
                        <h2 class="mb-0 fw-bold">{{ $enviadas->total() }}</h2>
                    </div>
                    <i class="fas fa-paper-plane fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- CARD: DEVUELTAS POR VIÁTICOS --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10 text-danger p-3 h-100 cursor-pointer {{ $activeTab == 'devueltas' ? 'active' : '' }}" 
                 id="tab-devueltas" data-bs-toggle="pill" data-bs-target="#devueltas" type="button" role="tab" style="transition: 0.3s;" onclick="document.getElementById('active_tab_input').value='devueltas';">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Devueltas / Corrección</p>
                        <h2 class="mb-0 fw-bold">{{ $devueltas->total() }}</h2>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    {{-- BUSCADOR INTELIGENTE --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body px-4 py-3 bg-light rounded-4">
            <form action="{{ route('supervisor_contrato.index') }}" method="GET" class="row g-3 align-items-center" id="searchForm">
                <input type="hidden" name="tab" id="active_tab_input" value="{{ $activeTab }}">
                {{-- Buscador --}}
                <div class="col-md-7">
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-white border-end-0 px-3">
                            <i class="fas fa-search text-success"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0 fw-medium" 
                               placeholder="Buscar por instructor, CC, fecha (DD/MM/YYYY) o ruta..." 
                               value="{{ request('search') }}"
                               style="font-size: 0.95rem;">
                        @if(request('search'))
                            <a href="{{ route('supervisor_contrato.index', ['tab' => $activeTab]) }}" class="btn btn-white border-start-0 text-muted" title="Limpiar búsqueda">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
                {{-- Selector per_page --}}
                <div class="col-md-2">
                    <select name="per_page" class="form-select form-select-sm shadow-sm bg-white" onchange="this.form.submit()" title="Registros por página" style="font-size:0.92rem;">
                        @foreach([5, 10, 25, 50] as $opt)
                            <option value="{{ $opt }}" {{ request('per_page', 5) == $opt ? 'selected' : '' }}>
                                {{ $opt }} por página
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Buscar --}}
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 px-3 shadow-sm" style="min-height: 48px;">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="tab-content" id="pills-tabContent">
        {{-- VISTA: PENDIENTES (Nuevas solicitudes) --}}
        <div class="tab-pane fade {{ $activeTab == 'pendientes' ? 'show active' : '' }}" id="pendientes" role="tabpanel">
            @include('coordinador.partials.table', [
                'lista' => $pendientes, 
                'tipo' => 'pendientes'
            ])
            <div class="d-flex justify-content-center mt-3">
                <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                    {{ $pendientes->appends(['tab' => 'pendientes'])->links() }}
                </div>
            </div>
        </div>

        {{-- VISTA: ENVIADAS (Ya firmadas por el coordinador) --}}
        <div class="tab-pane fade {{ $activeTab == 'enviadas' ? 'show active' : '' }}" id="enviadas" role="tabpanel">
            @include('coordinador.partials.table', [
                'lista' => $enviadas, 
                'tipo' => 'enviadas'
            ])
            <div class="d-flex justify-content-center mt-3">
                <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                    {{ $enviadas->appends(['tab' => 'enviadas'])->links() }}
                </div>
            </div>
        </div>

        {{-- VISTA: DEVUELTAS (Las que Viáticos rechazó) --}}
        <div class="tab-pane fade {{ $activeTab == 'devueltas' ? 'show active' : '' }}" id="devueltas" role="tabpanel">
            @include('coordinador.partials.table', [
                'lista' => $devueltas, 
                'tipo' => 'devueltas'
            ])
            <div class="d-flex justify-content-center mt-3">
                <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                    {{ $devueltas->appends(['tab' => 'devueltas'])->links() }}
                </div>
            </div>
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

    /* Ocultar texto por defecto del paginador */
    .custom-pagination nav > div:first-child,
    .custom-pagination nav div.d-none.flex-sm-fill > div:first-child,
    .custom-pagination nav p.text-muted {
        display: none !important;
    }
    
    .custom-pagination nav ul.pagination {
        margin-bottom: 0;
        justify-content: center;
    }

    /* Estilos Paginador SENA */
    .custom-pagination .page-item .page-link {
        border-radius: 50% !important;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 4px;
        border: none;
        color: #4b5563;
        font-weight: 600;
        transition: all 0.2s ease;
        background: transparent;
    }
    
    .custom-pagination .page-item.active .page-link {
        background-color: #39a900 !important;
        color: white !important;
        box-shadow: 0 4px 6px -1px rgba(57, 169, 0, 0.4);
    }
    
    .custom-pagination .page-item:not(.active) .page-link:hover {
        background-color: #e2e8f0;
        color: #39a900;
    }
</style>
@push('scripts')
<script>
    $(document).ready(function() {
        $('.form-autorizar-agenda').on('submit', function(e) {
            @php
                $user = auth()->user();
                $funcionario = \App\Models\Funcionario::where('numero_documento', $user->numero_documento)->first();
                $actualHasFirma = (!empty($user->firma) && $funcionario && !empty($funcionario->firma)) ? 'true' : 'false';
            @endphp
            const hasFirma = {{ $actualHasFirma }};
            
            if (!hasFirma) {
                e.preventDefault();
                Swal.fire({
                    title: 'Firma No Encontrada',
                    text: 'Debes cargar tu firma digital en el perfil antes de autorizar agendas.',
                    icon: 'error',
                    confirmButtonColor: '#39a900',
                    confirmButtonText: 'Cargar Firma'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#modalFirmaUsuario').modal('show');
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection