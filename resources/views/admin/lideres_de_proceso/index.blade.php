@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4 animate__animated animate__fadeIn">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Gestión de Líderes de Proceso</h2>
            <p class="text-muted mb-0">Administración de Supervisores, Ordenadores de Gasto y Tesorería</p>
        </div>
        <div class="bg-info bg-opacity-10 p-3 rounded-4">
            <i class="fas fa-user-tie fa-2x text-info"></i>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white border-bottom py-4 px-4">
            <form action="{{ route('admin.lideres_de_proceso.index') }}" method="GET" id="searchForm">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <h5 class="fw-bold mb-0 text-dark d-none d-md-block">Listado de Líderes de Proceso</h5>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group shadow-sm rounded-pill overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 ps-3">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="form-control border-start-0" 
                                placeholder="Buscar por nombre, cargo o tipo...">
                            <button type="submit" class="btn btn-info px-4 fw-bold">Buscar</button>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center gap-2 justify-content-end">
                        <select name="per_page" class="form-select rounded-pill w-auto shadow-sm" onchange="this.form.submit()">
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                        <button type="button" class="btn btn-info rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearLider">
                            <i class="fas fa-plus me-1"></i> Nuevo
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
                            <th class="ps-4 py-3 text-muted small fw-bold">NOMBRE</th>
                            <th class="py-3 text-muted small fw-bold">CARGO</th>
                            <th class="py-3 text-muted small fw-bold text-center">TIPO / ROL</th>
                            <th class="py-3 text-center text-muted small fw-bold">FIRMA ADJUNTADA</th>
                            <th class="pe-4 py-3 text-end text-muted small fw-bold">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lideres_de_proceso as $fun)
                        <tr>
                            <td class="ps-4 text-uppercase small">
                                <div class="fw-bold text-dark">{{ $fun->nombre }}</div>
                                <div class="text-muted"><span class="small fw-normal">{{ $fun->tipo_documento }}</span> {{ $fun->numero_documento }}</div>
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $fun->cargo }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $color = match($fun->tipo) {
                                        'SUPERVISOR' => 'success',
                                        'ORDENADOR' => 'primary',
                                        'VIATICOS' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} px-3 py-2 rounded-pill fw-bold">
                                    {{ $fun->tipo }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($fun->firma)
                                    <i class="fas fa-check-circle text-success" title="Firma registrada"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger" title="Sin firma"></i>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-link text-primary p-1" data-bs-toggle="modal" data-bs-target="#modalEditarLider{{ $fun->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.lideres_de_proceso.destroy', $fun->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-1 btn-confirm-delete" data-title="¿Eliminar Líder de Proceso?" data-text="Se eliminará al líder de proceso '{{ $fun->nombre }}'.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- MODAL EDITAR LIDER --}}
                        <div class="modal fade" id="modalEditarLider{{ $fun->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <form action="{{ route('admin.lideres_de_proceso.update', $fun->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0 bg-light p-4">
                                            <h5 class="fw-bold mb-0 text-dark">Editar Líder de Proceso</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nombre Completo</label>
                                                <input type="text" name="nombre" class="form-control rounded-3" value="{{ $fun->nombre }}" required>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Tipo Doc.</label>
                                                    <select name="tipo_documento" class="form-select rounded-3">
                                                        <option value="CC" {{ $fun->tipo_documento == 'CC' ? 'selected' : '' }}>CC</option>
                                                        <option value="CE" {{ $fun->tipo_documento == 'CE' ? 'selected' : '' }}>CE</option>
                                                        <option value="PEP" {{ $fun->tipo_documento == 'PEP' ? 'selected' : '' }}>PEP</option>
                                                        <option value="PPT" {{ $fun->tipo_documento == 'PPT' ? 'selected' : '' }}>PPT</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="form-label fw-bold">Número de Documento</label>
                                                    <input type="text" name="numero_documento" class="form-control rounded-3" value="{{ $fun->numero_documento }}" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Cargo / Dependencia</label>
                                                <input type="text" name="cargo" class="form-control rounded-3" value="{{ $fun->cargo }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tipo de Líder de Proceso</label>
                                                <select name="tipo" class="form-select rounded-3" required>
                                                    <option value="SUPERVISOR" {{ $fun->tipo == 'SUPERVISOR' ? 'selected' : '' }}>SUPERVISOR </option>
                                                    <option value="ORDENADOR" {{ $fun->tipo == 'ORDENADOR' ? 'selected' : '' }}>ORDENADOR DE GASTO </option>
                                                    <option value="VIATICOS" {{ $fun->tipo == 'VIATICOS' ? 'selected' : '' }}>VIÁTICOS</option>
                                                </select>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-bold">Nro. Cuenta - Tipo</label>
                                                @php
                                                    $userVinculado = \App\Models\User::where('numero_documento', $fun->numero_documento)->first();
                                                @endphp
                                                <input type="text" name="numero_cuenta_tipo" class="form-control rounded-3" value="{{ $userVinculado->numero_cuenta_tipo ?? '' }}" placeholder="Ej: 91255755752 Ahorros Bancolombia">
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
                            <td colspan="5" class="text-center py-5 text-muted">No hay lideres_de_proceso registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                @if($lideres_de_proceso->total() > $lideres_de_proceso->perPage())
                <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center">
                    <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                        {{ $lideres_de_proceso->links() }}
                    </div>
                </div>
                @endif
                <div class="card-footer bg-light border-0 py-3 px-4">
                    <p class="mb-0 small text-muted">
                        <i class="fas fa-info-circle me-1 text-primary"></i> 
                        Total: <strong>{{ $lideres_de_proceso->total() }}</strong> lideres_de_proceso registrados.
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


{{-- MODAL CREAR LIDER --}}
<div class="modal fade" id="modalCrearLider" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.lideres_de_proceso.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="fw-bold mb-0 text-dark">Nuevo Líder de Proceso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: MARIA DEL MAR" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo Doc.</label>
                            <select name="tipo_documento" class="form-select rounded-3">
                                <option value="CC" selected>CC</option>
                                <option value="CE">CE</option>
                                <option value="PEP">PEP</option>
                                <option value="PPT">PPT</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Número de Documento</label>
                            <input type="text" name="numero_documento" id="lider_doc_input" class="form-control rounded-3" placeholder="Ej: 1018..." required>
                            <div id="lider_doc_feedback" class="small mt-1 d-none"></div>
                            
                            <div id="lider_password_generator_container" class="mt-2 d-none">
                                <label class="form-label fw-bold text-success small mb-1"><i class="fas fa-key me-1"></i>Contraseña Sugerida</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="password" id="lider_generated_password" class="form-control bg-light border-success text-success fw-bold" readonly>
                                    <button type="button" class="btn btn-success" id="lider_btn_copy_password" title="Copiar contraseña">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <small id="lider_copy_tooltip" class="text-success d-none"><i class="fas fa-check me-1"></i>¡Copiado!</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cargo / Dependencia</label>
                        <input type="text" name="cargo" class="form-control rounded-3" placeholder="Ej: COORDINADORA FORMACION" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Líder de Proceso</label>
                        <select name="tipo" class="form-select rounded-3" required>
                            <option value="SUPERVISOR" selected>SUPERVISOR (Coord./Apoyo)</option>
                            <option value="ORDENADOR">ORDENADOR DE GASTO (Subdirector)</option>
                            <option value="VIATICOS">TESORERÍA / VIÁTICOS</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Nro. Cuenta - Tipo</label>
                        <input type="text" name="numero_cuenta_tipo" class="form-control rounded-3" placeholder="Ej: 91255755752 Ahorros Bancolombia">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" id="btn_submit_lider" class="btn btn-info w-100 rounded-pill fw-bold py-2 shadow-sm">Registrar Líder de Proceso</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function () {
        let randomSuffixLider = '';
        let docValidLider = false;

        function validateFormLider() {
            $('#btn_submit_lider').prop('disabled', !docValidLider);
        }

        // Resetear al abrir modal y generar el sufijo aleatorio de 2 dígitos
        $('#modalCrearLider').on('show.bs.modal', function () {
            randomSuffixLider = Math.floor(10 + Math.random() * 90).toString();
            $('#lider_generated_password').val('');
            $('#lider_password_generator_container').addClass('d-none');
            $('#lider_copy_tooltip').addClass('d-none');
            $('#lider_doc_feedback').addClass('d-none').removeClass('text-success text-danger text-muted').text('');
            $('#lider_doc_input').removeClass('is-valid is-invalid').val('');
            docValidLider = false;
            validateFormLider();
        });

        // Generar contraseña y validar documento al escribir
        $('#lider_doc_input').on('input', function () {
            const doc = $(this).val().trim();
            if (doc.length < 5) {
                $('#lider_doc_feedback').removeClass('d-none text-success text-danger').addClass('text-muted').text('Ingrese un documento válido');
                $('#lider_password_generator_container').addClass('d-none');
                $('#lider_generated_password').val('');
                docValidLider = false;
                validateFormLider();
                return;
            }

            const password = doc + randomSuffixLider;
            $('#lider_generated_password').val(password);

            $.get("{{ route('admin.usuarios.checkDocument') }}", { documento: doc }, function (data) {
                if (data.exists) {
                    $('#lider_doc_feedback').removeClass('d-none text-success text-muted').addClass('text-danger').text('Este documento ya está registrado');
                    $('#lider_doc_input').addClass('is-invalid').removeClass('is-valid');
                    docValidLider = false;
                    $('#lider_password_generator_container').addClass('d-none');
                } else {
                    $('#lider_doc_feedback').removeClass('d-none text-danger text-muted').addClass('text-success').text('Documento disponible');
                    $('#lider_doc_input').addClass('is-valid').removeClass('is-invalid');
                    docValidLider = true;
                    $('#lider_password_generator_container').removeClass('d-none');
                }
                validateFormLider();
            });
        });

        // Copiar contraseña
        $(document).on('click', '#lider_btn_copy_password', function () {
            const pwd = $('#lider_generated_password').val();
            if (pwd) {
                navigator.clipboard.writeText(pwd).then(() => {
                    $('#lider_copy_tooltip').removeClass('d-none').hide().fadeIn().delay(1500).fadeOut(function() {
                        $(this).addClass('d-none');
                    });
                }).catch(err => {
                    console.error('Error al copiar: ', err);
                });
            }
        });
    });
</script>
@endpush
@endsection

