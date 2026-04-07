<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Folio</th>
                        <th>Funcionario</th>
                        <th>Municipio Destino</th>
                        <th>Fecha Registro</th>
                        @if($tipo == 'devueltas')
                            <th class="text-danger">Motivo de Devolución</th>
                        @endif
                        <th class="text-end pe-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lista as $item)
                    <tr>
                        <td class="ps-4"><strong>#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td>{{ $item->municipio ?? $item->departamento ?? 'N/A' }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        
                        {{-- SOLO MUESTRA OBSERVACIONES SI ES LA TABLA DE DEVUELTAS --}}
                        @if($tipo == 'devueltas')
                            <td>
                                <div class="bg-danger bg-opacity-10 p-2 rounded small text-danger border-start border-danger border-3">
                                    {{ $item->observaciones_finanzas ?? 'Sin motivo registrado' }}
                                </div>
                            </td>
                        @endif

                        <td class="text-end pe-4">
                            <a href="{{ route('reportes.show', $item->id) }}" 
                               class="btn btn-sm {{ $tipo == 'pendientes' ? 'btn-success' : 'btn-outline-primary' }} rounded-pill px-3">
                               {{ $tipo == 'pendientes' ? 'Gestionar' : 'Ver Detalles' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No hay registros en esta categoría.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>