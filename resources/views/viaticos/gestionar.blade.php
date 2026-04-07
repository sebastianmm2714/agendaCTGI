@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        {{-- COLUMNA IZQUIERDA: VISOR DE PDF --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4" style="height: 80vh;">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Documento de Agenda #{{ $agenda->id }}</h5>
                    <a href="{{ route('agenda.pdf', $agenda->id) }}" target="_blank" class="btn btn-sm btn-light">
                        <i class="fas fa-external-link-alt"></i> Abrir en otra pestaña
                    </a>
                </div>
                <div class="card-body p-0">
                    {{-- Usamos un iframe para mostrar el PDF directamente --}}
                    <iframe src="{{ route('agenda.pdf', $agenda->id) }}" width="100%" height="100%" style="border: none;"></iframe>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: REVISIÓN Y OBSERVACIONES --}}
        <div class="col-lg-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Revisión Técnica</h5>
                    <p class="text-muted small">Verifique que los datos en el PDF coincidan con la solicitud.</p>
                    
                    <form action="{{ route('viaticos.procesar', $agenda->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Observaciones de la revisión</label>
                            <textarea name="observaciones" class="form-control" rows="6" 
                                placeholder="Escriba aquí si hay algún error o nota para la subdirección...">{{ $agenda->observaciones_finanzas }}</textarea>
                        </div>

                        {{-- Solo mostrar botones si está en estado de revisión --}}
                        @if($agenda->estado->nombre == 'APROBADA_SUPERVISOR')
                        <div class="d-grid gap-3">
                            <button type="submit" name="accion" value="aprobar" class="btn btn-success btn-lg fw-bold py-3">
                                <i class="fas fa-check-circle me-2"></i> Validar y Enviar
                            </button>
                            <button type="submit" name="accion" value="devolver" class="btn btn-outline-danger btn-lg">
                                <i class="fas fa-reply me-2"></i> Devolver Agenda
                            </button>
                        </div>
                        @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Esta agenda ya fue procesada.
                        </div>
                        @endif
                    </form>
                </div>
                <div class="card-footer bg-light border-0 text-center py-3">
                    <a href="{{ route('reportes') }}" class="text-decoration-none text-muted fw-bold">
                        <i class="fas fa-arrow-left me-1"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection