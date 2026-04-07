@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid px-4 py-5">
        <div class="row justify-content-center">
            <div class="col-xxl-10">

                {{-- Encabezado de Página --}}
                <div class="d-flex align-items-center justify-content-between mb-5 animate__animated animate__fadeIn">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-4 me-4">
                            <i class="fas fa-file-alt fa-2x text-success"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1 text-dark">Reportar Actividades</h2>
                            <p class="text-muted mb-0">Agenda #{{ $agenda->id }} - {{ $agenda->ruta }}</p>
                        </div>
                    </div>
                    <a href="{{ auth()->user()->role == 'contratista' ? route('reportar-dia') : route('reportes') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold hover-grow">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 p-4 animate__animated animate__fadeInUp">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3 fa-lg"></i>
                            <h6 class="fw-bold mb-0">{{ session('success') }}</h6>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-exclamation-circle text-danger me-2"></i>
                            <h6 class="fw-bold mb-0">Errores de validación:</h6>
                        </div>
                        <ul class="mb-0 ps-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-4">
                    {{-- Formulario de Reporte --}}
                    @if(in_array(auth()->user()->role, ['contratista', 'administrador']))
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp">
                            <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                                <div class="bg-success p-2 rounded-3 me-3">
                                    <i class="fas fa-calendar-plus text-white"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark">Nueva Actividad</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <form method="POST" action="{{ route('agenda.actividad.store', $agenda->id) }}" novalidate>
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small text-uppercase">Día a reportar</label>
                                                    <input type="date" name="fecha" class="form-control custom-input" 
                                                           min="{{ $proximaFecha }}" max="{{ $agenda->fecha_fin->format('Y-m-d') }}" 
                                                           value="{{ old('fecha', $proximaFecha) }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        @php
                                            $rutaPartes = explode(' - ', $agenda->ruta);
                                            $totalPartes = count($rutaPartes);
                                            $mitad = (int)($totalPartes / 2);
                                            $rutaIda = implode(' - ', array_slice($rutaPartes, 0, $mitad));
                                            $rutaRegreso = implode(' - ', array_slice($rutaPartes, $mitad));
                                        @endphp
                                        <input type="hidden" name="ruta_ida" value="{{ $rutaIda }}">
                                        <input type="hidden" name="ruta_regreso" value="{{ $rutaRegreso }}">
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 rounded-4 d-flex align-items-center mb-3" style="background-color: #f8fdf5; border: 1px solid #e1f0d7;">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #39a900; color: white;">
                                                        <i class="fas fa-route fa-sm"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-bold text-success small text-uppercase mb-0 d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Ruta de Ida</label>
                                                    <span class="text-dark fw-bold">{{ $rutaIda }}</span>
                                                </div>
                                            </div>
                                            
                                            @if($agenda->actividades->count() == 0)
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Medio de Transporte (Ida)</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="transport-option">
                                                    <input type="checkbox" name="transporte_ida[]" value="aereo" id="ti-aereo" class="btn-check">
                                                    <label class="btn btn-outline-success rounded-3 px-3 py-2 fw-bold d-flex align-items-center" for="ti-aereo">
                                                        <i class="fas fa-plane me-2"></i>Aéreo
                                                    </label>
                                                </div>
                                                <div class="transport-option">
                                                    <input type="checkbox" name="transporte_ida[]" value="terrestre" id="ti-terrestre" class="btn-check">
                                                    <label class="btn btn-outline-success rounded-3 px-3 py-2 fw-bold d-flex align-items-center" for="ti-terrestre">
                                                        <i class="fas fa-bus me-2"></i>Terrestre
                                                    </label>
                                                </div>
                                                <div class="transport-option">
                                                    <input type="checkbox" name="transporte_ida[]" value="fluvial" id="ti-fluvial" class="btn-check">
                                                    <label class="btn btn-outline-success rounded-3 px-3 py-2 fw-bold d-flex align-items-center" for="ti-fluvial">
                                                        <i class="fas fa-ship me-2"></i>Fluvial
                                                    </label>
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 rounded-4 d-flex align-items-center mb-3" style="background-color: #f8fdf5; border: 1px solid #e1f0d7;">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #b0bfc6; color: white;">
                                                        <i class="fas fa-undo fa-sm"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-bold text-muted small text-uppercase mb-0 d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Ruta de Regreso</label>
                                                    <span class="text-dark fw-bold">{{ $rutaRegreso }}</span>
                                                </div>
                                            </div>

                                            @if($agenda->actividades->count() == 0)
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Medio de Transporte (Regreso)</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="transport-option">
                                                    <input type="checkbox" name="transporte_regreso[]" value="aereo" id="tr-aereo" class="btn-check">
                                                    <label class="btn btn-outline-success rounded-3 px-3 py-2 fw-bold d-flex align-items-center" for="tr-aereo">
                                                        <i class="fas fa-plane me-2"></i>Aéreo
                                                    </label>
                                                </div>
                                                <div class="transport-option">
                                                    <input type="checkbox" name="transporte_regreso[]" value="terrestre" id="tr-terrestre" class="btn-check">
                                                    <label class="btn btn-outline-success rounded-3 px-3 py-2 fw-bold d-flex align-items-center" for="tr-terrestre">
                                                        <i class="fas fa-bus me-2"></i>Terrestre
                                                    </label>
                                                </div>
                                                <div class="transport-option">
                                                    <input type="checkbox" name="transporte_regreso[]" value="fluvial" id="tr-fluvial" class="btn-check">
                                                    <label class="btn btn-outline-success rounded-3 px-3 py-2 fw-bold d-flex align-items-center" for="tr-fluvial">
                                                        <i class="fas fa-ship me-2"></i>Fluvial
                                                    </label>
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <label class="form-label fw-semibold text-muted small text-uppercase mb-0">Actividades a ejecutar (Máximo 5 por día)</label>
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" id="add-actividad">
                                                    <i class="fas fa-plus me-1"></i> Agregar Actividad
                                                </button>
                                            </div>
                                            <div id="actividades-container">
                                                <div class="row g-2 activity-row mb-3 animate__animated animate__fadeInUp animate__faster">
                                                    <div class="col-md-3">
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-0 cursor-pointer select-time-btn"><i class="far fa-clock text-muted"></i></span>
                                                            <input type="text" name="actividades[0][hora]" 
                                                                   class="form-control custom-input time-picker" 
                                                                   placeholder="08:00 AM-09:00 AM" 
                                                                   title="Formato: 08:00 AM-09:00 AM"
                                                                   readonly
                                                                   required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div class="input-group">
                                                            <input type="text" name="actividades[0][actividad]" class="form-control custom-input" placeholder="Describa la actividad..." required maxlength="160">
                                                            <button type="button" class="btn btn-outline-danger border-2 ms-2 remove-actividad" style="border-radius: 0.85rem;" disabled>
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        @if($agenda->actividades->count() == 0)
                                        <div class="col-12 mt-3">
                                            <div class="card border-0 rounded-4 shadow-sm" style="background-color: #fcfcfc;">
                                                <div class="card-body p-4">
                                                    <h6 class="fw-bold mb-3 text-dark d-flex align-items-center">
                                                        <i class="fas fa-file-invoice-dollar text-success me-2"></i> Liquidación de Gastos
                                                    </h6>
                                                    <div class="row g-3">
                                                        <div class="col-md-4" id="wrapper_valor_aereo" style="display: none;">
                                                            <label class="form-label text-muted small text-uppercase fw-bold mb-1">Terminales Aéreas</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text border-0 bg-light text-muted">$</span>
                                                                <input type="text" name="valor_aereo" class="form-control border-0 bg-light" placeholder="Ej: 100.000">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4" id="wrapper_valor_terrestre" style="display: none;">
                                                            <label class="form-label text-muted small text-uppercase fw-bold mb-1">Terminales Terrestres</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text border-0 bg-light text-muted">$</span>
                                                                <input type="text" name="valor_terrestre" class="form-control border-0 bg-light" placeholder="Ej: 90.000">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4" id="wrapper_valor_intermunicipal" style="display: none;">
                                                            <label class="form-label text-muted small text-uppercase fw-bold mb-1">Intermunicipales</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text border-0 bg-light text-muted">$</span>
                                                                <input type="text" name="valor_intermunicipal" class="form-control border-0 bg-light" placeholder="Ej: 25.000">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-12 mt-4 text-center">
                                            <div class="d-flex justify-content-center gap-3">
                                                <button type="submit" class="btn btn-success rounded-pill px-5 py-3 fw-bold shadow-sm hover-grow">
                                                    <i class="fas fa-save me-2"></i>Guardar Actividad
                                                </button>

                                                @php
                                                    $diasTotal = $agenda->fecha_inicio->diffInDays($agenda->fecha_fin) + 1;
                                                    $diasReportados = $agenda->actividades->count();
                                                @endphp

                                                @if($diasReportados >= $diasTotal)
                                                    <button type="button" 
                                                            class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm hover-grow btn-final-enviar"
                                                            data-total="{{ $diasTotal }}"
                                                            data-reportados="{{ $diasReportados }}">
                                                        <i class="fas fa-paper-plane me-2"></i>Enviar Agenda Terminada
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Lista de Actividades Registradas --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp">
                            <div class="card-header bg-white border-0 py-4 px-4">
                                <h5 class="fw-bold mb-0 text-dark">Actividades Registradas</h5>
                            </div>
                            <div class="card-body p-0">
                                @if($agenda->actividades->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0 custom-table">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4 py-3 text-muted small text-uppercase fw-bold">Fecha</th>
                                                    <th class="py-3 text-muted small text-uppercase fw-bold">Actividades</th>
                                                    <th class="py-3 text-muted small text-uppercase fw-bold">Transporte</th>
                                                    <th class="py-3 text-muted small text-uppercase fw-bold">Liquidación</th>
                                                    <th class="pe-4 py-3 text-end text-muted small text-uppercase fw-bold">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($agenda->actividades as $actividad)
                                                    <tr>
                                                        <td class="ps-4 fw-bold text-dark">{{ $actividad->fecha->format('Y-m-d') }}</td>
                                                        <td>
                                                            <div class="text-dark">
                                                                @php $ejecutadas = $actividad->actividad; @endphp
                                                                @if(is_array($ejecutadas))
                                                                    <div class="d-flex flex-column gap-1">
                                                                        @foreach($ejecutadas as $item)
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <span class="badge bg-light text-success border border-success border-opacity-10 fw-normal py-1" style="min-width: 75px;">{{ $item['hora'] ?? '' }}</span>
                                                                                <span class="small">{{ $item['actividad'] ?? '' }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    {{ $actividad->actividad }}
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column gap-1">
                                                                <div class="small"><i class="fas fa-arrow-right text-success me-1 small"></i> <span class="text-muted">Ida:</span> 
                                                                    @php $ti = $actividad->transporte_ida ?? $actividad->medios_transporte ?? []; @endphp
                                                                    @foreach(is_array($ti) ? $ti : [$ti] as $medio)
                                                                        <span class="badge bg-light text-dark border-0 fw-normal">{{ ucfirst($medio) }}</span>
                                                                    @endforeach
                                                                </div>
                                                                <div class="small"><i class="fas fa-arrow-left text-muted me-1 small"></i> <span class="text-muted">Regreso:</span> 
                                                                    @php $tr = $actividad->transporte_regreso ?? $actividad->medios_transporte ?? []; @endphp
                                                                    @foreach(is_array($tr) ? $tr : [$tr] as $medio)
                                                                        <span class="badge bg-light text-dark border-0 fw-normal">{{ ucfirst($medio) }}</span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($actividad->valor_aereo || $actividad->valor_terrestre || $actividad->valor_intermunicipal)
                                                                <div class="d-flex flex-column gap-1">
                                                                    @if($actividad->valor_aereo) <span class="small text-muted">Aéreo: <span class="text-dark fw-bold">${{ $actividad->valor_aereo }}</span></span> @endif
                                                                    @if($actividad->valor_terrestre) <span class="small text-muted">Terr.: <span class="text-dark fw-bold">${{ $actividad->valor_terrestre }}</span></span> @endif
                                                                    @if($actividad->valor_intermunicipal) <span class="small text-muted">Inter.: <span class="text-dark fw-bold">${{ $actividad->valor_intermunicipal }}</span></span> @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted small">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td class="pe-4 text-end">
                                                            <span class="badge rounded-pill px-3 py-2 fw-normal" style="background-color: #f0f7ed; color: #39a900; border: 1px solid #e1f0d7;">
                                                                <i class="fas fa-check-circle me-1"></i>Reportado
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-5 text-center">
                                        <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                            <i class="fas fa-clipboard-list fa-3x text-muted opacity-50"></i>
                                        </div>
                                        <h6 class="text-muted mb-0">No hay actividades reportadas para esta agenda.</h6>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para seleccionar hora --}}
    <div class="modal fade" id="timePickerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark"><i class="far fa-clock text-success me-2"></i>Seleccionar Horario</h6>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
                <div class="modal-body p-4 pt-2">
                    <div class="row g-4">
                        <div class="col-md-6 text-center">
                            <label class="form-label small text-muted text-uppercase fw-bold mb-3">Hora de Inicio</label>
                            <div class="time-picker-wrapper">
                                <input type="text" id="start-time" class="form-control border-0 bg-light text-center fw-bold rounded-4 py-3 mb-2" style="font-size: 1.2rem; display: none;">
                                <div id="start-time-calendar"></div>
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <label class="form-label small text-muted text-uppercase fw-bold mb-3">Hora de Fin</label>
                            <div class="time-picker-wrapper">
                                <input type="text" id="end-time" class="form-control border-0 bg-light text-center fw-bold rounded-4 py-3 mb-2" style="font-size: 1.2rem; display: none;">
                                <div id="end-time-calendar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="button" class="btn btn-success btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm hover-grow" id="apply-time" style="background-color: #39a900; border: none;">
                            Aplicar Horario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
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

        .btn-check:checked + .btn-outline-success {
            background-color: #39a900 !important;
            color: #fff !important;
            box-shadow: 0 4px 6px -1px rgba(57, 169, 0, 0.2);
        }

        .btn-outline-success {
            border-color: #39a900;
            color: #39a900;
            border-width: 2px;
        }

        .btn-outline-success:hover {
            background-color: #39a90010;
            color: #39a900;
            border-color: #39a900;
        }

        .transport-badge {
            background-color: #39a90015;
            color: #39a900;
            border: 1px solid #39a90030;
            font-weight: 600;
        }

        .hover-grow {
            transition: all 0.2s ease;
        }

        .hover-grow:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(57, 169, 0, 0.3) !important;
        }

        .custom-table tbody tr {
            transition: all 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* SENA Colors */
        .text-success { color: #39a900 !important; }
        .bg-success { background-color: #39a900 !important; }
        
        .italic { font-style: italic; }

        .time-picker {
            text-align: center;
        }

        .activity-row .input-group-text {
            border-radius: 0.85rem 0 0 0.85rem;
        }

        .activity-row .custom-input.form-control {
            border-left: none;
        }

        .activity-row .col-md-9 .custom-input.form-control {
            border-left: 2px solid #f1f5f9;
        }

        /* Validaciones Custom */
        .invalid-feedback-custom {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.825em;
            color: #ef4444;
            font-weight: 500;
        }
        
        .is-invalid-custom {
            border-color: #ef4444 !important;
            padding-right: calc(1.5em + 0.75rem) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ef4444'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ef4444' stroke='none'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }

        .d-flex.is-invalid-custom {
            background-image: none !important;
            padding-right: 0 !important;
            border: 2px solid #ef4444 !important;
            border-radius: 0.85rem;
            padding: 0.5rem;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .btn-close-custom {
            background: #f1f5f9;
            border: 2px solid #3b82f630;
            color: #3b82f6;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            padding: 0;
            font-size: 1.2rem;
            position: relative;
            z-index: 10;
        }

        .btn-close-custom:hover {
            background: #3b82f610;
            border-color: #3b82f6;
            transform: scale(1.05);
            color: #2563eb;
        }

        /* Flatpickr Styling */
        .flatpickr-calendar {
            box-shadow: none !important;
            border: none !important;
            background: transparent !important;
            width: 100% !important;
        }

        .flatpickr-time {
            height: auto !important;
            line-height: inherit !important;
        }

        .flatpickr-time input {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #39a900 !important;
        }

        .flatpickr-am-pm {
            font-size: 1.2rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 5px !important;
            background: #f8fafc !important;
        }

        .flatpickr-time .numInputWrapper:hover {
            background: #f1f5f9 !important;
        }
    </style>

    @push('scripts')
    <script>
        $(document).ready(function() {
            let activityCount = 1;

            $('#add-actividad').click(function() {
                if ($('.activity-row').length >= 5) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Límite alcanzado',
                        text: 'El formato oficial solo permite hasta 5 actividades por día.',
                        confirmButtonColor: '#39a900',
                        customClass: {
                            confirmButton: 'rounded-pill px-4'
                        }
                    });
                    return;
                }

                const newRow = `
                    <div class="row g-2 activity-row mb-3 animate__animated animate__fadeInUp animate__faster">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 cursor-pointer select-time-btn"><i class="far fa-clock text-muted"></i></span>
                                <input type="text" name="actividades[${activityCount}][hora]" 
                                       class="form-control custom-input time-picker" 
                                       placeholder="08:00 AM-09:00 AM" 
                                       title="Formato: 08:00 AM-09:00 AM"
                                       readonly
                                       required>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" name="actividades[${activityCount}][actividad]" class="form-control custom-input" placeholder="Describa la actividad..." required maxlength="160">
                                <button type="button" class="btn btn-outline-danger border-2 ms-2 remove-actividad" style="border-radius: 0.85rem;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#actividades-container').append(newRow);
                activityCount++;
                updateRemoveButtons();
            });

            $(document).on('click', '.remove-actividad', function() {
                if ($('.activity-row').length > 1) {
                    const row = $(this).closest('.activity-row');
                    row.removeClass('animate__fadeInUp').addClass('animate__fadeOutDown');
                    setTimeout(() => {
                        row.remove();
                        updateRemoveButtons();
                    }, 400);
                }
            });

            // --- Lógica de Reloj Bonito (Flatpickr) ---
            let currentTargetInput = null;

            const fpStart = flatpickr("#start-time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                defaultDate: "08:00",
                inline: true,
                appendTo: document.getElementById('start-time-calendar')
            });

            const fpEnd = flatpickr("#end-time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                defaultDate: "09:00",
                inline: true,
                appendTo: document.getElementById('end-time-calendar')
            });

            $(document).on('click', '.select-time-btn, .time-picker', function() {
                currentTargetInput = $(this).closest('.input-group').find('.time-picker');
                const currentVal = currentTargetInput.val();
                
                if (currentVal && currentVal.includes('-')) {
                    const parts = currentVal.split('-');
                    fpStart.setDate(parts[0].trim());
                    fpEnd.setDate(parts[1].trim());
                }
                
                $('#timePickerModal').modal('show');
            });

            // Helper para convertir hora (08:00 AM) a minutos totales desde medianoche
            function timeToMinutes(timeStr) {
                if (!timeStr) return null;
                const match = timeStr.match(/(\d+):(\d+)\s*(AM|PM)/i);
                if (!match) return null;
                
                let hours = parseInt(match[1]);
                const minutes = parseInt(match[2]);
                const ampm = match[3].toUpperCase();
                
                if (ampm === 'PM' && hours < 12) hours += 12;
                if (ampm === 'AM' && hours === 12) hours = 0;
                
                return hours * 60 + minutes;
            }

            $('#apply-time').click(function() {
                const startStr = $('#start-time').val();
                const endStr = $('#end-time').val();
                
                if (startStr && endStr) {
                    const newStart = timeToMinutes(startStr);
                    const newEnd = timeToMinutes(endStr);
                    const newRangeStr = `${startStr} - ${endStr}`;
                    
                    if (newStart >= newEnd) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Horario Inválido',
                            text: 'La hora de fin debe ser posterior a la de inicio.',
                            confirmButtonColor: '#39a900'
                        });
                        return;
                    }

                    // Validar solapamientos
                    let overlapDetected = false;
                    $('.time-picker').each(function() {
                        if (this === currentTargetInput[0]) return true; // saltar el actual
                        
                        const existingVal = $(this).val();
                        if (existingVal && existingVal.includes('-')) {
                            const parts = existingVal.split('-');
                            const existingStart = timeToMinutes(parts[0].trim());
                            const existingEnd = timeToMinutes(parts[1].trim());
                            
                            // Lógica de solapamiento: (StartA < EndB) y (EndA > StartB)
                            if (newStart < existingEnd && newEnd > existingStart) {
                                overlapDetected = true;
                                return false; // break loop
                            }
                        }
                    });

                    if (overlapDetected) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cruce de Horarios',
                            text: 'El horario seleccionado se cruza con otra actividad ya registrada para este día.',
                            confirmButtonColor: '#39a900'
                        });
                        return;
                    }

                    currentTargetInput.val(newRangeStr);
                    currentTargetInput.trigger('change');
                    $('#timePickerModal').modal('hide');
                }
            });

            // --- FIN Lógica de Reloj ---

            function updateRemoveButtons() {
                const rows = $('.activity-row');
                if (rows.length === 1) {
                    rows.find('.remove-actividad').prop('disabled', true);
                } else {
                    rows.find('.remove-actividad').prop('disabled', false);
                }
            }

            // --- FUNCIONES DE VALIDACIÓN INLINE ---
            function showError(element, message) {
                const $el = $(element);
                $el.addClass('is-invalid-custom');
                
                let $insertionPoint = $el;
                if ($el.closest('.input-group').length) {
                    $insertionPoint = $el.closest('.input-group');
                }

                let $errorDiv = $insertionPoint.next('.invalid-feedback-custom');
                if (!$errorDiv.length) {
                    $errorDiv = $('<div class="invalid-feedback-custom"></div>');
                    $insertionPoint.after($errorDiv);
                }
                
                $errorDiv.text(message).show();
            }

            function clearError(element) {
                const $el = $(element);
                $el.removeClass('is-invalid-custom');
                
                let $insertionPoint = $el;
                if ($el.closest('.input-group').length) {
                    $insertionPoint = $el.closest('.input-group');
                }
                
                $insertionPoint.next('.invalid-feedback-custom').hide().text('');
            }

            function clearAllErrors() {
                $('.is-invalid-custom').removeClass('is-invalid-custom');
                $('.invalid-feedback-custom').hide().text('');
            }

            // Escuchar cambios para limpiar errores y validar en tiempo real
            $(document).on('input change', 'input, select, textarea', function() {
                clearError(this);
                
                // Validación en tiempo real para gastos
                if ($(this).attr('name') && $(this).attr('name').startsWith('valor_')) {
                    if ($(this).val() === '') {
                        showError(this, 'Este campo es obligatorio.');
                    } else if (parseFloat($(this).val()) < 0) {
                        showError(this, 'El valor no puede ser negativo.');
                    }
                }
            });

            // Validación en tiempo real para transportes (checkboxes)
            $(document).on('change', 'input[name="transporte_ida[]"], input[name="transporte_regreso[]"]', function() {
                const name = $(this).attr('name');
                const $checked = $(`input[name="${name}"]:checked`);
                
                // Limpiar errores
                if ($checked.length > 0) {
                    clearError($(this).closest('.d-flex'));
                } else {
                    const msg = name.includes('ida') ? 'Seleccione al menos un medio de transporte de ida.' : 'Seleccione al menos un medio de transporte de regreso.';
                    showError($(this).closest('.d-flex'), msg);
                }

                // --- Lógica de visibilidad de gastos ---
                const aereoSelected = $('input[name="transporte_ida[]"][value="aereo"]:checked').length > 0 || 
                                     $('input[name="transporte_regreso[]"][value="aereo"]:checked').length > 0;
                
                const terrestreSelected = $('input[name="transporte_ida[]"][value="terrestre"]:checked').length > 0 || 
                                         $('input[name="transporte_regreso[]"][value="terrestre"]:checked').length > 0 ||
                                         $('input[name="transporte_ida[]"][value="fluvial"]:checked').length > 0 || 
                                         $('input[name="transporte_regreso[]"][value="fluvial"]:checked').length > 0;

                // Mostrar/Ocultar y resetear si se oculta
                if (aereoSelected) {
                    $('#wrapper_valor_aereo').fadeIn();
                } else {
                    $('#wrapper_valor_aereo').fadeOut(() => $('input[name="valor_aereo"]').val(''));
                }

                if (terrestreSelected) {
                    $('#wrapper_valor_terrestre').fadeIn();
                    $('#wrapper_valor_intermunicipal').fadeIn();
                } else {
                    $('#wrapper_valor_terrestre').fadeOut(() => $('input[name="valor_terrestre"]').val(''));
                    $('#wrapper_valor_intermunicipal').fadeOut(() => $('input[name="valor_intermunicipal"]').val(''));
                }
            });

            // Validar formato de hora antes de enviar
            $('form').on('submit', function(e) {
                clearAllErrors();
                let hasErrors = false;
                const timeRegex = /^[0-9]{1,2}:[0-9]{2}\s?(AM|PM)\s*-\s*[0-9]{1,2}:[0-9]{2}\s?(AM|PM)$/i;
                
                // Validar Fecha
                const $fecha = $('input[name="fecha"]');
                if (!$fecha.val()) {
                    showError($fecha, 'Seleccione la fecha del reporte.');
                    hasErrors = true;
                }

                // Validar Transporte (Solo si están visibles - primer día)
                const $ti = $('input[name="transporte_ida[]"]');
                if ($ti.length && !$('input[name="transporte_ida[]"]:checked').length) {
                    showError($ti.closest('.d-flex'), 'Seleccione al menos un medio de transporte de ida.');
                    hasErrors = true;
                }

                const $tr = $('input[name="transporte_regreso[]"]');
                if ($tr.length && !$('input[name="transporte_regreso[]"]:checked').length) {
                    showError($tr.closest('.d-flex'), 'Seleccione al menos un medio de transporte de regreso.');
                    hasErrors = true;
                }

                // Validar Actividades (Solapamientos)
                const ranges = [];
                let hasOverlap = false;

                $('.time-picker').each(function() {
                    const val = $(this).val().trim();
                    if (!timeRegex.test(val)) {
                        showError(this, 'Formato: 08:00 AM-09:00 AM');
                        hasErrors = true;
                        return;
                    }

                    const parts = val.split('-');
                    const start = timeToMinutes(parts[0].trim());
                    const end = timeToMinutes(parts[1].trim());

                    if (start >= end) {
                        showError(this, 'La hora de fin debe ser posterior al inicio.');
                        hasErrors = true;
                        return;
                    }

                    // Comparar contra rangos ya procesados
                    for (const r of ranges) {
                        if (start < r.end && end > r.start) {
                            hasOverlap = true;
                            showError(this, 'Este horario se cruza con otro.');
                            break;
                        }
                    }
                    ranges.push({ start, end });
                });
                if (hasOverlap) hasErrors = true;

                $('.activity-row input[name*="[actividad]"]').each(function() {
                    if (!$(this).val().trim()) {
                        showError(this, 'Describa la actividad ejecutada.');
                        hasErrors = true;
                    }
                });

                // Validar Gastos (solo si son visibles en el primer reporte)
                ['valor_aereo', 'valor_terrestre', 'valor_intermunicipal'].forEach(name => {
                    const $input = $(`input[name="${name}"]`);
                    const $wrapper = $(`#wrapper_${name}`);
                    
                    if ($input.length && $wrapper.is(':visible')) {
                        if ($input.val().trim() === '') {
                            showError($input, 'Este campo es obligatorio.');
                            hasErrors = true;
                        }
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Datos incompletos',
                        text: 'Por favor corrija los errores marcados en rojo.',
                        confirmButtonColor: '#39a900'
                    });
                }
            });

            // Lógica para enviar la agenda desde esta vista
            $('.btn-final-enviar').on('click', function(e) {
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
                        // Crear un formulario dinámico para enviar
                        const form = $('<form>', {
                            'action': '{{ route("reportar-dia.enviar", $agenda->id) }}',
                            'method': 'POST'
                        }).append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
@endsection