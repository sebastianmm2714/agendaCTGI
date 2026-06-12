@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid py-4">
        {{-- Banner de Título --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4"
            style="background: linear-gradient(135deg, #39a900 0%, #2d8500 100%);">
            <div class="card-body p-4 text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-signature fa-3x me-4 opacity-75"></i>
                    <div>
                        <h1 class="display-6 fw-bold mb-0">Solicitud de Comisión de Servicios</h1>
                        <p class="lead mb-0 opacity-75">Formato Institucional GTH-F-064 V05</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <form action="{{ route('formulario.store') }}" method="POST" id="formComision" novalidate>
                        @csrf
                        @if(isset($agenda))
                            <input type="hidden" name="agenda_id" value="{{ $agenda->id }}">
                        @endif
                        <div class="card-body p-5 bg-white">
                            <div class="row g-4">
                                {{-- SECCIÓN 1: DATOS GENERALES --}}
                                <div class="col-12 mb-2">
                                    <h5 class="fw-bold text-success border-bottom pb-2">
                                        <i class="fas fa-user-tie me-2"></i>Información del Comisionado
                                    </h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Fecha de
                                        Elaboración</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i
                                                class="fas fa-calendar-day text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-0 fw-bold"
                                            value="{{ date('d/m/Y') }}" readonly>
                                        <input type="hidden" name="fecha_elaboracion" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Nombre
                                        Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i
                                                class="fas fa-user text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-0 fw-bold text-uppercase"
                                            value="{{ auth()->user()->name }}" readonly>
                                    </div>
                                </div>

                                {{-- SECCIÓN 2: DETALLES DE LA COMISIÓN --}}
                                <div class="col-12 mt-5 mb-2">
                                    <h5 class="fw-bold text-success border-bottom pb-2">
                                        <i class="fas fa-map-marked-alt me-2"></i>Detalles del Desplazamiento
                                    </h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Fecha Inicio</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-white border-0"><i
                                                class="fas fa-calendar-alt text-success"></i></span>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                                            class="form-control border-0 py-3" 
                                            value="{{ old('fecha_inicio', isset($agenda) ? $agenda->fecha_inicio->format('Y-m-d') : '') }}"
                                            min="{{ date('Y-m-d') }}"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Fecha Fin</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-white border-0"><i
                                                class="fas fa-calendar-check text-success"></i></span>
                                        <input type="date" name="fecha_fin" id="fecha_fin"
                                            class="form-control border-0 py-3" 
                                            value="{{ old('fecha_fin', isset($agenda) ? $agenda->fecha_fin->format('Y-m-d') : '') }}"
                                            required>
                                    </div>
                                </div>

                                {{-- DESTINO API --}}
                                <div class="col-12" id="destinos-container">
                                    <label class="form-label fw-bold text-dark small text-uppercase mt-2 mb-3">
                                        <i class="fas fa-location-dot text-danger me-2"></i>Destinos de la Comisión
                                    </label>
                                    
                                    @php
                                        $destinos = old('destinos', (isset($agenda) && $agenda->destinos) ? $agenda->destinos : [['departamento_id' => '', 'municipio_id' => '', 'vereda' => '', 'nombre' => '']]);
                                    @endphp

                                    @foreach($destinos as $index => $dest)
                                    <div class="destino-item mb-4 animate__animated animate__fadeIn" data-index="{{ $index }}">
                                        <div class="card border-0 bg-light rounded-4 shadow-sm">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-bold mb-0 text-success"><i class="fas fa-map-marker-alt me-2"></i>Destino {{ $index + 1 }}</h6>
                                                    @if($index > 0)
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-destino">
                                                            <i class="fas fa-trash-alt me-1"></i> Eliminar
                                                        </button>
                                                    @endif
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <label class="form-label fw-semibold text-muted small">Municipio o Departamento Destino</label>
                                                        <div class="destino-wrapper position-relative">
                                                            <div class="destino-trigger bg-white border-0 shadow-sm rounded-3 p-3 d-flex align-items-center justify-content-between"
                                                                tabindex="0" style="cursor:pointer; min-height:60px;">
                                                                <span class="destino-trigger-text {{ (!empty($dest['nombre'])) ? '' : 'text-muted fst-italic' }}">
                                                                    {{ $dest['nombre'] ?: 'Seleccione el municipio o departamento...' }}
                                                                </span>
                                                                <i class="fas fa-chevron-down ms-2 flex-shrink-0 text-success"></i>
                                                            </div>
                                                            <div class="destino-dropdown shadow-lg"></div>
                                                            <input type="hidden" name="destinos[{{ $index }}][departamento_id]" class="input-depto" value="{{ $dest['departamento_id'] }}" required>
                                                            <input type="hidden" name="destinos[{{ $index }}][municipio_id]" class="input-muni" value="{{ $dest['municipio_id'] }}">
                                                            <input type="hidden" name="destinos[{{ $index }}][nombre]" class="input-nombre" value="{{ $dest['nombre'] }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                    <button type="button" id="btn-add-destino" class="btn btn-outline-success w-100 rounded-4 py-3 border-dashed fw-bold mt-2">
                                        <i class="fas fa-plus-circle me-2"></i> AGREGAR OTRO MUNICIPIO DE DESTINO
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Dirección General /
                                        Regional</label>
                                    <input type="text" name="regional"
                                        class="form-control bg-white border-0 shadow-sm rounded-3 py-3 text-uppercase"
                                        value="{{ old('regional', isset($agenda) ? ($agenda->regional ?? '') : (auth()->user()->regional ?? '')) }}" 
                                        placeholder="EJ: REGIONAL ANTIOQUIA"
                                        maxlength="150"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Dependencia / Centro /
                                        Sede</label>
                                    <input type="text" name="centro_formacion"
                                        class="form-control bg-white border-0 shadow-sm rounded-3 py-3 text-uppercase"
                                        value="{{ old('centro_formacion', isset($agenda) ? ($agenda->centro ?? '') : (auth()->user()->centro_formacion ?? '')) }}"
                                        placeholder="EJ: CTGI"
                                        maxlength="150" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Objeto de la
                                        Comisión</label>
                                    <textarea name="objetivo_desplazamiento"
                                        class="form-control border-0 shadow-sm rounded-4 p-4 text-uppercase" rows="4"
                                        placeholder="Describa detalladamente el propósito de su comisión..."
                                        maxlength="1000" required style="font-size: 1.1rem;">{{ old('objetivo_desplazamiento', isset($agenda) ? $agenda->objetivo_desplazamiento : '') }}</textarea>
                                </div>


                            </div>
                        </div>

                        <div class="card-footer bg-light border-0 p-4 p-md-5">
                            <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3">
                                @if(isset($agenda))
                                    <a href="{{ session('back_url_reportar_dia', route('inicio')) }}"
                                        class="btn btn-link text-muted fw-bold text-decoration-none order-2 order-md-1">
                                        <i class="fas fa-arrow-left me-2"></i>Regresar al Inicio
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 px-md-5 shadow-lg fw-bold order-1 order-md-2 w-100 w-md-auto py-3 py-md-2">
                                        <i class="fas fa-save me-2"></i>
                                        <span class="d-inline-block">{{ ($agenda->estado && $agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas) ? 'Guardar y Corregir Agenda' : 'Actualizar Agenda' }}</span>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 px-md-5 shadow-lg fw-bold w-100 w-md-auto py-3 py-md-2">
                                        <i class="fas fa-save me-2"></i>
                                        <span class="d-inline-block">Crear y Guardar Agenda</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-dashed {
            border: 2px dashed #cbd5e1;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }
        .border-dashed:hover {
            background-color: #f1f5f9;
            border-color: #39a900;
            color: #39a900;
        }
        .destino-trigger:focus, .destino-trigger.open {
            border: 2px solid #39a900 !important;
            box-shadow: 0 0 0 4px rgba(57, 169, 0, 0.1) !important;
        }
        .destino-dropdown {
            display: none;
            flex-direction: column;
            position: absolute;
            z-index: 1060;
            width: 100%;
            background: white;
            border-radius: 12px;
            max-height: 300px;
            overflow: hidden;
            margin-top: 5px;
        }
        .destino-header {
            padding: 12px 16px;
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .back-btn {
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.2s;
            color: #39a900;
        }
        .back-btn:hover { background: #e2e8f0; }
        .destino-options-list { overflow-y: auto; flex: 1; }
        .destino-option {
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.15s;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .destino-option:hover { background-color: #f0f4f7; color: #39a900; }
        .destino-option.dept-navigation::after {
            content: "\f054";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 0.8rem;
            opacity: 0.5;
        }
        .destino-option.selected-item { background-color: #39a900 !important; color: #fff !important; }

        .invalid-feedback-custom {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
            font-weight: 500;
        }
        
        .is-invalid-custom {
            border: 2px solid #dc3545 !important;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1) !important;
            padding-right: calc(1.5em + 0.75rem) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }

        .is-invalid-custom + .invalid-feedback-custom {
            display: block;
        }

        .is-invalid {
            border: 2px solid #dc3545 !important;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1) !important;
        }

        /* Ajustes de Responsividad para Diferentes Pantallas (Celulares, Tablets, PC) */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 1rem 0.5rem !important;
            }
            .card-body {
                padding: 1.5rem !important;
            }
            .display-6 {
                font-size: 1.5rem;
            }
            .btn-lg {
                font-size: 1rem;
                padding: 0.85rem 1.5rem !important;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.75rem 0.25rem !important;
            }
            .card-body {
                padding: 1.25rem !important;
            }
            .display-6 {
                font-size: 1.25rem;
            }
            .lead {
                font-size: 0.9rem !important;
            }
            .input-group-text {
                padding: 0.6rem 0.75rem;
            }
            .form-control, .form-select {
                padding: 0.6rem 0.8rem !important;
                font-size: 0.95rem !important;
            }
            .form-label {
                font-size: 0.65rem !important;
                margin-bottom: 0.3rem !important;
            }
            textarea.form-control {
                font-size: 1rem !important;
                padding: 0.75rem !important;
            }
            .card {
                margin-bottom: 1rem !important;
            }
            .row.g-4 {
                --bs-gutter-y: 1.25rem;
            }
            /* Destinos items spacing */
            .destino-item {
                margin-bottom: 1rem !important;
            }
            .destino-item .card-body {
                padding: 1rem !important;
            }
            .destino-trigger {
                min-height: 50px !important;
                padding: 0.75rem !important;
            }
            .destino-trigger-text {
                font-size: 0.85rem !important;
            }
            .border-dashed {
                padding: 0.85rem !important;
                font-size: 0.85rem !important;
            }
            .card-footer {
                padding: 1.25rem !important;
            }
            .d-flex.flex-column.flex-md-row.align-items-center button {
                width: 100% !important;
                padding: 0.85rem !important;
                font-size: 0.95rem !important;
            }
            .d-flex.flex-column.flex-md-row.align-items-center a {
                width: 100% !important;
                text-align: center;
            }
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            let allData = [];
            let activeDropdown = null;
            let activeItem = null;

            // Cargar datos
            try {
                const res = await fetch('/api/destinos');
                if (res.ok) allData = await res.json();
            } catch (e) { console.error(e); }

            function renderView(dropdown, view, data = null) {
                if (view === 'DEP') {
                    dropdown.innerHTML = `
                        <div class="destino-header"><span>Seleccione Departamento</span></div>
                        <div class="destino-options-list">
                            ${allData.map(dep => `<div class="destino-option dept-navigation" data-id="${dep.id}">${dep.nombre}</div>`).join('')}
                        </div>`;
                } else {
                    dropdown.innerHTML = `
                        <div class="destino-header">
                            <i class="fas fa-arrow-left back-btn"></i>
                            <span data-id="${data.id}">${data.nombre}</span>
                        </div>
                        <div class="destino-options-list">
                            <div class="destino-option fw-bold text-primary" data-type="dept" data-id="${data.id}"><i class="fas fa-check-circle me-2"></i>Todo el Departamento</div>
                            ${(data.municipios || []).map(mun => `<div class="destino-option" data-type="mun" data-id="${mun.id}">${mun.nombre}</div>`).join('')}
                        </div>`;
                }
            }

            $(document).on('click', '.destino-trigger', function(e) {
                e.stopPropagation();
                const trigger = this;
                const dropdown = $(trigger).siblings('.destino-dropdown')[0];
                activeItem = $(trigger).closest('.destino-item');
                
                if (activeDropdown && activeDropdown !== dropdown) $(activeDropdown).hide();
                activeDropdown = dropdown;
                
                if (!$(dropdown).is(':visible')) {
                    $(dropdown).css('display', 'flex').show();
                    renderView(dropdown, 'DEP');
                } else {
                    $(dropdown).hide();
                }
            });

            $(document).on('click', '.destino-dropdown', function(e) {
                e.stopPropagation();
            });

            $(document).on('click', '.back-btn', function(e) { 
                e.stopPropagation();
                renderView(activeDropdown, 'DEP'); 
            });

            $(document).on('click', '.dept-navigation', function(e) {
                e.stopPropagation();
                const id = $(this).data('id');
                const depto = allData.find(d => d.id == id);
                renderView(activeDropdown, 'MUN', depto);
            });

            $(document).on('click', '.destino-option:not(.dept-navigation)', function(e) {
                e.stopPropagation();
                const type = $(this).data('type');
                const id = $(this).data('id');
                const text = $(this).text().trim().replace('Todo el Departamento', '').trim();
                const headerSpan = $(activeDropdown).find('.destino-header span');
                const deptoName = headerSpan.text();
                const deptoId = headerSpan.attr('data-id');
                
                const fullText = (type === 'dept') ? deptoName : `${deptoName} - ${text}`;
                
                activeItem.find('.destino-trigger-text').text(fullText).removeClass('text-muted fst-italic');
                activeItem.find('.input-depto').val(deptoId).trigger('change');
                activeItem.find('.input-muni').val(type === 'mun' ? id : '').trigger('change');
                activeItem.find('.input-nombre').val(fullText).trigger('change');
                $(activeDropdown).hide();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.destino-wrapper').length) {
                    $('.destino-dropdown').hide();
                }
            });

            $('#btn-add-destino').click(function() {
                const lastItem = $('.destino-item').last();
                const newIndex = lastItem.length ? (parseInt(lastItem.data('index')) + 1) : 0;
                
                const html = `
                <div class="destino-item mb-4 animate__animated animate__fadeIn" data-index="${newIndex}">
                    <div class="card border-0 bg-light rounded-4 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 text-success"><i class="fas fa-map-marker-alt me-2"></i>Destino ${newIndex + 1}</h6>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-destino">
                                    <i class="fas fa-trash-alt me-1"></i> Eliminar
                                </button>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-muted small">Municipio o Departamento Destino</label>
                                    <div class="destino-wrapper position-relative">
                                        <div class="destino-trigger bg-white border-0 shadow-sm rounded-3 p-3 d-flex align-items-center justify-content-between" tabindex="0" style="cursor:pointer; min-height:60px;">
                                            <span class="destino-trigger-text text-muted fst-italic">Seleccione el municipio o departamento...</span>
                                            <i class="fas fa-chevron-down ms-2 flex-shrink-0 text-success"></i>
                                        </div>
                                        <div class="destino-dropdown shadow-lg" style="display:none;"></div>
                                        <input type="hidden" name="destinos[${newIndex}][departamento_id]" class="input-depto" required>
                                        <input type="hidden" name="destinos[${newIndex}][municipio_id]" class="input-muni">
                                        <input type="hidden" name="destinos[${newIndex}][nombre]" class="input-nombre" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                $('#btn-add-destino').before(html);
            });

            $(document).on('click', '.remove-destino', function() {
                $(this).closest('.destino-item').remove();
                $('.destino-item').each(function(i) {
                    $(this).find('h6').html(`<i class="fas fa-map-marker-alt me-2"></i>Destino ${i + 1}`);
                });
            });

            // Sincronización de fechas
            $('#fecha_inicio').change(function() {
                const startVal = $(this).val();
                if(startVal) {
                    $('#fecha_fin').attr('min', startVal);
                    if($('#fecha_fin').val() && $('#fecha_fin').val() < startVal) {
                        $('#fecha_fin').val(startVal);
                    }
                }
            });

            // --- FUNCIONES AUXILIARES PARA ERRORES INLINE ---
            function showError(element, message) {
                let target = element;
                let insertionPoint = element;

                if ($(element).closest('.input-group').length) {
                    insertionPoint = $(element).closest('.input-group')[0];
                } else if ($(element).closest('.destino-wrapper').length) {
                    const item = $(element).closest('.destino-item');
                    target = item.find('.destino-trigger')[0];
                    insertionPoint = item.find('.destino-wrapper')[0];
                }

                $(target).addClass('is-invalid-custom');

                const errorId = `error-${element.id || element.name || Math.random()}`;
                let errorDiv = $(insertionPoint).parent().find(`.invalid-feedback-custom[data-for="${errorId}"]`);
                
                if (!errorDiv.length) {
                    errorDiv = $('<div class="invalid-feedback-custom"></div>');
                    errorDiv.attr('data-for', errorId);
                    $(insertionPoint).after(errorDiv);
                }
                errorDiv.text(message).show();
            }

            function clearError(element) {
                let target = element;
                let insertionPoint = element;

                if ($(element).closest('.input-group').length) {
                    insertionPoint = $(element).closest('.input-group')[0];
                } else if ($(element).closest('.destino-wrapper').length) {
                    const item = $(element).closest('.destino-item');
                    target = item.find('.destino-trigger')[0];
                    insertionPoint = item.find('.destino-wrapper')[0];
                }

                $(target).removeClass('is-invalid-custom');
                
                const errorId = `error-${element.id || element.name || Math.random()}`;
                $(insertionPoint).parent().find(`.invalid-feedback-custom[data-for="${errorId}"]`).hide().text('');
            }

            function clearAllErrors() {
                $('.is-invalid-custom').removeClass('is-invalid-custom');
                $('.invalid-feedback-custom').hide().text('');
            }

            // Validación del formulario
            $('#formComision').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                clearAllErrors();
                let hasErrors = false;
                let firstErrorElement = null;

                function validate(condition, element, message) {
                    if (condition) {
                        showError(element, message);
                        hasErrors = true;
                        if (!firstErrorElement) firstErrorElement = element;
                    }
                }

                // Validar Fechas
                const fechaInicio = $('#fecha_inicio')[0];
                const fechaFin = $('#fecha_fin')[0];
                
                validate(!$(fechaInicio).val(), fechaInicio, 'Ponga la fecha de inicio.');
                validate(!$(fechaFin).val(), fechaFin, 'Ponga la fecha de regreso.');

                // Validar Destinos
                $('.destino-item').each(function(index) {
                    const deptoInput = $(this).find('.input-depto')[0];
                    const deptoVal = $(deptoInput).val();
                    const nombreVal = $(this).find('.input-nombre').val();
                    validate(!deptoVal || !nombreVal, deptoInput, `Seleccione el destino ${index + 1}.`);
                });

                // Validar Campos de Texto
                const regionalInput = $('input[name="regional"]')[0];
                const centroInput = $('input[name="centro_formacion"]')[0];
                const objetivoInput = $('textarea[name="objetivo_desplazamiento"]')[0];

                validate(!$(regionalInput).val().trim(), regionalInput, 'Ponga la regional.');
                validate(!$(centroInput).val().trim(), centroInput, 'Ponga el centro.');
                
                const objVal = $(objetivoInput).val().trim();
                if (!objVal) {
                    validate(true, objetivoInput, 'Ponga el objetivo.');
                } else if (objVal.length < 10) {
                    validate(true, objetivoInput, 'El objetivo debe ser detallado (mínimo 10 caracteres).');
                }

                if (hasErrors) {
                    if (firstErrorElement) {
                        let scrollTarget = firstErrorElement;
                        if ($(firstErrorElement).hasClass('input-depto')) {
                            scrollTarget = $(firstErrorElement).closest('.destino-item')[0];
                        }
                        scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                // Confirmación Final
                Swal.fire({
                    title: '¿Confirmar Solicitud?',
                    text: "¿Está seguro de que desea guardar esta solicitud de comisión?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#39a900',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Guardando...',
                            text: 'Por favor espere un momento.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.submit();
                    }
                });
            });

            // Corrección en tiempo real: Quitar error al escribir o cambiar
            $(document).on('input change', 'input, textarea, select', function() {
                clearError(this);
            });
        });
    </script>
    @endpush
@endsection