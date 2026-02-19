@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid px-4 py-5">
        <div class="d-flex align-items-center mb-5">
            <div class="bg-info bg-opacity-10 p-3 rounded-4 me-4">
                <i class="fas fa-chart-line fa-2x text-info"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1 text-dark">Reporte General de Agendas</h2>
                <p class="text-muted mb-0">Consolidado de todas las agendas registradas en el sistema.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Usuario</th>
                                <th>Ciudad Destino</th>
                                <th>Fecha Inicio</th>
                                <th>Estado</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agendas as $agenda)
                                <tr>
                                    <td class="ps-4">#{{ $agenda->id }}</td>
                                    <td>{{ $agenda->nombre_completo }}</td>
                                    <td>{{ $agenda->ciudad_destino }}</td>
                                    <td>{{ $agenda->fecha_inicio_desplazamiento }}</td>
                                    <td>
                                        <span class="badge border text-dark rounded-pill px-3">{{ $agenda->estado }}</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('agenda.pdf', $agenda->id) }}" class="btn btn-sm btn-outline-danger"
                                            target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No se encontraron registros.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection