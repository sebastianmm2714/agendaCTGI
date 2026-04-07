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
                                        @if($agenda->estado && $agenda->estado->nombre == 'CORRECCIÓN')
                                            <div class="alert alert-danger mb-0 py-1 px-2 shadow-sm d-inline-block text-start" style="font-size: 0.75rem; border-left: 4px solid #dc3545;">
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
                                            @if($agenda->estado && $agenda->estado->nombre == 'CORRECCIÓN')
                                                <a href="{{ route('formulario', $agenda->id) }}"
                                                   class="btn btn-sm btn-warning rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center">
                                                    <i class="fas fa-edit me-1"></i> Corregir
                                                </a>
                                            @elseif(!$agenda->estado || strtoupper($agenda->estado->nombre) == 'BORRADOR')
                                                <a href="{{ route('reportar-dia.show', $agenda->id) }}"
                                                   class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center">
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

                                            <a href="{{ route('agenda.pdf', $agenda->id) }}" 
                                                class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                                style="width: 32px; height: 32px;"
                                                target="_blank" title="Ver PDF">
                                                <i class="fas fa-file-pdf"></i>
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
            </div>
        </div>
    </div>
</div>
@push('scripts')
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
    });
</script>
@endpush
@endsection