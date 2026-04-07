@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4 animate__animated animate__fadeIn">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Gestión de Catálogos</h2>
            <p class="text-muted mb-0">Administración de Estados de Agenda y Categorías de Personal</p>
        </div>
        <div class="bg-primary bg-opacity-10 p-3 rounded-4">
            <i class="fas fa-book-open fa-2x text-primary"></i>
        </div>
    </div>

    <div class="row g-4">
        {{-- SECCIÓN ESTADOS --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                    <h5 class="fw-bold mb-0"><i class="fas fa-tag text-success me-2"></i>Estados de Agenda</h5>
                    <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalCrearEstado">
                        <i class="fas fa-plus me-1"></i> Nuevo
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-muted small fw-bold">NOMBRE</th>
                                    <th class="text-muted small fw-bold">DESCRIPCIÓN</th>
                                    <th class="pe-4 text-end text-muted small fw-bold">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estados as $e)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $e->nombre }}</td>
                                    <td><small class="text-muted">{{ Str::limit($e->descripcion, 50) }}</small></td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-link text-primary p-1" data-bs-toggle="modal" data-bs-target="#modalEditarEstado{{ $e->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.estados.destroy', $e->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-1 btn-confirm-delete" data-title="¿Eliminar Estado?" data-text="Se eliminará el estado '{{ $e->nombre }}'.">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- MODAL EDITAR ESTADO --}}
                                <div class="modal fade" id="modalEditarEstado{{ $e->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <form action="{{ route('admin.estados.update', $e->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header border-0 bg-light p-4">
                                                    <h5 class="fw-bold mb-0">Editar Estado</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nombre del Estado</label>
                                                        <input type="text" name="nombre" class="form-control rounded-3" value="{{ $e->nombre }}" required>
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label fw-bold">Descripción (Opcional)</label>
                                                        <textarea name="descripcion" class="form-control rounded-3" rows="3">{{ $e->descripcion }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN CATEGORÍAS --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                    <h5 class="fw-bold mb-0"><i class="fas fa-users-cog text-primary me-2"></i>Categorías de Personal</h5>
                    <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearCategoria">
                        <i class="fas fa-plus me-1"></i> Nuevo
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-muted small fw-bold">NOMBRE</th>
                                    <th class="text-muted small fw-bold">DESCRIPCIÓN</th>
                                    <th class="pe-4 text-end text-muted small fw-bold">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categorias as $c)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $c->nombre }}</td>
                                    <td><small class="text-muted">{{ Str::limit($c->descripcion, 50) }}</small></td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-link text-primary p-1" data-bs-toggle="modal" data-bs-target="#modalEditarCategoria{{ $c->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.categorias.destroy', $c->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-1 btn-confirm-delete" data-title="¿Eliminar Categoría?" data-text="Se eliminará la categoría '{{ $c->nombre }}'.">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- MODAL EDITAR CATEGORÍA --}}
                                <div class="modal fade" id="modalEditarCategoria{{ $c->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <form action="{{ route('admin.categorias.update', $c->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header border-0 bg-light p-4">
                                                    <h5 class="fw-bold mb-0">Editar Categoría</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nombre de Categoría</label>
                                                        <input type="text" name="nombre" class="form-control rounded-3" value="{{ $c->nombre }}" required>
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label fw-bold">Descripción (Opcional)</label>
                                                        <textarea name="descripcion" class="form-control rounded-3" rows="3">{{ $c->descripcion }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR ESTADO --}}
<div class="modal fade" id="modalCrearEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.estados.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="fw-bold mb-0">Nuevo Estado de Agenda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Estado</label>
                        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: ENVIADA, APROBADA..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Descripción (Opcional)</label>
                        <textarea name="descripcion" class="form-control rounded-3" rows="3" placeholder="Descripción breve del estado"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2">Crear Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CREAR CATEGORÍA --}}
<div class="modal fade" id="modalCrearCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.categorias.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="fw-bold mb-0">Nueva Categoría de Personal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Categoría</label>
                        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: INSTRUCTOR, APOYO..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Descripción (Opcional)</label>
                        <textarea name="descripcion" class="form-control rounded-3" rows="3" placeholder="Descripción breve de la categoría"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Crear Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
