@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <h2 class="fw-bold mb-4">Agendas Pendientes por Autorizar</h2>

        @if(session('alerta_exitosa'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('alerta_exitosa') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Contratista</th>
                            <th class="py-3">Municipio</th>
                            <th class="py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendas as $agenda)
                            <tr>
                                <td class="px-4 fw-bold">{{ $agenda->nombre_completo }}</td>
                                <td>{{ $agenda->ciudad_destino }}</td>
                                <td class="text-center">
                                    <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm me-2">
                                        <i class="fas fa-eye me-1"></i> Revisar PDF
                                    </a>

                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFirma{{ $agenda->id }}">
                                        <i class="fas fa-pen-nib me-1"></i> Firmar y Autorizar
                                    </button>

                                    <div class="modal fade" id="modalFirma{{ $agenda->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('agenda.autorizar', $agenda->id) }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Subir Firma de Autorización</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p class="mb-0 text-muted small">Destino:
                                                            <strong>{{ $agenda->ciudad_destino }}</strong>
                                                        </p>
                                                        <p class="text-muted small">Cargue la imagen de su firma para autorizar
                                                            la agenda de <strong>{{ $agenda->nombre_completo }}</strong>.</p>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Archivo de firma (PNG o
                                                                JPG)</label>
                                                            <input type="file" name="firma_archivo" class="form-control"
                                                                accept="image/*" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-success">Autorizar Ahora</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">No hay agendas esperando su firma.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection