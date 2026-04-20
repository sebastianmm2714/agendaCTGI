@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4 animate__animated animate__fadeIn">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Gestión de Usuarios</h2>
            <p class="text-muted mb-0">Control de Contratistas y asignación de responsables</p>
        </div>
        <div class="bg-success bg-opacity-10 p-3 rounded-4">
            <i class="fas fa-users fa-2x text-success"></i>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white border-bottom py-4 px-4">
            <form action="{{ route('admin.usuarios.index') }}" method="GET" id="searchForm">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <h5 class="fw-bold mb-0 text-dark d-none d-md-block">Listado de Usuarios</h5>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group shadow-sm rounded-pill overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 ps-3">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="form-control border-start-0" 
                                placeholder="Buscar por nombre o documento...">
                            <button type="submit" class="btn btn-success px-4 fw-bold">Buscar</button>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center gap-2 justify-content-end">
                        <select name="per_page" class="form-select rounded-pill w-auto shadow-sm" onchange="this.form.submit()">
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                        <button type="button" class="btn btn-success rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                            <i class="fas fa-user-plus me-1"></i> Nuevo
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold">DOCUMENTO / NOMBRE</th>
                            <th class="py-3 text-muted small fw-bold">CONTACTO</th>
                            <th class="py-3 text-muted small fw-bold">CATEGORÍA</th>
                            <th class="py-3 text-muted small fw-bold">RESPONSABLES</th>
                            <th class="pe-4 py-3 text-end text-muted small fw-bold">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><span class="text-muted small">{{ $user->tipo_documento }}</span> {{ $user->numero_documento }}</div>
                                <div class="text-uppercase small text-muted">{{ $user->name }}</div>
                            </td>
                            <td>
                                <div class="small"><i class="fas fa-envelope text-muted me-1"></i> {{ $user->email }}</div>
                                <div class="small mt-1"><span class="badge bg-light text-dark border">{{ $user->role }}</span></div>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 fw-normal" style="background-color: #f0f7ff; color: #007bff; border: 1px solid #d0e7ff;">
                                    {{ $user->categoria->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="small"><strong>Sup:</strong> {{ $user->supervisor->nombre ?? 'No asignado' }}</div>
                                <div class="small mt-1"><strong>Ord:</strong> {{ $user->ordenador->nombre ?? 'No asignado' }}</div>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-link text-primary p-1" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario{{ $user->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.usuarios.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-1 btn-confirm-delete" data-title="¿Eliminar Usuario?" data-text="Se eliminará al contratista '{{ $user->name }}'.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- MODAL EDITAR USUARIO --}}
                        <div class="modal fade" id="modalEditarUsuario{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <form action="{{ route('admin.usuarios.update', $user->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0 bg-light p-4">
                                            <h5 class="fw-bold mb-0 text-dark">Editar Usuario: {{ $user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Nombre Completo</label>
                                                    <input type="text" name="name" class="form-control rounded-3" value="{{ $user->name }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Correo Electrónico</label>
                                                    <input type="email" name="email" class="form-control rounded-3" value="{{ $user->email }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold">Tipo Doc.</label>
                                                    <select name="tipo_documento" class="form-select rounded-3" required>
                                                        <option value="CC" {{ $user->tipo_documento == 'CC' ? 'selected' : '' }}>CC</option>
                                                        <option value="CE" {{ $user->tipo_documento == 'CE' ? 'selected' : '' }}>CE</option>
                                                        <option value="PEP" {{ $user->tipo_documento == 'PEP' ? 'selected' : '' }}>PEP</option>
                                                        <option value="PPT" {{ $user->tipo_documento == 'PPT' ? 'selected' : '' }}>PPT</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold">Número de Documento</label>
                                                    <input type="text" name="numero_documento" class="form-control rounded-3" value="{{ $user->numero_documento }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Salario / Honorarios</label>
                                                    <input type="number" name="salario_honorarios" class="form-control rounded-3" value="{{ $user->salario_honorarios }}" step="0.01">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Nro. Cuenta - Tipo</label>
                                                    <input type="text" name="numero_cuenta_tipo" class="form-control rounded-3" value="{{ $user->numero_cuenta_tipo }}" placeholder="Ej: 91255755752 Ahorros Bancolombia">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Rol</label>
                                                    <select name="role" class="form-select rounded-3" required>
                                                        <option value="contratista" {{ $user->role == 'contratista' ? 'selected' : '' }}>Contratista</option>
                                                        <option value="funcionario" {{ $user->role == 'funcionario' ? 'selected' : '' }}>Funcionario</option>
                                                        <option value="administrador" {{ $user->role == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                                        <option value="viaticos" {{ $user->role == 'viaticos' ? 'selected' : '' }}>Viáticos / Finanzas</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label fw-bold">Categoría de Personal</label>
                                                    <select name="categoria_personal_id" class="form-select rounded-3" required>
                                                        @foreach($categorias as $cat)
                                                            <option value="{{ $cat->id }}" {{ $user->categoria_personal_id == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Número de Contrato</label>
                                                    <input type="text" name="numero_contrato" class="form-control rounded-3" value="{{ $user->numero_contrato }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold">Año</label>
                                                    <input type="number" name="anio_contrato" class="form-control rounded-3" value="{{ $user->anio_contrato }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold">Vencimiento</label>
                                                    <input type="date" name="fecha_vencimiento" class="form-control rounded-3" value="{{ $user->fecha_vencimiento }}" required>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label fw-bold">Objeto Contractual</label>
                                                    <textarea name="objeto_contractual" class="form-control rounded-3" rows="2" required>{{ $user->objeto_contractual }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-success">Supervisor Asignado</label>
                                                    <select name="supervisor_id" class="form-select rounded-3 border-success">
                                                        <option value="">-- Sin asignar --</option>
                                                        @foreach($supervisores as $sup)
                                                            <option value="{{ $sup->id }}" {{ $user->supervisor_id == $sup->id ? 'selected' : '' }}>{{ $sup->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-primary">Ordenador de Gasto</label>
                                                    <select name="ordenador_id" class="form-select rounded-3 border-primary">
                                                        <option value="">-- Sin asignar --</option>
                                                        @foreach($ordenadores as $ord)
                                                            <option value="{{ $ord->id }}" {{ $user->ordenador_id == $ord->id ? 'selected' : '' }}>{{ $ord->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 p-4 pt-0">
                                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No se encontraron contratistas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($users->total() > $users->perPage())
                <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center">
                    <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                        {{ $users->links() }}
                    </div>
                </div>
                @endif
                <div class="card-footer bg-light border-0 py-3 px-4">
                    <p class="mb-0 small text-muted">
                        <i class="fas fa-info-circle me-1 text-primary"></i> 
                        Total: <strong>{{ $users->total() }}</strong> usuarios registrados.
                    </p>
                </div>
            </div>
        </div>
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

{{-- MODAL CREAR USUARIO --}}
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="fw-bold mb-0 text-dark">Registrar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Ej: JUAN PEREZ" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="correo@sena.edu.co" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Tipo Doc.</label>
                            <select name="tipo_documento" class="form-select rounded-3" required>
                                <option value="CC" selected>CC</option>
                                <option value="CE">CE</option>
                                <option value="PEP">PEP</option>
                                <option value="PPT">PPT</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número de Documento</label>
                            <input type="text" name="numero_documento" id="doc_input" class="form-control rounded-3" placeholder="Sin puntos ni comas" required>
                            <div id="doc_feedback" class="small mt-1 d-none"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Salario / Honorarios</label>
                            <input type="number" name="salario_honorarios" class="form-control rounded-3" placeholder="Ej: 5000000" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nro. Cuenta - Tipo</label>
                            <input type="text" name="numero_cuenta_tipo" class="form-control rounded-3" placeholder="Ej: 91255755752 Ahorros Bancolombia">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Rol en Sistema</label>
                            <select name="role" class="form-select rounded-3" required>
                                <option value="contratista" selected>Contratista</option>
                                <option value="funcionario">Funcionario</option>
                                <option value="administrador">Administrador</option>
                                <option value="viaticos">Viáticos / Finanzas</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Categoría de Personal</label>
                            <select name="categoria_personal_id" id="cat_input" class="form-select rounded-3" required>
                                <option value="" disabled selected>-- Seleccione una categoría --</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="cat_feedback" class="small mt-1 d-none text-danger">Por favor seleccione una categoría</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Número de Contrato</label>
                            <input type="text" name="numero_contrato" class="form-control rounded-3" placeholder="Ej: 123-2024" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Año</label>
                            <input type="number" name="anio_contrato" class="form-control rounded-3" placeholder="2024" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Objeto Contractual</label>
                            <textarea name="objeto_contractual" class="form-control rounded-3" rows="2" placeholder="Descripción del objeto del contrato" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success">Supervisor de Contrato</label>
                            <select name="supervisor_id" class="form-select rounded-3 border-success">
                                <option value="">-- Sin asignar --</option>
                                @foreach($supervisores as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">Ordenador de Gasto</label>
                            <select name="ordenador_id" class="form-select rounded-3 border-primary">
                                <option value="">-- Sin asignar --</option>
                                @foreach($ordenadores as $ord)
                                    <option value="{{ $ord->id }}">{{ $ord->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-info mt-4 mb-0 small rounded-3">
                        <i class="fas fa-info-circle me-1"></i> La contraseña por defecto será el número de documento del usuario.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" id="btn_submit_user" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let docValid = false;
    let catValid = false;

    function validateForm() {
        $('#btn_submit_user').prop('disabled', !(docValid && catValid));
    }

    // Validación de documento en tiempo real
    $('#doc_input').on('input', function() {
        const doc = $(this).val();
        if (doc.length < 5) {
            $('#doc_feedback').removeClass('d-none text-success text-danger').addClass('text-muted').text('Ingrese un documento válido');
            docValid = false;
            validateForm();
            return;
        }

        $.get("{{ route('admin.usuarios.checkDocument') }}", { documento: doc }, function(data) {
            if (data.exists) {
                $('#doc_feedback').removeClass('d-none text-success text-muted').addClass('text-danger').text('Este documento ya está registrado');
                $('#doc_input').addClass('is-invalid').removeClass('is-valid');
                docValid = false;
            } else {
                $('#doc_feedback').removeClass('d-none text-danger text-muted').addClass('text-success').text('Documento disponible');
                $('#doc_input').addClass('is-valid').removeClass('is-invalid');
                docValid = true;
            }
            validateForm();
        });
    });

    // Validación de categoría
    $('#cat_input').on('change', function() {
        if ($(this).val()) {
            $('#cat_feedback').addClass('d-none');
            $(this).addClass('is-valid').removeClass('is-invalid');
            catValid = true;
        } else {
            $('#cat_feedback').removeClass('d-none');
            $(this).addClass('is-invalid').removeClass('is-valid');
            catValid = false;
        }
        validateForm();
    });

    // Resetear al abrir modal
    $('#modalCrearUsuario').on('shown.bs.modal', function () {
        validateForm();
    });

    // Filtro de búsqueda en tiempo real
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });
});
</script>
@endpush
@endsection
