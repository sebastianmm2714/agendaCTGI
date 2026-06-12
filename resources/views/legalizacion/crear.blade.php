@extends('layouts.dashboard')

@section('content')
@php
    $isFuncionario = ($agenda->user && $agenda->user->role === 'funcionario');
    $step = 1;
@endphp
<div class="container-fluid px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-xxl-9 col-xl-10">

            {{-- Encabezado de Página --}}
            <div class="d-flex flex-column flex-sm-row align-items-center mb-5 text-center text-sm-start">
                <div class="bg-success bg-opacity-10 p-3 rounded-4 mb-3 mb-sm-0 me-sm-4">
                    <i class="fas fa-file-invoice-dollar fa-2x text-success"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1 text-dark">Legalización de la Agenda #{{ $agenda->id }}</h2>
                    <p class="text-muted mb-0">Ruta: <strong class="text-success">{{ $agenda->ruta }}</strong> | Registre los soportes, evidencias y número de orden de viaje para completar la legalización de su agenda.</p>
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

            {{-- Resumen de la Agenda Aprobada --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-light bg-opacity-50">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                    <div class="bg-dark p-2 rounded-3 me-3">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark">Resumen de la Agenda Aprobada #{{ $agenda->id }}</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="d-block text-muted small fw-semibold text-uppercase">Contratista</span>
                            <span class="fw-bold text-dark fs-6">{{ $agenda->user->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="d-block text-muted small fw-semibold text-uppercase">Objetivo</span>
                            <span class="fw-bold text-dark fs-6">{{ $agenda->objetivo_desplazamiento }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="d-block text-muted small fw-semibold text-uppercase">Ruta / Desplazamiento</span>
                            <span class="fw-bold text-success fs-6"><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $agenda->ruta }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="d-block text-muted small fw-semibold text-uppercase">Fecha Inicio</span>
                            <span class="fw-bold text-dark fs-6">{{ $agenda->fecha_inicio?->format('d/m/Y') }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="d-block text-muted small fw-semibold text-uppercase">Fecha Fin</span>
                            <span class="fw-bold text-dark fs-6">{{ $agenda->fecha_fin?->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulario de Legalización --}}
            <form action="{{ route('legalizacion.guardar', $agenda->id) }}" method="POST" enctype="multipart/form-data" id="legalizacion-form" novalidate>
                @csrf

                <div class="row g-4">
                    {{-- 1. NÚMERO DE ORDEN DE VIAJE No --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                <span class="step-badge me-3">{{ $step++ }}</span>
                                <h5 class="fw-bold mb-0 text-dark">{{ $isFuncionario ? 'No. COMISIÓN DE SERVICIOS' : 'Número de Orden de Viaje' }}</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-muted small text-uppercase">{{ $isFuncionario ? 'No. COMISIÓN DE SERVICIOS' : 'ORDEN DE VIAJE No' }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-hashtag text-muted"></i></span>
                                        <input type="text" name="orden_viaje" id="orden_viaje"
                                            class="form-control custom-input bg-light"
                                            value="{{ old('orden_viaje', $agenda->orden_viaje ?? '') }}"
                                            placeholder="{{ $isFuncionario ? 'Ingrese el No. COMISIÓN DE SERVICIOS (Ej: 119626)' : 'Ingrese el número de orden de viaje (Ej: 119626)' }}" required>
                                    </div>
                                    <div class="form-text text-muted">Este número se reflejará en la cabecera correspondiente del formato oficial.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($isFuncionario)
                    {{-- CLASIFICACIÓN DE LA INFORMACIÓN --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                <span class="step-badge me-3">{{ $step++ }}</span>
                                <h5 class="fw-bold mb-0 text-dark">Clasificación de la Información</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="row g-3">
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
                                                    <h6 class="fw-bold mb-1">{{ $clasificacion->nombre }}</h6>
                                                    <span class="small text-muted d-block" style="line-height: 1.2;">
                                                        {{ $clasificacion->nombre == 'PÚBLICA' ? 'Acceso general para todos' : ($clasificacion->nombre == 'INTERNA' ? 'Acceso restringido por ley' : 'Reserva por defensa nacional') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 2. EVICENCIAS FOTOGRÁFICAS --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                <span class="step-badge me-3">{{ $step++ }}</span>
                                <h5 class="fw-bold mb-0 text-dark">Evidencias Fotográficas de la Actividad (Mínimo 1)</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="bg-light p-4 rounded-4 border text-center mb-3">
                                    <div class="text-success mb-3">
                                        <i class="fas fa-camera fa-3x"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2">Seleccione al menos 1 foto de evidencia</h6>
                                    <p class="small text-muted mb-3">Suba capturas o fotografías de las sesiones de formación, visitas o reuniones que evidencien el desarrollo de las actividades.</p>
                                    
                                    <input type="file" name="fotos[]" id="fotos" class="d-none" accept="image/*" multiple required>
                                    <label for="fotos" class="btn btn-outline-success rounded-pill px-4 fw-bold shadow-sm cursor-pointer">
                                        <i class="fas fa-images me-2"></i> Seleccionar Imágenes
                                    </label>
                                    
                                    <div id="fotos-preview-container" class="row g-2 mt-4 justify-content-center">
                                        @if(!empty($agenda->legalizacion_fotos))
                                            @foreach($agenda->legalizacion_fotos as $foto)
                                                <div class="col-6 col-sm-4 col-md-3 position-relative p-2 animate__animated animate__fadeIn" style="max-width: 150px;">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $foto) }}" class="preview-card img-fluid rounded-3 border" style="height: 100px; width: 100%; object-fit: cover;" alt="Evidencia Guardada">
                                                        <div class="mt-1 text-center">
                                                            <a href="{{ asset('storage/' . $foto) }}" target="_blank" class="small text-success fw-bold text-decoration-none">Ver Foto {{ $loop->iteration }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="form-text text-muted text-center" id="fotos-counter-text">
                                    @if(!empty($agenda->legalizacion_fotos))
                                        <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Ya cuenta con {{ count($agenda->legalizacion_fotos) }} imágenes guardadas. Puede seleccionar nuevas para reemplazarlas.</span>
                                    @else
                                        Ninguna imagen seleccionada. Se requiere mínimo 1.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. PLANILLAS DE ASISTENCIA --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                <span class="step-badge me-3">{{ $step++ }}</span>
                                <h5 class="fw-bold mb-0 text-dark">Registro de Asistencia / Listado de Planillas (Mínimo 1)</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="bg-light p-4 rounded-4 border text-center mb-3">
                                    <div class="text-success mb-3">
                                        <i class="fas fa-clipboard-list fa-3x"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2">Seleccione al menos 1 foto de la planilla</h6>
                                    <p class="small text-muted mb-3">Suba el listado o planillas de asistencia debidamente diligenciadas y firmadas por los aprendices o participantes.</p>
                                    
                                    <input type="file" name="planillas[]" id="planillas" class="d-none" accept="image/*" multiple required>
                                    <label for="planillas" class="btn btn-outline-success rounded-pill px-4 fw-bold shadow-sm cursor-pointer">
                                        <i class="fas fa-file-upload me-2"></i> Seleccionar Imágenes de Planillas
                                    </label>
                                    
                                    <div id="planillas-preview-container" class="row g-2 mt-4 justify-content-center">
                                        @if(!empty($agenda->legalizacion_planillas))
                                            @foreach($agenda->legalizacion_planillas as $planilla)
                                                <div class="col-6 col-sm-4 col-md-3 position-relative p-2 animate__animated animate__fadeIn" style="max-width: 150px;">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $planilla) }}" class="preview-card img-fluid rounded-3 border" style="height: 100px; width: 100%; object-fit: cover;" alt="Planilla Guardada">
                                                        <div class="mt-1 text-center">
                                                            <a href="{{ asset('storage/' . $planilla) }}" target="_blank" class="small text-success fw-bold text-decoration-none">Ver Planilla {{ $loop->iteration }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="form-text text-muted text-center" id="planillas-counter-text">
                                    @if(!empty($agenda->legalizacion_planillas))
                                        <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Ya cuenta con {{ count($agenda->legalizacion_planillas) }} planillas guardadas. Puede seleccionar nuevas para reemplazarlas.</span>
                                    @else
                                        Ninguna planilla seleccionada. Se requiere mínimo 1.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. RESULTADOS --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                <span class="step-badge me-3">{{ $step++ }}</span>
                                <h5 class="fw-bold mb-0 text-dark">{{ $isFuncionario ? 'Resultados de la Comisión' : 'Resultados de la Actividad' }}</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div id="resultados-container" class="d-flex flex-column gap-3">
                                    @if(old('resultados'))
                                        @foreach(old('resultados') as $index => $resultado)
                                            <div class="resultado-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                                <span class="fw-bold text-muted min-w-20">{{ $index + 1 }}.</span>
                                                <input type="text" name="resultados[]" class="form-control custom-input bg-light" value="{{ $resultado }}" placeholder="Ej: Se desarrolló sin novedad la actividad de aprendizaje..." required>
                                                <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-resultado" style="width: 38px; height: 38px;" title="Eliminar resultado">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @elseif(!empty($agenda->legalizacion_resultados))
                                        @foreach($agenda->legalizacion_resultados as $index => $resultado)
                                            <div class="resultado-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                                <span class="fw-bold text-muted min-w-20">{{ $index + 1 }}.</span>
                                                <input type="text" name="resultados[]" class="form-control custom-input bg-light" value="{{ $resultado }}" placeholder="Ej: Se desarrolló sin novedad la actividad de aprendizaje..." required>
                                                <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-resultado" style="width: 38px; height: 38px;" title="Eliminar resultado">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="resultado-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                            <span class="fw-bold text-muted min-w-20">1.</span>
                                            <input type="text" name="resultados[]" class="form-control custom-input bg-light" value="" placeholder="Ej: Se desarrolló sin novedad la actividad de aprendizaje..." required>
                                            <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-resultado" style="width: 38px; height: 38px;" title="Eliminar resultado">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" id="btn-add-resultado" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold mt-3">
                                    <i class="fas fa-plus me-1"></i> Agregar Resultado
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($isFuncionario)
                    {{-- SOPORTES DE DESPLAZAMIENTO --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                <span class="step-badge me-3">{{ $step++ }}</span>
                                <h5 class="fw-bold mb-0 text-dark">Soportes de Desplazamiento</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div id="soportes-container" class="d-flex flex-column gap-3">
                                    @if(old('soportes_desplazamiento'))
                                        @foreach(old('soportes_desplazamiento') as $soporte)
                                            <div class="soporte-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                                <span class="text-success fw-bold fs-4">•</span>
                                                <input type="text" name="soportes_desplazamiento[]" class="form-control custom-input bg-light" value="{{ $soporte }}" placeholder="Ej: Anexe los pasabordos y/o tiquetes terrestres..." required>
                                                <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-soporte" style="width: 38px; height: 38px;" title="Eliminar soporte">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @elseif(!empty($agenda->legalizacion_soportes_desplazamiento))
                                        @foreach($agenda->legalizacion_soportes_desplazamiento as $soporte)
                                            <div class="soporte-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                                <span class="text-success fw-bold fs-4">•</span>
                                                <input type="text" name="soportes_desplazamiento[]" class="form-control custom-input bg-light" value="{{ $soporte }}" placeholder="Ej: Anexe los pasabordos y/o tiquetes terrestres..." required>
                                                <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-soporte" style="width: 38px; height: 38px;" title="Eliminar soporte">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="soporte-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                            <span class="text-success fw-bold fs-4">•</span>
                                            <input type="text" name="soportes_desplazamiento[]" class="form-control custom-input bg-light" value="" placeholder="Ej: Anexe los pasabordos y/o tiquetes terrestres..." required>
                                            <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-soporte" style="width: 38px; height: 38px;" title="Eliminar soporte">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" id="btn-add-soporte" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold mt-3">
                                    <i class="fas fa-plus me-1"></i> Agregar Soporte
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!$isFuncionario)
                    {{-- 5. COMPROMISOS --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                <span class="step-badge me-3">{{ $step++ }}</span>
                                <h5 class="fw-bold mb-0 text-dark">Compromisos Adquiridos</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0" id="compromisos-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;" class="text-muted small text-uppercase">#</th>
                                                <th style="width: 45%;" class="text-muted small text-uppercase">Actividad</th>
                                                <th style="width: 25%;" class="text-muted small text-uppercase">Responsable</th>
                                                <th style="width: 20%;" class="text-muted small text-uppercase">Fecha Límite</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="compromisos-container">
                                            @if(old('compromisos'))
                                                @foreach(old('compromisos') as $index => $compromiso)
                                                    <tr class="compromiso-row animate__animated animate__fadeIn">
                                                        <td class="fw-bold text-muted compromiso-index">{{ $index + 1 }}.</td>
                                                        <td data-label="Actividad">
                                                            <textarea name="compromisos[{{ $index }}][actividad]" class="form-control custom-input bg-light" rows="2" placeholder="Ej: Se desarrolló actividad de socialización..." required>{{ $compromiso['actividad'] ?? '' }}</textarea>
                                                        </td>
                                                        <td data-label="Responsable">
                                                            <input type="text" name="compromisos[{{ $index }}][responsable]" class="form-control custom-input bg-light" value="{{ $compromiso['responsable'] ?? ($agenda->user->name ?? '') }}" placeholder="Nombre del responsable" required>
                                                        </td>
                                                        <td data-label="Fecha Límite">
                                                            <input type="date" name="compromisos[{{ $index }}][fecha]" class="form-control custom-input bg-light" value="{{ $compromiso['fecha'] ?? '' }}" required>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-compromiso" style="width: 38px; height: 38px;" title="Eliminar compromiso">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @elseif(!empty($agenda->legalizacion_compromisos))
                                                @foreach($agenda->legalizacion_compromisos as $index => $compromiso)
                                                    <tr class="compromiso-row animate__animated animate__fadeIn">
                                                        <td class="fw-bold text-muted compromiso-index">{{ $index + 1 }}.</td>
                                                        <td data-label="Actividad">
                                                            <textarea name="compromisos[{{ $index }}][actividad]" class="form-control custom-input bg-light" rows="2" placeholder="Ej: Se desarrolló actividad de socialización..." required>{{ $compromiso['actividad'] ?? '' }}</textarea>
                                                        </td>
                                                        <td data-label="Responsable">
                                                            <input type="text" name="compromisos[{{ $index }}][responsable]" class="form-control custom-input bg-light" value="{{ $compromiso['responsable'] ?? ($agenda->user->name ?? '') }}" placeholder="Nombre del responsable" required>
                                                        </td>
                                                        <td data-label="Fecha Límite">
                                                            <input type="date" name="compromisos[{{ $index }}][fecha]" class="form-control custom-input bg-light" value="{{ $compromiso['fecha'] ?? '' }}" required>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-compromiso" style="width: 38px; height: 38px;" title="Eliminar compromiso">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr class="compromiso-row animate__animated animate__fadeIn">
                                                    <td class="fw-bold text-muted compromiso-index">1.</td>
                                                    <td data-label="Actividad">
                                                        <textarea name="compromisos[0][actividad]" class="form-control custom-input bg-light" rows="2" placeholder="Ej: Se desarrolló actividad de socialización..." required></textarea>
                                                    </td>
                                                    <td data-label="Responsable">
                                                        <input type="text" name="compromisos[0][responsable]" class="form-control custom-input bg-light" value="{{ $agenda->user->name ?? '' }}" placeholder="Nombre del responsable" required>
                                                    </td>
                                                    <td data-label="Fecha Límite">
                                                        <input type="date" name="compromisos[0][fecha]" class="form-control custom-input bg-light" value="" required>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-compromiso" style="width: 38px; height: 38px;" title="Eliminar compromiso">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" id="btn-add-compromiso" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold mt-3">
                                    <i class="fas fa-plus me-1"></i> Agregar Compromiso
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!$isFuncionario)
                    {{-- 6. CONCLUSIONES --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                <span class="step-badge me-3">{{ $step++ }}</span>
                                <h5 class="fw-bold mb-0 text-dark">Conclusiones del Informe</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div id="conclusiones-container" class="d-flex flex-column gap-3">
                                    @if(old('conclusiones'))
                                        @foreach(old('conclusiones') as $index => $conclusion)
                                            <div class="conclusion-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                                <span class="fw-bold text-muted min-w-20">{{ $index + 1 }}.</span>
                                                <input type="text" name="conclusiones[]" class="form-control custom-input bg-light" value="{{ $conclusion }}" placeholder="Ej: Se cumplieron las sesiones planificadas..." required>
                                                <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-conclusion" style="width: 38px; height: 38px;" title="Eliminar conclusión">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @elseif(!empty($agenda->legalizacion_conclusiones))
                                        @foreach($agenda->legalizacion_conclusiones as $index => $conclusion)
                                            <div class="conclusion-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                                <span class="fw-bold text-muted min-w-20">{{ $index + 1 }}.</span>
                                                <input type="text" name="conclusiones[]" class="form-control custom-input bg-light" value="{{ $conclusion }}" placeholder="Ej: Se cumplieron las sesiones planificadas..." required>
                                                <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-conclusion" style="width: 38px; height: 38px;" title="Eliminar conclusión">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="conclusion-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                            <span class="fw-bold text-muted min-w-20">1.</span>
                                            <input type="text" name="conclusiones[]" class="form-control custom-input bg-light" value="" placeholder="Ej: Se cumplieron las sesiones planificadas..." required>
                                            <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-conclusion" style="width: 38px; height: 38px;" title="Eliminar conclusión">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" id="btn-add-conclusion" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold mt-3">
                                    <i class="fas fa-plus me-1"></i> Agregar Conclusión
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 7. PREGUNTA DECLARACIÓN --}}
                    @php
                        $realizaDec = old('realiza_declaracion');
                        if (is_null($realizaDec)) {
                            if (isset($agenda->realiza_declaracion)) {
                                $realizaDec = $agenda->realiza_declaracion ? '1' : '0';
                            } else {
                                if ($agenda->legalizacion_declaracion || !empty($agenda->legalizacion_gastos_transporte)) {
                                    $realizaDec = '1';
                                } elseif (!empty($agenda->legalizacion_tiquetes)) {
                                    $realizaDec = '0';
                                }
                            }
                        }
                    @endphp
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                <span class="step-badge me-3">{{ $step++ }}</span>
                                <h5 class="fw-bold mb-0 text-dark">Declaración de Transporte Informal</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <p class="text-muted small mb-3">¿Desea realizar la declaración no juramentada de transporte informal?</p>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="realiza_declaracion" id="realiza_declaracion_si" value="1" {{ $realizaDec === '1' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-bold text-dark cursor-pointer" for="realiza_declaracion_si">
                                            Sí
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="realiza_declaracion" id="realiza_declaracion_no" value="0" {{ $realizaDec === '0' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-bold text-dark cursor-pointer" for="realiza_declaracion_no">
                                            No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- GRUPO DECLARACIÓN (SI) --}}
                    <div id="grupo-declaracion-si" class="row g-4 m-0 p-0 col-12" style="display: none;">
                        {{-- 8. DECLARACIÓN NO JURAMENTADA --}}
                        <div class="col-12 mt-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                    <span class="step-badge me-3">{{ $step }}</span>
                                    <h5 class="fw-bold mb-0 text-dark">Declaración No Juramentada (Opcional)</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <div class="bg-light p-4 rounded-4 border text-center mb-3">
                                        <div class="text-success mb-3">
                                            <i class="fas fa-file-signature fa-3x"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-2">Seleccione el archivo de declaración no juramentada</h6>
                                        <p class="small text-muted mb-3">Suba el documento firmado en formato PDF o Imagen (JPG, PNG, GIF).</p>
                                        
                                        <input type="file" name="declaracion" id="declaracion" class="d-none" accept="image/*,application/pdf">
                                        <label for="declaracion" class="btn btn-outline-success rounded-pill px-4 fw-bold shadow-sm cursor-pointer">
                                            <i class="fas fa-file-pdf me-2"></i> Seleccionar Archivo
                                        </label>
                                        
                                        <div id="declaracion-preview-container" class="mt-4 d-flex justify-content-center align-items-center flex-column">
                                            @if($agenda->legalizacion_declaracion)
                                                @php
                                                    $ext = pathinfo($agenda->legalizacion_declaracion, PATHINFO_EXTENSION);
                                                @endphp
                                                @if(strtolower($ext) === 'pdf')
                                                    <div class="p-3 border rounded-3 bg-white d-flex align-items-center gap-3" style="max-width: 400px;">
                                                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                        <div class="text-start">
                                                            <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">declaracion_guardada.pdf</div>
                                                            <a href="{{ asset('storage/' . $agenda->legalizacion_declaracion) }}" target="_blank" class="small text-success fw-bold text-decoration-none">Ver Archivo Guardado</a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $agenda->legalizacion_declaracion) }}" class="preview-card img-fluid" style="max-height: 200px; width: auto;" alt="Declaración Guardada">
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/' . $agenda->legalizacion_declaracion) }}" target="_blank" class="small text-success fw-bold text-decoration-none">Ver Imagen Completa</a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-text text-muted text-center" id="declaracion-counter-text">
                                        @if($agenda->legalizacion_declaracion)
                                            <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Ya cuenta con un archivo guardado. Puede subir uno nuevo para reemplazarlo.</span>
                                        @else
                                            Ningún archivo seleccionado (Opcional).
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 9. COMPROBANTE DE GASTOS DE TRANSPORTE INFORMAL (GRF-F-076) --}}
                        <div class="col-12 mt-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                    <span class="step-badge me-3">{{ $step + 1 }}</span>
                                    <h5 class="fw-bold mb-0 text-dark">Comprobante de Gastos de Transporte Informal (Formato GRF-F-076)</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <p class="text-muted small mb-4">Complete esta sección para justificar el uso de transporte informal durante sus traslados. Esta información generará el formato oficial GRF-F-076.</p>
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Código Regional</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                                <input type="text" name="legalizacion_codigo_regional" id="legalizacion_codigo_regional"
                                                    class="form-control custom-input bg-light"
                                                    value="{{ old('legalizacion_codigo_regional', $agenda->legalizacion_codigo_regional ?? '') }}"
                                                    placeholder="Ej: 5">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Código Centro</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i class="fas fa-building text-muted"></i></span>
                                                <input type="text" name="legalizacion_codigo_centro" id="legalizacion_codigo_centro"
                                                    class="form-control custom-input bg-light"
                                                    value="{{ old('legalizacion_codigo_centro', $agenda->legalizacion_codigo_centro ?? '') }}"
                                                    placeholder="Ej: 9206">
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $possibleTrayectos = [];
                                        if ($agenda->destinos) {
                                            foreach ($agenda->destinos as $dest) {
                                                if (!empty($dest['nombre'])) {
                                                    $destNom = strtoupper(trim($dest['nombre']));
                                                    $possibleTrayectos[] = "MEDELLIN-{$destNom}";
                                                    $possibleTrayectos[] = "{$destNom}-MEDELLIN";
                                                }
                                            }
                                        }
                                        if (!empty($agenda->ruta)) {
                                            $partes = array_map('trim', explode('-', $agenda->ruta));
                                            for ($i = 0; $i < count($partes) - 1; $i++) {
                                                $part1 = strtoupper($partes[$i]);
                                                $part2 = strtoupper($partes[$i+1]);
                                                if ($part1 !== $part2) {
                                                    $possibleTrayectos[] = "{$part1}-{$part2}";
                                                }
                                            }
                                        }
                                        $possibleTrayectos = array_unique(array_filter($possibleTrayectos));
                                        if (empty($possibleTrayectos)) {
                                            $possibleTrayectos = ['MEDELLIN-GIRARDOTA', 'GIRARDOTA-MEDELLIN'];
                                        }
                                    @endphp

                                    <datalist id="trayectos-list">
                                        @foreach($possibleTrayectos as $t)
                                            <option value="{{ $t }}">
                                        @endforeach
                                    </datalist>

                                    <div class="table-responsive">
                                        <table class="table table-borderless align-middle mb-0" id="gastos-transporte-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;" class="text-muted small text-uppercase">#</th>
                                                    <th style="width: 25%;" class="text-muted small text-uppercase">Fecha</th>
                                                    <th style="width: 35%;" class="text-muted small text-uppercase">Trayecto Generador del Pago (Ruta / Tramo)</th>
                                                    <th style="width: 15%;" class="text-muted small text-uppercase">Medio Transporte</th>
                                                    <th style="width: 15%;" class="text-muted small text-uppercase">Valor Pagado ($)</th>
                                                    <th style="width: 5%;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="gastos-transporte-container">
                                                @php
                                                    $gastos = old('gastos_transporte', $agenda->legalizacion_gastos_transporte ?? []);
                                                @endphp
                                                @if(!empty($gastos))
                                                    @foreach($gastos as $index => $gasto)
                                                        <tr class="gasto-row animate__animated animate__fadeIn">
                                                            <td class="fw-bold text-muted gasto-index">{{ $index + 1 }}.</td>
                                                            <td data-label="Fecha">
                                                                <input type="date" name="gastos_transporte[{{ $index }}][fecha]" 
                                                                    class="form-control custom-input bg-light" 
                                                                    value="{{ $gasto['fecha'] ?? '' }}" 
                                                                    min="{{ $agenda->fecha_inicio?->format('Y-m-d') }}" 
                                                                    max="{{ $agenda->fecha_fin?->format('Y-m-d') }}">
                                                            </td>
                                                            <td data-label="Trayecto">
                                                                <input type="text" name="gastos_transporte[{{ $index }}][trayecto]" 
                                                                    class="form-control custom-input bg-light" 
                                                                    value="{{ $gasto['trayecto'] ?? '' }}" 
                                                                    placeholder="Ej: MEDELLIN-GIRARDOTA" 
                                                                    list="trayectos-list">
                                                            </td>
                                                            <td data-label="Medio Transporte">
                                                                <select name="gastos_transporte[{{ $index }}][medio]" class="form-select custom-input bg-light">
                                                                    <option value="BUS" {{ ($gasto['medio'] ?? '') === 'BUS' ? 'selected' : '' }}>BUS</option>
                                                                    <option value="BARCO" {{ ($gasto['medio'] ?? '') === 'BARCO' ? 'selected' : '' }}>BARCO</option>
                                                                    <option value="AVION" {{ ($gasto['medio'] ?? '') === 'AVION' ? 'selected' : '' }}>AVIÓN</option>
                                                                </select>
                                                            </td>
                                                            <td data-label="Valor Pagado ($)">
                                                                <input type="number" name="gastos_transporte[{{ $index }}][valor]" 
                                                                    class="form-control custom-input bg-light input-valor-gasto" 
                                                                    value="{{ $gasto['valor'] ?? '' }}" 
                                                                    placeholder="Valor" min="0">
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-gasto" style="width: 38px; height: 38px;" title="Eliminar fila">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr class="gasto-row animate__animated animate__fadeIn">
                                                        <td class="fw-bold text-muted gasto-index">1.</td>
                                                        <td data-label="Fecha">
                                                            <input type="date" name="gastos_transporte[0][fecha]" 
                                                                class="form-control custom-input bg-light" 
                                                                value="" 
                                                                min="{{ $agenda->fecha_inicio?->format('Y-m-d') }}" 
                                                                max="{{ $agenda->fecha_fin?->format('Y-m-d') }}">
                                                        </td>
                                                        <td data-label="Trayecto">
                                                            <input type="text" name="gastos_transporte[0][trayecto]" 
                                                                class="form-control custom-input bg-light" 
                                                                value="" 
                                                                placeholder="Ej: MEDELLIN-GIRARDOTA" 
                                                                list="trayectos-list">
                                                        </td>
                                                        <td data-label="Medio Transporte">
                                                            <select name="gastos_transporte[0][medio]" class="form-select custom-input bg-light">
                                                                <option value="BUS" selected>BUS</option>
                                                                <option value="BARCO">BARCO</option>
                                                                <option value="AVION">AVIÓN</option>
                                                            </select>
                                                        </td>
                                                        <td data-label="Valor Pagado ($)">
                                                            <input type="number" name="gastos_transporte[0][valor]" 
                                                                class="form-control custom-input bg-light input-valor-gasto" 
                                                                value="" 
                                                                placeholder="Valor" min="0">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-gasto" style="width: 38px; height: 38px;" title="Eliminar fila">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                        <button type="button" id="btn-add-gasto" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
                                            <i class="fas fa-plus me-1"></i> Agregar Gasto de Transporte
                                        </button>
                                        <div class="fs-5 fw-bold text-dark">
                                            Total Transporte Informal: <span class="text-success" id="total-gastos-label">$ 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- GRUPO TIQUETES --}}
                    <div id="grupo-tiquetes-no" class="row g-4 m-0 p-0 col-12" style="display: none;">
                        <div class="col-12 mt-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                    <span class="step-badge me-3">{{ $step }}</span>
                                    <h5 class="fw-bold mb-0 text-dark">Adjuntar Tiquetes del Trayecto</h5>
                                    </div>
                                    <div class="card-body p-4 pt-0">
                                        <div class="bg-light p-4 rounded-4 border text-center mb-3">
                                            <div class="text-success mb-3">
                                                <i class="fas fa-ticket-alt fa-3x"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-2">Seleccione los tiquetes de su trayecto</h6>
                                            <p class="small text-muted mb-3">Suba los tiquetes de bus, peajes u otros documentos soporte en formato de Imagen (JPG, PNG, GIF).</p>
                                            
                                            <input type="file" name="tiquetes[]" id="tiquetes" class="d-none" accept="image/*" multiple>
                                            <label for="tiquetes" class="btn btn-outline-success rounded-pill px-4 fw-bold shadow-sm cursor-pointer">
                                                <i class="fas fa-upload me-2"></i> Seleccionar Tiquetes
                                            </label>
                                            
                                            <div id="tiquetes-preview-container" class="row g-2 mt-4 justify-content-center"></div>
                                        </div>
                                        <div class="form-text text-muted text-center" id="tiquetes-counter-text">
                                            @if($agenda->legalizacion_tiquetes)
                                                <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Ya cuenta con tiquetes guardados. Puede subir nuevos para reemplazarlos.</span>
                                                <div class="d-flex flex-wrap gap-3 justify-content-center mt-3">
                                                     @foreach($agenda->legalizacion_tiquetes as $tiquete)
                                                         @php $ext = pathinfo($tiquete, PATHINFO_EXTENSION); @endphp
                                                         <div class="position-relative text-center m-1 animate__animated animate__fadeIn" style="width: 100px;">
                                                             <div class="position-relative text-center">
                                                                 @if(strtolower($ext) === 'pdf')
                                                                     <div class="d-flex flex-column align-items-center justify-content-center border rounded-3 bg-white" style="height: 100px; width: 100px; margin: 0 auto;">
                                                                         <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                                                     </div>
                                                                 @else
                                                                     <img src="{{ asset('storage/' . $tiquete) }}" class="preview-card img-fluid rounded-3 border" style="height: 100px; width: 100px; object-fit: cover; margin: 0 auto;" alt="Tiquete Guardado">
                                                                 @endif
                                                                 <div class="mt-1 text-center">
                                                                     <a href="{{ asset('storage/' . $tiquete) }}" target="_blank" class="small text-success fw-bold text-decoration-none" style="font-size: 0.75rem;">Ver Tiquete {{ $loop->iteration }}</a>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     @endforeach
                                                 </div>
                                            @else
                                                Ningún tiquete seleccionado.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    {{-- BOTONES ACCIÓN --}}
                    <div class="col-12 mt-4 mb-5">
                        <div class="d-flex flex-column flex-md-row gap-3">
                            <a href="{{ route('reportes') }}" class="btn btn-outline-secondary btn-lg flex-grow-1 rounded-4 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center">
                                <i class="fas fa-times me-2"></i> Cancelar
                            </a>
                            <button type="submit" id="btn-submit" class="btn btn-success btn-lg flex-grow-2 rounded-4 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center">
                                <i class="fas fa-file-invoice-dollar me-2"></i> Registrar y Guardar Legalización
                            </button>
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

    .cursor-pointer {
        cursor: pointer;
    }

    .preview-card {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.2s;
    }

    .preview-card:hover {
        transform: scale(1.05);
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

    /* --- RESPONSIVE OPTIMIZATIONS FOR MOBILE & TABLETS --- */
    @media (max-width: 576px) {
        .container-fluid {
            padding: 1.5rem 0.75rem !important;
        }
        .card-body {
            padding: 1.25rem !important;
        }
        .card-header {
            padding: 1.1rem 1.25rem !important;
        }
        .custom-input {
            padding: 0.65rem 0.85rem;
            font-size: 0.95rem;
        }
        .input-group-text {
            padding: 0.65rem 0.8rem;
        }
        .step-badge {
            width: 26px;
            height: 26px;
            min-width: 26px;
            font-size: 0.75rem;
            margin-right: 0.5rem !important;
        }
        .card-header h5 {
            font-size: 0.95rem;
            line-height: 1.25;
        }
        .form-label {
            font-size: 0.65rem !important;
            margin-bottom: 0.3rem !important;
        }
        .card {
            margin-bottom: 1rem !important;
        }
        .row.g-4 {
            --bs-gutter-y: 1.5rem;
        }
        .d-flex.flex-column.flex-md-row.gap-3 button {
            width: 100% !important;
            padding: 1rem !important;
            font-size: 0.95rem !important;
        }
    }

    /* Stackable Dynamic Card-Tables on Small Screens */
    @media (max-width: 767.98px) {
        #compromisos-table, #compromisos-table thead, #compromisos-table tbody, #compromisos-table th, #compromisos-table td, #compromisos-table tr,
        #gastos-transporte-table, #gastos-transporte-table thead, #gastos-transporte-table tbody, #gastos-transporte-table th, #gastos-transporte-table td, #gastos-transporte-table tr {
            display: block;
            width: 100% !important;
        }
        
        #compromisos-table thead, #gastos-transporte-table thead {
            display: none; /* Ocultar cabeceras reales de tabla */
        }
        
        #compromisos-table tr, #gastos-transporte-table tr {
            border: 2px solid #e2e8f0;
            border-radius: 1.2rem;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            background-color: #f8fafc;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        
        #compromisos-table td, #gastos-transporte-table td {
            border: none;
            padding: 0.5rem 0 !important;
            position: relative;
            width: 100% !important;
        }
        
        #compromisos-table td::before, #gastos-transporte-table td::before {
            content: attr(data-label);
            display: block;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            color: #64748b;
            margin-bottom: 0.35rem;
        }
        
        /* Modificadores estéticos para celda de índice */
        #compromisos-table td.compromiso-index, #gastos-transporte-table td.gasto-index {
            font-size: 1.1rem;
            color: #39a900;
            padding-bottom: 0.3rem !important;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 0.75rem;
            font-weight: 800;
        }
        
        #compromisos-table td.compromiso-index::before, #gastos-transporte-table td.gasto-index::before {
            content: "REGISTRO";
            font-size: 0.7rem;
            display: inline-block;
            margin-right: 0.5rem;
            margin-bottom: 0;
            font-weight: 700;
        }
        
        /* Ocultar etiquetas adicionales no deseadas */
        #compromisos-table td:last-child::before, #gastos-transporte-table td:last-child::before {
            display: none;
        }
        
        #compromisos-table td:last-child, #gastos-transporte-table td:last-child {
            text-align: right;
            padding-top: 0.75rem !important;
            border-top: 1px dashed #cbd5e1;
            margin-top: 0.5rem;
        }
    }

    /* Estilos de validación inline */
    .invalid-feedback-custom {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
        font-weight: 500;
        text-align: left;
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
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fallback robusto para Swal en caso de que falle la carga del CDN
        const Swal = window.Swal || {
            fire: function(options) {
                let msg = options.text || options.html || '';
                if (options.html) {
                    const temp = document.createElement('div');
                    temp.innerHTML = options.html;
                    msg = temp.textContent || temp.innerText || '';
                }
                alert((options.title || 'Atención') + '\n\n' + msg);
            }
        };

        const fotosInput = document.getElementById('fotos');
        const fotosPreview = document.getElementById('fotos-preview-container');
        const fotosCounter = document.getElementById('fotos-counter-text');

        const planillasInput = document.getElementById('planillas');
        const planillasPreview = document.getElementById('planillas-preview-container');
        const planillasCounter = document.getElementById('planillas-counter-text');

        const declaracionInput = document.getElementById('declaracion');
        const declaracionPreview = document.getElementById('declaracion-preview-container');
        const declaracionCounter = document.getElementById('declaracion-counter-text');

        const tiquetesInput = document.getElementById('tiquetes');
        const tiquetesPreview = document.getElementById('tiquetes-preview-container');
        const tiquetesCounter = document.getElementById('tiquetes-counter-text');

        const radioSi = document.getElementById('realiza_declaracion_si');
        const radioNo = document.getElementById('realiza_declaracion_no');
        const grupoDeclaracionSi = document.getElementById('grupo-declaracion-si');
        const grupoTiquetesNo = document.getElementById('grupo-tiquetes-no');

        const form = document.getElementById('legalizacion-form');

        // Toggle sections based on selection
        function toggleSections() {
            if (radioSi && radioNo) {
                if (radioSi.checked) {
                    grupoDeclaracionSi.style.display = 'flex';
                    grupoTiquetesNo.style.display = 'none';
                } else if (radioNo.checked) {
                    grupoDeclaracionSi.style.display = 'none';
                    grupoTiquetesNo.style.display = 'flex';
                } else {
                    grupoDeclaracionSi.style.display = 'none';
                    grupoTiquetesNo.style.display = 'none';
                }
            }
        }

        if (radioSi && radioNo) {
            radioSi.addEventListener('change', toggleSections);
            radioNo.addEventListener('change', toggleSections);
            toggleSections(); // Run initially
        }

        // Manejador de previsualización para fotos
        if (fotosInput) {
            fotosInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                const maxSizeBytes = 10 * 1024 * 1024; // 10MB
                let tooLarge = false;
                let notAnImage = false;
                files.forEach(file => {
                    if (file.size > maxSizeBytes) tooLarge = true;
                    if (!file.type.startsWith('image/')) notAnImage = true;
                });
                if (tooLarge) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo demasiado grande',
                        text: 'Cada foto de evidencia no debe superar los 10 MB.',
                        confirmButtonColor: '#39a900'
                    });
                    fotosInput.value = '';
                    return;
                }
                if (notAnImage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formato no permitido',
                        text: 'Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF) para las evidencias fotográficas.',
                        confirmButtonColor: '#39a900'
                    });
                    fotosInput.value = '';
                    return;
                }

                document.querySelector('label[for="fotos"]').classList.remove('border-danger', 'text-danger');
                clearError(document.getElementById('fotos-counter-text'));
                fotosPreview.innerHTML = '';
                const minFotos = 1;
                const minFotosText = '1';
                
                if (files.length > 0) {
                    if (files.length < minFotos) {
                        fotosCounter.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Seleccionó ${files.length} imagen(es). Se requiere mínimo ${minFotosText}.</span>`;
                    } else {
                        fotosCounter.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> ¡Perfecto! Seleccionó ${files.length} imágenes.</span>`;
                    }
                    
                    files.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'preview-card m-1 animate__animated animate__fadeIn';
                            fotosPreview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    const hasExisting = {{ !empty($agenda->legalizacion_fotos) ? 'true' : 'false' }};
                    if (hasExisting) {
                        fotosCounter.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Ya cuenta con imágenes guardadas. Puede subir nuevas para reemplazarlas.</span>`;
                    } else {
                        fotosCounter.textContent = `Ninguna imagen seleccionada. Se requiere mínimo ${minFotosText}.`;
                    }
                }
            });
        }

        // Manejador de previsualización para planillas
        if (planillasInput) {
            planillasInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                const maxSizeBytes = 10 * 1024 * 1024; // 10MB
                let tooLarge = false;
                let notAnImage = false;
                files.forEach(file => {
                    if (file.size > maxSizeBytes) tooLarge = true;
                    if (!file.type.startsWith('image/')) notAnImage = true;
                });
                if (tooLarge) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo demasiado grande',
                        text: 'Cada planilla no debe superar los 10 MB.',
                        confirmButtonColor: '#39a900'
                    });
                    planillasInput.value = '';
                    return;
                }
                if (notAnImage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formato no permitido',
                        text: 'Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF) para las planillas.',
                        confirmButtonColor: '#39a900'
                    });
                    planillasInput.value = '';
                    return;
                }

                document.querySelector('label[for="planillas"]').classList.remove('border-danger', 'text-danger');
                clearError(document.getElementById('planillas-counter-text'));
                planillasPreview.innerHTML = '';
                const minPlanillas = 1;
                
                if (files.length > 0) {
                    if (files.length < minPlanillas) {
                        planillasCounter.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Seleccionó ${files.length} planilla(s). Se requiere mínimo ${minPlanillas}.</span>`;
                    } else {
                        planillasCounter.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> ¡Perfecto! Seleccionó ${files.length} planillas.</span>`;
                    }
                    
                    files.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'preview-card m-1 animate__animated animate__fadeIn';
                            planillasPreview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    const hasExisting = {{ !empty($agenda->legalizacion_planillas) ? 'true' : 'false' }};
                    if (hasExisting) {
                        planillasCounter.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Ya cuenta con planillas guardadas. Puede subir nuevas para reemplazarlas.</span>`;
                    } else {
                        planillasCounter.textContent = `Ninguna planilla seleccionada. Se requiere mínimo ${minPlanillas}.`;
                    }
                }
            });
        }

        // Manejador de previsualización para declaración no juramentada
        if (declaracionInput) {
            declaracionInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const maxSizeBytes = 10 * 1024 * 1024; // 10MB
                    if (file.size > maxSizeBytes) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Archivo demasiado grande',
                            text: 'El archivo de declaración no debe superar los 10 MB.',
                            confirmButtonColor: '#39a900'
                        });
                        declaracionInput.value = '';
                        return;
                    }
                }

                declaracionPreview.innerHTML = '';
                if (file) {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    declaracionCounter.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Archivo seleccionado: ${file.name} (${fileSizeMB} MB)</span>`;
                    
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'position-relative animate__animated animate__fadeIn';
                            wrapper.innerHTML = `
                                <img src="${e.target.result}" class="preview-card img-fluid" style="max-height: 200px; width: auto;" alt="Declaración Seleccionada">
                            `;
                            declaracionPreview.appendChild(wrapper);
                        };
                        reader.readAsDataURL(file);
                    } else if (file.type === 'application/pdf') {
                        const pdfCard = document.createElement('div');
                        pdfCard.className = 'p-3 border rounded-3 bg-white d-flex align-items-center gap-3 animate__animated animate__fadeIn';
                        pdfCard.style.maxWidth = '400px';
                        pdfCard.innerHTML = `
                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                            <div class="text-start">
                                <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">${file.name}</div>
                                <span class="small text-muted">Documento PDF listo para subir</span>
                            </div>
                        `;
                        declaracionPreview.appendChild(pdfCard);
                    } else {
                        declaracionCounter.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> El tipo de archivo no es compatible. Suba PDF o Imagen.</span>`;
                        declaracionInput.value = '';
                    }
                } else {
                    const hasExisting = {{ !empty($agenda->legalizacion_declaracion) ? 'true' : 'false' }};
                    if (hasExisting) {
                        declaracionCounter.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Ya cuenta con un archivo guardado. Puede subir uno nuevo para reemplazarlo.</span>`;
                    } else {
                        declaracionCounter.textContent = 'Ningún archivo seleccionado (Opcional).';
                    }
                }
            });
        }

        // Manejador de previsualización para tiquetes
        if (tiquetesInput) {
            tiquetesInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                const maxSizeBytes = 10 * 1024 * 1024; // 10MB
                let tooLarge = false;
                let notAnImage = false;
                
                files.forEach(file => {
                    if (file.size > maxSizeBytes) tooLarge = true;
                    if (!file.type.startsWith('image/')) notAnImage = true;
                });
                
                if (tooLarge) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo demasiado grande',
                        text: 'Cada tiquete no debe superar los 10 MB.',
                        confirmButtonColor: '#39a900'
                    });
                    tiquetesInput.value = '';
                    return;
                }
                
                if (notAnImage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formato no permitido',
                        text: 'Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF). No se admiten archivos PDF u otros formatos.',
                        confirmButtonColor: '#39a900'
                    });
                    tiquetesInput.value = '';
                    return;
                }

                document.querySelector('label[for="tiquetes"]').classList.remove('border-danger', 'text-danger');
                clearError(document.getElementById('tiquetes-counter-text'));
                tiquetesPreview.innerHTML = '';
                
                if (files.length > 0) {
                    tiquetesCounter.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> ¡Perfecto! Seleccionó ${files.length} tiquete(s).</span>`;
                    
                    files.forEach(file => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'preview-card m-1 animate__animated animate__fadeIn';
                                tiquetesPreview.appendChild(img);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                } else {
                    const hasExisting = {{ !empty($agenda->legalizacion_tiquetes) ? 'true' : 'false' }};
                    if (hasExisting) {
                        tiquetesCounter.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Ya cuenta con tiquetes guardados. Puede subir nuevos para reemplazarlos.</span>`;
                    } else {
                        tiquetesCounter.textContent = 'Ningún tiquete seleccionado.';
                    }
                }
            });
        }

        // --- RESULTADOS ---
        const resultadosContainer = document.getElementById('resultados-container');
        const btnAddResultado = document.getElementById('btn-add-resultado');

        function updateResultadosIndices() {
            const items = resultadosContainer.querySelectorAll('.resultado-item');
            items.forEach((item, index) => {
                item.querySelector('.min-w-20').textContent = `${index + 1}.`;
            });
        }

        btnAddResultado.addEventListener('click', function() {
            const index = resultadosContainer.querySelectorAll('.resultado-item').length;
            const itemHtml = `
                <div class="resultado-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                    <span class="fw-bold text-muted min-w-20">${index + 1}.</span>
                    <input type="text" name="resultados[]" class="form-control custom-input bg-light" placeholder="Ej: Se desarrolló sin novedad la actividad de aprendizaje..." required>
                    <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-resultado" style="width: 38px; height: 38px;" title="Eliminar resultado">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>`;
            resultadosContainer.insertAdjacentHTML('beforeend', itemHtml);
        });

        resultadosContainer.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-remove-resultado');
            if (btn) {
                if (resultadosContainer.querySelectorAll('.resultado-item').length > 1) {
                    btn.closest('.resultado-item').remove();
                    updateResultadosIndices();
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Atención',
                        text: 'Debe ingresar al menos un resultado.',
                        confirmButtonColor: '#39a900'
                    });
                }
            }
        });

        // --- COMPROMISOS ---
        const compromisosContainer = document.getElementById('compromisos-container');
        const btnAddCompromiso = document.getElementById('btn-add-compromiso');


        function updateCompromisosIndices() {
            const rows = compromisosContainer.querySelectorAll('.compromiso-row');
            rows.forEach((row, index) => {
                row.querySelector('.compromiso-index').textContent = `${index + 1}.`;
                row.querySelector('textarea').name = `compromisos[${index}][actividad]`;
                row.querySelector('input[type="text"]').name = `compromisos[${index}][responsable]`;
                row.querySelector('input[type="date"]').name = `compromisos[${index}][fecha]`;
            });
        }

        if (btnAddCompromiso && compromisosContainer) {
            btnAddCompromiso.addEventListener('click', function() {
                const index = compromisosContainer.querySelectorAll('.compromiso-row').length;
                const rowHtml = `
                    <tr class="compromiso-row animate__animated animate__fadeIn">
                        <td class="fw-bold text-muted compromiso-index">${index + 1}.</td>
                        <td data-label="Actividad">
                            <textarea name="compromisos[${index}][actividad]" class="form-control custom-input bg-light" rows="2" placeholder="Ej: Se desarrolló actividad de socialización..." required></textarea>
                        </td>
                        <td data-label="Responsable">
                            <input type="text" name="compromisos[${index}][responsable]" class="form-control custom-input bg-light" value="{{ $agenda->user->name ?? '' }}" placeholder="Nombre del responsable" required>
                        </td>
                        <td data-label="Fecha Límite">
                            <input type="date" name="compromisos[${index}][fecha]" class="form-control custom-input bg-light" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-compromiso" style="width: 38px; height: 38px;" title="Eliminar compromiso">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>`;
                compromisosContainer.insertAdjacentHTML('beforeend', rowHtml);
            });

            compromisosContainer.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-remove-compromiso');
                if (btn) {
                    if (compromisosContainer.querySelectorAll('.compromiso-row').length > 1) {
                        btn.closest('.compromiso-row').remove();
                        updateCompromisosIndices();
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Atención',
                            text: 'Debe ingresar al menos un compromiso.',
                            confirmButtonColor: '#39a900'
                        });
                    }
                }
            });
        }

        // --- CONCLUSIONES ---
        const conclusionesContainer = document.getElementById('conclusiones-container');
        const btnAddConclusion = document.getElementById('btn-add-conclusion');

        function updateConclusionesIndices() {
            if (conclusionesContainer) {
                const items = conclusionesContainer.querySelectorAll('.conclusion-item');
                items.forEach((item, index) => {
                    item.querySelector('.min-w-20').textContent = `${index + 1}.`;
                });
            }
        }

        if (btnAddConclusion && conclusionesContainer) {
            btnAddConclusion.addEventListener('click', function() {
                const index = conclusionesContainer.querySelectorAll('.conclusion-item').length;
                const itemHtml = `
                    <div class="conclusion-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                        <span class="fw-bold text-muted min-w-20">${index + 1}.</span>
                        <input type="text" name="conclusiones[]" class="form-control custom-input bg-light" placeholder="Ej: Se cumplieron las sesiones planificadas..." required>
                        <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-conclusion" style="width: 38px; height: 38px;" title="Eliminar conclusión">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>`;
                conclusionesContainer.insertAdjacentHTML('beforeend', itemHtml);
            });

            conclusionesContainer.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-remove-conclusion');
                if (btn) {
                    if (conclusionesContainer.querySelectorAll('.conclusion-item').length > 1) {
                        btn.closest('.conclusion-item').remove();
                        updateConclusionesIndices();
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Atención',
                            text: 'Debe ingresar al menos una conclusión.',
                            confirmButtonColor: '#39a900'
                        });
                    }
                }
            });
        }

        // --- SOPORTES DE DESPLAZAMIENTO ---
        const soportesContainer = document.getElementById('soportes-container');
        const btnAddSoporte = document.getElementById('btn-add-soporte');

        if (btnAddSoporte && soportesContainer) {
            btnAddSoporte.addEventListener('click', function() {
                const itemHtml = `
                    <div class="soporte-item d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                        <span class="text-success fw-bold fs-4">•</span>
                        <input type="text" name="soportes_desplazamiento[]" class="form-control custom-input bg-light" placeholder="Ej: Anexe los pasabordos y/o tiquetes terrestres..." required>
                        <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-soporte" style="width: 38px; height: 38px;" title="Eliminar soporte">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>`;
                soportesContainer.insertAdjacentHTML('beforeend', itemHtml);
            });

            soportesContainer.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-remove-soporte');
                if (btn) {
                    if (soportesContainer.querySelectorAll('.soporte-item').length > 1) {
                        btn.closest('.soporte-item').remove();
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Atención',
                            text: 'Debe ingresar al menos un soporte de desplazamiento.',
                            confirmButtonColor: '#39a900'
                        });
                    }
                }
            });
        }

        // --- GASTOS DE TRANSPORTE INFORMAL (GRF-F-076) ---
        const gastosContainer = document.getElementById('gastos-transporte-container');
        const btnAddGasto = document.getElementById('btn-add-gasto');
        const totalGastosLabel = document.getElementById('total-gastos-label');

        function formatCurrency(value) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        }

        function calculateTotalGastos() {
            let total = 0;
            if (gastosContainer) {
                gastosContainer.querySelectorAll('.input-valor-gasto').forEach(input => {
                    const val = parseFloat(input.value) || 0;
                    total += val;
                });
            }
            if (totalGastosLabel) {
                totalGastosLabel.textContent = formatCurrency(total);
            }
        }

        function updateGastosIndices() {
            const rows = gastosContainer.querySelectorAll('.gasto-row');
            rows.forEach((row, index) => {
                row.querySelector('.gasto-index').textContent = `${index + 1}.`;
                row.querySelector('input[type="date"]').name = `gastos_transporte[${index}][fecha]`;
                row.querySelector('input[type="text"]').name = `gastos_transporte[${index}][trayecto]`;
                row.querySelector('select').name = `gastos_transporte[${index}][medio]`;
                row.querySelector('.input-valor-gasto').name = `gastos_transporte[${index}][valor]`;
            });
            calculateTotalGastos();
        }

        if (btnAddGasto) {
            btnAddGasto.addEventListener('click', function() {
                const index = gastosContainer.querySelectorAll('.gasto-row').length;
                const rowHtml = `
                    <tr class="gasto-row animate__animated animate__fadeIn">
                        <td class="fw-bold text-muted gasto-index">${index + 1}.</td>
                        <td data-label="Fecha">
                            <input type="date" name="gastos_transporte[${index}][fecha]" 
                                class="form-control custom-input bg-light" 
                                value="" 
                                min="{{ $agenda->fecha_inicio?->format('Y-m-d') }}" 
                                max="{{ $agenda->fecha_fin?->format('Y-m-d') }}" required>
                        </td>
                        <td data-label="Trayecto">
                            <input type="text" name="gastos_transporte[${index}][trayecto]" 
                                class="form-control custom-input bg-light" 
                                value="" 
                                placeholder="Ej: MEDELLIN-GIRARDOTA" 
                                list="trayectos-list" required>
                        </td>
                        <td data-label="Medio Transporte">
                            <select name="gastos_transporte[${index}][medio]" class="form-select custom-input bg-light" required>
                                <option value="BUS" selected>BUS</option>
                                <option value="BARCO">BARCO</option>
                                <option value="AVION">AVIÓN</option>
                            </select>
                        </td>
                        <td data-label="Valor Pagado ($)">
                            <input type="number" name="gastos_transporte[${index}][valor]" 
                                class="form-control custom-input bg-light input-valor-gasto" 
                                value="" 
                                placeholder="Valor" min="0" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-remove-gasto" style="width: 38px; height: 38px;" title="Eliminar fila">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>`;
                gastosContainer.insertAdjacentHTML('beforeend', rowHtml);
                calculateTotalGastos();
            });
        }

        if (gastosContainer) {
            gastosContainer.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-remove-gasto');
                if (btn) {
                    if (gastosContainer.querySelectorAll('.gasto-row').length > 1) {
                        btn.closest('.gasto-row').remove();
                        updateGastosIndices();
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Atención',
                            text: 'Debe ingresar al menos un gasto de transporte.',
                            confirmButtonColor: '#39a900'
                        });
                    }
                }
            });

            gastosContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('input-valor-gasto')) {
                    calculateTotalGastos();
                }
            });
        }

        // Calcular total inicial
        calculateTotalGastos();


        // --- FUNCIONES AUXILIARES PARA ERRORES INLINE ---
        function showError(element, message) {
            if (!element) return;
            let target = element;
            let insertionPoint = element;

            if (element.closest('.input-group')) {
                insertionPoint = element.closest('.input-group');
            }

            target.classList.add('is-invalid-custom');

            const errorId = `error-${element.id || element.name || Math.random().toString(36).substr(2, 9)}`;
            let errorDiv = insertionPoint.parentNode.querySelector(`.invalid-feedback-custom[data-for="${errorId}"]`);
            
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback-custom';
                errorDiv.setAttribute('data-for', errorId);
                insertionPoint.parentNode.insertBefore(errorDiv, insertionPoint.nextSibling);
            }
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        function clearError(element) {
            if (!element) return;
            let target = element;
            let insertionPoint = element;

            if (element.closest('.input-group')) {
                insertionPoint = element.closest('.input-group');
            }

            target.classList.remove('is-invalid-custom');
            
            const feedbackDivs = insertionPoint.parentNode.querySelectorAll(`.invalid-feedback-custom`);
            feedbackDivs.forEach(div => {
                div.style.display = 'none';
                div.textContent = '';
            });
        }

        function clearAllErrors() {
            document.querySelectorAll('.is-invalid-custom').forEach(el => el.classList.remove('is-invalid-custom'));
            document.querySelectorAll('.invalid-feedback-custom').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });
            document.querySelectorAll('label.border-danger').forEach(el => el.classList.remove('border-danger', 'text-danger'));
        }

        // Limpieza de errores en tiempo real al escribir o cambiar
        document.addEventListener('input', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                clearError(e.target);
            }
        });
        document.addEventListener('change', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                clearError(e.target);
            }
        });

        // Validación antes de enviar el formulario
        form.addEventListener('submit', function(e) {
            const isFuncionario = {{ $isFuncionario ? 'true' : 'false' }};
            const fotosFiles = fotosInput.files.length;
            const planillasFiles = planillasInput.files.length;
            const ordenViajeInput = document.getElementById('orden_viaje');

            clearAllErrors();
            let hasErrors = false;
            let firstErrorElement = null;

            function validateField(condition, element, message) {
                if (condition) {
                    showError(element, message);
                    hasErrors = true;
                    if (!firstErrorElement) firstErrorElement = element;
                }
            }

            // Validar Orden de Viaje
            validateField(!ordenViajeInput.value.trim(), ordenViajeInput, isFuncionario ? 'El No. COMISIÓN DE SERVICIOS es obligatorio.' : 'El número de orden de viaje es obligatorio.');

            // Validar tamaños de archivos seleccionados (Máximo 10 MB)
            const maxSizeBytes = 10 * 1024 * 1024; // 10MB

            if (fotosInput && fotosInput.files.length > 0) {
                let fotosTooLarge = false;
                let fotosNotImage = false;
                Array.from(fotosInput.files).forEach(file => {
                    if (file.size > maxSizeBytes) fotosTooLarge = true;
                    if (!file.type.startsWith('image/')) fotosNotImage = true;
                });
                if (fotosTooLarge) {
                    validateField(true, document.getElementById('fotos-counter-text'), 'Una o más fotos de evidencia superan el límite de 10 MB.');
                    document.querySelector('label[for="fotos"]').classList.add('border-danger', 'text-danger');
                }
                if (fotosNotImage) {
                    validateField(true, document.getElementById('fotos-counter-text'), 'Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF) para las evidencias fotográficas.');
                    document.querySelector('label[for="fotos"]').classList.add('border-danger', 'text-danger');
                }
            }

            if (planillasInput && planillasInput.files.length > 0) {
                let planillasTooLarge = false;
                let planillasNotImage = false;
                Array.from(planillasInput.files).forEach(file => {
                    if (file.size > maxSizeBytes) planillasTooLarge = true;
                    if (!file.type.startsWith('image/')) planillasNotImage = true;
                });
                if (planillasTooLarge) {
                    validateField(true, document.getElementById('planillas-counter-text'), 'Una o más planillas de asistencia superan el límite de 10 MB.');
                    document.querySelector('label[for="planillas"]').classList.add('border-danger', 'text-danger');
                }
                if (planillasNotImage) {
                    validateField(true, document.getElementById('planillas-counter-text'), 'Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF) para las planillas.');
                    document.querySelector('label[for="planillas"]').classList.add('border-danger', 'text-danger');
                }
            }

            if (declaracionInput && declaracionInput.files.length > 0) {
                let declaracionTooLarge = false;
                Array.from(declaracionInput.files).forEach(file => {
                    if (file.size > maxSizeBytes) declaracionTooLarge = true;
                });
                if (declaracionTooLarge) {
                    validateField(true, document.getElementById('declaracion-counter-text'), 'El archivo de declaración no juramentada supera el límite de 10 MB.');
                }
            }

            if (tiquetesInput && tiquetesInput.files.length > 0) {
                let tiquetesTooLarge = false;
                let tiquetesNotImage = false;
                Array.from(tiquetesInput.files).forEach(file => {
                    if (file.size > maxSizeBytes) tiquetesTooLarge = true;
                    if (!file.type.startsWith('image/')) tiquetesNotImage = true;
                });
                if (tiquetesTooLarge) {
                    validateField(true, document.getElementById('tiquetes-counter-text'), 'Uno o más tiquetes superan el límite de 10 MB.');
                    document.querySelector('label[for="tiquetes"]').classList.add('border-danger', 'text-danger');
                }
                if (tiquetesNotImage) {
                    validateField(true, document.getElementById('tiquetes-counter-text'), 'Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF) para los tiquetes.');
                    document.querySelector('label[for="tiquetes"]').classList.add('border-danger', 'text-danger');
                }
            }

            // Validar Evidencias Fotográficas
            const hasFotos = {{ !empty($agenda->legalizacion_fotos) ? 'true' : 'false' }};
            const hasPlanillas = {{ !empty($agenda->legalizacion_planillas) ? 'true' : 'false' }};
            const minFotos = 1;
            const minPlanillas = 1;

            if (fotosFiles < minFotos && !hasFotos) {
                validateField(true, document.getElementById('fotos-counter-text'), 'Debe subir al menos 1 fotografía de evidencia.');
                document.querySelector('label[for="fotos"]').classList.add('border-danger', 'text-danger');
            }

            // Validar Planillas
            if (planillasFiles < minPlanillas && !hasPlanillas) {
                validateField(true, document.getElementById('planillas-counter-text'), 'Debe subir al menos 1 imagen del listado de planillas de asistencia.');
                document.querySelector('label[for="planillas"]').classList.add('border-danger', 'text-danger');
            }

            // Validar Resultados
            const resultadosInputs = Array.from(resultadosContainer.querySelectorAll('input[name="resultados[]"]'));
            if (resultadosInputs.length === 0) {
                validateField(true, resultadosContainer, 'Debe registrar al menos un resultado.');
            } else {
                resultadosInputs.forEach(input => {
                    validateField(!input.value.trim(), input, 'El resultado no puede estar vacío.');
                });
            }

            if (!isFuncionario) {
                // Validar Compromisos
                const compromisosRows = Array.from(compromisosContainer.querySelectorAll('.compromiso-row'));
                if (compromisosRows.length === 0) {
                    validateField(true, compromisosContainer, 'Debe registrar al menos un compromiso.');
                } else {
                    compromisosRows.forEach(row => {
                        const actVal = row.querySelector('textarea');
                        const respVal = row.querySelector('input[type="text"]');
                        const fechaVal = row.querySelector('input[type="date"]');
                        validateField(!actVal.value.trim(), actVal, 'La actividad es obligatoria.');
                        validateField(!respVal.value.trim(), respVal, 'El responsable es obligatorio.');
                        validateField(!fechaVal.value.trim(), fechaVal, 'La fecha límite es obligatoria.');
                    });
                }

                // Validar Conclusiones
                const conclusionesInputs = Array.from(conclusionesContainer.querySelectorAll('input[name="conclusiones[]"]'));
                if (conclusionesInputs.length === 0) {
                    validateField(true, conclusionesContainer, 'Debe registrar al menos una conclusión.');
                } else {
                    conclusionesInputs.forEach(input => {
                        validateField(!input.value.trim(), input, 'La conclusión no puede estar vacía.');
                    });
                }
            }

            if (isFuncionario) {
                // Validar Clasificación
                const clasificacion = document.querySelector('input[name="clasificacion_id"]:checked');
                validateField(!clasificacion, document.querySelector('input[name="clasificacion_id"]'), 'Seleccione una clasificación de la información.');

                // Validar Soportes de Desplazamiento
                if (soportesContainer) {
                    const soportesInputs = Array.from(soportesContainer.querySelectorAll('input[name="soportes_desplazamiento[]"]'));
                    if (soportesInputs.length === 0) {
                        validateField(true, soportesContainer, 'Debe registrar al menos un soporte de desplazamiento.');
                    } else {
                        soportesInputs.forEach(input => {
                            validateField(!input.value.trim(), input, 'El soporte de desplazamiento no puede estar vacío.');
                        });
                    }
                }
            }

            // Validaciones condicionales según la pregunta (aplica para funcionarios y contratistas)
            const realizaDeclaracion = document.querySelector('input[name="realiza_declaracion"]:checked');
            if (!realizaDeclaracion) {
                validateField(true, document.querySelector('input[name="realiza_declaracion"]').closest('.d-flex'), 'Debe responder si desea realizar la declaración no juramentada de transporte informal.');
            } else if (realizaDeclaracion.value === '1') {
                // Validar código regional y de centro
                const regional = document.getElementById('legalizacion_codigo_regional');
                const centro = document.getElementById('legalizacion_codigo_centro');
                validateField(!regional.value.trim(), regional, 'El Código Regional es obligatorio.');
                validateField(!centro.value.trim(), centro, 'El Código de Centro es obligatorio.');

                // Validar Gastos de Transporte
                const gastosRows = Array.from(gastosContainer.querySelectorAll('.gasto-row'));
                if (gastosRows.length === 0) {
                    validateField(true, gastosContainer, 'Debe registrar al menos un gasto de transporte informal.');
                } else {
                    gastosRows.forEach(row => {
                        const fechaVal = row.querySelector('input[type="date"]');
                        const trayectoVal = row.querySelector('input[type="text"]');
                        const valorVal = row.querySelector('.input-valor-gasto');
                        validateField(!fechaVal.value.trim(), fechaVal, 'La fecha es obligatoria.');
                        validateField(!trayectoVal.value.trim(), trayectoVal, 'El trayecto es obligatorio.');
                        validateField(!valorVal.value.trim(), valorVal, 'El valor es obligatorio.');
                    });
                }
            } else {
                // Validar que haya subido tiquetes
                const tiquetesFiles = tiquetesInput.files.length;
                const hasExistingTiquetes = {{ !empty($agenda->legalizacion_tiquetes) ? 'true' : 'false' }};
                if (tiquetesFiles === 0 && !hasExistingTiquetes) {
                    validateField(true, document.getElementById('tiquetes-counter-text'), 'Debe adjuntar al menos un tiquete de viaje.');
                    document.querySelector('label[for="tiquetes"]').classList.add('border-danger', 'text-danger');
                }
            }

            if (hasErrors) {
                e.preventDefault();
                if (firstErrorElement) {
                    firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan Requisitos',
                    text: 'Por favor complete todos los campos obligatorios marcados en rojo antes de enviar.',
                    confirmButtonColor: '#39a900',
                    confirmButtonText: 'Entendido'
                });
            }
        });
    });
</script>
@endpush
@endsection
