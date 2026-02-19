@extends('layouts.dashboard')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<div class="container-fluid mt-3">
    {{-- Header con Logo --}}
    <div class="card border shadow-sm mb-4">
        <div class="card-body d-flex align-items-center justify-content-center py-3 mt-3">
            <img src="{{ asset('images/sena/logo250.png') }}" alt="SENA" style="height: 60px; margin-right: 20px;">
            <h1 class="h2 mb-0 fw-bold text-dark" style="letter-spacing: -1px;">Agenda CTGI</h1>
        </div>
    </div>

    {{-- Banner de Bienvenida --}}
    <div class="card border-0 shadow bg-sena text-white mb-5 ">
        <div class="card-body py-5 px-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-3 text-white">
                        ¡Bienvenid@, {{ auth()->user()->name }}!
                    </h1>
                    <p class="lead mb-4 text-white">
                        Usted ha ingresado con el rol de: <strong>{{ ucfirst(auth()->user()->rol) }}</strong>.
                    </p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <i class="fas fa-user-shield fa-5x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <section class="mb-4">
        <h4 class="fw-bold mb-3 ms-2 text-dark">
            <i class="fas fa-rocket me-2 text-primary mt-3"></i>Accesos Rápidos
        </h4>
        <div class="row g-3 mt-3">
            
            {{-- BOTÓN INICIO (Todos) --}}
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('inicio') }}" class="text-decoration-none">
                    <div class="card h-100 border border-primary shadow-sm card-hover">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary-light rounded-circle p-4 d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-home fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title fw-bold text-dark mt-2">Inicio</h5>
                            <p class="card-text text-muted small">Panel principal</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- BOTÓN FORMULARIO (Solo Contratista) --}}
            @if(auth()->user()->rol == 'user' || auth()->user()->rol == 'contratista')
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('formulario') }}" class="text-decoration-none">
                    <div class="card h-100 border border-success shadow-sm card-hover">
                        <div class="card-body text-center p-4">
                            <div class="bg-success-light rounded-circle p-4 d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-file-signature fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title fw-bold text-dark mt-2">Formulario</h5>
                            <p class="card-text text-muted small">Registrar nueva agenda</p>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            {{-- BOTÓN POR AUTORIZAR (Dinámico para Coordinador o Subdirector) --}}
            @if(auth()->user()->rol == 'supervisor_contrato' || auth()->user()->rol == 'ordenador_gasto' || auth()->user()->rol == 'viaticos')
            <div class="col-md-6 col-lg-3">
                @php 
                    $rutaAutorizar = match(auth()->user()->rol) {
                        'supervisor_contrato' => route('coordinador.index'),
                        'viaticos' => route('viaticos.index'),
                        'ordenador_gasto' => route('subdirector.index'),
                        default => '#'
                    };
                    $total = match(auth()->user()->rol) {
                        'supervisor_contrato' => $pendientesCoord,
                        'viaticos' => $pendientesViaticos,
                        'ordenador_gasto' => $pendientesSub,
                        default => 0
                    };
                @endphp
                
                <a href="{{ $rutaAutorizar }}" class="text-decoration-none position-relative">
                    <div class="card h-100 border border-warning shadow-sm card-hover">
                        <div class="card-body text-center p-4">
                            <div class="bg-warning-light rounded-circle p-4 d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-signature fa-3x text-warning"></i>
                                
                                @if($total > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="margin-top: 20px; margin-left: -40px;">
                                        {{ $total }}
                                    </span>
                                @endif
                            </div>
                            <h5 class="card-title fw-bold text-dark mt-2">Por Autorizar</h5>
                            <p class="card-text text-muted small">Revisar y firmar agendas</p>
                            <h4 class="fw-bold mb-0" style="font-size: 0.9rem;">{{ auth()->user()->rol == 'supervisor_contrato' ? 'Supervisor Contrato' : (auth()->user()->rol == 'ordenador_gasto' ? 'Ordenador Gasto' : 'Viáticos') }}</h4>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            {{-- BOTÓN REPORTES (Todos) --}}
            <div class="col-md-6 col-lg-3">
                @php
                    $rutaBtnReportes = (auth()->user()->rol == 'user' || auth()->user()->rol == 'contratista') ? route('reportar-dia') : route('reportes');
                @endphp
                <a href="{{ $rutaBtnReportes }}" class="text-decoration-none">
                    <div class="card h-100 border border-info shadow-sm card-hover">
                        <div class="card-body text-center p-4">
                            <div class="bg-info-light rounded-circle p-4 d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-chart-bar fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title fw-bold text-dark mt-2">Reportes</h5>
                            <p class="card-text text-muted small">Estadísticas y descargas</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </section>
</div>

<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .bg-success-light {
        background-color: rgba(57, 169, 0, 0.1);
    }
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-info-light {
        background-color: rgba(13, 202, 240, 0.1);
    }
    .bg-sena {
        background-color: #39a900 !important;
    }
</style>
@endsection
