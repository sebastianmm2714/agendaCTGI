<x-dashboard-layout>
    <div class="container-fluid px-4 py-5">
        <div class="row justify-content-center">
            <div class="col-xxl-10">

                {{-- Encabezado de Página --}}
                <div class="d-flex align-items-center justify-content-between mb-5 animate__animated animate__fadeIn">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-4 me-4">
                            <i class="fas fa-calendar-check fa-2x text-success"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1 text-dark">Mis Agendas</h2>
                            <p class="text-muted mb-0">Gestione sus desplazamientos y reporte sus actividades diarias.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('formulario') }}"
                        class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm hover-grow">
                        @if (auth()->user()->rol == 'user' || auth()->user()->rol == 'contratista')
                            <i class="fas fa-plus me-2"></i>Nueva Agenda
                        @endif
                    </a>
                </div>

                @if (session('success'))
                    <div
                        class="alert alert-success border-0 shadow-sm rounded-4 mb-4 p-4 animate__animated animate__fadeInUp">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3 fa-lg"></i>
                            <h6 class="fw-bold mb-0">{{ session('success') }}</h6>
                        </div>
                    </div>
                @endif

                {{-- Card de Tabla --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp">
                    <div class="card-header bg-white border-0 py-4 px-4">
                        <h5 class="fw-bold mb-0 text-dark">Seleccione una agenda para reportar</h5>

                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 custom-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 text-muted small text-uppercase fw-bold">ID</th>
                                        <th class="py-3 text-muted small text-uppercase fw-bold">Municipio Destino</th>
                                        <th class="py-3 text-muted small text-uppercase fw-bold">Fecha Inicio</th>
                                        <th class="py-3 text-muted small text-uppercase fw-bold">Fecha Fin</th>
                                        <th class="py-3 text-muted small text-uppercase fw-bold">Estado</th>
                                        <th class="pe-4 py-3 text-end text-muted small text-uppercase fw-bold">Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($agendas as $agenda)
                                        <tr>
                                            <td class="ps-4">
                                                <span
                                                    class="badge bg-light text-dark rounded-pill px-2 py-1 border fw-bold">#{{ $agenda->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <p class="text-muted mb-0 small"><i
                                                            class="fas fa-map-marker-alt me-1"></i>{{ $agenda->ciudad_destino }}
                                                    </p>
                                                    <small class="text-muted">{{ $agenda->ruta }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center text-dark">
                                                    <i class="far fa-calendar-alt me-2 text-muted"></i>
                                                    {{ $agenda->fecha_inicio_desplazamiento }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center text-dark">
                                                    <i class="far fa-calendar-check me-2 text-muted"></i>
                                                    {{ $agenda->fecha_fin_desplazamiento }}
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusInfo = match ($agenda->estado) {
                                                        'BORRADOR' => [
                                                            'color' => '#94a3b8',
                                                            'label' => 'Borrador',
                                                            'icon' => 'fa-edit'
                                                        ],
                                                        'ENVIADA' => [
                                                            'color' => '#00b4d8',
                                                            'label' => 'Enviada',
                                                            'icon' => 'fa-paper-plane'
                                                        ],
                                                        'APROBADA' => [
                                                            'color' => '#39a900',
                                                            'label' => 'Aprobada',
                                                            'icon' => 'fa-check-double'
                                                        ],
                                                        'RECHAZADA' => [
                                                            'color' => '#ef4444',
                                                            'label' => 'Rechazada',
                                                            'icon' => 'fa-times-circle'
                                                        ],
                                                        'REVISION' => [
                                                            'color' => '#f59e0b',
                                                            'label' => 'En Revisión',
                                                            'icon' => 'fa-clock'
                                                        ],
                                                        default => [
                                                            'color' => '#64748b',
                                                            'label' => $agenda->estado,
                                                            'icon' => 'fa-question-circle'
                                                        ]
                                                    };
                                                @endphp
                                                <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill status-badge"
                                                    style="background-color: {{ $statusInfo['color'] }}15; color: {{ $statusInfo['color'] }}; border: 1px solid {{ $statusInfo['color'] }}30;">
                                                    <i class="fas {{ $statusInfo['icon'] }} me-2 small"></i>
                                                    <span class="fw-bold small">{{ $statusInfo['label'] }}</span>
                                                </div>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    @if($agenda->estado == 'BORRADOR')
                                                        <form action="{{ route('agenda.enviar', $agenda->id) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-success rounded-pill px-3 py-2 fw-semibold hover-grow"
                                                                title="Enviar a Coordinación">
                                                                <i class="fas fa-paper-plane me-1"></i> Enviar
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <a href="{{ route('reportar-dia.show', $agenda->id) }}"
                                                        class="btn btn-sm btn-white border rounded-pill px-3 py-2 fw-semibold hover-grow"
                                                        title="Reportar Actividades">
                                                        <i class="fas fa-tasks text-success me-1"></i> Actividades
                                                    </a>
                                                    <a href="{{ route('agenda.pdf', $agenda->id) }}"
                                                        class="btn btn-sm btn-white border rounded-pill p-2 hover-grow"
                                                        target="_blank" title="Ver PDF">
                                                        <i class="fas fa-file-pdf text-danger"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="py-4">
                                                    <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                                        <i class="fas fa-folder-open fa-3x text-muted opacity-50"></i>
                                                    </div>
                                                    <h5 class="fw-bold text-dark">No hay agendas registradas</h5>
                                                    <p class="text-muted">Parece que aún no ha creado ninguna agenda de
                                                        desplazamiento.</p>
                                                    <a href="{{ route('formulario') }}"
                                                        class="btn btn-success rounded-pill px-4 mt-2">
                                                        Comenzar primera agenda
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-table tbody tr {
            transition: all 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .status-badge {
            white-space: nowrap;
        }

        .hover-grow {
            transition: all 0.2s ease;
        }

        .hover-grow:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }

        .btn-white {
            background-color: #fff;
            color: #1e293b;
            border-color: #e2e8f0;
        }

        .btn-white:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        .bg-success {
            background-color: #39a900 !important;
        }

        .text-success {
            color: #39a900 !important;
        }
    </style>
</x-dashboard-layout>