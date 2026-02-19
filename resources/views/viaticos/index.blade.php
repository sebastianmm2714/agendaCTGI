@extends('layouts.dashboard')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Bandeja de Viáticos</h2>
                <p class="text-muted">Revisión de agendas antes de la firma del ordenador de gasto.</p>
            </div>
            <span class="badge bg-primary fs-6">{{ $agendas->count() }} Pendientes</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Contratista</th>
                            <th>Ruta / Destino</th>
                            <th>Fecha Inicio</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendas as $agenda)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $agenda->nombre_completo }}</div>
                                    <small class="text-muted">{{ $agenda->numero_documento }}</small>
                                </td>
                                <td>
                                    <div class="badge bg-light text-dark border">{{ $agenda->ciudad_destino }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt me-2 text-primary"></i>
                                        <span>{{ \Carbon\Carbon::parse($agenda->fecha_inicio_desplazamiento)->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                            <i class="fas fa-file-pdf me-1"></i> Revisar PDF
                                        </a>

                                        <form action="{{ route('viaticos.aprobar', $agenda->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                                <i class="fas fa-check-circle me-1"></i> Aprobar Revisión
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-check fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">No hay agendas pendientes por revisión de viáticos.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection