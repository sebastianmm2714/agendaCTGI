@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Gestión de Personal</h2>
            <p class="text-muted">Administra contratistas y funcionarios registrados en el sistema.</p>
        </div>
        <button class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-user-plus me-2"></i> Nuevo Personal
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filtros y Búsqueda --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill" placeholder="Buscar por nombre, cédula o email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="roleFilter" class="form-select rounded-pill">
                        <option value="">Todos los Roles</option>
                        <option value="contratista">Contratista</option>
                        <option value="supervisor_contrato">Supervisor (Coordinador)</option>
                        <option value="ordenador_gasto">Ordenador Gasto (Subdirector)</option>
                        <option value="viaticos">Viáticos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="vinculacionFilter" class="form-select rounded-pill">
                        <option value="">Todas las Vinculaciones</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->nombre }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Personal --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="personalTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nombre Completo</th>
                            <th>Identificación</th>
                            <th>Contacto</th>
                            <th>Rol / Vinculación</th>
                            <th>N° Cuenta</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="user-row" 
                            data-name="{{ strtolower($user->name) }}" 
                            data-cedula="{{ $user->numero_documento }}" 
                            data-email="{{ strtolower($user->email) }}"
                            data-role="{{ $user->role }}"
                            data-vinculacion="{{ $user->categoria->nombre ?? '' }}">
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
                                <div class="font-monospace small">{{ $user->numero_cuenta ?? 'Sin registrar' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3">Activo</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle edit-user-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-tipo-doc="{{ $user->tipo_documento }}"
                                            data-documento="{{ $user->numero_documento }}"
                                            data-role="{{ $user->role }}"
                                            data-categoria="{{ $user->categoria_personal_id }}"
                                            data-salario="{{ $user->salario_honorarios }}"
                                            data-cuenta-tipo="{{ $user->numero_cuenta_tipo }}"
                                            data-contrato="{{ $user->numero_contrato }}"
                                            data-anio="{{ $user->anio_contrato }}"
                                            data-vencimiento="{{ $user->fecha_vencimiento }}"
                                            data-objeto="{{ $user->objeto_contractual }}"
                                            data-supervisor="{{ $user->supervisor_id }}"
                                            data-ordenador="{{ $user->ordenador_id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('viaticos.personal.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="noResults" class="p-5 text-center d-none">
                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No se encontraron registros que coincidan con la búsqueda.</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear Personal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Registrar Nuevo Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('viaticos.personal.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Nombre Completo</label>
                            <input type="text" name="name" class="form-control rounded-3" required placeholder="Ej: JUAN PEREZ">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control rounded-3" required placeholder="correo@sena.edu.co">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase">Tipo Doc.</label>
                            <select name="tipo_documento" class="form-select rounded-3" required>
                                <option value="CC">CC</option>
                                <option value="CE">CE</option>
                                <option value="PEP">PEP</option>
                                <option value="PPT">PPT</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Número de Documento</label>
                            <input type="text" name="numero_documento" id="doc_input" class="form-control rounded-3" required placeholder="Sin puntos ni comas">
                            <div id="doc_feedback" class="small mt-1 d-none"></div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-uppercase">Salario / Honorarios</label>
                            <input type="number" name="salario_honorarios" class="form-control rounded-3" placeholder="Ej: 5000000">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Nro. Cuenta - Tipo</label>
                            <input type="text" name="numero_cuenta_tipo" class="form-control rounded-3" placeholder="Ej: 91255755752 Ahorros Bancolombia">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Rol en Sistema</label>
                            <select name="role" class="form-select rounded-3" required>
                                <option value="contratista">Contratista</option>
                                <option value="supervisor_contrato">Supervisor (Coordinador)</option>
                                <option value="ordenador_gasto">Ordenador Gasto (Subdirector)</option>
                                <option value="viaticos">Viáticos</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Categoría de Personal</label>
                            <select name="categoria_personal_id" id="cat_input" class="form-select rounded-3" required>
                                <option value="">-- Seleccione una categoría --</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="cat_feedback" class="small mt-1 text-danger d-none">Debe seleccionar una categoría</div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-uppercase">Número de Contrato</label>
                            <input type="text" name="numero_contrato" class="form-control rounded-3" required placeholder="Ej: 123-2024">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase">Año</label>
                            <input type="number" name="anio_contrato" value="{{ date('Y') }}" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" class="form-control rounded-3" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Objeto Contractual</label>
                            <textarea name="objeto_contractual" class="form-control rounded-3" rows="2" required placeholder="Descripción del objeto del contrato"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-success">Supervisor de Contrato</label>
                            <select name="supervisor_id" class="form-select border-success border-opacity-25 rounded-3">
                                <option value="">-- Sin asignar --</option>
                                @foreach($supervisores as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-primary">Ordenador de Gasto</label>
                            <select name="ordenador_id" class="form-select border-primary border-opacity-25 rounded-3">
                                <option value="">-- Sin asignar --</option>
                                @foreach($ordenadores as $ord)
                                    <option value="{{ $ord->id }}">{{ $ord->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3 py-2 border-0 rounded-3 mb-0" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-2"></i> La contraseña por defecto será el número de documento del usuario.
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btn_submit_user" class="btn btn-success rounded-pill px-4 fw-bold">Crear Personal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Personal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Editar Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Nombre Completo</label>
                            <input type="text" name="name" id="edit_name" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Correo Electrónico</label>
                            <input type="email" name="email" id="edit_email" class="form-control rounded-3" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase">Tipo Doc.</label>
                            <select name="tipo_documento" id="edit_tipo_doc" class="form-select rounded-3" required>
                                <option value="CC">CC</option>
                                <option value="CE">CE</option>
                                <option value="PEP">PEP</option>
                                <option value="PPT">PPT</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Número de Documento</label>
                            <input type="text" name="numero_documento" id="edit_documento" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-uppercase">Salario / Honorarios</label>
                            <input type="number" name="salario_honorarios" id="edit_salario" class="form-control rounded-3">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Nro. Cuenta - Tipo</label>
                            <input type="text" name="numero_cuenta_tipo" id="edit_cuenta_tipo" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Rol en Sistema</label>
                            <select name="role" id="edit_role" class="form-select rounded-3" required>
                                <option value="contratista">Contratista</option>
                                <option value="supervisor_contrato">Supervisor (Coordinador)</option>
                                <option value="ordenador_gasto">Ordenador Gasto (Subdirector)</option>
                                <option value="viaticos">Viáticos</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Categoría de Personal</label>
                            <select name="categoria_personal_id" id="edit_categoria" class="form-select rounded-3" required>
                                <option value="">-- Seleccione una categoría --</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-uppercase">Número de Contrato</label>
                            <input type="text" name="numero_contrato" id="edit_contrato" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase">Año</label>
                            <input type="number" name="anio_contrato" id="edit_anio" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" id="edit_vencimiento" class="form-control rounded-3" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Objeto Contractual</label>
                            <textarea name="objeto_contractual" id="edit_objeto" class="form-control rounded-3" rows="2" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-success">Supervisor de Contrato</label>
                            <select name="supervisor_id" id="edit_supervisor" class="form-select border-success border-opacity-25 rounded-3">
                                <option value="">-- Sin asignar --</option>
                                @foreach($supervisores as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-primary">Ordenador de Gasto</label>
                            <select name="ordenador_id" id="edit_ordenador" class="form-select border-primary border-opacity-25 rounded-3">
                                <option value="">-- Sin asignar --</option>
                                @foreach($ordenadores as $ord)
                                    <option value="{{ $ord->id }}">{{ $ord->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Actualizar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const vinculacionFilter = document.getElementById('vinculacionFilter');
    const rows = document.querySelectorAll('.user-row');
    const noResults = document.getElementById('noResults');
    const table = document.getElementById('personalTable');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedRole = roleFilter.value;
        const selectedVinculacion = vinculacionFilter.value;
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.dataset.name;
            const cedula = row.dataset.cedula;
            const email = row.dataset.email;
            const role = row.dataset.role;
            const vinculacion = row.dataset.vinculacion;

            const matchesSearch = name.includes(searchTerm) || 
                                cedula.includes(searchTerm) || 
                                email.includes(searchTerm);
            
            const matchesRole = selectedRole === '' || role === selectedRole;
            const matchesVinculacion = selectedVinculacion === '' || vinculacion === selectedVinculacion;

            if (matchesSearch && matchesRole && matchesVinculacion) {
                row.classList.remove('d-none');
                visibleCount++;
            } else {
                row.classList.add('d-none');
            }
        });

        if (visibleCount === 0) {
            table.classList.add('d-none');
            noResults.classList.remove('d-none');
        } else {
            table.classList.remove('d-none');
            noResults.classList.add('d-none');
        }
    }

    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    vinculacionFilter.addEventListener('change', filterTable);

    // Lógica para cargar datos en el Modal de Edición
    const editBtns = document.querySelectorAll('.edit-user-btn');
    const editForm = document.getElementById('editForm');
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const email = this.dataset.email;
            const tipoDoc = this.dataset.tipoDoc;
            const documento = this.dataset.documento;
            const role = this.dataset.role;
            const categoria = this.dataset.categoria;
            const salario = this.dataset.salario;
            const cuentaTipo = this.dataset.cuentaTipo;
            const contrato = this.dataset.contrato;
            const anio = this.dataset.anio;
            const vencimiento = this.dataset.vencimiento;
            const objeto = this.dataset.objeto;
            const supervisor = this.dataset.supervisor;
            const ordenador = this.dataset.ordenador;

            // Actualizar la acción del formulario
            editForm.action = `/viaticos/personal/${id}`;
            
            // Poblar campos
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_tipo_doc').value = tipoDoc;
            document.getElementById('edit_documento').value = documento;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_categoria').value = categoria;
            document.getElementById('edit_salario').value = salario;
            document.getElementById('edit_cuenta_tipo').value = cuentaTipo;
            document.getElementById('edit_contrato').value = contrato;
            document.getElementById('edit_anio').value = anio;
            document.getElementById('edit_vencimiento').value = vencimiento;
            document.getElementById('edit_objeto').value = objeto;
            document.getElementById('edit_supervisor').value = supervisor || '';
            document.getElementById('edit_ordenador').value = ordenador || '';
        });
    });

    // --- NUEVAS VALIDACIONES INLINE (AJAX) ---
    let docValid = false;
    let catValid = false;

    function validateSubmitButton() {
        const btn = document.getElementById('btn_submit_user');
        btn.disabled = !(docValid && catValid);
    }

    // Validación de documento en tiempo real
    const docInput = document.getElementById('doc_input');
    const docFeedback = document.getElementById('doc_feedback');

    if (docInput) {
        docInput.addEventListener('input', function() {
            const doc = this.value;
            if (doc.length < 5) {
                docFeedback.classList.remove('d-none', 'text-success', 'text-danger');
                docFeedback.classList.add('text-muted');
                docFeedback.textContent = 'Ingrese un documento válido';
                this.classList.remove('is-valid', 'is-invalid');
                docValid = false;
                validateSubmitButton();
                return;
            }

            fetch(`{{ route('viaticos.personal.checkDocument') }}?documento=${doc}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        docFeedback.classList.remove('d-none', 'text-success', 'text-muted');
                        docFeedback.classList.add('text-danger');
                        docFeedback.textContent = 'Este documento ya está registrado';
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                        docValid = false;
                    } else {
                        docFeedback.classList.remove('d-none', 'text-danger', 'text-muted');
                        docFeedback.classList.add('text-success');
                        docFeedback.textContent = 'Documento disponible';
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                        docValid = true;
                    }
                    validateSubmitButton();
                });
        });
    }

    // Validación de categoría
    const catInput = document.getElementById('cat_input');
    const catFeedback = document.getElementById('cat_feedback');

    if (catInput) {
        catInput.addEventListener('change', function() {
            if (this.value) {
                catFeedback.classList.add('d-none');
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                catValid = true;
            } else {
                catFeedback.classList.remove('d-none');
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                catValid = false;
            }
            validateSubmitButton();
        });
    }

    // Resetear validación al abrir modal
    const createModal = document.getElementById('createModal');
    if (createModal) {
        createModal.addEventListener('shown.bs.modal', function () {
            validateSubmitButton();
        });
    }

    // --- SWEETALERT PARA ELIMINAR ---
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede deshacer y el personal será eliminado permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
