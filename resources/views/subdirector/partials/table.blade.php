<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" style="width: 100px;">Folio</th>
                        <th>Contratista / Instructor</th>
                        <th>Destino</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        @if($tipo == 'devueltas')
                            <th class="text-danger fw-bold">Motivo del Rechazo</th>
                        @endif
                        <th class="text-end pe-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lista as $item)
                    <tr style="transition: all 0.2s;">
                        <td class="ps-4">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->user->name ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ $item->user->categoria->nombre ?? 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <span class="text-dark small text-uppercase fw-bold">
                                <i class="fas fa-map-marker-alt text-danger me-1 small"></i>
                                @if($item->destinos)
                                    {{ implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $item->destinos)))) }}
                                @else
                                    {{ $item->ciudad_destino ?: $item->ruta ?: 'N/A' }}
                                @endif
                            </span>
                        </td>
                        <td>
                            <div class="text-dark">{{ $item->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="text-dark">{{ $item->fecha_fin?->format('d/m/Y') ?? 'N/A' }}</div>
                        </td>
                        
                        {{-- COLUMNA DINÁMICA DE RECHAZO --}}
                        @if($tipo == 'devueltas')
                            <td>
                                <div class="p-2 rounded-3 border-start border-danger border-4 bg-danger bg-opacity-10 text-danger" style="font-size: 0.85rem;">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    <strong>Nota:</strong> {{ $item->observaciones_finanzas ?? 'No se especificó un motivo.' }}
                                </div>
                            </td>
                        @endif

                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                @if($tipo == 'pendientes')
                                    <button type="button" 
                                            class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-bold"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalFirma{{ $item->id }}">
                                        <i class="fas fa-pen-nib me-1"></i> Firmar
                                    </button>
                                @endif

                                <a href="{{ route('agenda.pdf', $item->id) }}" 
                                   target="_blank" 
                                   class="btn btn-dark btn-sm rounded-pill shadow-sm fw-bold d-flex align-items-center gap-1"
                                   style="min-width:70px; px-2;"
                                   title="Vista previa PDF">
                                    <i class="fas fa-eye"></i> <span style="font-size:0.78rem;">PDF</span>
                                </a>
                            </div>

                            {{-- MODALES --}}
                            @if($tipo == 'pendientes')
                                {{-- MODAL DE FIRMA --}}
                                <div class="modal fade" id="modalFirma{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4 text-start">
                                            <form action="{{ route('ordenador_gasto.autorizar', $item->id) }}" method="POST" class="form-autorizar-agenda">
                                                @csrf
                                                <div class="modal-header border-0 bg-light rounded-top-4 py-3">
                                                    <h5 class="modal-title fw-bold text-dark">Autorización Final</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="text-muted small mb-3">
                                                        Al firmar esta agenda, usted otorga la <strong>autorización final</strong> para el desplazamiento de <strong>{{ $item->user->name ?? 'N/A' }}</strong>.
                                                    </p>
                                                    <div class="alert alert-info py-2 small d-flex align-items-center">
                                                        <i class="fas fa-info-circle me-2 fs-5"></i>
                                                        <span>Se aplicará su firma digital registrada en el sistema.</span>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm text-white">
                                                        <i class="fas fa-check-circle me-1"></i> Confirmar y Firmar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $tipo == 'devueltas' ? '7' : '6' }}" class="text-center py-5">
                            <div class="opacity-50">
                                <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>
                                <p class="text-muted fw-bold">No hay agendas registradas en esta categoría.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
