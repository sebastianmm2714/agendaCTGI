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
                    <a href="{{ route('formulario') }}" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="fas fa-plus me-1"></i> Nueva Agenda
                    </a>
                @endif
            </div>

            <div class="card-body p-0">
                {{-- BUSCADOR INTELIGENTE --}}
                <div class="px-4 py-4 bg-light border-bottom">
                    <form action="{{ route('reportar-dia') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-7">
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0 px-3">
                                    <i class="fas fa-search text-success"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0 fw-medium" 
                                       placeholder="Buscar por ruta, destino o fecha (DD/MM/YYYY)..." 
                                       value="{{ request('search') }}"
                                       style="font-size: 0.95rem;">
                                @if(request('search'))
                                    <a href="{{ route('reportar-dia') }}" class="btn btn-white border-start-0 text-muted" title="Limpiar búsqueda">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 position-relative">
                            @php
                                $estadoActivoTexto = 'Todos los estados';
                                if(request('estado_id')) {
                                    $estadoSeleccionado = $estados->firstWhere('id', request('estado_id'));
                                    if($estadoSeleccionado) {
                                        $estadoActivoTexto = str_replace('_', ' ', $estadoSeleccionado->nombre);
                                    }
                                }
                            @endphp

                            <div class="custom-select-trigger d-flex justify-content-between align-items-center shadow-sm w-100" tabindex="0">
                                <span id="estado_trigger_text" class="text-truncate fw-medium {{ request('estado_id') ? 'text-dark fw-bold' : 'text-muted' }}">
                                    {{ $estadoActivoTexto }}
                                </span>
                                <i class="fas fa-chevron-down ms-2 flex-shrink-0 text-muted"></i>
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
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-dark">
                                <th class="ps-4 py-3 text-muted small text-uppercase"># ID</th>
                                <th class="py-3 text-muted small text-uppercase">Municipio Destino</th>
                                <th class="py-3 text-muted small text-uppercase">Ruta</th>
                                <th class="py-3 text-muted small text-uppercase">Fecha de inicio</th>
                                <th class="py-3 text-muted small text-uppercase">Fecha final</th>
                                <th class="py-3 text-center text-muted small text-uppercase" style="width: 250px;">Estado</th>
                                <th class="text-center py-3 text-muted small text-uppercase" style="width: 250px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($agendas as $agenda)
                                <tr>
                                    <td class="ps-4 fw-bold text-success">
                                        #{{ $agenda->id }}
                                    </td>

                                    <td class="fw-bold text-dark text-uppercase">
                                        @if($agenda->destinos)
                                            {{ implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $agenda->destinos)))) }}
                                        @else
                                            {{ $agenda->municipio_destino ?: $agenda->ciudad_destino }}
                                        @endif
                                    </td>

                                    <td style="max-width: 300px;">
                                        <span class="text-muted small text-uppercase mb-0" style="display: block; line-height: 1.2;">
                                            {{ $agenda->ruta }}
                                        </span>
                                    </td>

                                    <td class="text-dark">
                                        {{ $agenda->fecha_inicio->format('d/m/Y') }}
                                    </td>

                                    <td class="text-dark">
                                        {{ $agenda->fecha_fin->format('d/m/Y') }}
                                    </td>

                                    <td class="text-center">
                                        {{-- LÓGICA DE DEVOLUCIÓN --}}
                                        @if($agenda->estado && ($agenda->estado->nombre == 'CORRECCIÓN' || ($agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas)))
                                            <div class="alert alert-danger mb-0 py-1 px-2 shadow-sm d-inline-block text-start clickable-alert" 
                                                 style="font-size: 0.75rem; border-left: 4px solid #dc3545; cursor: pointer;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#modalInfo{{ $agenda->id }}"
                                                 title="Clic para ver detalles">
                                                <strong class="d-block text-uppercase small"><i class="fas fa-undo-alt me-1"></i> Devuelta</strong>
                                                <span class="text-dark">{{ Str::limit($agenda->observaciones_finanzas, 30) }}</span>
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
                                                {{ str_replace('_', ' ', $estadoAMostrar) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center px-4">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            @php
                                                $diasTotal = $agenda->fecha_inicio->diffInDays($agenda->fecha_fin) + 1;
                                                $diasReportados = $agenda->actividades->count();
                                                $reporteCompleto = $diasReportados >= $diasTotal;
                                            @endphp

                                            {{-- Lógica de Edición y Reporte para Borradores y Correcciones --}}
                                            @if($agenda->estado && (strtoupper($agenda->estado->nombre) == 'BORRADOR' || $agenda->estado->nombre == 'CORRECCIÓN' || ($agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas)))
                                                
                                                {{-- Botón Editar/Corregir --}}
                                                <a href="{{ route('formulario', $agenda->id) }}"
                                                   class="btn btn-sm {{ strtoupper($agenda->estado->nombre) == 'BORRADOR' ? 'btn-outline-warning' : 'btn-warning' }} rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center"
                                                   title="Editar los datos básicos de la agenda">
                                                    <i class="fas fa-edit me-1"></i> {{ strtoupper($agenda->estado->nombre) == 'BORRADOR' ? 'Editar' : 'Corregir' }}
                                                </a>

                                                {{-- Botón Reportar --}}
                                                <a href="{{ route('reportar-dia.show', $agenda->id) }}"
                                                   class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center"
                                                   title="Gestionar el reporte de actividades diarias">
                                                    <i class="fas fa-calendar-plus me-1"></i> Reportar
                                                </a>
                                            @endif

                                            {{-- Botón Enviar --}}
                                            @if(!$agenda->estado || ($agenda->estado && $agenda->estado->nombre == 'BORRADOR') || ($agenda->estado && $agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas))
                                                @php
                                                    $diasTotal = $agenda->fecha_inicio->diffInDays($agenda->fecha_fin) + 1;
                                                    $diasReportados = $agenda->actividades->count();
                                                @endphp
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

                                            <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank" 
                                                class="btn btn-sm btn-dark rounded-pill px-2 shadow-sm fw-bold d-flex align-items-center justify-content-center" 
                                                style="min-width: 60px;"
                                                title="Ver PDF">
                                                <i class="fas fa-eye me-1"></i> PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
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
                <div class="px-4 py-4 bg-light border-top d-flex justify-content-center align-items-center">
                    <div class="pagination-container shadow-sm p-1 bg-white rounded-pill d-inline-block custom-pagination">
                        {{ $agendas->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALES DE REVISIÓN PARA AGENDAS DEVUELTAS --}}
@foreach($agendas as $agenda)
    @if($agenda->estado && ($agenda->estado->nombre == 'CORRECCIÓN' || ($agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas)))
    
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
                        <label class="small text-muted fw-bold d-block">CONTRATISTA REGISTRADO</label>
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
    /* ── Dropdown custom Estados ── */
    .custom-select-trigger {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.75rem;
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .custom-select-trigger:hover, .custom-select-trigger:focus {
        border-color: #39a900;
        box-shadow: 0 0 0 0.25rem rgba(57, 169, 0, 0.1);
        outline: none;
    }
    .custom-select-dropdown {
        position: absolute;
        top: 105%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        padding: 0.5rem;
        max-height: 300px;
        overflow-y: auto;
    }
    .custom-select-option {
        padding: 0.7rem 1rem;
        border-radius: 0.6rem;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 500;
        color: #475569;
        transition: all 0.15s ease;
    }
    .custom-select-option:hover { background: #f1f5f9; color: #39a900; }
    .custom-select-option.selected-item { background: #ecfdf5; color: #059669; font-weight: 700; }

    /* Ocultar texto por defecto del paginador */
    .custom-pagination nav > div:first-child,
    .custom-pagination nav div.d-none.flex-sm-fill > div:first-child,
    .custom-pagination nav p.text-muted {
        display: none !important;
    }
    
    .custom-pagination nav ul.pagination {
        margin-bottom: 0;
        justify-content: center;
    }

    /* Estilos Paginador SENA */
    .custom-pagination .page-item .page-link {
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
    
    .custom-pagination .page-item.active .page-link {
        background-color: #39a900 !important;
        color: white !important;
        box-shadow: 0 4px 6px -1px rgba(57, 169, 0, 0.4);
    }
    
    .custom-pagination .page-item:not(.active) .page-link:hover {
        background-color: #e2e8f0;
        color: #39a900;
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
        // Lógica para el dropdown personalizado de estados
        $('.custom-select-trigger').on('click', function(e) {
            e.stopPropagation();
            $(this).next('.custom-select-dropdown').toggle();
        });

        $('.custom-select-option').on('click', function() {
            const val = $(this).data('value');
            const txt = $(this).data('text');
            $('#estado_input').val(val);
            $('#estado_trigger_text').text(txt).addClass('text-dark fw-bold');
            $('.custom-select-dropdown').hide();
        });

        $(document).on('click', function() {
            $('.custom-select-dropdown').hide();
        });

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#39a900'
            });
        @endif

        $('.btn-enviar-agenda').on('click', function(e) {
            e.preventDefault();
            const btn = $(this);
            const total = parseInt(btn.data('total'));
            const reportados = parseInt(btn.data('reportados'));
            const form = btn.closest('form');
            const hasFirma = {{ auth()->user()->firma ? 'true' : 'false' }};

            if (!hasFirma) {
                Swal.fire({
                    title: 'Firma Faltante',
                    text: 'Debes cargar tu firma en el apartado "Mi Firma" para poder enviar la agenda.',
                    icon: 'error',
                    confirmButtonColor: '#39a900',
                    confirmButtonText: 'Ir a Mi Firma'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#modalFirmaUsuario').modal('show');
                    }
                });
                return;
            }

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
    });
</script>
@endpush
@endsection