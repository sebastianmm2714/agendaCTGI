<div class="card border-0 shadow-sm rounded-4">
    @if($tipo == 'aprobadas')
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Listado de Agendas</h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalExportSupervisor">
                    <i class="fas fa-user-tie me-2"></i> Exportar por Supervisor
                </button>
                <button type="submit" id="btn-export-bulk" class="btn btn-success rounded-pill px-4 shadow-sm d-none">
                    <i class="fas fa-file-excel me-2"></i> Descarga Masiva Excel (<span id="selected-count">0</span>)
                </button>
            </div>
        </div>
    @else
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Listado de Agendas</h5>
        </div>
    @endif
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        @if($tipo == 'aprobadas')
                        <th class="ps-4" style="width: 40px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        @endif
                        <th class="{{ $tipo != 'aprobadas' ? 'ps-4' : '' }}">Contratista</th>
                        <th>Destino</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lista as $agenda)
                    @php
                        $canExport = in_array($agenda->estado->nombre, ['APROBADA_VIATICOS', 'APROBADA']);
                    @endphp
                    <tr>
                        @if($tipo == 'aprobadas')
                        <td class="ps-4">
                            @if($canExport)
                                <input type="checkbox" name="ids[]" value="{{ $agenda->id }}" class="form-check-input agenda-checkbox">
                            @else
                                <input type="checkbox" class="form-check-input" disabled title="Esta agenda aún no ha sido aprobada por viáticos">
                            @endif
                        </td>
                        @endif
                        <td class="{{ $tipo != 'aprobadas' ? 'ps-4' : '' }}">
                            <div class="fw-bold text-dark">{{ $agenda->user->name ?? $agenda->nombre_completo }}</div>
                            <div class="small text-muted">ID: #{{ $agenda->id }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <span>
                                    @if($agenda->destinos)
                                        {{ implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $agenda->destinos)))) }}
                                    @else
                                        {{ $agenda->ciudad_destino ?: 'N/A' }}
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td>{{ $agenda->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td>{{ $agenda->fecha_fin?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td>{{ $agenda->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($agenda->estado->nombre == 'APROBADA_SUPERVISOR')
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #e0f2fe; color: #0369a1; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #bae6fd;">Pendiente Revisar</span>
                            @elseif($agenda->estado->nombre == 'APROBADA_VIATICOS')
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #dcfce7; color: #166534; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #bbf7d0;">Aprobada</span>
                            @elseif($agenda->estado->nombre == 'APROBADA')
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #d1fae5; color: #065f46; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #a7f3d0;">Finalizada</span>
                            @elseif($agenda->estado->nombre == 'CORRECCIÓN' || $agenda->observaciones_finanzas)
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #fee2e2; color: #991b1b; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #fecaca;">Devuelta</span>
                            @else
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #e0f2fe; color: #075985; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #bae6fd;">En Proceso</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('viaticos.gestionar', ['id' => $agenda->id, 'tab' => $tipo]) }}" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-eye me-1"></i> Gestionar
                                </a>
                                @if($canExport)
                                    <a href="{{ route('viaticos.export', $agenda->id) }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-file-excel me-1"></i> Excel
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $tipo == 'aprobadas' ? '8' : '7' }}" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <p class="mb-0">No hay agendas en este estado.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
