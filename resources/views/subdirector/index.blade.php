@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Bandeja de Subdirección (Ordenador de Gasto)</h2>
        <span class="badge bg-primary">{{ $agendas->count() }} Pendientes</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Contratista</th>
                        <th>Ruta / Destino</th>
                        <th>Fecha Inicio</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendas as $agenda)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $agenda->nombre_completo }}</div>
                            <small class="text-muted">{{ $agenda->numero_documento }}</small>
                        </td>
                        <td>{{ $agenda->ciudad_destino }}</td>
                        <td>{{ $agenda->fecha_inicio_desplazamiento }}</td>
                        <td class="text-center">
                            <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-file-pdf"></i> Revisar PDF
                            </a>

                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalFirma{{ $agenda->id }}">
                                <i class="fas fa-signature"></i> Firmar Ahora
                            </button>

                            <div class="modal fade" id="modalFirma{{ $agenda->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('subdirector.autorizar', $agenda->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Aprobación de Ordenador de Gasto</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <p>Al firmar, usted autoriza el desplazamiento de <strong>{{ $agenda->nombre_completo }}</strong>.</p>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Subir imagen de su firma:</label>
                                                    <input type="file" name="firma_ordenador" class="form-control" accept="image/*" required>
                                                    <small class="text-muted">Formato: PNG o JPG (Fondo blanco o transparente recomendado)</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Confirmar y Firmar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No hay agendas pendientes por firma de subdirección.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection