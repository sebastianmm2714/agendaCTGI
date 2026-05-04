@extends('layouts.dashboard')

@section('content')
<div class="py-4">
    <div class="container-fluid px-md-5 overflow-hidden">

        {{-- SECCIÓN DEL TÍTULO --}}
        <div class="mt-3">
            <div class="card border shadow-sm mb-4">
                <div class="card-body d-flex align-items-center justify-content-center py-3">
                    <img src="{{ asset('images/sena/logo250.png') }}" alt="SENA" style="height: 60px; margin-right: 20px;">
                    <h1 class="h2 mb-0 fw-bold text-dark" style="letter-spacing: -1px;">Agenda CTGI</h1>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD PRINCIPAL DE LA TABLA --}}
    <div class="container-fluid px-md-5 overflow-hidden">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    Seleccione una agenda para reportar actividades
                </h5>
                @if(auth()->user()->role == 'contratista')
                    <a href="{{ route('formulario') }}" class="btn btn-success">Nueva Agenda</a>
                @endif
            </div>

            <div class="card-body p-0">
                {{-- BUSCADOR INTELIGENTE --}}
                @if(in_array(auth()->user()->role, ['supervisor_contrato', 'ordenador_gasto', 'administrador']))
                    <div class="px-4 py-4 bg-light border-bottom">
                        <form action="{{ route('reportes') }}" method="GET" class="row g-3 align-items-center">
                            <div class="col-md-7">
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 px-3">
                                        <i class="fas fa-search text-success"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0 fw-medium" 
                                           placeholder="Buscar por instructor, CC, fecha (DD/MM/YYYY) o ruta..." 
                                           value="{{ request('search') }}"
                                           style="font-size: 0.95rem;">
                                    @if(request('search'))
                                        <a href="{{ route('reportes') }}" class="btn btn-white border-start-0 text-muted" title="Limpiar búsqueda">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 position-relative">
                                @php
                                    // Determinar el texto activo por defecto
                                    $estadoActivoTexto = 'Todos los estados';
                                    if(request('estado_id')) {
                                        $estadoSeleccionado = $estados->firstWhere('id', request('estado_id'));
                                        if($estadoSeleccionado) {
                                            $estadoActivoTexto = str_replace('_', ' ', $estadoSeleccionado->nombre);
                                        }
                                    }
                                @endphp

                                <div class="custom-select-trigger d-flex justify-content-between align-items-center shadow-sm w-100" tabindex="0">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        <i class="fas fa-layer-group text-success me-2 flex-shrink-0" style="font-size: 0.85rem;"></i>
                                        <span id="estado_trigger_text" class="text-truncate fw-bold {{ request('estado_id') ? 'text-dark' : 'text-muted' }}" style="font-size: 0.85rem; letter-spacing: 0.3px; text-transform: uppercase;">
                                            {{ $estadoActivoTexto }}
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down ms-2 flex-shrink-0 text-muted" style="font-size: 0.8rem;"></i>
                                </div>
                                
                                <div class="custom-select-dropdown" style="display:none;">
                                    <div class="custom-select-option {{ !request('estado_id') ? 'selected-item' : '' }}" data-value="" data-text="Todos los estados">
                                        Todos los estados
                                    </div>
                                    @foreach($estados as $estado)
                                        @php $n = str_replace('_', ' ', $estado->nombre); @endphp
                                        <div class="custom-select-option {{ request('estado_id') == $estado->id ? 'selected-item' : '' }}" 
                                             data-value="{{ $estado->id }}" 
                                             data-text="{{ $n }}">
                                            {{ $n }}
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="estado_id" id="estado_input" value="{{ request('estado_id') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                                    <i class="fas fa-filter me-1"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #23d647ff; border-bottom: 2px solid #eee;">
                            <tr class="text-dark">
                                @if(in_array(auth()->user()->role, ['supervisor_contrato', 'ordenador_gasto', 'administrador']))
                                    <th class="ps-4 py-3" style="width: 80px;">#</th>
                                    <th class="py-3">Contratista / Instructor</th>
                                    <th class="py-3">Ruta / Destino</th>
                                    <th class="py-3">Fecha Inicio</th>
                                    <th class="py-3">Fecha Fin</th>
                                @else
                                    <th class="ps-4 py-3">#</th>
                                    <th class="py-3">Ruta / Destino</th>
                                    <th class="py-3">Fecha de inicio</th>
                                    <th class="py-3">Fecha final</th>
                                @endif
                                <th class="py-3 text-center" style="width: 250px;">Estado</th>
                                <th class="text-end pe-4 py-3">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($agendas as $agenda)
                                <tr>
                                    @if(in_array(auth()->user()->role, ['supervisor_contrato', 'ordenador_gasto', 'administrador']))
                                        <td class="ps-4 fw-bold text-success">
                                            #{{ $agenda->id }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $agenda->user->name ?? 'N/A' }}</div>
                                            <div class="text-muted small text-uppercase" style="font-size: 0.70rem;">{{ $agenda->user->categoria->nombre ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                <span class="fw-bold text-dark">
                                                    @if($agenda->destinos)
                                                        {{ implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $agenda->destinos)))) }}
                                                    @else
                                                        {{ $agenda->ciudad_destino ?: $agenda->ruta ?: 'N/A' }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="small text-muted text-uppercase" style="font-size: 0.70rem;">
                                                {{ $agenda->ruta }}
                                            </div>
                                        </td>
                                        <td class="text-dark">
                                            {{ $agenda->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}
                                        </td>
                                        <td class="text-dark">
                                            {{ $agenda->fecha_fin?->format('d/m/Y') ?? 'N/A' }}
                                        </td>
                                    @else
                                        <td class="ps-4 fw-bold text-success">
                                            #{{ $agenda->id }}
                                        </td>

                                        <td class="fw-bold text-dark text-uppercase">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                @if($agenda->destinos)
                                                    {{ implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $agenda->destinos)))) }}
                                                @else
                                                    {{ $agenda->ciudad_destino ?: $agenda->ruta ?: 'N/A' }}
                                                @endif
                                            </div>
                                            <div class="small text-muted text-uppercase fw-normal" style="font-size: 0.70rem;">
                                                {{ $agenda->ruta }}
                                            </div>
                                        </td>

                                        <td class="text-dark">
                                            {{ $agenda->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}
                                        </td>

                                        <td class="text-dark">
                                            {{ $agenda->fecha_fin?->format('d/m/Y') ?? 'N/A' }}
                                        </td>
                                    @endif

                                    <td class="text-center">
                                        {{-- LÓGICA DE DEVOLUCIÓN --}}
                                        @if($agenda->estado && $agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas)
                                            <div class="alert alert-danger mb-0 py-1 px-2 shadow-sm clickable-alert" 
                                                 style="font-size: 0.75rem; border-left: 4px solid #dc3545; cursor: pointer;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#modalInfo{{ $agenda->id }}"
                                                 title="Clic para ver detalles">
                                                <strong class="d-block text-uppercase"><i class="fas fa-undo-alt me-1"></i> Devuelta</strong>
                                                <span class="text-dark">{{ $agenda->observaciones_finanzas }}</span>
                                            </div>
                                        @else
                                            @php
                                                $estadoAMostrar = $agenda->estado ? $agenda->estado->nombre : '---';
                                                $colores = match(strtoupper($estadoAMostrar)) {
                                                    'BORRADOR'             => ['bg' => '#64748b', 'text' => '#ffffff'],
                                                    'ENVIADA'              => ['bg' => '#0ea5e9', 'text' => '#ffffff'],
                                                    'APROBADA_SUPERVISOR'  => ['bg' => '#10b981', 'text' => '#ffffff'],
                                                    'APROBADA_VIATICOS'    => ['bg' => '#8b5cf6', 'text' => '#ffffff'],
                                                    'APROBADA_ORDENADOR'   => ['bg' => '#39a900', 'text' => '#ffffff'],
                                                    'APROBADA'             => ['bg' => '#39a900', 'text' => '#ffffff'],
                                                    'CORRECCIÓN'           => ['bg' => '#ef4444', 'text' => '#ffffff'],
                                                    'RECHAZADA'            => ['bg' => '#ef4444', 'text' => '#ffffff'],
                                                    default                => ['bg' => '#0ea5e9', 'text' => '#ffffff'],
                                                };
                                            @endphp

                                            <span class="badge rounded-pill px-3 py-2 text-uppercase" 
                                                  style="background-color: {{ $colores['bg'] }}; color: {{ $colores['text'] }}; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                {{ str_replace('_', ' ', strtoupper($estadoAMostrar) == 'CORRECCIÓN' ? 'DEVUELTA' : $estadoAMostrar) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2 align-items-center">
                                            @if(in_array(auth()->user()->role, ['contratista', 'administrador']) && !in_array(strtoupper($agenda->estado ? $agenda->estado->nombre : ''), ['APROBADA', 'APROBADA_ORDENADOR', 'ENVIADA']))
                                                @php
                                                    $diasTotal = \Carbon\Carbon::parse($agenda->fecha_inicio)->diffInDays(\Carbon\Carbon::parse($agenda->fecha_fin)) + 1;
                                                    $diasReportados = $agenda->actividades->count();
                                                @endphp

                                                @if($agenda->estado && $agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas)
                                                    <a href="{{ route('formulario', $agenda->id) }}"
                                                       class="btn btn-sm btn-warning rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center">
                                                        <i class="fas fa-edit me-1"></i> Corregir
                                                    </a>
                                                @else
                                                    @if($diasReportados < $diasTotal)
                                                        <a href="{{ route('reportes.show', $agenda->id) }}"
                                                           class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center">
                                                            <i class="fas fa-calendar-plus me-1"></i> Reportar
                                                        </a>
                                                    @endif
                                                @endif

                                                {{-- Botón Enviar --}}
                                                @if(!$agenda->estado || ($agenda->estado && $agenda->estado->nombre == 'BORRADOR') || ($agenda->estado && $agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas))
                                                    <form action="{{ route('reportar-dia.enviar', $agenda->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm btn-enviar-agenda d-flex align-items-center"
                                                                data-total="{{ $diasTotal }}"
                                                                data-reportados="{{ $diasReportados }}">
                                                            <i class="fas fa-paper-plane me-1"></i> Enviar
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                            <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank" 
                                               class="btn btn-sm btn-dark rounded-pill px-2 shadow-sm fw-bold" title="Ver PDF" style="min-width: 70px;">
                                                <i class="fas fa-eye"></i> PDF
                                            </a>

                                            {{-- Acción de Eliminar (Solo para Supervisor o Admin) --}}
                                            @if(in_array(auth()->user()->role, ['supervisor_contrato', 'administrador']))
                                                <form action="{{ route('reportes.destroy', $agenda->id) }}" method="POST" class="delete-agenda-form d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" 
                                                            style="width: 32px; height: 32px; padding: 0;" title="Eliminar Agenda">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role == 'supervisor_contrato' ? '7' : '7' }}" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">No hay agendas registradas.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINACIÓN --}}
                <div class="card-footer bg-white border-top py-4">
                    <div class="d-flex justify-content-center">
                        <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block">
                            {{ $agendas->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODALES DE REVISIÓN PARA AGENDAS DEVUELTAS --}}
@foreach($agendas as $agenda)
    @if($agenda->estado && ($agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas))
    
    {{-- MODAL: SOLO INFORMACIÓN (El botón cerrar solo cierra el modal) --}}
    <div class="modal fade" id="modalInfo{{ $agenda->id }}" tabindex="-1" aria-hidden="true">
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
                        <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-4 shadow-sm fw-bold">
                            ABRIR ARCHIVO
                        </a>
                    </div>
                    <div class="text-center mb-4">
                        <label class="small text-muted fw-bold d-block">CONTRATISTA / INSTRUCTOR REGISTRADO</label>
                        <p class="fs-4 fw-bold text-dark text-uppercase mb-0">{{ $agenda->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3">
                        <label class="small fw-bold text-warning-emphasis">NOTA DE REVISIÓN:</label>
                        <p class="mb-0 small text-dark text-italic">"{{ $agenda->observaciones_finanzas }}"</p>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@push('scripts')
<style>
    /* ── Dropdown custom Estados (Mismo estilo que municipios) ── */
    .custom-select-trigger {
        user-select: none;
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        padding: 0.5rem 1.2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #fff;
        cursor: pointer;
        min-height: 46px;
    }

    .custom-select-trigger:hover {
        border-color: #39a900;
        background-color: #f8fafc;
    }

    .custom-select-trigger:focus,
    .custom-select-trigger.open {
        border-color: #39a900;
        box-shadow: 0 0 0 4px rgba(57, 169, 0, 0.15);
        outline: none;
    }

    .custom-select-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        z-index: 1000;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1.2rem;
        max-height: 300px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        animation: dropdownIn 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        padding: 0.5rem;
    }

    @keyframes dropdownIn {
        from { opacity: 0; transform: translateY(-10px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .custom-select-option {
        padding: 10px 14px;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 0.8rem;
        font-size: 0.82rem;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
    }
    
    .custom-select-option:last-child {
        margin-bottom: 0;
    }

    .custom-select-option:hover {
        background-color: #f0fdf4;
        color: #39a900;
    }

    .custom-select-option.selected-item {
        background-color: #39a900 !important;
        color: #fff !important;
        font-weight: 600;
    }

    /* Scrollbars estilizadas */
    .custom-select-dropdown::-webkit-scrollbar {
        width: 7px;
    }
    .custom-select-dropdown::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 0 0.85rem 0.85rem 0;
    }
    .custom-select-dropdown::-webkit-scrollbar-thumb {
        background: #39a900;
        border-radius: 4px;
    }

    /* Ocultar los textos redundantes del Paginador por defecto de Laravel */
    .pagination-container nav > div:first-child,
    .pagination-container nav div.d-none.flex-sm-fill > div:first-child,
    .pagination-container nav p.text-muted {
        display: none !important;
    }
    
    /* Mostrar sólo la lista de botones de paginación y centrarla */
    .pagination-container nav ul.pagination {
        margin-bottom: 0;
        justify-content: center;
    }

    /* Estilos Premium circulares para la Paginación estilo SENA */
    .pagination-container .page-item .page-link {
        border-radius: 50% !important;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 4px;
        border: none;
        color: #4b5563;
        font-weight: 600;
        transition: all 0.2s ease;
        background: transparent;
    }
    
    .pagination-container .page-item.active .page-link {
        background-color: #39a900 !important;
        color: white !important;
        box-shadow: 0 4px 6px -1px rgba(57, 169, 0, 0.4);
    }
    
    .pagination-container .page-item:not(.active) .page-link:hover {
        background-color: #e2e8f0;
        color: #39a900;
    }

    .pagination-container .page-item.disabled .page-link {
        color: #cbd5e1;
        background: transparent;
    }

    /* Ocultar el texto automático de Laravel pagination */
    .pagination-container nav div:first-child {
        display: none !important;
    }

    /* Animación para el recuadro clickable */
    .clickable-alert {
        transition: all 0.2s ease;
    }
    .clickable-alert:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.15) !important;
        filter: brightness(0.95);
    }
</style>
<script>
    $(document).ready(function() {
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#39a900'
            });
        @endif

        // ============================================
        // LÓGICA DEL DROPDOWN CUSTOM (ESTADOS)
        // ============================================
        const $trigger = $('.custom-select-trigger');
        const $dropdown = $('.custom-select-dropdown');
        const $inputEstado = $('#estado_input');
        const $form = $('#searchForm');

        $trigger.on('click', function(e) {
            e.stopPropagation();
            $dropdown.toggle();
            $trigger.toggleClass('open');
        });

        $('.custom-select-option').on('click', function(e) {
            e.stopPropagation();
            const value = $(this).data('value');
            const text = $(this).data('text');
            
            // UI Updates
            $('.custom-select-option').removeClass('selected-item');
            $(this).addClass('selected-item');
            $('#estado_trigger_text').text(text).removeClass('text-muted').addClass('text-dark fw-bold');
            
            // Value Update & Submit
            $inputEstado.val(value);
            $dropdown.hide();
            $trigger.removeClass('open');
            
            // Optional Auto Submit (Si quieres usar auto-submit)
            // $form.submit();
        });

        // Click outside to close
        $(document).on('click', function(e) {
            if(!$(e.target).closest('.custom-select-trigger, .custom-select-dropdown').length) {
                $dropdown.hide();
                $trigger.removeClass('open');
            }
        });

        $('.btn-enviar-agenda').on('click', function(e) {
            e.preventDefault();
            const btn = $(this);
            const total = parseInt(btn.data('total'));
            const reportados = parseInt(btn.data('reportados'));
            const form = btn.closest('form');

            if (reportados < total) {
                Swal.fire({
                    title: 'Reporte Incompleto',
                    html: `Esta agenda es de <b>${total} días</b>, pero solo has reportado <b>${reportados} día(s)</b>.<br><br>Debes reportar todos los días antes de enviar la agenda.`,
                    icon: 'warning',
                    confirmButtonColor: '#39a900',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Estás seguro de enviar esta agenda al supervisor? Una vez enviada no podrás modificar sus actividades.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#39a900',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Sí, enviar',
                cancelButtonText: 'Cancelar',
                borderRadius: '1rem',
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

        // --- LÓGICA DE ELIMINACIÓN DE AGENDA ---
        $('.delete-agenda-form').on('submit', function(e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: '¿Eliminar Agenda?',
                text: "Esta acción borrará permanentemente la agenda y todas sus actividades. No se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar permanentemente',
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
@endpush
@endsection