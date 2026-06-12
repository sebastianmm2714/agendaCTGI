<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Listado de Legalizaciones</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Contratista</th>
                        <th>Orden Viaje</th>
                        <th>Ruta/Destino</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Declaración</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lista as $agenda)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $agenda->user->name ?? 'N/A' }}</div>
                            <div class="small text-muted">ID: #{{ $agenda->id }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-3 py-2 rounded-pill border">
                                {{ $agenda->orden_viaje }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-route text-success me-2 opacity-75"></i>
                                <span class="small fw-semibold text-dark">{{ Str::limit($agenda->ruta, 45) }}</span>
                            </div>
                        </td>
                        <td>{{ $agenda->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td>{{ $agenda->fecha_fin?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td>
                            @if($agenda->realiza_declaracion)
                                <span class="badge bg-info bg-opacity-10 text-info fw-bold px-3 py-2 rounded-pill border border-info border-opacity-25">
                                    <i class="fas fa-file-signature me-1"></i> Informal
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-3 py-2 rounded-pill border border-warning border-opacity-25">
                                    <i class="fas fa-ticket-alt me-1"></i> Tiquetes
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($agenda->legalizacion_estado == 'APROBADA_SUPERVISOR')
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #e0f2fe; color: #0369a1; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #bae6fd;">Pendiente Revisar</span>
                            @elseif(in_array($agenda->legalizacion_estado, ['APROBADA_LEGALIZACION', 'APROBADA_ORDENADOR']))
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #dcfce7; color: #166534; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #bbf7d0;">Aprobada</span>
                            @elseif(in_array($agenda->legalizacion_estado, ['DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']))
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #fee2e2; color: #991b1b; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #fecaca;">Devuelta</span>
                            @elseif($agenda->legalizacion_estado == 'ENVIADA')
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #e0f2fe; color: #075985; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #bae6fd;">En Proceso</span>
                            @else
                                <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: #f3f4f6; color: #4b5563; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid #e5e7eb;">{{ $agenda->legalizacion_estado ?? 'Sin Estado' }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('legalizacion.gestionar', ['id' => $agenda->id, 'tab' => $tipo]) }}" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-eye me-1"></i> Gestionar
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <p class="mb-0">No hay legalizaciones en este estado.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
