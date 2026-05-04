@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid px-4 py-5">
        <div class="row justify-content-center">
            <div class="col-xxl-9 col-xl-10">

                {{-- Encabezado de Página --}}
                <div class="d-flex align-items-center mb-5">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4 me-4">
                        <i class="fas fa-file-signature fa-2x text-success"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1 text-dark">
                            @if(!isset($agenda))
                                Nueva Agenda de Desplazamiento
                            @elseif(strtoupper($agenda->estado->nombre) == 'BORRADOR')
                                Editar Agenda de Desplazamiento
                            @else
                                Corregir Agenda de Desplazamiento
                            @endif
                        </h2>
                        <p class="text-muted mb-0">
                            @if(!isset($agenda))
                                Complete todos los campos para registrar su comisión de servicio.
                            @elseif(strtoupper($agenda->estado->nombre) == 'BORRADOR')
                                Modifique los datos de su borrador antes de enviarlo a revisión.
                            @else
                                Modifique los campos necesarios para corregir su agenda devuelta.
                            @endif
                        </p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-exclamation-circle text-danger me-2"></i>
                            <h6 class="fw-bold mb-0">Por favor corrija los siguientes errores:</h6>
                        </div>
                        <ul class="mb-0 ps-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div
                        class="alert alert-success border-0 shadow-sm rounded-4 mb-5 p-4 animate__animated animate__fadeIn">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success me-2 fa-lg"></i>
                            <h5 class="fw-bold mb-0">{{ session('success') }}</h5>
                        </div>
                        @if (isset($agenda) && !session('success'))
                            <div class="d-flex gap-2 mt-2">
                                <a href="{{ route('agenda.pdf', $agenda->id) }}"
                                    class="btn btn-dark rounded-pill px-4 shadow-sm" target="_blank">
                                    <i class="fas fa-file-pdf me-2"></i>Ver PDF
                                </a>
                                <a href="{{ route('reportar-dia.show', $agenda->id) }}"
                                    class="btn btn-success rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-calendar-check me-2"></i>Reportar Actividades
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <form action="{{ route('formulario.store') }}" method="POST" enctype="multipart/form-data" 
              class="animate__animated animate__fadeIn" id="agenda-form" novalidate>
                    @csrf
                    @if(isset($agenda))
                        <input type="hidden" name="agenda_id" value="{{ $agenda->id }}">
                    @endif

                    <div class="row g-4">
                        {{-- SECCIÓN 0: CLASIFICACIÓN DE LA INFORMACIÓN --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                    <div class="bg-dark p-2 rounded-3 me-3">
                                        <i class="fas fa-shield-alt text-white"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-dark">Clasificación de la Información</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <div class="row g-2">
                                        @foreach($clasificaciones as $clasificacion)
                                        <div class="col-md-4">
                                            <input type="radio" name="clasificacion_id" value="{{ $clasificacion->id }}"
                                                id="c-{{ $clasificacion->id }}" class="btn-check" 
                                                {{ old('clasificacion_id', $agenda->clasificacion_id ?? '') == $clasificacion->id ? 'checked' : ($loop->first && !isset($agenda) ? 'checked' : '') }} required>
                                            <label
                                                class="btn btn-outline-success w-100 rounded-3 p-3 text-start border-2 shadow-sm"
                                                for="c-{{ $clasificacion->id }}">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas {{ $clasificacion->nombre == 'PÚBLICA' ? 'fa-eye' : ($clasificacion->nombre == 'INTERNA' ? 'fa-user-shield' : 'fa-lock') }} fa-lg me-3 opacity-75"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ $clasificacion->nombre }}</div>
                                                        <div class="small opacity-75">
                                                            {{ $clasificacion->nombre == 'PÚBLICA' ? 'Acceso general para todos' : ($clasificacion->nombre == 'INTERNA' ? 'Acceso restringido por ley' : 'Reserva por defensa nacional') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 1: DATOS PERSONALES --}}
                        <div class="col-12">
                            <div class="form-section card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                                    <span class="step-badge me-3">1</span>
                                    <h5 class="fw-bold mb-0 text-dark">Información del Contratista</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label
                                                class="form-label fw-semibold text-muted small text-uppercase">Nombres y
                                                Apellidos Completos</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i
                                                        class="fas fa-user text-muted"></i></span>
                                                <input type="text" name="nombre_completo"
                                                    class="form-control custom-input bg-light"
                                                    value="{{ old('nombre_completo', auth()->user()->name) }}"
                                                    placeholder="Ej: Juan Pablo Pérez" readonly required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Tipo
                                                de Documento</label>
                                             <input type="text" class="form-control custom-input bg-light" value="Cédula de Ciudadanía" readonly>
                                             <input type="hidden" name="tipo_documento" value="CC">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Número
                                                de Documento</label>
                                             <input type="text" name="numero_documento" class="form-control custom-input bg-light"
                                                value="{{ old('numero_documento', auth()->user()->numero_documento) }}" placeholder="Ej: 1018..." readonly required>
                                        </div>
                                        <div class="col-md-12">
                                             @php
                                                $userRol = auth()->user()->role;
                                                $cargoValue = ($userRol === 'contratista' || $userRol === 'supervisor_contrato') ? 'Contratista' : 'Servidor_Publico';
                                                $cargoLabel = $cargoValue === 'Contratista' ? 'Contratista (Planta)' : 'Servidor Público';
                                             @endphp
                                             {{-- Campo visual de solo lectura --}}
                                             <input type="text" id="cargo" class="form-select custom-input bg-light" value="{{ $cargoLabel }}" readonly>
                                             {{-- Input hidden para enviar el valor real al servidor --}}
                                             <input type="hidden" name="cargo" id="cargo_hidden" value="{{ $cargoValue }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-section card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                                    <span class="step-badge me-3">2</span>
                                    <h5 class="fw-bold mb-0 text-dark">Detalles del Contrato y Seguimiento</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Número de Contrato</label>
                                            <input type="text" class="form-control custom-input bg-light" value="{{ last(explode('.', $user->numero_contrato)) }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Año</label>
                                            <input type="text" class="form-control custom-input bg-light" value="{{ $user->anio_contrato }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Vencimiento</label>
                                            <input type="text" class="form-control custom-input bg-light" value="{{ $user->fecha_vencimiento }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Categoría</label>
                                            <input type="text" class="form-control custom-input bg-light" value="{{ $user->categoria->nombre ?? 'N/A' }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Fecha Elaboración Agenda</label>
                                            <input type="hidden" name="fecha_elaboracion" value="{{ old('fecha_elaboracion', (isset($agenda) && $agenda->fecha_elaboracion) ? (is_string($agenda->fecha_elaboracion) ? substr($agenda->fecha_elaboracion, 0, 10) : $agenda->fecha_elaboracion->format('Y-m-d')) : date('Y-m-d')) }}">
                                            <input type="date" class="form-control custom-input bg-light" 
                                                value="{{ old('fecha_elaboracion', (isset($agenda) && $agenda->fecha_elaboracion) ? (is_string($agenda->fecha_elaboracion) ? substr($agenda->fecha_elaboracion, 0, 10) : $agenda->fecha_elaboracion->format('Y-m-d')) : date('Y-m-d')) }}" readonly disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Supervisor</label>
                                            <input type="text" class="form-control custom-input bg-light" value="{{ $user->supervisor->nombre ?? 'Sin asignar' }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Ordenador</label>
                                            <input type="text" class="form-control custom-input bg-light" value="{{ $user->ordenador->nombre ?? 'Sin asignar' }}" readonly>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Objeto Contractual</label>
                                            <textarea class="form-control custom-input bg-light" rows="3" readonly>{{ $user->objeto_contractual }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: INFORMACIÓN DEL DESPLAZAMIENTO --}}
                        <div class="col-12">
                            <div class="form-section card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                                    <span class="step-badge me-3">3</span>
                                    <h5 class="fw-bold mb-0 text-dark">Información del Desplazamiento</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <div class="row g-4">
                                        <div class="col-12" id="destinos-container">
                                            <label class="form-label fw-semibold text-muted small text-uppercase mb-3">Ruta de Desplazamiento</label>
                                            
                                            {{-- Origen Fijo --}}
                                            <div class="bg-light p-3 rounded-4 mb-3 d-flex align-items-center">
                                                <div class="bg-success text-white px-3 py-2 rounded-3 me-3 fw-bold">ORIGEN</div>
                                                <div class="fw-bold fs-5">MEDELLÍN</div>
                                            </div>

                                            @php
                                                $destinos = old('destinos', (isset($agenda) && $agenda->destinos) ? $agenda->destinos : [['departamento_id' => '', 'municipio_id' => '', 'vereda' => '', 'nombre' => '']]);
                                            @endphp

                                            @foreach($destinos as $index => $dest)
                                            <div class="destino-item mb-4 animate__animated animate__fadeIn" data-index="{{ $index }}">
                                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-start border-success border-4">
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
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold text-muted small">Municipio Destino</label>
                                                                <div class="destino-wrapper position-relative">
                                                                    <div class="destino-trigger custom-input form-select d-flex align-items-center justify-content-between {{ (!empty($dest['nombre'])) ? 'has-value' : '' }}"
                                                                         tabindex="0" style="cursor:pointer; min-height:50px;">
                                                                        <span class="destino-trigger-text {{ (!empty($dest['nombre'])) ? '' : 'text-muted fst-italic' }}">
                                                                            {{ $dest['nombre'] ?: 'Seleccione un destino...' }}
                                                                        </span>
                                                                        <i class="fas fa-chevron-down ms-2 flex-shrink-0"></i>
                                                                    </div>
                                                                    <div class="destino-dropdown" style="display:none;">
                                                                        {{-- Contenido dinámico --}}
                                                                    </div>
                                                                    <input type="hidden" name="destinos[{{ $index }}][departamento_id]" class="input-depto" value="{{ $dest['departamento_id'] }}">
                                                                    <input type="hidden" name="destinos[{{ $index }}][municipio_id]" class="input-muni" value="{{ $dest['municipio_id'] }}">
                                                                    <input type="hidden" name="destinos[{{ $index }}][nombre]" class="input-nombre" value="{{ $dest['nombre'] }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold text-muted small">Vereda (Opcional)</label>
                                                                <input type="text" name="destinos[{{ $index }}][vereda]" class="form-control custom-input" 
                                                                    value="{{ $dest['vereda'] }}" 
                                                                    placeholder="Ej: Corregimiento Alegrías">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

                                            <button type="button" id="btn-add-destino" class="btn btn-outline-success w-100 rounded-4 py-3 border-dashed hover-grow fw-bold">
                                                <i class="fas fa-plus-circle me-2"></i> AGREGAR OTRO MUNICIPIO DE DESTINO
                                            </button>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase text-truncate d-block">Direccion General/Regional</label>
                                            <input type="text" name="regional" class="form-control custom-input @error('regional') is-invalid @enderror" value="{{ old('regional', $agenda->regional ?? '') }}" placeholder="Ej: Regional Antioquia" required>
                                            @error('regional')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase text-truncate d-block">Dependencia/Centro</label>
                                            <input type="text" name="centro" class="form-control custom-input @error('centro') is-invalid @enderror" value="{{ old('centro', $agenda->centro ?? '') }}" placeholder="Ej: CTGI" required>
                                            @error('centro')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Fecha
                                                Inicio</label>
                                            <input type="date" name="fecha_inicio" id="fecha_inicio"
                                                class="form-control custom-input" 
                                                value="{{ old('fecha_inicio', isset($agenda) ? $agenda->fecha_inicio->format('Y-m-d') : '') }}"
                                                {{ isset($agenda) ? '' : 'min=' . date('Y-m-d') }} required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Fecha
                                                Regreso</label>
                                            <input type="date" name="fecha_fin" id="fecha_fin"
                                                class="form-control custom-input" 
                                                value="{{ old('fecha_fin', isset($agenda) ? $agenda->fecha_fin->format('Y-m-d') : '') }}"
                                                required>
                                        </div>

                                        <div class="col-12">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label
                                                        class="form-label fw-semibold text-muted small text-uppercase">Entidad
                                                        / Empresa</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-0"><i
                                                                class="fas fa-building text-muted"></i></span>
                                                        <input type="text" name="entidad_empresa"
                                                            class="form-control custom-input"
                                                            placeholder="Ej: Sena CTGI"
                                                            value="{{ old('entidad_empresa', $agenda->entidad_empresa ?? '') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label
                                                        class="form-label fw-semibold text-muted small text-uppercase">Nombre
                                                        del Contacto</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-0"><i
                                                                class="fas fa-user-tie text-muted"></i></span>
                                                        <input type="text" name="contacto"
                                                            class="form-control custom-input"
                                                            placeholder="Ej: Coordinador de área"
                                                            value="{{ old('contacto', $agenda->contacto ?? '') }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label
                                                class="form-label fw-semibold text-muted small text-uppercase">Objetivo
                                                del Desplazamiento</label>
                                            <textarea name="objetivo_desplazamiento" class="form-control custom-input"
                                                rows="3" maxlength="160"
                                                placeholder="Describa brevemente el motivo del viaje..."
                                                required>{{ old('objetivo_desplazamiento', $agenda->objetivo_desplazamiento ?? '') }}</textarea>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold text-muted small text-uppercase mb-3">
                                                Obligaciones del Contrato Relacionadas
                                            </label>
                                            <div id="obligaciones-container" class="bg-light p-4 rounded-4 border">
                                                @if($user->categoria && $user->categoria->obligaciones->count() > 0)
                                                    <div class="row g-3">
                                                        @foreach($user->categoria->obligaciones as $obligacion)
                                                            <div class="col-12">
                                                                <div class="form-check custom-checkbox-card p-0">
                                                                    <input class="form-check-input d-none" type="checkbox" 
                                                                        name="obligaciones[]" value="{{ $obligacion->id }}" 
                                                                        id="ob-{{ $obligacion->id }}"
                                                                        {{ (is_array(old('obligaciones')) && in_array($obligacion->id, old('obligaciones'))) || (isset($agenda) && $agenda->obligaciones->contains($obligacion->id)) ? 'checked' : '' }}>
                                                                    <label class="form-check-label w-100 p-3 rounded-3 border bg-white cursor-pointer shadow-sm transition-all" for="ob-{{ $obligacion->id }}">
                                                                        <div class="d-flex">
                                                                            <div class="check-box-ui me-3 flex-shrink-0">
                                                                                <i class="fas fa-check"></i>
                                                                            </div>
                                                                            <span class="small">{{ $obligacion->nombre }}</span>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning mb-0 border-0 shadow-sm">
                                                        <i class="fas fa-exclamation-triangle me-2"></i> No hay obligaciones configuradas para su categoría.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Firma Eliminada de aquí, ahora es modal global --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BOTÓN DE ENVÍO --}}
                        <div class="col-12 mt-4 mb-5">
                            <div class="d-flex flex-column flex-md-row gap-3">
                                @if(isset($agenda))
                                    <button type="button" 
                                        class="btn btn-outline-secondary btn-lg flex-grow-1 rounded-4 py-3 fw-bold shadow-sm hover-grow"
                                        onclick="confirmarRegresar()">
                                        <i class="fas fa-times me-2"></i> Regresar
                                    </button>
                                    <button type="submit"
                                        class="btn btn-warning btn-lg flex-grow-2 rounded-4 py-3 fw-bold shadow hover-grow">
                                        <i class="fas fa-save me-2"></i> {{ strtoupper($agenda->estado->nombre) == 'BORRADOR' ? 'Guardar Edición' : 'Guardar Correcciones' }}
                                    </button>
                                @else
                                    <button type="submit"
                                        class="btn btn-success btn-lg w-100 rounded-4 py-3 fw-bold shadow hover-grow">
                                        <i class="fas fa-paper-plane me-2"></i> Crear y Guardar Agenda
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .step-badge {
            width: 32px;
            height: 32px;
            background-color: #39a900;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .custom-input {
            border: 2px solid #f1f5f9;
            border-radius: 0.85rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .custom-input:focus {
            border-color: #39a900;
            box-shadow: 0 0 0 4px rgba(57, 169, 0, 0.1);
            background-color: #fff;
        }

        .form-section {
            transition: transform 0.3s ease;
        }

        .border-dashed {
            border: 2px dashed #cbd5e1;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        .btn-check:checked + label {
            border-color: #39a900 !important;
            background-color: #39a900 !important;
            color: white !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .hover-grow {
            transition: all 0.2s ease;
        }

        .hover-grow:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(57, 169, 0, 0.3) !important;
        }

        .file-upload-input {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }

        .input-group-text {
            border-radius: 0.85rem 0 0 0.85rem;
        }

        .custom-input.form-control:not(.obligation-item textarea) {
            border-left: none;
        }

        /* Animaciones para los items de obligaciones */
        .obligation-item {
            border: 2px solid #f1f5f9;
        }

        .btn-success {
            background-color: #39a900;
            border-color: #39a900;
        }

        .btn-success:hover {
            background-color: #2d8600;
            border-color: #2d8600;
        }

        .text-success {
            color: #39a900 !important;
        }

        .bg-success {
            background-color: #39a900 !important;
        }

        /* ── Dropdown custom Objetivo Contractual ── */
        .objetivo-wrapper {
            position: relative;
        }

        .objetivo-trigger {
            user-select: none;
            border: 2px solid #f1f5f9;
            border-radius: 0.85rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.2s ease;
            background-image: none !important;  /* quita la flecha nativa de Bootstrap form-select */
        }

        .objetivo-trigger:focus,
        .objetivo-trigger.open {
            border-color: #39a900;
            box-shadow: 0 0 0 4px rgba(57, 169, 0, 0.1);
            outline: none;
        }

        .objetivo-trigger #objetivo_trigger_text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }

        .objetivo-dropdown {
            position: fixed;            /* fixed escapa de overflow:hidden del padre */
            z-index: 9999;
            background: #fff;
            border: 2px solid #39a900;
            border-radius: 0.85rem;
            overflow-x: auto;
            overflow-y: auto;
            max-height: 300px;
            min-width: 300px;           /* ancho mínimo legible */
            box-shadow: 0 8px 24px rgba(57,169,0,0.18);
            animation: dropdownIn 0.18s ease;
        }

        @keyframes dropdownIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .objetivo-option {
            padding: 14px 18px;
            white-space: nowrap;        /* ← texto largo sin corte */
            cursor: pointer;
            font-size: 0.95rem;
            border-bottom: 1px solid #f0f4f0;
            transition: background 0.15s;
        }

        .objetivo-option:last-child {
            border-bottom: none;
        }

        .objetivo-option:hover {
            background-color: #f0fdf4;
            color: #39a900;
        }

        .objetivo-option.selected {
            background-color: #39a900;
            color: #fff;
            font-weight: 600;
        }

        /* Scrollbars estilizadas */
        .objetivo-dropdown::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }
        .objetivo-dropdown::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .objetivo-dropdown::-webkit-scrollbar-thumb {
            background: #39a900;
            border-radius: 4px;
        }
        .objetivo-dropdown::-webkit-scrollbar-thumb:hover {
            background: #2d8600;
        }
        /* ── Dropdown custom Destino (Drill-down) ── */
        .destino-wrapper {
            position: relative;
        }

        .destino-trigger {
            user-select: none;
            border: 2px solid #f1f5f9;
            border-radius: 0.85rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.2s ease;
            background-image: none !important;
        }

        .destino-trigger:focus,
        .destino-trigger.open {
            border-color: #39a900;
            box-shadow: 0 0 0 4px rgba(57, 169, 0, 0.1);
            outline: none;
        }

        .destino-trigger #destino_trigger_text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }

        .destino-dropdown {
            position: fixed;
            z-index: 9999;
            background: #fff;
            border: 2px solid #39a900;
            border-radius: 0.85rem;
            max-height: 350px;
            min-width: 320px;
            box-shadow: 0 8px 24px rgba(57,169,0,0.18);
            animation: dropdownIn 0.18s ease;
            display: flex;
            flex-direction: column;
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
            border-radius: 0.85rem 0.85rem 0 0;
        }

        .back-btn {
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.2s;
            color: #39a900;
        }

        .back-btn:hover {
            background: #e2e8f0;
        }

        .destino-options-list {
            overflow-y: auto;
            flex: 1;
        }

        .destino-option {
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.15s;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .destino-option:hover {
            background-color: #f0fdf4;
            color: #39a900;
        }

        .destino-option.dept-navigation::after {
            content: "\f054"; /* chevron-right */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 0.8rem;
            opacity: 0.5;
        }

        .destino-option.selected-item {
            background-color: #39a900 !important;
            color: #fff !important;
            font-weight: 600;
        }

        .destino-options-list::-webkit-scrollbar {
            width: 7px;
        }
        .destino-options-list::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .destino-options-list::-webkit-scrollbar-thumb {
            background: #39a900;
            border-radius: 4px;
        }
        .invalid-feedback-custom {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
            font-weight: 500;
        }
        
        .is-invalid-custom {
            border-color: #dc3545 !important;
            padding-right: calc(1.5em + 0.75rem) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }

        .is-invalid-custom + .invalid-feedback-custom {
            display: block;
        }

        /* Styles for new obligations checkboxes */
        .custom-checkbox-card .form-check-label {
            transition: all 0.2s ease;
            border: 2px solid #f1f5f9 !important;
        }

        .custom-checkbox-card .form-check-input:checked + .form-check-label {
            border-color: #39a900 !important;
            background-color: #f0fdf4 !important;
            box-shadow: 0 4px 12px rgba(57, 169, 0, 0.1) !important;
        }

        .check-box-ui {
            width: 24px;
            height: 24px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            transition: all 0.2s ease;
        }

        .custom-checkbox-card .form-check-input:checked + .form-check-label .check-box-ui {
            background-color: #39a900;
            border-color: #39a900;
            color: white;
        }

        .transition-all {
            transition: all 0.2s ease;
        }

        .hover-grow:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Pasar estado al JS
                                            window.agendaConfig = {
                                                esEdicion: {{ isset($agenda) ? 'true' : 'false' }},
                                                estado: "{{ isset($agenda) ? strtoupper($agenda->estado->nombre) : '' }}",
                                                tieneFirma: {{ (isset($agenda) && $agenda->firma_contratista) ? 'true' : 'false' }}
                                            };

            function confirmarRegresar() {
                const esBorrador = window.agendaConfig.estado === 'BORRADOR';
                const titulo = '¿Está seguro?';
                const texto = esBorrador 
                    ? "¿Desea dejar de editar la agenda? Los cambios no guardados se perderán."
                    : "¿Desea dejar de corregir la agenda? Los cambios no guardados se perderán.";

                Swal.fire({
                    title: titulo,
                    text: texto,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#39a900',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check me-1"></i> Sí, salir',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        confirmButton: 'rounded-pill px-4',
                        cancelButton: 'rounded-pill px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ session('back_url_reportar_dia', route('reportar-dia')) }}";
                    }
                });
            }

            // Alerta de éxito si existe en la sesión
            @if(session('success'))
                Swal.fire({
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                    confirmButtonColor: '#39a900',
                    customClass: {
                        popup: 'rounded-4'
                    }
                });
            @endif
        </script>
        <script src="{{ asset('js/formulario_validaciones.js') }}"></script>
        <script>
            $(document).ready(function () {
                // Manejo de carga de archivo de firma
                $('#firma_contratista').on('change', function(e) {
                    const fileName = e.target.files[0]?.name || 'Haga clic si prefiere subir un archivo';
                    $('#file-name-preview').text(fileName);
                });

                // --- EXTRACCIÓN INTELIGENTE DE NÚMERO DE CONTRATO ---
                $('#numero_contrato').on('input', function() {
                    let val = $(this).val();
                    // Si el valor tiene letras o separadores
                    if (/[a-zA-Z\.\-]/.test(val)) {
                        // Dividimos por cualquier cosa que NO sea un número
                        let segments = val.split(/[^0-9]/).filter(Boolean);
                        if (segments.length > 0) {
                            // Tomamos el ÚLTIMO segmento numérico
                            // (Evita el "1" de CO1 si hay un número de contrato real después)
                            let lastSegment = segments[segments.length - 1];
                            
                            // Solo extraemos si el usuario ha puesto un separador al final 
                            // o si pegó el formato completo indicando intención de contrato.
                            if (val.includes('.') || val.includes('-')) {
                                $(this).val(lastSegment);
                            }
                        }
                    }
                });

                // Limpieza final obligatoria al perder el foco (solo deja números)
                $('#numero_contrato').on('blur', function() {
                    let val = $(this).val();
                    if (val) {
                        $(this).val(val.replace(/\D/g, ''));
                    }
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
            });

            document.addEventListener('DOMContentLoaded', async () => {
                let allData = [];
                let activeDropdown = null;
                let activeItem = null;

                // Cargar datos una sola vez
                try {
                    const res = await fetch('/api/destinos');
                    if (res.ok) {
                        allData = await res.json();
                    } else {
                        console.error('Error cargando destinos:', res.status, res.statusText);
                    }
                } catch (e) { 
                    console.error('Error de conexión API:', e); 
                }

                function positionDropdown(trigger, dropdown) {
                    const rect = trigger.getBoundingClientRect();
                    // Con position: fixed, no sumamos scrollY
                    dropdown.style.top = (rect.bottom + 6) + 'px';
                    dropdown.style.left = rect.left + 'px';
                    dropdown.style.width = rect.width + 'px';
                }

                function renderView(dropdown, view, data = null) {
                    if (view === 'DEP') {
                        dropdown.innerHTML = `
                            <div class="destino-header"><span>Seleccione Departamento</span></div>
                            <div class="destino-options-list">
                                ${allData.length ? allData.map(dep => `<div class="destino-option dept-navigation" data-id="${dep.id}">${dep.nombre}</div>`).join('') : '<div class="p-3 text-muted">Cargando destinos...</div>'}
                            </div>`;
                    } else {
                        dropdown.innerHTML = `
                            <div class="destino-header">
                                <i class="fas fa-arrow-left back-btn"></i>
                                <span data-id="${data.id}">${data.nombre}</span>
                            </div>
                            <div class="destino-options-list">
                                <div class="destino-option fw-bold text-success" data-type="dept" data-id="${data.id}"><i class="fas fa-check-circle me-2"></i>Todo el Departamento</div>
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
                    const isOpening = !$(dropdown).is(':visible');
                    
                    if (isOpening) {
                        $(dropdown).css('display', 'flex').show();
                        renderView(dropdown, 'DEP');
                        positionDropdown(trigger, dropdown);
                    } else {
                        $(dropdown).hide();
                    }
                });

                $(document).on('click', '.destino-dropdown', function(e) { e.stopPropagation(); });

                $(document).on('click', '.back-btn', function() {
                    renderView(activeDropdown, 'DEP');
                });

                $(document).on('click', '.dept-navigation', function() {
                    const id = $(this).data('id');
                    const depto = allData.find(d => d.id == id);
                    renderView(activeDropdown, 'MUN', depto);
                });

                $(document).on('click', '.destino-option:not(.dept-navigation)', function() {
                    const type = $(this).data('type');
                    const id = $(this).data('id');
                    const text = $(this).text().trim().replace('Todo el Departamento', '').trim();
                    const headerSpan = $(activeDropdown).find('.destino-header span');
                    const deptoName = headerSpan.text();
                    const deptoId = headerSpan.attr('data-id'); // Cambiado .data() por .attr() para seguridad con innerHTML
                    
                    const fullText = (type === 'dept') ? deptoName : `${deptoName} - ${text}`;
                    
                    activeItem.find('.destino-trigger-text').text(fullText).removeClass('text-muted fst-italic');
                    activeItem.find('.input-depto').val(deptoId);
                    activeItem.find('.input-muni').val(type === 'mun' ? id : '');
                    activeItem.find('.input-nombre').val(fullText);
                    
                    $(activeDropdown).hide();
                });

                $(document).on('click', function() { $('.destino-dropdown').hide(); });

                $('#btn-add-destino').click(function() {
                    const container = $('#destinos-container');
                    const lastItem = $('.destino-item').last();
                    const newIndex = lastItem.length ? (parseInt(lastItem.data('index')) + 1) : 0;
                    
                    const html = `
                    <div class="destino-item mb-4 animate__animated animate__fadeIn" data-index="${newIndex}">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-start border-success border-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0 text-success"><i class="fas fa-map-marker-alt me-2"></i>Destino ${newIndex + 1}</h6>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-destino">
                                        <i class="fas fa-trash-alt me-1"></i> Eliminar
                                    </button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Municipio Destino</label>
                                        <div class="destino-wrapper position-relative">
                                            <div class="destino-trigger custom-input form-select d-flex align-items-center justify-content-between" tabindex="0" style="cursor:pointer; min-height:50px;">
                                                <span class="destino-trigger-text text-muted fst-italic">Seleccione un destino...</span>
                                                <i class="fas fa-chevron-down ms-2 flex-shrink-0"></i>
                                            </div>
                                            <div class="destino-dropdown" style="display:none;"></div>
                                            <input type="hidden" name="destinos[${newIndex}][departamento_id]" class="input-depto">
                                            <input type="hidden" name="destinos[${newIndex}][municipio_id]" class="input-muni">
                                            <input type="hidden" name="destinos[${newIndex}][nombre]" class="input-nombre">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Vereda (Opcional)</label>
                                        <input type="text" name="destinos[${newIndex}][vereda]" class="form-control custom-input" placeholder="Ej: Corregimiento Alegrías">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    
                    $('#btn-add-destino').before(html);
                });

                $(document).on('click', '.remove-destino', function() {
                    $(this).closest('.destino-item').remove();
                    // Re-indexar? No es estrictamente necesario si usamos arrays en PHP, pero por limpieza:
                    $('.destino-item').each(function(i) {
                        $(this).find('h6').html(`<i class="fas fa-map-marker-alt me-2"></i>Destino ${i + 1}`);
                    });
                });

                window.addEventListener('scroll', () => { if (activeDropdown && $(activeDropdown).is(':visible')) positionDropdown($(activeDropdown).siblings('.destino-trigger')[0], activeDropdown); }, true);
            });
        </script>
    @endpush
@endsection
