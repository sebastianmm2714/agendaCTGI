<x-dashboard-layout>
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
                            <p class="text-muted mb-0">Agenda #{{ $agenda->id }} - {{ $agenda->ciudad_destino }}</p>
                        </div>
                    </div>
                    <a href="{{ route('reportar-dia') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold hover-grow">
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
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp">
                            <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                                <div class="bg-success p-2 rounded-3 me-3">
                                    <i class="fas fa-calendar-plus text-white"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark">Nueva Actividad</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <form method="POST" action="{{ route('agenda.actividad.store', $agenda->id) }}">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small text-uppercase">Día a reportar</label>
                                                    <input type="date" name="fecha_reporte" class="form-control custom-input" 
                                                           min="{{ $proximaFecha }}" max="{{ $agenda->fecha_fin_desplazamiento }}" 
                                                           value="{{ old('fecha_reporte', $proximaFecha) }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 rounded-4 d-flex align-items-center mb-3" style="background-color: #f8fdf5; border: 1px solid #e1f0d7;">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #39a900; color: white;">
                                                        <i class="fas fa-route fa-sm"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-bold text-success small text-uppercase mb-0 d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Ruta de Ida</label>
                                                    <span class="text-dark fw-bold">MEDELLIN - {{ $agenda->ciudad_destino }}</span>
                                                </div>
                                            </div>
                                            
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
                                                    <span class="text-dark fw-bold">{{ $agenda->ciudad_destino }} - MEDELLIN</span>
                                                </div>
                                            </div>

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
                                                            <span class="input-group-text bg-light border-0"><i class="far fa-clock text-muted"></i></span>
                                                            <input type="text" name="actividades[0][hora]" class="form-control custom-input time-picker" placeholder="08:00 AM" required>
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


                                        <div class="col-12 mt-3">
                                            <div class="card border-0 rounded-4 shadow-sm" style="background-color: #fcfcfc;">
                                                <div class="card-body p-4">
                                                    <h6 class="fw-bold mb-3 text-dark d-flex align-items-center">
                                                        <i class="fas fa-file-invoice-dollar text-success me-2"></i> Liquidación de Gastos
                                                    </h6>
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label text-muted small text-uppercase fw-bold mb-1">Terminales Aéreas</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text border-0 bg-light text-muted">$</span>
                                                                <input type="text" name="valor_aereo" class="form-control border-0 bg-light" placeholder="Valor o N/A">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label text-muted small text-uppercase fw-bold mb-1">Terminales Terrestres</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text border-0 bg-light text-muted">$</span>
                                                                <input type="text" name="valor_terrestre" class="form-control border-0 bg-light" placeholder="Valor o N/A">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label text-muted small text-uppercase fw-bold mb-1">Intermunicipales</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text border-0 bg-light text-muted">$</span>
                                                                <input type="text" name="valor_intermunicipal" class="form-control border-0 bg-light" placeholder="Valor o N/A">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-4 text-center">
                                            <button type="submit" class="btn btn-success rounded-pill px-5 py-3 fw-bold shadow-sm hover-grow">
                                                <i class="fas fa-save me-2"></i>Guardar Actividad
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

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
                                                        <td class="ps-4 fw-bold text-dark">{{ $actividad->fecha_reporte->format('Y-m-d') }}</td>
                                                        <td>
                                                            <div class="text-dark">
                                                                @if(is_array($actividad->actividades_ejecutar))
                                                                    <div class="d-flex flex-column gap-1">
                                                                        @foreach($actividad->actividades_ejecutar as $item)
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <span class="badge bg-light text-success border border-success border-opacity-10 fw-normal py-1" style="min-width: 75px;">{{ $item['hora'] ?? '' }}</span>
                                                                                <span class="small">{{ $item['actividad'] ?? '' }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    {{ $actividad->actividades_ejecutar }}
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
                        text: 'El formato oficial solo permite hasta 5 actividades por día.'
                    });
                    return;
                }

                const newRow = `
                    <div class="row g-2 activity-row mb-3 animate__animated animate__fadeInUp animate__faster">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="far fa-clock text-muted"></i></span>
                                <input type="text" name="actividades[${activityCount}][hora]" class="form-control custom-input time-picker" placeholder="08:00 AM" required>
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

            function updateRemoveButtons() {
                const rows = $('.activity-row');
                if (rows.length === 1) {
                    rows.find('.remove-actividad').prop('disabled', true);
                } else {
                    rows.find('.remove-actividad').prop('disabled', false);
                }
            }
        });
    </script>
    @endpush
</x-dashboard-layout>