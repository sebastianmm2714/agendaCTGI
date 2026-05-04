<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Agenda CTGI</title>
    <link rel="icon" href="{{ asset('images/sena/logo-sena-verde.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <style>
        :root {
            --brand: #39a900; 
            --ink: #1f3727ff;
            --border: #e5e7eb;
            --hover: #f1f5f9;
            --sidebar-width: 22rem; 
        }

        html { font-size: 18px; } 

        body { 
            background: #f8fafc; 
            margin: 0; 
            font-family: 'Figtree', sans-serif;
            color: var(--ink);
        }
        
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #fff;
            color: var(--ink);
            border-right: 2px solid var(--border);
            z-index: 1000;
        }
        
        .sidebar a {
            color: var(--ink);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .sidebar a:hover {
            background: var(--hover);
            color: var(--brand);
            transform: translateX(5px);
        }

        .sidebar a.active-link {
            background: var(--brand) !important;
            color: white !important;
            box-shadow: 0 10px 15px -3px rgba(57, 169, 0, 0.3);
        }

        .nav-icon {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
        }

        .content {
            margin-left: var(--sidebar-width);
            padding: 3rem;
            width: calc(100% - var(--sidebar-width));
        }

        .brand-logo {
            max-width: 290px;
            height: auto;
            transition: transform 0.3s ease;
        }
        
        .brand-logo:hover {
            transform: scale(1.05);
        }

        .btn-logout {
            font-size: 1.1rem;
            padding: 0.8rem;
            border-width: 2px;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <aside class="sidebar d-flex flex-column position-fixed top-0 start-0 p-4">
        
        <div class="text-center mb-5 mt-2">
            <img src="{{ asset('images/sena/logo250.png') }}" class="brand-logo mb-3" alt="SENA">
            <hr class="mx-4 opacity-10">
        </div>

        <nav class="flex-grow-1">
            {{-- ENLACE: INICIO (Oculto para Admin) --}}
            @if(auth()->user()->role != 'administrador')
            <a href="{{ route('inicio') }}" 
            class="{{ request()->routeIs('inicio') ? 'active-link' : '' }}">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                    </svg>
                    <span>Inicio</span>
                </div>
            </a>
            @endif

            {{-- ENLACE: FORMULARIO (Solo Contratista, Administrador tiene su propio panel) --}}
            @if(auth()->user()->role == 'contratista')
            <a href="{{ route('formulario') }}" 
            class="{{ request()->routeIs('formulario') ? 'active-link' : '' }} mt-2">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span>Formulario</span>
                </div>
            </a>
            @endif

            {{-- ENLACE: POR AUTORIZAR (Supervisor Contrato o Ordenador Gasto) - SIN ALERTAS --}}
            @if(auth()->user()->role == 'supervisor_contrato' || auth()->user()->role == 'ordenador_gasto')
                @php 
                    $rutaMenu = (auth()->user()->role == 'supervisor_contrato') ? route('supervisor_contrato.index') : route('ordenador_gasto.index');
                @endphp
                
                <a href="{{ $rutaMenu }}" 
                class="{{ request()->routeIs('supervisor_contrato.index') || request()->routeIs('ordenador_gasto.index') ? 'active-link' : '' }} mt-2">
                    <div class="d-flex align-items-center gap-3">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>Por Autorizar</span>
                    </div>
                </a>
            @endif

            {{-- ENLACE UNIFICADO: REPORTES / GESTIÓN VIÁTICOS (Oculto para Admin) --}}
            @if(auth()->user()->role != 'administrador')
                @php
                    $enlace_reportes = route('reportes');
                    if (auth()->user()->role == 'contratista') $enlace_reportes = route('reportar-dia');
                    if (auth()->user()->role == 'viaticos') $enlace_reportes = route('viaticos.index');
                @endphp
                <a href="{{ $enlace_reportes }}" 
                class="{{ request()->routeIs('reportes') || request()->routeIs('reportes.show') || request()->routeIs('reportar-dia') || request()->routeIs('reportar-dia.show') || request()->routeIs('viaticos.index') || request()->routeIs('viaticos.gestionar') ? 'active-link' : '' }} mt-2">
                    <div class="d-flex align-items-center gap-3">
                        @if(auth()->user()->role == 'viaticos')
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Gestión Viáticos</span>
                        @else
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M4 20h16M7 16v-6M12 16v-9M17 16v-3"/>
                            </svg>
                            <span>Reportes</span>
                        @endif
                    </div>
                </a>
            @endif

            {{-- ENLACE: CONTRATISTAS (Solo para Viáticos) --}}
            @if(auth()->user()->role == 'viaticos')
            <a href="{{ route('viaticos.personal.index') }}" 
            class="{{ request()->routeIs('viaticos.personal.index') ? 'active-link' : '' }} mt-2">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-2.533-3.076c-1.03-.353-2.14-.545-3.213-.545a10.115 10.115 0 0 0-4.052.876 1.5 1.5 0 0 0-.903 1.341V18c0 .265.105.52.293.707.188.188.442.293.707.293h2.362Z" />
                        <path d="M12.14 16.14V15a3 3 0 0 0-3-3h-1.5a3 3 0 0 0-3 3v1.14M11.07 13.07a3 3 0 1 1-4.24-4.24 3 3 0 0 1 4.24 4.24ZM18.75 12a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" />
                    </svg>
                    <span>Contratistas</span>
                </div>
            </a>
            @endif

            {{-- ENLACE: MI FIRMA (Oculto para Admin y Viáticos, Admin tiene su propio panel) --}}
            @if(auth()->user()->role != 'viaticos' && auth()->user()->role != 'administrador')
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalFirmaUsuario" class="mt-2">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    <span>Mi Firma</span>
                </div>
            </a>
            @endif

            {{-- SECCIÓN: ADMINISTRACIÓN (Solo Administrador) --}}
            @if(auth()->user()->role == 'administrador')
            
            
            <a href="{{ route('admin.dashboard') }}" 
            class="{{ request()->routeIs('admin.dashboard') ? 'active-link' : '' }}">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                        <path d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                    </svg>
                    <span>Panel Admin</span>
                </div>
            </a>

            <a href="{{ route('admin.catalogos') }}" 
            class="{{ request()->routeIs('admin.catalogos*') ? 'active-link' : '' }} mt-2">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75c.621 0 1.125.504 1.125 1.125v17.25c0 .621-.504 1.125-1.125 1.125H5.625c-.621 0-1.125-.504-1.125-1.125V5.625c0-.621.504-1.125 1.125-1.125Z" />
                    </svg>
                    <span>Estados/Categoría</span>
                </div>
            </a>

            <a href="{{ route('admin.obligaciones.index') }}" 
            class="{{ request()->routeIs('admin.obligaciones*') ? 'active-link' : '' }} mt-2">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span>Obligaciones</span>
                </div>
            </a>

            <a href="{{ route('admin.usuarios.index') }}" 
            class="{{ request()->routeIs('admin.usuarios*') ? 'active-link' : '' }} mt-2">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-2.533-3.076c-1.03-.353-2.14-.545-3.213-.545a10.115 10.115 0 0 0-4.052.876 1.5 1.5 0 0 0-.903 1.341V18c0 .265.105.52.293.707.188.188.442.293.707.293h2.362Z" />
                        <path d="M12.14 16.14V15a3 3 0 0 0-3-3h-1.5a3 3 0 0 0-3 3v1.14M11.07 13.07a3 3 0 1 1-4.24-4.24 3 3 0 0 1 4.24 4.24ZM18.75 12a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" />
                    </svg>
                    <span>Contratistas</span>
                </div>
            </a>

            <a href="{{ route('admin.lideres_de_proceso.index') }}" 
            class="{{ request()->routeIs('admin.lideres_de_proceso*') ? 'active-link' : '' }} mt-2">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0a5.995 5.995 0 0 0-4.058-2.522M6 18.72a6 6 0 0 1 1.06-3.197m0 0a5.996 5.996 0 0 1 4.058-2.522m0 0a3 3 0 1 0-4.682-2.72M9.75 3a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM14.25 3a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                    </svg>
                    <span>Líderes de Proceso</span>
                </div>
            </a>

            <a href="{{ route('admin.carga-masiva.index') }}" 
            class="{{ request()->routeIs('admin.carga-masiva*') ? 'active-link' : '' }} mt-2">
                <div class="d-flex align-items-center gap-3">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                    <span>Carga Masiva</span>
                </div>
            </a>
            @endif
        </nav>

        <div class="mt-auto pt-4 border-top">
            <div class="d-flex align-items-center gap-3 mb-4 px-2">
                <div class="bg-light rounded-circle p-2 text-center" style="width: 45px; height: 45px;">
                    <i class="fas fa-user text-muted"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="mb-0 fw-bold text-truncate small">{{ auth()->user()->name }}</p>
                    <p class="mb-0 text-muted small text-uppercase" style="font-size: 0.65rem;">{{ auth()->user()->role }}</p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 fw-bold btn-logout rounded-3">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="content">
        @yield('content')
    </main>
</div>

{{-- MODAL GESTIÓN DE FIRMA (Oculto para Viáticos) --}}
@if(auth()->user()->role != 'viaticos')
<div class="modal fade" id="modalFirmaUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-light rounded-top-4 py-4 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-pen-nib text-success me-2"></i>Mi Firma Digital</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="firma-preview-container bg-white rounded-4 border p-4 d-flex align-items-center justify-content-center position-relative shadow-sm" style="min-width: 250px; min-height: 160px;">
                        @php
                            $user = auth()->user();
                            $firmaPath = $user->firma;
                            $hasFirmaActual = !empty(trim($firmaPath));
                            
                            // Si es supervisor u ordenador, la firma "real" es la de la tabla lideres_de_proceso
                            if (in_array($user->role, ['supervisor_contrato', 'ordenador_gasto'])) {
                                $funcionario = \App\Models\LiderDeProceso::where('numero_documento', trim($user->numero_documento))->first();
                                if (!$funcionario || empty(trim($funcionario->firma))) {
                                    $hasFirmaActual = false;
                                } else {
                                    $firmaPath = $funcionario->firma;
                                }
                            }
                        @endphp

                        @if($hasFirmaActual)
                            @php $fBase64 = $getFirmaBase64($firmaPath); @endphp
                            
                            @if($fBase64)
                                <div class="position-absolute top-0 start-50 translate-middle-x" style="margin-top: -12px;">
                                    <span class="badge bg-success shadow-sm px-3 py-2 rounded-pill">Firma Actual</span>
                                </div>
                                <img src="{{ $fBase64 }}" id="previewFirmaActual" class="img-fluid rounded-3" style="max-height: 120px; max-width: 100%; object-fit: contain;" alt="Firma Actual">
                            @else
                                <div class="py-4 text-muted fst-italic" id="noFirmaMsg">Archivo de firma no encontrado.</div>
                            @endif
                        @else
                            <div class="py-4 text-muted fst-italic" id="noFirmaMsg">No has registrado una firma aún.</div>
                        @endif
                        <div id="loadingFirma" class="position-absolute top-50 start-50 translate-middle d-none">
                            <div class="spinner-border text-success" role="status"></div>
                        </div>
                    </div>
                </div>

                <form id="formUpdateFirma" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Subir Nueva Imagen de Firma</label>
                        <div class="input-group">
                            <input type="file" name="firma" id="inputFirma" class="form-control rounded-start-3" accept="image/*" required>
                        </div>
                        <div class="form-text mt-2 small">Sube una imagen clara (PNG con transparencia recomendado).</div>
                    </div>
                    
                    {{-- Contenedor para vista previa de la nueva imagen a subir (Oculto por defecto) --}}
                    <div class="text-center mb-3 d-none" id="previewContainerNuevo">
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-sm mt-3">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Guardar la firma original para poder resetearla si cancelan
        const originalSignature = $('#previewFirmaActual').attr('src');

        // Vista previa en tiempo real
        $('#inputFirma').change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    if ($('#previewFirmaActual').length) {
                        $('#previewFirmaActual').attr('src', event.target.result);
                        $('.badge').addClass('d-none'); // Ocultar el badge de "Firma Actual" al previsualizar nueva
                    } else {
                        $('.firma-preview-container').html(`<img src="${event.target.result}" id="previewFirmaActual" class="img-fluid rounded-3 shadow-sm" style="max-height: 150px;" alt="Firma Actual">`);
                    }
                }
                reader.readAsDataURL(file);
            }
        });

        // Resetear al cerrar el modal si no se guardó
        $('#modalFirmaUsuario').on('hidden.bs.modal', function () {
            $('#formUpdateFirma')[0].reset();
            if (originalSignature) {
                $('#previewFirmaActual').attr('src', originalSignature);
                $('.badge').removeClass('d-none');
            } else {
                $('.firma-preview-container').html('<div class="py-4 text-muted fst-italic" id="noFirmaMsg">No has registrado una firma aún.</div>');
            }
        });

        $('#formUpdateFirma').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $('#loadingFirma').removeClass('d-none');
            
            $.ajax({
                url: "{{ route('user.signature.update') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#loadingFirma').addClass('d-none');
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'La firma se ha guardado correctamente.',
                            timer: 2000,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.reload();
                            }
                        });
                    }
                },
                error: function(xhr) {
                    $('#loadingFirma').addClass('d-none');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Hubo un problema al subir la firma.'
                    });
                }
            });
        });

        // --- SCRIPT GLOBAL PARA CONFIRMACIÓN DE ELIMINACIÓN CON SWEETALERT ---
        $(document).on('click', '.btn-confirm-delete', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const title = $(this).data('title') || '¿Estás seguro?';
            const text = $(this).data('text') || 'Esta acción no se puede deshacer.';

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

{{-- ALERTAS GLOBALES DE SESIÓN Y VALIDACIÓN --}}
@if(session('success') || session('alerta_exitosa'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{!! session('success') ?? session('alerta_exitosa') !!}',
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{!! session('error') !!}'
        });
    });
</script>
@endif

@if(session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: '{!! session('warning') !!}'
        });
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: '¡Atención!',
            html: `
                <ul style="text-align: left; margin-bottom: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `
        });
    });
</script>
@endif

@stack('scripts')

</body>
</html>