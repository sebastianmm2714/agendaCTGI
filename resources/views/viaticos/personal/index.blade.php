@extends('layouts.dashboard')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Personal Registrado</h2>
            <p class="text-muted">Consulta de contratistas e instructores registrados en el sistema.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filtros y Búsqueda --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('viaticos.personal.index') }}" method="GET" id="filterForm">
                <div class="row g-3 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                                class="form-control border-start-0 rounded-end-pill" 
                                placeholder="Nombre, cédula o email...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="vinculacion" id="vinculacionFilter" class="form-select rounded-pill" onchange="this.form.submit()">
                            <option value="">Todas las Vinculaciones</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->nombre }}" {{ request('vinculacion') == $cat->nombre ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success rounded-pill w-100 fw-bold shadow-sm">
                            Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Personal --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="personalTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nombre Completo</th>
                            <th>Identificación</th>
                            <th>Contacto</th>
                            <th>Rol / Vinculación</th>
                            <th>Nro. Cuenta - Tipo</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="user-row">
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                <div class="small text-muted">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark fw-normal border">{{ $user->numero_documento }}</span>
                            </td>
                            <td>
                                <div class="small"><i class="fas fa-envelope me-2 text-muted"></i>{{ $user->email }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-uppercase small" style="color: var(--brand)">{{ $user->role }}</div>
                                <div class="small text-muted">{{ $user->categoria->nombre ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="font-monospace small">{{ $user->numero_cuenta_tipo ?? 'Sin registrar' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3">Activo</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-user-slash fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No se encontraron registros que coincidan con la búsqueda.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->total() > $users->perPage())
        <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center">
            <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

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
