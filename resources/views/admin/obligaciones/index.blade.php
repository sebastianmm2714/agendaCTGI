@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4 animate__animated animate__fadeIn">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Gestión de Obligaciones</h2>
            <p class="text-muted mb-0">Administración de Obligaciones de Contrato vinculadas a Categorías de Personal</p>
        </div>
        <div class="bg-warning bg-opacity-10 p-3 rounded-4 shadow-sm border border-warning border-opacity-10">
            <i class="fas fa-file-contract fa-2x text-warning"></i>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-bottom py-4 px-4">
                    <form action="{{ route('admin.obligaciones.index') }}" method="GET" id="searchForm">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <h5 class="fw-bold mb-0 text-dark">
                                    <i class="fas fa-list-check text-warning me-2"></i>Listado de Obligaciones
                                </h5>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0 ps-3">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search') }}" 
                                        class="form-control border-start-0" 
                                        placeholder="Buscar por descripción o categoría...">
                                    <button type="submit" class="btn btn-warning px-4 fw-bold">Buscar</button>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-center gap-2 justify-content-end">
                                <select name="per_page" class="form-select rounded-pill w-auto shadow-sm" onchange="this.form.submit()">
                                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                <button type="button" class="btn btn-dark rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearObligacion">
                                    <i class="fas fa-plus me-1"></i> Nueva
                                </button>
                            </div>
                        </div>
                    </form>
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
                @if($obligaciones->total() > $obligaciones->perPage())
                <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center">
                    <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                        {{ $obligaciones->links() }}
                    </div>
                </div>
                @endif
                <div class="card-footer bg-light border-0 py-3 px-4">
                    <p class="mb-0 small text-muted">
                        <i class="fas fa-info-circle me-1 text-primary"></i> 
                        Total: <strong>{{ $obligaciones->total() }}</strong> obligaciones encontradas.
                    </p>
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

<style>
    /* Ocultar texto por defecto del paginador */
    .custom-pagination nav > div:first-child,
    .custom-pagination nav div.d-none.flex-sm-fill > div:first-child,
    .custom-pagination nav p.text-muted {
        display: none !important;
    }
    
    .custom-pagination nav > div.d-none.flex-sm-fill > div {
        display: flex !important;
        justify-content: center !important;
    }

    /* Diseño circular Sena */
    .custom-pagination .pagination {
        margin-bottom: 0 !important;
        gap: 0.3rem;
    }
    .custom-pagination .page-item .page-link {
        border-radius: 50% !important;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        color: #4b5563;
        font-weight: 600;
        background: transparent;
        transition: all 0.2s ease;
    }
    .custom-pagination .page-item:not(.active) .page-link:hover {
        background: #f3f4f6;
        color: #39a900;
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #39a900 !important;
        color: white !important;
        box-shadow: 0 4px 6px -1px rgba(57, 169, 0, 0.4);
    }
    
    .custom-pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background: transparent;
    }
</style>
@endsection

