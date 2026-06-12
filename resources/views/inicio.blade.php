@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid ">
        {{-- Banner Institucional --}}
        <div class="card border shadow-sm mb-4">
            <div
                class="card-body d-flex flex-column flex-sm-row align-items-center justify-content-center py-2 text-center text-sm-start">
                <img src="{{ asset('images/sena/logo250.png') }}" alt="SENA" style="height: 45px;"
                    class="mb-2 mb-sm-0 me-sm-3">
                <h1 class="h4 mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">Agenda CTGI</h1>
            </div>
        </div>

        {{-- Banner de Bienvenida --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4"
            style="background: linear-gradient(135deg, #39a900 0%, #2d8500 100%);">
            <div class="card-body py-4 px-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-9">
                        <h1 class="fw-bold mb-2 h3 text-white">¡Bienvenid@, {{ auth()->user()->name }}!</h1>
                        <p class="lead mb-0 small text-white opacity-90">Rol actual:
                            <strong>{{ auth()->user()->role === 'funcionario' ? 'Servidor Público' : ucfirst(auth()->user()->role) }}</strong>
                        </p>
                        @if(auth()->user()->role === 'administrador')
                            <span class="badge bg-white text-success mt-2 fw-bold small">Panel Personal: Solo se muestran sus
                                agendas</span>
                        @endif
                    </div>
                    <div class="col-md-3 text-center d-none d-md-block">
                        <i
                            class="fas {{ auth()->user()->role === 'funcionario' ? 'fa-user-tie' : 'fa-user-shield' }} fa-4x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERTA DE FIRMA FALTANTE --}}
        @php
            $user = auth()->user();
            $hasFirma = !empty(trim($user->firma));

            // Si es supervisor u ordenador, verificamos también su ficha de líder de proceso
            if ($hasFirma && in_array($user->role, ['supervisor_contrato', 'ordenador_gasto'])) {
                $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
                if (!$funcionario || empty(trim($funcionario->firma))) {
                    $hasFirma = false;
                }
            }
        @endphp

        @if(!$hasFirma && in_array($user->role, ['contratista', 'supervisor_contrato', 'ordenador_gasto', 'funcionario']))
            <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 p-4 animate__animated animate__shakeX"
                style="border-left: 6px solid #ffc107 !important;">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 d-none d-md-block">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="alert-heading fw-bold text-dark mb-1">¡Atención! Falta tu firma digital</h4>
                        <p class="mb-0 fs-5 text-dark">
                            @if($user->role == 'contratista' || $user->role == 'funcionario')
                                Detectamos que aún no has cargado tu firma en el sistema. Es <strong>obligatorio</strong> tener una
                                firma registrada para poder enviar {{ $user->role == 'funcionario' ? 'comisiones' : 'agendas' }}.
                            @else
                                Detectamos que aún no has cargado tu firma en el sistema. Es <strong>obligatorio</strong> tener una
                                firma registrada para poder autorizar agendas.
                            @endif
                        </p>
                        <div class="mt-3">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#modalFirmaUsuario"
                                class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">
                                <i class="fas fa-pen-nib me-1"></i> Ir a cargar mi firma ahora
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- LAS 4 TARJETAS DE CONTROL --}}
        <div class="row g-2 g-md-3 mb-4">
            {{-- Tarjeta Pendientes --}}
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-3 h-100 transition-all position-relative {{ $filtro == 'pendientes' ? 'bg-primary text-white shadow-lg' : 'bg-white text-primary border-start border-primary border-4' }}"
                    style="min-height: 110px;">
                    <a href="{{ route('inicio', ['ver' => 'pendientes']) }}" class="stretched-link"></a>
                    <div class="d-flex justify-content-between align-items-start h-100">
                        <div class="d-flex flex-column justify-content-between h-100 pe-2">
                            <h6 class="fw-bold text-uppercase mb-1 small {{ $filtro == 'pendientes' ? 'text-white' : 'text-muted' }}"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                @if(auth()->user()->role == 'viaticos' || auth()->user()->role == 'legalizacion')
                                    Pendientes
                                @elseif(auth()->user()->role == 'ordenador_gasto')
                                    Autorizar
                                @elseif(auth()->user()->role == 'supervisor_contrato')
                                    Firmar
                                @else
                                    Borradores
                                @endif
                            </h6>
                            <h2 class="fw-bold mb-0" style="font-size: 1.6rem; line-height: 1;">{{ $stats['pendientes'] }}
                            </h2>
                            @if(in_array(auth()->user()->role, ['viaticos', 'supervisor_contrato', 'ordenador_gasto']))
                                <a href="{{ route('inicio', ['ver' => 'borradores']) }}"
                                    class="small mt-1 d-block fw-bold position-relative {{ $filtro == 'borradores' ? 'text-white' : 'text-primary' }}"
                                    style="text-decoration: underline; z-index: 5; font-size: 0.65rem;">
                                    <i class="fas fa-trash-alt me-1"></i> Borradores ({{ $stats['borradores'] ?? 0 }})
                                </a>
                            @endif
                        </div>
                        <i
                            class="fas {{ auth()->user()->role == 'supervisor_contrato' ? 'fa-signature' : 'fa-clock' }} fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>

            {{-- Tarjeta En Trámite (Antigua Enviadas) --}}
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-3 h-100 transition-all position-relative {{ $filtro == 'enviadas' ? 'bg-info text-white shadow-lg' : 'bg-white text-info border-start border-info border-4' }}"
                    style="min-height: 110px;">
                    <a href="{{ route('inicio', ['ver' => 'enviadas']) }}" class="stretched-link"></a>
                    <div class="d-flex justify-content-between align-items-start h-100">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <h6 class="fw-bold text-uppercase mb-1 small {{ $filtro == 'enviadas' ? 'text-white' : 'text-muted' }}"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                En Proceso
                            </h6>
                            <h2 class="fw-bold mb-0" style="font-size: 1.6rem; line-height: 1;">{{ $stats['enviadas'] }}
                            </h2>
                        </div>
                        <i class="fas fa-sync fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>

            {{-- Tarjeta Aprobadas / Finalizadas --}}
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-3 h-100 transition-all position-relative {{ $filtro == 'finalizadas' ? 'bg-success text-white shadow-lg' : 'bg-white text-success border-start border-success border-4' }}"
                    style="min-height: 110px;">
                    <a href="{{ route('inicio', ['ver' => 'finalizadas']) }}" class="stretched-link"></a>
                    <div class="d-flex justify-content-between align-items-start h-100">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <h6 class="fw-bold text-uppercase mb-1 small {{ $filtro == 'finalizadas' ? 'text-white' : 'text-muted' }}"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                Aprobadas
                            </h6>
                            <h2 class="fw-bold mb-0" style="font-size: 1.6rem; line-height: 1;">{{ $stats['finalizadas'] }}
                            </h2>
                        </div>
                        <i class="fas fa-check-double fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>

            {{-- Tarjeta Devueltas --}}
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-3 h-100 transition-all position-relative {{ $filtro == 'devueltas' ? 'bg-danger text-white shadow-lg' : 'bg-white text-danger border-start border-danger border-4' }}"
                    style="min-height: 110px;">
                    <a href="{{ route('inicio', ['ver' => 'devueltas']) }}" class="stretched-link"></a>
                    <div class="d-flex justify-content-between align-items-start h-100">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <h6 class="fw-bold text-uppercase mb-1 small {{ $filtro == 'devueltas' ? 'text-white' : 'text-muted' }}"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                Devueltas
                            </h6>
                            <h2 class="fw-bold mb-0" style="font-size: 1.6rem; line-height: 1;">{{ $stats['devueltas'] }}
                            </h2>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- BUSCADOR Y FILTROS --}}
        @if($filtro)
            <div class="card border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
                <div class="card-body px-4 py-4 bg-light rounded-4">
                    <form action="{{ route('inicio') }}" method="GET" class="row g-3 align-items-center" id="searchForm">
                        <input type="hidden" name="ver" value="{{ $filtro }}">
                        {{-- Buscador --}}
                        <div class="col-12 col-md-7">
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0 px-3">
                                    <i class="fas fa-search text-success"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0 fw-medium"
                                    placeholder="Buscar por ruta, destino o fecha..." value="{{ request('search') }}"
                                    style="font-size: 0.95rem;">
                                @if(request('search'))
                                    <a href="{{ route('inicio', ['ver' => $filtro]) }}"
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
                <div
                    class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            @if($filtro == 'pendientes')
                                <i class="fas fa-clock text-primary me-2"></i>
                                @if(auth()->user()->role == 'viaticos')
                                    Agendas por Revisar
                                @elseif(auth()->user()->role == 'legalizacion')
                                    Legalizaciones por Revisar
                                @elseif(auth()->user()->role == 'ordenador_gasto')
                                    Agendas por Autorizar
                                @else
                                    Mis Comisiones Pendientes
                                @endif
                            @elseif($filtro == 'enviadas')
                                <i class="fas fa-sync text-info me-2"></i> {{ auth()->user()->role == 'legalizacion' ? 'Legalizaciones en Trámite' : 'Agendas en Trámite' }}
                            @elseif($filtro == 'finalizadas')
                                <i class="fas fa-check-double text-success me-2"></i> {{ auth()->user()->role == 'legalizacion' ? 'Legalizaciones Aprobadas' : 'Agendas Aprobadas' }}
                            @elseif($filtro == 'devueltas')
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i> {{ auth()->user()->role == 'legalizacion' ? 'Legalizaciones Devueltas' : 'Agendas / Legalizaciones Devueltas' }}
                            @elseif($filtro == 'borradores')
                                <i class="fas fa-trash-alt text-danger me-2"></i> Borradores del Sistema
                            @endif
                        </h4>
                        <p class="text-muted mb-0 small">Se muestran {{ $agendas->count() }} de {{ $agendas->total() }}
                            registros</p>

                        {{-- Selector de Modo para Viáticos y Supervisores --}}
                        @if(in_array(auth()->user()->role, ['viaticos', 'supervisor_contrato', 'ordenador_gasto']) && in_array($filtro, ['pendientes', 'borradores']))
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a href="{{ route('inicio', ['ver' => 'pendientes']) }}"
                                    class="btn btn-sm rounded-pill px-3 fw-bold transition-all {{ $filtro == 'pendientes' ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ auth()->user()->role == 'viaticos' ? 'Agendas por Revisar' : 'Agendas por Firmar' }}
                                </a>
                                <a href="{{ route('inicio', ['ver' => 'borradores']) }}"
                                    class="btn btn-sm rounded-pill px-3 fw-bold transition-all {{ $filtro == 'borradores' ? 'btn-danger shadow-sm' : 'btn-outline-secondary' }}">
                                    <i class="fas fa-trash-alt me-1"></i> Borradores del Sistema ({{ $stats['borradores'] ?? 0 }})
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        @if(auth()->user()->role == 'viaticos' && $filtro == 'enviadas')
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 shadow-sm fw-bold"
                                data-bs-toggle="modal" data-bs-target="#modalExportSupervisor">
                                <i class="fas fa-user-tie me-2"></i> Por Supervisor
                            </button>
                            <button type="button" id="btn-export-bulk-dashboard"
                                class="btn btn-success btn-sm rounded-pill px-4 shadow-sm d-none fw-bold">
                                <i class="fas fa-file-excel me-2"></i> Descarga Masiva (<span
                                    id="selected-count-dashboard">0</span>)
                            </button>
                        @endif
                        <a href="{{ route('inicio') }}" class="btn btn-light btn-sm rounded-pill px-3 border text-muted">
                            <i class="fas fa-times me-1"></i> Cerrar Filtro
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-2">
                        <table class="table table-hover table-borderless align-middle mb-0"
                            style="border-collapse: separate; border-spacing: 0 8px;">
                            <thead>
                                @php
                                    $userRole = auth()->user()->role;
                                    $isAdminRole = in_array($userRole, ['supervisor_contrato', 'ordenador_gasto']);

                                    // Ocultar Gestión en 'Devueltas' solo para admin
                                    $showGestion = true;
                                    if ($filtro == 'devueltas' && $userRole == 'administrador') {
                                        $showGestion = false;
                                    }
                                @endphp
                                <tr class="border-bottom border-light">
                                    @if(auth()->user()->role == 'viaticos' && $filtro == 'enviadas')
                                        <th class="ps-4 pb-3" style="width: 40px;">
                                            <input type="checkbox" id="select-all-dashboard" class="form-check-input shadow-sm">
                                        </th>
                                    @endif
                                    <th class="ps-4 pb-3 text-uppercase fw-bold text-muted text-nowrap"
                                        style="min-width: 150px; font-size: 0.7rem; letter-spacing: 0.5px;">Ruta / Destino</th>
                                    @if(in_array($userRole, ['supervisor_contrato', 'ordenador_gasto', 'viaticos', 'legalizacion']))
                                        <th class="pb-3 text-uppercase fw-bold text-muted text-nowrap"
                                            style="min-width: 120px; font-size: 0.7rem; letter-spacing: 0.5px;">Contratista</th>
                                    @endif
                                    <th class="pb-3 text-uppercase fw-bold text-muted text-nowrap"
                                        style="min-width: 100px; font-size: 0.7rem; letter-spacing: 0.5px;">Fechas</th>
                                    @if($filtro == 'devueltas')
                                        <th class="pb-3 text-uppercase fw-bold text-muted text-center text-nowrap"
                                            style="min-width: 120px; font-size: 0.7rem; letter-spacing: 0.5px;">Motivo Error</th>
                                    @else
                                        <th class="pb-3 text-uppercase fw-bold text-muted text-center text-nowrap"
                                            style="min-width: 100px; font-size: 0.7rem; letter-spacing: 0.5px;">Estado</th>
                                    @endif
                                    @if($showGestion)
                                        @if(auth()->user()->role !== 'legalizacion')
                                            <th class="pb-3 text-center text-uppercase fw-bold text-muted text-nowrap"
                                                style="min-width: 130px; font-size: 0.7rem; letter-spacing: 0.5px;">Gestión Agenda</th>
                                        @endif
                                        @if(auth()->user()->role !== 'viaticos' && !(auth()->user()->role !== 'legalizacion' && $filtro == 'enviadas'))
                                            <th class="pb-3 text-center text-uppercase fw-bold text-muted text-nowrap"
                                                style="min-width: 130px; font-size: 0.7rem; letter-spacing: 0.5px;">Gestión Legalización</th>
                                        @endif
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agendas as $agenda)
                                    <tr class="align-middle bg-white rounded-3 shadow-sm transition-all hover-translate-y">
                                        @if(auth()->user()->role == 'viaticos' && $filtro == 'enviadas')
                                            <td class="ps-4 py-3 rounded-start-3">
                                                @if(in_array($agenda->estado->nombre, ['APROBADA_VIATICOS', 'APROBADA']))
                                                    <input type="checkbox" name="ids[]" value="{{ $agenda->id }}"
                                                        class="form-check-input agenda-checkbox-dashboard shadow-sm">
                                                @else
                                                    <input type="checkbox" class="form-check-input" disabled title="Aún no aprobada">
                                                @endif
                                            </td>
                                        @endif
                                        <td
                                            class="ps-4 py-3 {{ (auth()->user()->role != 'viaticos' || $filtro != 'enviadas') ? 'rounded-start-3' : '' }}">
                                            <div class="fw-bold text-dark text-uppercase" style="font-size: 0.9rem;">
                                                <i class="fas fa-route text-success me-2 opacity-75"></i> {{ $agenda->ruta }}
                                            </div>
                                            <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                                ID: <span class="fw-bold">#{{ $agenda->id }}</span> |
                                                @if($agenda->destinos)
                                                    {{ implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $agenda->destinos)))) }}
                                                @else
                                                    {{ $agenda->ciudad_destino ?: 'Sin destino' }}
                                                @endif
                                            </div>
                                            @if($filtro == 'pendientes' && in_array(auth()->user()->role, ['supervisor_contrato', 'ordenador_gasto', 'viaticos']))
                                                <div class="mt-1">
                                                    @if(($userRole == 'supervisor_contrato' && $agenda->legalizacion_estado == 'ENVIADA') || ($userRole == 'ordenador_gasto' && $agenda->legalizacion_estado == 'APROBADA_LEGALIZACION'))
                                                        <span class="badge rounded-pill bg-warning text-dark px-2 py-1 small fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.3px;">
                                                            <i class="fas fa-file-invoice-dollar me-1"></i> Trámite: Legalización
                                                        </span>
                                                    @else
                                                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-2 py-1 small fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.3px;">
                                                            <i class="fas fa-calendar-alt me-1"></i> Trámite: Agenda de Viaje
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                             @if(auth()->user()->role !== 'viaticos' && $agenda->legalizacion_estado && (!in_array($agenda->legalizacion_estado, ['BORRADOR', 'CREADA']) || $agenda->user_id == auth()->id()))
                                                 <div class="mt-2">
                                                    @php
                                                        $legEst = $agenda->legalizacion_estado;
                                                        $legBadgeStyle = match ($legEst) {
                                                            'CREADA', 'BORRADOR' => 'background-color: #64748b; color: white;',
                                                            'ENVIADA' => 'background-color: #0ea5e9; color: white;',
                                                            'APROBADA_SUPERVISOR' => 'background-color: #0d9488; color: white;',
                                                            'APROBADA_LEGALIZACION' => 'background-color: #8b5cf6; color: white;',
                                                            'APROBADA_ORDENADOR' => 'background-color: #22c55e; color: white;',
                                                            'DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR' => 'background-color: #ef4444; color: white;',
                                                            default => 'background-color: #334155; color: white;'
                                                        };
                                                        $legBadgeText = match ($legEst) {
                                                            'CREADA', 'BORRADOR' => 'Borrador (Legalización)',
                                                            'ENVIADA' => 'Legalización: Enviada a Supervisor',
                                                            'APROBADA_SUPERVISOR' => 'Legalización: Aprobada por Supervisor',
                                                            'APROBADA_LEGALIZACION' => 'Legalización: Aprobada por Legalización',
                                                            'APROBADA_ORDENADOR' => 'Legalización: Aprobada Final',
                                                            'DEVUELTA_SUPERVISOR' => 'Legalización: Devuelta por Supervisor',
                                                            'DEVUELTA_LEGALIZACION' => 'Legalización: Devuelta por Legalización',
                                                            'DEVUELTA_ORDENADOR' => 'Legalización: Devuelta por Ordenador',
                                                            default => $legEst
                                                        };
                                                    @endphp
                                                    <span class="badge rounded-pill px-2 py-1 text-uppercase fw-bold shadow-sm" 
                                                          style="{{ $legBadgeStyle }} font-size: 0.62rem; letter-spacing: 0.3px;">
                                                        {{ $legBadgeText }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        @if(in_array(auth()->user()->role, ['supervisor_contrato', 'ordenador_gasto', 'viaticos', 'legalizacion']))
                                            <td class="py-3">
                                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                    {{ $agenda->user->name ?? 'N/A' }}</div>
                                                <div class="text-muted"
                                                    style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.3px;">
                                                    {{ $agenda->user->categoria->nombre ?? 'N/A' }}
                                                </div>
                                            </td>
                                        @endif
                                        <td class="py-3">
                                            <div class="d-flex flex-column gap-1">
                                                <span class="fw-bold text-dark" style="font-size: 0.8rem;"><i
                                                        class="fas fa-calendar-alt text-muted me-1 opacity-50"></i>
                                                    {{ $agenda->fecha_inicio?->format('d/m/Y') }}</span>
                                                <span class="text-muted" style="font-size: 0.8rem;"><i
                                                        class="fas fa-arrow-right text-muted me-1 opacity-50"></i>
                                                    {{ $agenda->fecha_fin?->format('d/m/Y') }}</span>
                                            </div>
                                        </td>

                                        @if($filtro == 'devueltas')
                                            <td class="text-center py-3 {{ !$showGestion ? 'rounded-end-3' : '' }}">
                                                <div class="d-inline-block p-2 rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 clickable-badge transition-all shadow-sm"
                                                    style="cursor: pointer; min-width: 100px;" data-bs-toggle="modal"
                                                    data-bs-target="#modalGestion{{ $agenda->id }}" title="Ver detalles de devolución">
                                                    <div class="text-danger fw-bold small text-uppercase mb-1"
                                                        style="font-size: 0.7rem;">
                                                        <i class="fas fa-undo-alt me-1"></i> 
                                                        @if(\Illuminate\Support\Str::startsWith($agenda->legalizacion_estado ?? '', 'DEVUELTA_'))
                                                            Leg. Devuelta
                                                        @else
                                                            Agenda Devuelta
                                                        @endif
                                                    </div>
                                                    <div class="text-dark fw-medium" style="font-size: 0.65rem; line-height: 1.1;">
                                                        {{ Str::limit(\Illuminate\Support\Str::startsWith($agenda->legalizacion_estado ?? '', 'DEVUELTA_') ? $agenda->legalizacion_observaciones : $agenda->observaciones_finanzas, 30) }}
                                                    </div>
                                                </div>
                                            </td>
                                        @else
                                            <td class="text-center py-3 {{ !$showGestion ? 'rounded-end-3' : '' }}">
                                                @php
                                                    $est = $agenda->estado->nombre ?? 'N/A';
                                                    $badgeStyle = match (strtoupper($est)) {
                                                        'BORRADOR' => 'background-color: #64748b; color: white;',
                                                        'ENVIADA' => 'background-color: #0ea5e9; color: white;',
                                                        'APROBADA_SUPERVISOR' => 'background-color: #10b981; color: white;',
                                                        'APROBADA_VIATICOS' => 'background-color: #8b5cf6; color: white;',
                                                        'APROBADA' => 'background-color: #39a900; color: white;',
                                                        default => 'background-color: #334155; color: white;'
                                                    };
                                                @endphp
                                                <span class="badge rounded-pill px-3 py-2 text-uppercase fw-bold shadow-sm"
                                                    style="{{ $badgeStyle }} font-size: 0.68rem; letter-spacing: 0.5px;">
                                                    {{ str_replace('_', ' ', $est) }}
                                                </span>
                                            </td>
                                        @endif

                                        @if($showGestion)
                                            @php
                                                $userRole = auth()->user()->role;

                                                $funcionario = \App\Models\LiderDeProceso::where('numero_documento', auth()->user()->numero_documento)->first();
                                                $funcionarioId = $funcionario ? $funcionario->id : null;

                                                $isOwner = in_array($userRole, ['contratista', 'funcionario']) && $agenda->user_id == auth()->id();

                                                $isSupervisor = in_array($userRole, ['supervisor_contrato', 'ordenador_gasto', 'administrador']) ||
                                                    ($funcionarioId && ($agenda->supervisor_id === $funcionarioId || $agenda->ordenador_id === $funcionarioId));

                                                $estadoNombre = $agenda->estado->nombre ?? '';
                                            @endphp

                                            {{-- COLUMNA 1: GESTIÓN AGENDA --}}
                                            @if(auth()->user()->role !== 'legalizacion')
                                                <td class="text-center py-3 {{ (auth()->user()->role == 'viaticos' || (auth()->user()->role !== 'legalizacion' && $filtro == 'enviadas')) ? 'rounded-end-3' : '' }}">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        @if($isOwner)
                                                            {{-- Caso 1: Devueltas --}}
                                                            @if($filtro == 'devueltas')
                                                                @if(!in_array($agenda->legalizacion_estado, ['DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']))
                                                                    @if($estadoNombre !== 'APROBADA')
                                                                        <a href="{{ route('formulario', $agenda->id) }}"
                                                                            class="btn btn-warning btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm text-dark"
                                                                            title="Corregir Agenda #{{ $agenda->id }}">
                                                                            <i class="fas fa-exclamation-triangle"></i> <span
                                                                                class="d-none d-md-inline ms-1">Corregir</span>
                                                                        </a>
                                                                        <a href="{{ route('reportes.show', $agenda->id) }}"
                                                                            class="btn btn-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                            title="Reportar Actividades Agenda #{{ $agenda->id }}">
                                                                            <i class="fas fa-calendar-check"></i> <span
                                                                                class="d-none d-md-inline ms-1">Reportar Actividades</span>
                                                                        </a>
                                                                    @endif
                                                                    <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                                                        class="btn btn-outline-danger btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm bg-white"
                                                                        title="Ver PDF Agenda #{{ $agenda->id }}">
                                                                        <i class="fas fa-file-pdf"></i> <span class="d-none d-md-inline ms-1">PDF</span>
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted small fw-medium text-nowrap"><i class="fas fa-check-circle text-success me-1"></i> Agenda Aprobada</span>
                                                                @endif
                                                            @endif

                                                            {{-- Caso 2: Pendientes --}}
                                                            @if($filtro == 'pendientes' && $estadoNombre == 'BORRADOR')
                                                                <a href="{{ route('formulario', $agenda->id) }}"
                                                                    class="btn btn-outline-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm bg-white"
                                                                    title="Editar Agenda #{{ $agenda->id }}">
                                                                    <i class="fas fa-edit"></i> <span class="d-none d-md-inline ms-1">Editar</span>
                                                                </a>
                                                                <a href="{{ route('reportes.show', $agenda->id) }}"
                                                                    class="btn btn-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                    title="Reportar Actividades Agenda #{{ $agenda->id }}">
                                                                    <i class="fas fa-calendar-check"></i> <span
                                                                        class="d-none d-md-inline ms-1">Reportar Actividades</span>
                                                                </a>
                                                                <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                                                    class="btn btn-outline-danger btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm bg-white"
                                                                    title="Ver PDF Agenda #{{ $agenda->id }}">
                                                                    <i class="fas fa-file-pdf"></i> <span class="d-none d-md-inline ms-1">PDF</span>
                                                                </a>
                                                                <form action="{{ route('reportar-dia.enviar', $agenda->id) }}" method="POST"
                                                                    class="d-inline form-enviar">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="btn btn-primary btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm btn-enviar-confirm"
                                                                        data-total="{{ \Carbon\Carbon::parse($agenda->fecha_inicio)->diffInDays(\Carbon\Carbon::parse($agenda->fecha_fin)) + 1 }}"
                                                                        data-reportados="{{ $agenda->actividades->count() }}" title="Enviar Agenda #{{ $agenda->id }}">
                                                                        <i class="fas fa-paper-plane"></i> <span
                                                                            class="d-none d-md-inline ms-1">Enviar</span>
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            {{-- Caso Proceso/Finalizadas --}}
                                                            @if(in_array($filtro, ['enviadas', 'finalizadas']))
                                                                <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                                                    class="btn btn-outline-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                    title="Ver PDF Agenda #{{ $agenda->id }}">
                                                                    <i class="fas fa-file-pdf"></i> <span class="d-none d-md-inline">PDF {{ $filtro == 'finalizadas' ? 'Final' : 'Previa' }}</span>
                                                                </a>
                                                            @endif
                                                        @endif

                                                        @if($isSupervisor)
                                                            @if($filtro == 'pendientes')
                                                                @if($agenda->estado->nombre === 'APROBADA')
                                                                    <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                                                        class="btn btn-outline-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                        title="Ver PDF Agenda #{{ $agenda->id }}">
                                                                        <i class="fas fa-file-pdf"></i> <span class="d-none d-md-inline">Ver PDF</span>
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                                                        class="btn btn-outline-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                        title="Ver PDF Agenda #{{ $agenda->id }}">
                                                                        <i class="fas fa-file-pdf"></i> <span class="d-none d-md-inline">PDF</span>
                                                                    </a>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger rounded-pill px-2 px-md-3 shadow-sm fw-bold d-inline-flex align-items-center btn-open-devolver"
                                                                        data-id="{{ $agenda->id }}" data-nombre="{{ $agenda->user->name ?? 'N/A' }}"
                                                                        data-role="{{ $userRole }}" title="Devolver Agenda #{{ $agenda->id }}">
                                                                        <i class="fas fa-undo"></i> <span class="d-none d-md-inline ms-1">Devolver</span>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-primary rounded-pill px-2 px-md-3 shadow-sm fw-bold d-inline-flex align-items-center btn-open-firma"
                                                                        data-id="{{ $agenda->id }}" data-nombre="{{ $agenda->user->name ?? 'N/A' }}"
                                                                        data-role="{{ $userRole }}" title="Firmar Agenda #{{ $agenda->id }}">
                                                                        <i class="fas fa-pen-nib"></i> <span class="d-none d-md-inline ms-1">Firmar</span>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                            @if(in_array($filtro, ['enviadas', 'finalizadas']))
                                                                <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                                                    class="btn btn-outline-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                    title="Ver PDF Agenda #{{ $agenda->id }}">
                                                                    <i class="fas fa-file-pdf"></i> <span class="d-none d-md-inline">Ver PDF</span>
                                                                </a>
                                                            @endif
                                                            <form action="{{ route('reportes.destroy', $agenda->id) }}" method="POST"
                                                                class="d-inline form-eliminar">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-link text-danger btn-sm p-2"
                                                                    title="Eliminar Agenda #{{ $agenda->id }} Permanentemente">
                                                                    <i class="fas fa-trash-alt fa-lg"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        {{-- Caso 4: Viáticos (Excel, Gestión) --}}
                                                        @if($userRole == 'viaticos')
                                                            @if(in_array($estadoNombre, ['APROBADA_VIATICOS', 'APROBADA']))
                                                                <a href="{{ route('viaticos.export', $agenda->id) }}"
                                                                    class="btn btn-success btn-sm rounded-pill px-3 shadow-sm fw-bold">
                                                                    <i class="fas fa-file-excel me-1"></i> Excel
                                                                </a>
                                                            @endif

                                                            <a href="{{ route('viaticos.gestionar', ['id' => $agenda->id, 'tab' => $filtro]) }}"
                                                                class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                                                <i class="fas fa-tasks me-1"></i> Gestión
                                                            </a>

                                                            @if($filtro === 'borradores' && $estadoNombre === 'BORRADOR')
                                                                <form action="{{ route('reportes.destroy', $agenda->id) }}" method="POST"
                                                                    class="d-inline form-eliminar">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                                                        <i class="fas fa-trash-alt me-1"></i> Eliminar
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            @endif

                                            {{-- COLUMNA 2: GESTIÓN LEGALIZACIÓN --}}
                                            @if(auth()->user()->role !== 'viaticos' && !(auth()->user()->role !== 'legalizacion' && $filtro == 'enviadas'))
                                                <td class="text-center py-3 rounded-end-3" style="background-color: rgba(57, 169, 0, 0.015); border-left: 1px dashed rgba(0,0,0,0.08);">
                                                <div class="d-flex justify-content-center gap-2 align-items-center">
                                                    @if($agenda->estado->nombre !== 'APROBADA')
                                                         <span class="text-muted small fw-medium text-nowrap"><i class="fas fa-lock me-1 opacity-50"></i> Requiere Aprobación Agenda</span>
                                                    @else
                                                        {{-- Contratista / Propietario --}}
                                                        @if($isOwner)
                                                            @if(empty($agenda->legalizacion_estado))
                                                                <a href="{{ route('legalizacion.crear', $agenda->id) }}"
                                                                    class="btn btn-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                    title="Registrar Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                    <i class="fas fa-file-invoice-dollar"></i> <span
                                                                        class="d-none d-md-inline ms-1">Legalización</span>
                                                                </a>
                                                            @else
                                                                @if(in_array($agenda->legalizacion_estado, ['CREADA', 'BORRADOR', 'DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']))
                                                                    <a href="{{ route('legalizacion.ver', $agenda->id) }}" target="_blank"
                                                                        class="btn btn-outline-primary btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm bg-white"
                                                                        title="Ver PDF de Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                        <i class="fas fa-file-contract"></i> <span class="d-none d-md-inline ms-1">Ver Leg.</span>
                                                                    </a>
                                                                    @if(in_array($agenda->legalizacion_estado, ['DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']))
                                                                        <a href="{{ route('legalizacion.crear', $agenda->id) }}"
                                                                            class="btn btn-warning btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm text-dark"
                                                                            title="Corregir Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                            <i class="fas fa-exclamation-triangle"></i> <span class="d-none d-md-inline ms-1">Corregir Leg.</span>
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ route('legalizacion.crear', $agenda->id) }}"
                                                                            class="btn btn-outline-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm bg-white"
                                                                            title="Editar Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                            <i class="fas fa-edit"></i> <span class="d-none d-md-inline ms-1">Editar Leg.</span>
                                                                        </a>
                                                                    @endif
                                                                    <form action="{{ route('legalizacion.enviar', $agenda->id) }}" method="POST" class="d-inline form-enviar-legalizacion">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm btn-enviar-leg-confirm" title="Enviar Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                            <i class="fas fa-paper-plane"></i> <span class="d-none d-md-inline ms-1">Enviar Leg.</span>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <a href="{{ route('legalizacion.ver', $agenda->id) }}" target="_blank"
                                                                        class="btn btn-outline-primary btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                        title="Ver PDF de Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                        <i class="fas fa-file-contract"></i> <span class="d-none d-md-inline ms-1">Ver Leg.</span>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        @endif

                                                        {{-- Supervisor / Revisor --}}
                                                        @if($isSupervisor)
                                                            @if(!empty($agenda->legalizacion_estado) && !in_array($agenda->legalizacion_estado, ['BORRADOR', 'CREADA']))
                                                                <a href="{{ route('legalizacion.ver', $agenda->id) }}" target="_blank"
                                                                    class="btn btn-outline-primary btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                    title="Ver PDF de Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                    <i class="fas fa-file-contract"></i> <span class="d-none d-md-inline ms-1">Ver Leg.</span>
                                                                </a>
                                                                @if($filtro == 'pendientes' && $agenda->legalizacion_estado == 'ENVIADA' && (auth()->user()->role == 'supervisor_contrato' || $agenda->supervisor_id == $funcionarioId))
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger rounded-pill px-2 px-md-3 shadow-sm fw-bold d-inline-flex align-items-center btn-open-devolver-leg"
                                                                        data-id="{{ $agenda->id }}" data-nombre="{{ $agenda->user->name ?? 'N/A' }}"
                                                                        data-role="supervisor" title="Devolver Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                        <i class="fas fa-undo"></i> <span class="d-none d-md-inline ms-1">Devolver Leg.</span>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-primary rounded-pill px-2 px-md-3 shadow-sm fw-bold d-inline-flex align-items-center btn-open-firma-leg"
                                                                        data-id="{{ $agenda->id }}" data-nombre="{{ $agenda->user->name ?? 'N/A' }}"
                                                                        data-role="supervisor" title="Firmar Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                        <i class="fas fa-pen-nib"></i> <span class="d-none d-md-inline ms-1">Firmar Leg.</span>
                                                                    </button>
                                                                @endif
                                                                @if($filtro == 'pendientes' && $agenda->legalizacion_estado == 'APROBADA_LEGALIZACION' && (auth()->user()->role == 'ordenador_gasto' || $agenda->ordenador_id == $funcionarioId))
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger rounded-pill px-2 px-md-3 shadow-sm fw-bold d-inline-flex align-items-center btn-open-devolver-leg"
                                                                        data-id="{{ $agenda->id }}" data-nombre="{{ $agenda->user->name ?? 'N/A' }}"
                                                                        data-role="subdirector" title="Devolver Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                        <i class="fas fa-undo"></i> <span class="d-none d-md-inline ms-1">Devolver Leg.</span>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-primary rounded-pill px-2 px-md-3 shadow-sm fw-bold d-inline-flex align-items-center btn-open-firma-leg"
                                                                        data-id="{{ $agenda->id }}" data-nombre="{{ $agenda->user->name ?? 'N/A' }}"
                                                                        data-role="subdirector" title="Firmar Legalización de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                        <i class="fas fa-pen-nib"></i> <span class="d-none d-md-inline ms-1">Firmar Leg.</span>
                                                                    </button>
                                                                @endif
                                                            @else
                                                                <span class="text-muted small fw-medium text-nowrap">No registrada</span>
                                                            @endif
                                                        @endif

                                                        {{-- Legalización --}}
                                                        @if(auth()->user()->role == 'legalizacion')
                                                            @if(!empty($agenda->legalizacion_estado) && !in_array($agenda->legalizacion_estado, ['BORRADOR', 'CREADA']))
                                                                @if($filtro == 'pendientes' && $agenda->legalizacion_estado == 'APROBADA_SUPERVISOR')
                                                                    <a href="{{ route('legalizacion.gestionar', ['id' => $agenda->id, 'tab' => $filtro]) }}"
                                                                        class="btn btn-success btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                        title="Gestionar Legalización #{{ $agenda->id }}">
                                                                        <i class="fas fa-tasks"></i> <span class="d-none d-md-inline ms-1">Gestionar</span>
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('legalizacion.ver', $agenda->id) }}" target="_blank"
                                                                        class="btn btn-outline-primary btn-sm rounded-pill px-2 px-md-3 fw-bold shadow-sm"
                                                                        title="Ver PDF de Legalización #{{ $agenda->id }}">
                                                                        <i class="fas fa-file-contract"></i> <span class="d-none d-md-inline ms-1">Ver Leg.</span>
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <span class="text-muted small fw-medium text-nowrap">No registrada</span>
                                                            @endif
                                                        @endif

                                                        {{-- Viáticos --}}
                                                        @if($userRole == 'viaticos')
                                                            @if(!empty($agenda->orden_viaje))
                                                                <a href="{{ route('legalizacion.ver', $agenda->id) }}" target="_blank"
                                                                    class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm"
                                                                    title="Ver Liquidación de la Agenda #{{ $agenda->id }} (Ruta: {{ $agenda->ruta }})">
                                                                    <i class="fas fa-file-contract me-1"></i> Ver Liquidación
                                                                </a>
                                                            @else
                                                                <span class="text-muted small fw-medium text-nowrap">No registrada</span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                            @endif
                                        @endif

                                    </tr>
                                @empty
                                    <tr>
                                        @php
                                            $colSpan = 3; // Ruta/Destino, Fechas, Estado/Motivo
                                            if (in_array($userRole, ['supervisor_contrato', 'ordenador_gasto', 'viaticos', 'legalizacion'])) {
                                                $colSpan++; // Contratista
                                            }
                                            if (auth()->user()->role == 'viaticos' && $filtro == 'enviadas') {
                                                $colSpan++; // Checkbox
                                            }
                                            if ($showGestion) {
                                                $activeGestionColumns = 2;
                                                if (auth()->user()->role == 'legalizacion') {
                                                    $activeGestionColumns = 1;
                                                } elseif (auth()->user()->role == 'viaticos') {
                                                    $activeGestionColumns = 1;
                                                } elseif (auth()->user()->role !== 'legalizacion' && $filtro == 'enviadas') {
                                                    $activeGestionColumns = 1;
                                                }
                                                $colSpan += $activeGestionColumns;
                                            }
                                        @endphp
                                        <td colspan="{{ $colSpan }}" class="text-center p-5 text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0 fs-5">No hay registros para mostrar en esta categoría.</p>
                                            @if(request('search'))
                                                <a href="{{ route('inicio', ['ver' => $filtro]) }}"
                                                    class="btn btn-link text-success p-0 mt-2">Limpiar búsqueda</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-4 d-flex justify-content-center">
                    <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block">
                        {{ $agendas->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    @foreach($agendas as $agenda)
        {{-- MODAL GESTIÓN BÁSICA (DASHBOARD) --}}
        <div class="modal fade" id="modalGestion{{ $agenda->id }}" tabindex="-1" aria-hidden="true">
            {{-- ... contenido del modal gestión ... --}}
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-white p-4 pb-0">
                        <h5 class="fw-bold mb-0 text-dark fs-4">Detalles de la Agenda #{{ $agenda->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4 text-center">
                        {{-- Fila PDF --}}
                        <div
                            class="bg-light rounded-4 p-3 d-flex align-items-center justify-content-between mb-4 border border-opacity-10">
                            @php
                                $esLegDevuelta = \Illuminate\Support\Str::startsWith($agenda->legalizacion_estado ?? '', 'DEVUELTA_');
                                $pdfUrl = $esLegDevuelta ? route('legalizacion.ver', $agenda->id) : route('agenda.pdf', $agenda->id);
                                $pdfLabel = $esLegDevuelta ? 'PDF Legalización' : 'PDF Agenda';
                            @endphp
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">{{ $pdfLabel }}</span>
                            </div>
                            <a href="{{ $pdfUrl }}" target="_blank"
                                class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm btn-sm">
                                ABRIR ARCHIVO
                            </a>
                        </div>

                        {{-- Responsable --}}
                        <div class="mb-4">
                            <label class="small text-muted fw-bold d-block text-uppercase mb-1" style="letter-spacing: 1px;">
                                {{ auth()->user()->role === 'funcionario' ? 'Comisionado Registrado' : 'Contratista Registrado' }}
                            </label>
                            <h2 class="fw-bold text-dark text-uppercase mb-0"
                                style="font-size: 1.75rem; letter-spacing: -0.5px;">{{ $agenda->user->name ?? 'N/A' }}</h2>
                        </div>

                        {{-- Observaciones de Devolución --}}
                        @if($agenda->observaciones_finanzas || $agenda->legalizacion_observaciones)
                            <div class="mt-4 p-4 rounded-4 text-start border border-warning border-opacity-25"
                                style="background-color: #fffbeb;">
                                <label class="small fw-bold text-warning-emphasis text-uppercase mb-2 d-block"
                                    style="letter-spacing: 0.5px;">
                                    <i class="fas fa-comment-dots me-1"></i> Nota de Revisión:
                                </label>
                                <p class="mb-0 text-dark fw-medium"
                                    style="font-style: italic; font-size: 0.95rem; color: #92400e !important;">
                                    "{{ \Illuminate\Support\Str::startsWith($agenda->legalizacion_estado ?? '', 'DEVUELTA_') ? $agenda->legalizacion_observaciones : $agenda->observaciones_finanzas }}"
                                </p>
                            </div>
                        @else
                            {{-- Info de Ruta si no hay observaciones --}}
                            <div class="mt-4 p-3 bg-light rounded-4 border border-opacity-10 text-start">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="small text-muted fw-bold d-block text-uppercase mb-1">Ruta</label>
                                        <p class="fw-bold text-dark mb-0 small"><i class="fas fa-route me-1 text-success"></i>
                                            {{ $agenda->ruta }}</p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <label class="small text-muted fw-bold d-block text-uppercase mb-1">Periodo</label>
                                        <p class="fw-bold text-dark mb-0 small">{{ $agenda->fecha_inicio?->format('d/m/Y') }} -
                                            {{ $agenda->fecha_fin?->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary rounded-pill px-5 fw-bold shadow-sm"
                            style="background-color: #64748b; border: none;" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL UNIVERSAL DE FIRMA --}}
        <div class="modal fade text-start" id="modalFirmaUniversal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                    <form id="formFirmaUniversal" method="POST" class="form-autorizar-agenda">
                        @csrf
                        <div class="modal-header border-0 bg-primary bg-opacity-10 py-3">
                            <h5 class="modal-title fw-bold text-primary" id="firma-modal-title"><i class="fas fa-pen-nib me-2"></i>Autorización de
                                Agenda</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <p class="text-muted mb-3" id="firma-body-text">
                                Al firmar esta agenda, certifica la revisión técnica de las actividades reportadas por <strong
                                    id="firma-nombre-usuario">...</strong>.
                            </p>
                            <div class="alert alert-info py-2 small d-flex align-items-center rounded-3 border-0 shadow-sm">
                                <i class="fas fa-info-circle me-2 fs-5"></i>
                                <span id="firma-banner-text">Se utilizará su <strong>Firma Digital</strong> registrada para autorizar esta
                                    agenda.</span>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-1"></i> Confirmar y Firmar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL UNIVERSAL DE DEVOLVER --}}
        <div class="modal fade text-start" id="modalDevolverUniversal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                    <form id="formDevolverUniversal" method="POST">
                        @csrf
                        <div class="modal-header border-0 bg-danger bg-opacity-10 text-danger py-3">
                            <h5 class="modal-title fw-bold" id="devolver-modal-title"><i class="fas fa-undo me-2"></i>Devolver Agenda</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <p class="text-muted small mb-3" id="devolver-body-text">
                                Está a punto de devolver la agenda de <strong id="devolver-nombre-usuario">...</strong>. Indique
                                el motivo para que el usuario pueda ajustarla.
                            </p>
                            <label class="form-label fw-bold text-dark small text-uppercase">Motivo de la corrección <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control rounded-3 border-2" name="observaciones" rows="4"
                                placeholder="Ej: Faltan detalles en la ruta o actividades..." required
                                maxlength="500"></textarea>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-paper-plane me-1"></i> Enviar a Corrección
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- MODAL EXPORTAR POR SUPERVISOR (DASHBOARD) --}}
    @if(auth()->user()->role == 'viaticos')
        <div class="modal fade" id="modalExportSupervisor" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header border-bottom-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold">Exportar Agendas por Supervisor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-uppercase mb-2">Seleccione el Supervisor</label>
                            <select id="select-supervisor-modal-dashboard"
                                class="form-select rounded-pill shadow-sm border-0 bg-light px-4">
                                <option value="">-- Buscar Supervisor --</option>
                                @foreach($supervisores as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->nombre }} ({{ $sup->cargo }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="agendas-supervisor-container-dashboard" class="d-none">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 text-dark">Agendas Aprobadas Encontradas</h6>
                                <span id="agendas-count-dashboard" class="badge bg-primary rounded-pill px-3">0 agendas</span>
                            </div>
                            <div class="table-responsive rounded-3 border">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Contratista</th>
                                            <th>Ruta/Destino</th>
                                            <th>Fecha</th>
                                            <th class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="agendas-supervisor-list-dashboard">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="no-agendas-msg-dashboard" class="text-center py-5 d-none">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No se encontraron agendas aprobadas para este supervisor.</p>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <form id="form-export-supervisor-dashboard" action="{{ route('viaticos.exportBulk') }}" method="POST"
                            class="d-none">
                            @csrf
                            <div id="hidden-ids-container-dashboard"></div>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                <i class="fas fa-file-excel me-2"></i> Descargar Todas
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
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

            /* Estilo para la paginación circular */
            .pagination-container nav>div:first-child,
            .pagination-container nav div.d-none.flex-sm-fill>div:first-child,
            .pagination-container nav p.text-muted {
                display: none !important;
            }

            .pagination-container nav ul.pagination {
                margin-bottom: 0;
                gap: 5px;
            }

            .pagination-container .page-item .page-link {
                border-radius: 50% !important;
                width: 38px;
                height: 38px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: none;
                color: #4b5563;
                font-weight: 600;
                background: transparent;
                transition: all 0.2s;
            }

            .pagination-container .page-item.active .page-link {
                background-color: #39a900 !important;
                color: white !important;
                box-shadow: 0 4px 6px -1px rgba(57, 169, 0, 0.4);
            }

            .pagination-container .page-item:not(.active) .page-link:hover {
                background-color: #f1f5f9;
                color: #39a900;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function () {
                // Confirmación de envío
                $('.btn-enviar-confirm').on('click', function (e) {
                    e.preventDefault();
                    const btn = $(this);
                    const form = btn.closest('.form-enviar');
                    const total = parseInt(btn.data('total'));
                    const reportados = parseInt(btn.data('reportados'));

                    if (reportados < total) {
                        Swal.fire({
                            title: 'Reporte Incompleto',
                            html: `Esta agenda es de <b>${total} días</b>, pero solo has reportado <b>${reportados} día(s)</b>.<br><br>Debes reportar todos los días antes de enviar.`,
                            icon: 'warning',
                            confirmButtonColor: '#39a900'
                        });
                        return;
                    }

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Has terminado de reportar todas tus actividades? Una vez enviada no podrás modificar nada.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#39a900',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Sí, enviar agenda',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'rounded-pill px-4',
                            cancelButton: 'rounded-pill px-4'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });

                // Confirmación de eliminación
                $('.form-eliminar').on('submit', function (e) {
                    e.preventDefault();
                    const form = this;
                    Swal.fire({
                        title: '¿Eliminar Agenda?',
                        text: "Esta acción es permanente y no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });

                // --- LÓGICA DE MODALES UNIVERSALES (EVITA PARPADEO) ---

                // Abrir Modal de Firma
                $('.btn-open-firma').on('click', function () {
                    const id = $(this).data('id');
                    const nombre = $(this).data('nombre');
                    const role = $(this).data('role');

                    // Definir ruta según rol
                    let action = role === 'supervisor_contrato'
                        ? "{{ route('supervisor_contrato.autorizar', ':id') }}"
                        : "{{ route('ordenador_gasto.autorizar', ':id') }}";

                    $('#formFirmaUniversal').attr('action', action.replace(':id', id));
                    
                    // Restablecer textos para Agenda
                    $('#firma-modal-title').html('<i class="fas fa-pen-nib me-2"></i>Autorización de Agenda');
                    $('#firma-body-text').html('Al firmar esta agenda, certifica la revisión técnica de las actividades reportadas por <strong id="firma-nombre-usuario">' + nombre + '</strong>.');
                    $('#firma-banner-text').html('Se utilizará su <strong>Firma Digital</strong> registrada para autorizar esta agenda.');

                    $('#modalFirmaUniversal').modal('show');
                });

                // Abrir Modal de Devolución
                $('.btn-open-devolver').on('click', function () {
                    const id = $(this).data('id');
                    const nombre = $(this).data('nombre');
                    const role = $(this).data('role');

                    // Definir ruta según rol
                    let action = role === 'supervisor_contrato'
                        ? "{{ route('supervisor_contrato.devolver', ':id') }}"
                        : "{{ route('ordenador_gasto.devolver', ':id') }}";

                    $('#formDevolverUniversal').attr('action', action.replace(':id', id));
                    
                    // Restablecer textos para Agenda
                    $('#devolver-modal-title').html('<i class="fas fa-undo me-2"></i>Devolver Agenda');
                    $('#devolver-body-text').html('Está a punto de devolver la agenda de <strong id="devolver-nombre-usuario">' + nombre + '</strong>. Indique el motivo para que el usuario pueda ajustarla.');

                    $('#modalDevolverUniversal').modal('show');
                });

                // Abrir Modal de Firma de Legalización
                $('.btn-open-firma-leg').on('click', function () {
                    const id = $(this).data('id');
                    const nombre = $(this).data('nombre');
                    const role = $(this).data('role');

                    let action = role === 'supervisor'
                        ? "{{ route('supervisor_contrato.autorizar_legalizacion', ':id') }}"
                        : "{{ route('ordenador_gasto.autorizar_legalizacion', ':id') }}";

                    $('#formFirmaUniversal').attr('action', action.replace(':id', id));
                    
                    // Cambiar textos para Legalización
                    $('#firma-modal-title').html('<i class="fas fa-pen-nib me-2"></i>Autorización de Legalización');
                    $('#firma-body-text').html('Al firmar esta legalización, certifica la revisión técnica de las actividades reportadas por <strong id="firma-nombre-usuario">' + nombre + '</strong>.');
                    $('#firma-banner-text').html('Se utilizará su <strong>Firma Digital</strong> registrada para autorizar esta legalización.');

                    $('#modalFirmaUniversal').modal('show');
                });

                // Abrir Modal de Devolución de Legalización
                $('.btn-open-devolver-leg').on('click', function () {
                    const id = $(this).data('id');
                    const nombre = $(this).data('nombre');
                    const role = $(this).data('role');

                    let action = role === 'supervisor'
                        ? "{{ route('supervisor_contrato.devolver_legalizacion', ':id') }}"
                        : "{{ route('ordenador_gasto.devolver_legalizacion', ':id') }}";

                    $('#formDevolverUniversal').attr('action', action.replace(':id', id));
                    
                    // Cambiar textos para Legalización
                    $('#devolver-modal-title').html('<i class="fas fa-undo me-2"></i>Devolver Legalización');
                    $('#devolver-body-text').html('Está a punto de devolver la legalización de <strong id="devolver-nombre-usuario">' + nombre + '</strong>. Indique el motivo para que el usuario pueda ajustarla.');

                    $('#modalDevolverUniversal').modal('show');
                });

                // Acciones de Legalización (Rol Legalización)
                $(document).on('click', '.btn-aprobar-leg-dash', function (e) {
                    e.preventDefault();
                    const form = $(this).closest('form');
                    Swal.fire({
                        title: '¿Aprobar legalización?',
                        text: 'Una vez aprobada, se enviará al ordenador del gasto.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#39a900',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, aprobar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });

                $(document).on('click', '.btn-devolver-leg-dash', function () {
                    const id = $(this).data('id');
                    const nombre = $(this).data('nombre');
                    Swal.fire({
                        title: 'Devolver Legalización',
                        html: `Está a punto de devolver la legalización de <strong>${nombre}</strong>.<br><br>`,
                        icon: 'question',
                        input: 'textarea',
                        inputLabel: 'Motivo de la devolución',
                        inputPlaceholder: 'Describa los ajustes o correcciones necesarias...',
                        inputAttributes: { required: true, maxlength: 1000 },
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, devolver',
                        cancelButtonText: 'Cancelar',
                        preConfirm: (obs) => {
                            if (!obs) {
                                Swal.showValidationMessage('Debe indicar el motivo de la devolución');
                            }
                            return obs;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const action = "{{ route('legalizacion.procesar', ':id') }}".replace(':id', id);
                            const form = $('<form>', {
                                method: 'POST',
                                action: action
                            }).append(
                                $('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }),
                                $('<input>', { type: 'hidden', name: 'accion', value: 'devolver' }),
                                $('<input>', { type: 'hidden', name: 'observaciones', value: result.value })
                            );
                            form.appendTo('body').submit();
                        }
                    });
                });

                // Confirmación de envío de legalización
                $(document).on('click', '.btn-enviar-leg-confirm', function (e) {
                    e.preventDefault();
                    const form = $(this).closest('.form-enviar-legalizacion');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Deseas enviar la legalización? Una vez enviada no podrás modificar nada.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#39a900',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Sí, enviar legalización',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'rounded-pill px-4',
                            cancelButton: 'rounded-pill px-4'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });



                // Validación de firma para supervisores
                $('.form-autorizar-agenda').on('submit', function (e) {
                    @php
                        $user = auth()->user();
                        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
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

            // Lógica de Viáticos para el Dashboard
            @if(auth()->user()->role == 'viaticos')
                // 1. Selección masiva en el dashboard
                const selectAllDashboard = document.getElementById('select-all-dashboard');
                const checkboxesDashboard = document.querySelectorAll('.agenda-checkbox-dashboard');
                const btnExportBulkDashboard = document.getElementById('btn-export-bulk-dashboard');
                const selectedCountDashboard = document.getElementById('selected-count-dashboard');

                function updateBulkButtonDashboard() {
                    if (!btnExportBulkDashboard || !selectedCountDashboard) return;
                    const checkedCount = document.querySelectorAll('.agenda-checkbox-dashboard:checked').length;
                    selectedCountDashboard.textContent = checkedCount;
                    if (checkedCount > 0) {
                        btnExportBulkDashboard.classList.remove('d-none');
                    } else {
                        btnExportBulkDashboard.classList.add('d-none');
                    }
                }

                if (selectAllDashboard) {
                    selectAllDashboard.addEventListener('change', function () {
                        checkboxesDashboard.forEach(cb => {
                            cb.checked = selectAllDashboard.checked;
                        });
                        updateBulkButtonDashboard();
                    });
                }

                checkboxesDashboard.forEach(cb => {
                    cb.addEventListener('change', function () {
                        updateBulkButtonDashboard();
                        if (selectAllDashboard) {
                            if (!this.checked) {
                                selectAllDashboard.checked = false;
                            } else if (document.querySelectorAll('.agenda-checkbox-dashboard:checked').length === checkboxesDashboard.length) {
                                selectAllDashboard.checked = true;
                            }
                        }
                    });
                });

                if (btnExportBulkDashboard) {
                    btnExportBulkDashboard.addEventListener('click', function () {
                        const selectedIds = Array.from(document.querySelectorAll('.agenda-checkbox-dashboard:checked')).map(cb => cb.value);
                        if (selectedIds.length === 0) return;

                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('viaticos.exportBulk') }}";

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = "{{ csrf_token() }}";
                        form.appendChild(csrf);

                        selectedIds.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });

                        document.body.appendChild(form);
                        form.submit();
                    });
                }

                // 2. Exportar por Supervisor (Dashboard)
                const selectSupervisorDashboard = document.getElementById('select-supervisor-modal-dashboard');
                const agendasContainerDashboard = document.getElementById('agendas-supervisor-container-dashboard');
                const agendasListDashboard = document.getElementById('agendas-supervisor-list-dashboard');
                const noAgendasMsgDashboard = document.getElementById('no-agendas-msg-dashboard');
                const agendasCountBadgeDashboard = document.getElementById('agendas-count-dashboard');
                const formExportSupervisorDashboard = document.getElementById('form-export-supervisor-dashboard');
                const hiddenIdsContainerDashboard = document.getElementById('hidden-ids-container-dashboard');

                if (selectSupervisorDashboard) {
                    selectSupervisorDashboard.addEventListener('change', function () {
                        const supervisorId = this.value;
                        if (!supervisorId) {
                            agendasContainerDashboard.classList.add('d-none');
                            noAgendasMsgDashboard.classList.add('d-none');
                            formExportSupervisorDashboard.classList.add('d-none');
                            return;
                        }

                        agendasListDashboard.innerHTML = '<tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Cargando...</td></tr>';
                        agendasContainerDashboard.classList.remove('d-none');
                        noAgendasMsgDashboard.classList.add('d-none');
                        formExportSupervisorDashboard.classList.add('d-none');

                        fetch(`{{ route('viaticos.agendasPorSupervisor') }}?supervisor_id=${supervisorId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.agendas.length > 0) {
                                    agendasListDashboard.innerHTML = '';
                                    hiddenIdsContainerDashboard.innerHTML = '';
                                    agendasCountBadgeDashboard.textContent = `${data.agendas.length} agendas`;

                                    data.agendas.forEach(agenda => {
                                        const row = `
                                                    <tr>
                                                        <td class="fw-bold">${agenda.contratista}</td>
                                                        <td>${agenda.destino}</td>
                                                        <td>${agenda.fecha_inicio}</td>
                                                        <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success small">${agenda.estado}</span></td>
                                                    </tr>
                                                `;
                                        agendasListDashboard.insertAdjacentHTML('beforeend', row);

                                        const input = `<input type="hidden" name="ids[]" value="${agenda.id}">`;
                                        hiddenIdsContainerDashboard.insertAdjacentHTML('beforeend', input);
                                    });
                                    formExportSupervisorDashboard.classList.remove('d-none');
                                } else {
                                    agendasContainerDashboard.classList.add('d-none');
                                    noAgendasMsgDashboard.classList.remove('d-none');
                                }
                            });
                    });
                }
            @endif
        </script>
    @endpush
@endsection