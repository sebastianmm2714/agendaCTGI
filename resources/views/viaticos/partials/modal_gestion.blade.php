<div class="modal fade" id="modalGestion{{ $agenda->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold">Gestión de Agenda #{{ $agenda->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('viaticos.procesar', $agenda->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">CONTRATISTA</label>
                            <p class="fw-bold mb-0">{{ $agenda->user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">FIRMA COORDINADOR</label>
                            <div>
                                @if(in_array($agenda->estado->nombre, ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA']))
                                    <span class="text-success"><i class="fas fa-check-circle"></i> Verificada</span>
                                @else
                                    <span class="text-danger"><i class="fas fa-times-circle"></i> No disponible</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Observaciones Técnicas / Motivo Devolución</label>
                        <textarea class="form-control rounded-3" name="observaciones" rows="4" placeholder="Escribe aquí los detalles de la liquidación o la razón de la devolución..." required>{{ $agenda->observaciones_finanzas }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="submit" name="accion" value="devolver" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                        <i class="fas fa-undo me-2"></i> Devolver para Corrección
                    </button>
                    <button type="submit" name="accion" value="aprobar" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-check me-2"></i> Liquidar y Aprobar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>