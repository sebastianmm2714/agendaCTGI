@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4 animate__animated animate__fadeIn">
        <div>
            <h2 class="fw-bold mb-1 text-dark text-uppercase font-monospace">Gestión de Obligaciones</h2>
            <p class="text-muted mb-0">Administración de Obligaciones de Contrato vinculadas a Categorías de Personal</p>
        </div>
        <div class="bg-warning bg-opacity-10 p-3 rounded-4 shadow-sm border border-warning border-opacity-10">
            <i class="fas fa-file-contract fa-2x text-warning"></i>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-list-check text-warning me-2"></i>Listado de Obligaciones</h5>
                    <button class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearObligacion">
                        <i class="fas fa-plus me-1"></i> Nueva Obligación
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4 text-muted small fw-bold py-3 text-uppercase" style="width: 25%;">CATEGORÍA</th>
                                    <th class="text-muted small fw-bold py-3 text-uppercase">OBLIGACIÓN / DESCRIPCIÓN</th>
                                    <th class="pe-4 text-end text-muted small fw-bold py-3 text-uppercase" style="width: 150px;">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($obligaciones as $ob)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill fw-bold">
                                            {{ $ob->categoria->nombre ?? 'Sin categoría' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark text-uppercase small">{{ $ob->nombre }}</td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-outline-primary btn-sm rounded-pill p-1 px-2 border-0" data-bs-toggle="modal" data-bs-target="#modalEditarObligacion{{ $ob->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.obligaciones.destroy', $ob->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill p-1 px-2 border-0 btn-confirm-delete" data-title="¿Eliminar Obligación?" data-text="Se eliminará la obligación '{{ $ob->nombre }}'.">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <div class="mb-3 opacity-25">
                                            <i class="fas fa-file-contract fa-3x text-warning"></i>
                                        </div>
                                        <p class="mb-0 small fw-bold">No hay obligaciones configuradas en el sistema.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 px-4">
                    <p class="mb-0 small text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Las obligaciones se filtran por categoría de personal en las agendas de los contratistas.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR OBLIGACIÓN --}}
<div class="modal fade" id="modalCrearObligacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.obligaciones.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-primary bg-opacity-10 p-4">
                    <h5 class="fw-bold mb-0 text-primary">Nueva Obligación de Contrato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Categoría de Personal</label>
                        <select name="categoria_personal_id" class="form-select rounded-3 border-secondary-subtle p-2" required>
                            <option value="" disabled selected>-- SELECCIONE CATEGORÍA --</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Nombre de la Obligación</label>
                        <textarea name="nombre" class="form-control rounded-3 border-secondary-subtle font-monospace small shadow-sm" rows="6" placeholder="Ej: IMPARTIR FORMACIÓN PROFESIONAL INTEGRAL..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">Guardar Obligación</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODALES DE EDICIÓN --}}
@foreach($obligaciones as $ob)
<div class="modal fade" id="modalEditarObligacion{{ $ob->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.obligaciones.update', $ob->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 bg-primary bg-opacity-10 p-4">
                    <h5 class="fw-bold mb-0 text-primary">Editar Obligación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Categoría de Personal</label>
                        <select name="categoria_personal_id" class="form-select rounded-3 border-secondary-subtle p-2" required>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ $ob->categoria_personal_id == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Nombre de la Obligación</label>
                        <textarea name="nombre" class="form-control rounded-3 border-secondary-subtle font-monospace small shadow-sm" rows="6" required>{{ $ob->nombre }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
