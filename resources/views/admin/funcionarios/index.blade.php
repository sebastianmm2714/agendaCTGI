@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4 animate__animated animate__fadeIn">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Gestión de Funcionarios</h2>
            <p class="text-muted mb-0">Administración de Supervisores, Ordenadores de Gasto y Tesorería</p>
        </div>
        <div class="bg-info bg-opacity-10 p-3 rounded-4">
            <i class="fas fa-user-tie fa-2x text-info"></i>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
            <div class="d-flex align-items-center gap-3 flex-grow-1 me-4">
                <h5 class="fw-bold mb-0 text-dark d-none d-md-block">Listado</h5>
                <div class="input-group input-group-sm" style="max-width: 400px;">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-light border-0" placeholder="Buscar por nombre, cargo o tipo...">
                </div>
            </div>
            <button class="btn btn-info btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearFuncionario">
                <i class="fas fa-plus me-1"></i> Nuevo Funcionario
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold">NOMBRE</th>
                            <th class="py-3 text-muted small fw-bold">CARGO</th>
                            <th class="py-3 text-muted small fw-bold">TIPO / ROL</th>
                            <th class="py-3 text-center text-muted small fw-bold">FIRMA ADJUNTADA</th>
                            <th class="pe-4 py-3 text-end text-muted small fw-bold">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funcionarios as $fun)
                        <tr>
                            <td class="ps-4 text-uppercase small">
                                <div class="fw-bold text-dark">{{ $fun->nombre }}</div>
                                <div class="text-muted"><span class="small fw-normal">{{ $fun->tipo_documento }}</span> {{ $fun->numero_documento }}</div>
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $fun->cargo }}</div>
                                <div class="small text-muted"><i class="fas fa-envelope me-1"></i>{{ $fun->email }}</div>
                            </td>
                            <td>
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
                                <button class="btn btn-link text-primary p-1" data-bs-toggle="modal" data-bs-target="#modalEditarFuncionario{{ $fun->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.funcionarios.destroy', $fun->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-1 btn-confirm-delete" data-title="¿Eliminar Funcionario?" data-text="Se eliminará al funcionario '{{ $fun->nombre }}'.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- MODAL EDITAR FUNCIONARIO --}}
                        <div class="modal fade" id="modalEditarFuncionario{{ $fun->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <form action="{{ route('admin.funcionarios.update', $fun->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0 bg-light p-4">
                                            <h5 class="fw-bold mb-0 text-dark">Editar Funcionario</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nombre Completo</label>
                                                <input type="text" name="nombre" class="form-control rounded-3" value="{{ $fun->nombre }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Correo Electrónico</label>
                                                <input type="email" name="email" class="form-control rounded-3" value="{{ $fun->email }}" required>
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
                                                <label class="form-label fw-bold">Tipo de Funcionario</label>
                                                <select name="tipo" class="form-select rounded-3" required>
                                                    <option value="SUPERVISOR" {{ $fun->tipo == 'SUPERVISOR' ? 'selected' : '' }}>SUPERVISOR </option>
                                                    <option value="ORDENADOR" {{ $fun->tipo == 'ORDENADOR' ? 'selected' : '' }}>ORDENADOR DE GASTO </option>
                                                    <option value="VIATICOS" {{ $fun->tipo == 'VIATICOS' ? 'selected' : '' }}>VIÁTICOS</option>
                                                </select>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-bold">Nro. Cuenta - Tipo</label>
                                                @php
                                                    $userVinculado = \App\Models\User::where('email', $fun->email)->orWhere('numero_documento', $fun->numero_documento)->first();
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
                            <td colspan="5" class="text-center py-5 text-muted">No hay funcionarios registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR FUNCIONARIO --}}
<div class="modal fade" id="modalCrearFuncionario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.funcionarios.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="fw-bold mb-0 text-dark">Nuevo Funcionario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: MARIA DEL MAR" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="correo@sena.edu.co" required>
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
                            <input type="text" name="numero_documento" class="form-control rounded-3" placeholder="Ej: 1018..." required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cargo / Dependencia</label>
                        <input type="text" name="cargo" class="form-control rounded-3" placeholder="Ej: COORDINADORA FORMACION" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Funcionario</label>
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
                    <button type="submit" class="btn btn-info w-100 rounded-pill fw-bold py-2 shadow-sm">Registrar Funcionario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
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
