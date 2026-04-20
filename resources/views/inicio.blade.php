@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid ">
        {{-- Banner Institucional --}}
        <div class="card border shadow-sm mb-4">
            <div class="card-body d-flex align-items-center justify-content-center py-3 mt-3">
                <img src="{{ asset('images/sena/logo250.png') }}" alt="SENA" style="height: 60px; margin-right: 20px;">
                <h1 class="h2 mb-0 fw-bold text-dark" style="letter-spacing: -1px;">Agenda CTGI</h1>
            </div>
        </div>

        {{-- Banner de Bienvenida --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4"
            style="background: linear-gradient(135deg, #39a900 0%, #2d8500 100%);">
            <div class="card-body py-5 px-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 fw-bold mb-3">¡Bienvenid@, {{ auth()->user()->name }}!</h1>
                        <p class="lead mb-0 text-white">Rol actual: <strong>{{ ucfirst(auth()->user()->role) }}</strong></p>
                        @if(auth()->user()->role === 'administrador')
                            <span class="badge bg-white text-success mt-2 fw-bold">Panel Personal: Solo se muestran sus
                                agendas</span>
                        @endif
                    </div>
                    <div class="col-md-4 text-center d-none d-md-block">
                        <i class="fas fa-user-shield fa-5x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERTA DE FIRMA FALTANTE --}}
        @php
            $user = auth()->user();
            $hasFirma = !empty(trim($user->firma));

            // Si es supervisor u ordenador, verificamos también su ficha de funcionario
            if ($hasFirma && in_array($user->role, ['supervisor_contrato', 'ordenador_gasto'])) {
                $funcionario = \App\Models\Funcionario::where('numero_documento', $user->numero_documento)->first();
                if (!$funcionario || empty(trim($funcionario->firma))) {
                    $hasFirma = false;
                }
            }
        @endphp

        @if(!$hasFirma && in_array($user->role, ['contratista', 'supervisor_contrato', 'ordenador_gasto']))
            <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 p-4 animate__animated animate__shakeX"
                style="border-left: 6px solid #ffc107 !important;">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 d-none d-md-block">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="alert-heading fw-bold text-dark mb-1">¡Atención! Falta tu firma digital</h4>
                        <p class="mb-0 fs-5 text-dark">
                            @if($user->role == 'contratista')
                                Detectamos que aún no has cargado tu firma en el sistema. Es <strong>obligatorio</strong> tener una
                                firma registrada para poder enviar agendas.
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

        {{-- LAS 3 TARJETAS DE CONTROL --}}
        <div class="row g-4 mb-5">
            {{-- Tarjeta Pendientes --}}
            <div class="col-md-4">
                <a href="{{ route('inicio', ['ver' => 'pendientes']) }}" class="text-decoration-none">
                    <div
                        class="card border-0 shadow-sm rounded-4 p-4 border-start border-primary border-5 {{ $filtro == 'pendientes' ? 'bg-primary bg-opacity-10' : 'bg-white' }}">
                        <div class="d-flex justify-content-between text-primary">
                            <i class="fas fa-clock fa-2x"></i>
                            <span class="h3 fw-bold mb-0">{{ $stats['pendientes'] }}</span>
                        </div>
                        <h5 class="fw-bold text-dark mt-2 mb-0">
                            @if(auth()->user()->role == 'viaticos')
                                Pendientes por revisar
                            @elseif(auth()->user()->role == 'ordenador_gasto')
                                Por autorizar
                            @else
                                Pendientes
                            @endif
                        </h5>
                        <small class="text-muted">
                            @if(auth()->user()->role == 'viaticos')
                                Agendas autorizadas por Supervisores
                            @elseif(auth()->user()->role == 'ordenador_gasto')
                                Esperando su firma digital
                            @else
                                Por revisión inicial
                            @endif
                        </small>
                    </div>
                </a>
            </div>

            {{-- Tarjeta Enviadas --}}
            <div class="col-md-4">
                <a href="{{ route('inicio', ['ver' => 'enviadas']) }}" class="text-decoration-none">
                    <div
                        class="card border-0 shadow-sm rounded-4 p-4 border-start border-success border-5 {{ $filtro == 'enviadas' ? 'bg-success bg-opacity-10' : 'bg-white' }}">
                        <div class="d-flex justify-content-between text-success">
                            <i class="fas fa-paper-plane fa-2x"></i>
                            <span class="h3 fw-bold mb-0">{{ $stats['enviadas'] }}</span>
                        </div>
                        <h5 class="fw-bold text-dark mt-2 mb-0">
                            @if(auth()->user()->role == 'viaticos')
                                Aprobadas
                            @elseif(auth()->user()->role == 'ordenador_gasto')
                                Aprobadas
                            @else
                                Enviadas
                            @endif
                        </h5>
                        <small class="text-muted">
                            @if(auth()->user()->role == 'viaticos')
                                Agendas aprobadas
                            @elseif(auth()->user()->role == 'ordenador_gasto')
                                Proceso finalizado
                            @else
                                En proceso de firmas
                            @endif
                        </small>
                    </div>
                </a>
            </div>

            {{-- Tarjeta Devueltas --}}
            <div class="col-md-4">
                <a href="{{ route('inicio', ['ver' => 'devueltas']) }}" class="text-decoration-none">
                    <div
                        class="card border-0 shadow-sm rounded-4 p-4 border-start border-danger border-5 {{ $filtro == 'devueltas' ? 'bg-danger bg-opacity-10' : 'bg-white' }}">
                        <div class="d-flex justify-content-between text-danger">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                            <span class="h3 fw-bold mb-0">{{ $stats['devueltas'] }}</span>
                        </div>
                        <h5 class="fw-bold text-dark mt-2 mb-0">Devueltas</h5>
                        <small class="text-muted">Con observaciones</small>
                    </div>
                </a>
            </div>
        </div>

        {{-- LISTADO DINÁMICO --}}
        @if($filtro)
            <div class="card border-0 shadow-sm rounded-4 animate__animated animate__fadeIn">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center border-bottom">
                    <h4 class="fw-bold mb-0 text-dark">Viendo: <span class="text-capitalize">{{ $filtro }}</span></h4>
                    <a href="{{ route('inicio') }}" class="btn btn-light btn-sm rounded-pill px-3 border text-muted">Cerrar</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">CONTRATISTA</th>
                                    @if($filtro == 'devueltas')
                                    <th>MOTIVO DE ERROR</th> @endif
                                    <th class="text-center">PDF</th>
                                    <th class="text-center">GESTIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agendas as $agenda)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark text-uppercase fs-6">{{ $agenda->user->name ?? 'N/A' }}
                                            </div>
                                            <div class="small text-muted">ID Agenda: #{{ $agenda->id }}</div>
                                        </td>

                                        @if($filtro == 'devueltas')
                                            <td>
                                                <div class="text-danger small fw-bold">
                                                    <i class="fas fa-comment-dots me-1"></i>
                                                    {{ Str::limit($agenda->observaciones_finanzas, 80) }}
                                                </div>
                                            </td>
                                        @endif

                                        <td class="text-center">
                                            <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                                class="btn btn-link text-danger p-0">
                                                <i class="fas fa-file-pdf fa-2x"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <button
                                                class="btn {{ (auth()->user()->role === 'supervisor_contrato' && $agenda->estado && $agenda->estado->nombre == 'ENVIADA' && !$agenda->observaciones_finanzas) ? 'btn-dark' : 'btn-secondary' }} btn-sm rounded-pill px-4 fw-bold shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#modalGestion{{ $agenda->id }}">
                                                Ver Info
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center p-5 text-muted">No hay registros para mostrar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- MODALES DINÁMICOS --}}
    @foreach($agendas as $agenda)
        <div class="modal fade" id="modalGestion{{ $agenda->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 bg-light p-4">
                        <h5 class="fw-bold mb-0">Detalles de la Agenda #{{ $agenda->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>


                    <div class="modal-body p-4">
                        <div class="mb-4 p-3 bg-light border rounded-3 d-flex align-items-center justify-content-between">
                            <div>
                                <i class="fas fa-file-pdf fa-2x text-danger me-2"></i>
                                <span class="fw-bold small text-dark">DOCUMENTO PDF</span>
                            </div>
                            <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                class="btn btn-sm btn-danger rounded-pill px-4 shadow-sm fw-bold">
                                ABRIR ARCHIVO
                            </a>
                        </div>

                        <div class="text-center mb-4">
                            <label class="small text-muted fw-bold d-block">CONTRATISTA REGISTRADO</label>
                            <p class="fs-4 fw-bold text-dark text-uppercase mb-0">{{ $agenda->user->name ?? 'N/A' }}</p>
                        </div>

                        {{-- Gestión de Observaciones (Solo lectura para Supervisor en Dashboard) --}}
                        @if($agenda->observaciones_finanzas)
                            <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3">
                                <label class="small fw-bold text-warning-emphasis">NOTA DE REVISIÓN:</label>
                                <p class="mb-0 small text-dark text-italic">"{{ $agenda->observaciones_finanzas }}"</p>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <style>
        .card {
            transition: all 0.2s ease;
        }

        .text-decoration-none:hover .card {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection