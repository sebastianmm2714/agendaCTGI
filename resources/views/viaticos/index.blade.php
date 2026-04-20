@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Gestión Técnica de Viáticos</h2>
        <p class="text-muted">Valida los soportes y aprueba las agendas autorizadas por coordinación.</p>
    </div>

    {{-- TARJETAS FILTRADORAS (Actúan como Pestañas) --}}
    <div class="row mb-4 nav" id="pills-tab" role="tablist">
        {{-- CARD: PENDIENTES --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10 text-primary p-3 h-100 cursor-pointer {{ $activeTab == 'pendientes' ? 'active' : '' }}" 
                 id="tab-pendientes" data-bs-toggle="pill" data-bs-target="#pendientes" type="button" role="tab" style="transition: 0.3s;" onclick="document.getElementById('active_tab_input').value='pendientes';">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Pendientes por Revisar</p>
                        <h2 class="mb-0 fw-bold">{{ $pendientes->total() }}</h2>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- CARD: APROBADAS --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10 text-success p-3 h-100 cursor-pointer {{ $activeTab == 'aprobadas' ? 'active' : '' }}" 
                 id="tab-aprobadas" data-bs-toggle="pill" data-bs-target="#aprobadas" type="button" role="tab" style="transition: 0.3s;" onclick="document.getElementById('active_tab_input').value='aprobadas';">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Aprobadas</p>
                        <h2 class="mb-0 fw-bold">{{ $aprobadas->total() }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- CARD: DEVUELTAS --}}
        <div class="col-md-4 mb-3" role="presentation">
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10 text-danger p-3 h-100 cursor-pointer {{ $activeTab == 'devueltas' ? 'active' : '' }}" 
                 id="tab-devueltas" data-bs-toggle="pill" data-bs-target="#devueltas" type="button" role="tab" style="transition: 0.3s;" onclick="document.getElementById('active_tab_input').value='devueltas';">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 small fw-bold text-uppercase">Devueltas / Corrección</p>
                        <h2 class="mb-0 fw-bold">{{ $devueltas->total() }}</h2>
                    </div>
                    <i class="fas fa-undo-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- BUSCADOR INTELIGENTE --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body px-4 py-3 bg-light rounded-4">
            <form action="{{ route('viaticos.index') }}" method="GET" class="row g-3 align-items-center" id="searchForm">
                <input type="hidden" name="tab" id="active_tab_input" value="{{ $activeTab }}">
                {{-- Buscador --}}
                <div class="col-md-7">
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-white border-end-0 px-3">
                            <i class="fas fa-search text-success"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0 fw-medium" 
                               placeholder="Buscar por instructor, documento, fecha (DD/MM/YYYY) o destino..." 
                               value="{{ request('search') }}"
                               style="font-size: 0.95rem;">
                        @if(request('search'))
                            <a href="{{ route('viaticos.index', ['tab' => $activeTab]) }}" class="btn btn-white border-start-0 text-muted" title="Limpiar búsqueda">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
                {{-- Selector per_page --}}
                <div class="col-md-2">
                    <select name="per_page" class="form-select form-select-sm shadow-sm bg-white" onchange="this.form.submit()" title="Registros por página" style="font-size:0.92rem;">
                        @foreach([5, 10, 25, 50] as $opt)
                            <option value="{{ $opt }}" {{ request('per_page', 5) == $opt ? 'selected' : '' }}>
                                {{ $opt }} por página
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Buscar --}}
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 px-3 shadow-sm" style="min-height: 48px;">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ZONA DE TABS (Contenedores de tabla) --}}
    <div class="tab-content" id="pills-tabContent">
        {{-- VISTA: PENDIENTES --}}
        <div class="tab-pane fade {{ $activeTab == 'pendientes' ? 'show active' : '' }}" id="pendientes" role="tabpanel">
            @include('viaticos.partials.table', [
                'lista' => $pendientes, 
                'tipo' => 'pendientes'
            ])
            <div class="d-flex justify-content-center mt-3">
                <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                    {{ $pendientes->appends(['tab' => 'pendientes'])->links() }}
                </div>
            </div>
        </div>

        {{-- VISTA: APROBADAS --}}
        <div class="tab-pane fade {{ $activeTab == 'aprobadas' ? 'show active' : '' }}" id="aprobadas" role="tabpanel">
            <form id="bulk-export-form" action="{{ route('viaticos.exportBulk') }}" method="POST">
                @csrf
                @include('viaticos.partials.table', [
                    'lista' => $aprobadas, 
                    'tipo' => 'aprobadas'
                ])
            </form>
            <div class="d-flex justify-content-center mt-3">
                <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                    {{ $aprobadas->appends(['tab' => 'aprobadas'])->links() }}
                </div>
            </div>
        </div>

        {{-- VISTA: DEVUELTAS --}}
        <div class="tab-pane fade {{ $activeTab == 'devueltas' ? 'show active' : '' }}" id="devueltas" role="tabpanel">
            @include('viaticos.partials.table', [
                'lista' => $devueltas, 
                'tipo' => 'devueltas'
            ])
            <div class="d-flex justify-content-center mt-3">
                <div class="pagination-container shadow-sm p-1 bg-light rounded-pill d-inline-block custom-pagination">
                    {{ $devueltas->appends(['tab' => 'devueltas'])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Exportar por Supervisor --}}
<div class="modal fade" id="modalExportSupervisor" tabindex="-1" aria-labelledby="modalExportSupervisorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalExportSupervisorLabel">Exportar Agendas por Supervisor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-uppercase mb-2">Seleccione el Supervisor</label>
                    <select id="select-supervisor-modal" class="form-select rounded-pill shadow-sm border-0 bg-light px-4">
                        <option value="">-- Buscar Supervisor --</option>
                        @foreach($supervisores as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->nombre }} ({{ $sup->cargo }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="agendas-supervisor-container" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark">Agendas Aprobadas Encontradas</h6>
                        <span id="agendas-count" class="badge bg-primary rounded-pill px-3">0 agendas</span>
                    </div>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th>Contratista</th>
                                    <th>Ruta/Destino</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody id="agendas-supervisor-list">
                                {{-- Se llena por AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="no-agendas-msg" class="text-center py-5 d-none">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No se encontraron agendas aprobadas para este supervisor.</p>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <form id="form-export-supervisor" action="{{ route('viaticos.exportBulk') }}" method="POST" class="d-none">
                    @csrf
                    <div id="hidden-ids-container"></div>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="fas fa-file-excel me-2"></i> Descargar Todas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Lógica de selección individual
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.agenda-checkbox');
    const btnExportBulk = document.getElementById('btn-export-bulk');
    const selectedCount = document.getElementById('selected-count');

    function updateBulkButton() {
        if (!btnExportBulk || !selectedCount) return;
        const checkedCount = document.querySelectorAll('.agenda-checkbox:checked').length;
        selectedCount.textContent = checkedCount;
        if (checkedCount > 0) {
            btnExportBulk.classList.remove('d-none');
        } else {
            btnExportBulk.classList.add('d-none');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkButton();
            if (selectAll) {
                if (!this.checked) {
                    selectAll.checked = false;
                } else if (document.querySelectorAll('.agenda-checkbox:checked').length === checkboxes.length) {
                    selectAll.checked = true;
                }
            }
        });
    });

    // 2. Lógica de exportación por Supervisor
    const selectSupervisor = document.getElementById('select-supervisor-modal');
    const agendasContainer = document.getElementById('agendas-supervisor-container');
    const agendasList = document.getElementById('agendas-supervisor-list');
    const noAgendasMsg = document.getElementById('no-agendas-msg');
    const agendasCountBadge = document.getElementById('agendas-count');
    const formExportSupervisor = document.getElementById('form-export-supervisor');
    const hiddenIdsContainer = document.getElementById('hidden-ids-container');

    if (selectSupervisor) {
        selectSupervisor.addEventListener('change', function() {
            const supervisorId = this.value;
            if (!supervisorId) {
                agendasContainer.classList.add('d-none');
                noAgendasMsg.classList.add('d-none');
                formExportSupervisor.classList.add('d-none');
                return;
            }

            // Mostrar carga o estado inicial
            agendasList.innerHTML = '<tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Cargando agendas...</td></tr>';
            agendasContainer.classList.remove('d-none');
            noAgendasMsg.classList.add('d-none');
            formExportSupervisor.classList.add('d-none');

            // Llamada AJAX
            fetch(`{{ route('viaticos.agendasPorSupervisor') }}?supervisor_id=${supervisorId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.agendas.length > 0) {
                        agendasList.innerHTML = '';
                        hiddenIdsContainer.innerHTML = '';
                        agendasCountBadge.textContent = `${data.agendas.length} agendas`;

                        data.agendas.forEach(agenda => {
                            // Añadir a la tabla visual
                            const row = `
                                <tr>
                                    <td class="fw-bold">${agenda.contratista}</td>
                                    <td>${agenda.destino}</td>
                                    <td>${agenda.fecha_inicio}</td>
                                    <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success small">${agenda.estado}</span></td>
                                </tr>
                            `;
                            agendasList.insertAdjacentHTML('beforeend', row);

                            // Añadir input oculto para el form
                            const input = `<input type="hidden" name="ids[]" value="${agenda.id}">`;
                            hiddenIdsContainer.insertAdjacentHTML('beforeend', input);
                        });

                        formExportSupervisor.classList.remove('d-none');
                    } else {
                        agendasContainer.classList.add('d-none');
                        noAgendasMsg.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    agendasList.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Error al cargar las agendas.</td></tr>';
                });
        });
    }
});
</script>

</div>

<style>
    .card.active {
        box-shadow: 0 0 0 3px currentColor !important;
        transform: scale(1.02);
    }
    .cursor-pointer:hover {
        transform: translateY(-5px);
        filter: brightness(0.95);
    }

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