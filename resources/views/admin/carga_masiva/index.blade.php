@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bold text-dark mb-1">Carga Masiva de Usuarios</h3>
                            <p class="text-muted mb-0">Sube un archivo Excel para registrar múltiples contratistas y líderes de proceso simultáneamente.</p>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-file-excel text-success fa-2x"></i>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-lg-5">
                    <!-- Dropzone Area -->
                    <div id="dropzone" class="upload-area border-2 border-dashed border-success rounded-4 p-5 text-center transition-all cursor-pointer mb-5 position-relative">
                        <input type="file" id="fileInput" style="opacity: 0; position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer; z-index: 10;" accept=".xlsx,.xls,.csv">
                        <div id="uploadPlaceholder">
                            <div class="mb-3">
                                <i class="fas fa-cloud-upload-alt text-success display-4"></i>
                            </div>
                            <h4 class="fw-bold">Arrastra tu archivo aquí</h4>
                            <p class="text-muted">O haz clic para buscar en tu equipo</p>
                            <span class="badge bg-light text-dark px-3 py-2 border rounded-pill">Soportado: .xlsx, .xls, .csv</span>
                        </div>
                        <div id="filePreview" class="d-none" style="position: relative; z-index: 20;">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <i class="fas fa-file-excel text-success fa-3x"></i>
                                <div class="text-start">
                                    <h5 id="fileName" class="fw-bold mb-0 text-truncate" style="max-width: 300px;">nombre_archivo.xlsx</h5>
                                    <p id="fileSize" class="text-muted mb-0 small">0 KB</p>
                                </div>
                                <button type="button" id="removeFile" class="btn btn-link text-danger p-0 ms-3">
                                    <i class="fas fa-times-circle fa-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-5">
                        <button id="btnImport" class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm d-none">
                            <i class="fas fa-rocket me-2"></i> Procesar Carga Masiva
                        </button>
                    </div>

                    <!-- Results Section (Hidden by default) -->
                    <div id="resultsSection" class="d-none animate__animated animate__fadeIn">
                        <hr class="my-5 opacity-10">
                        
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h4 class="fw-bold mb-1">Resultados de la Carga Actual</h4>
                                <p class="text-muted small">A continuación se muestran las credenciales generadas para los usuarios procesados en esta sesión.</p>
                            </div>
                            <button id="btnDownload" class="btn btn-outline-success rounded-pill px-4 fw-bold">
                                <i class="fas fa-download me-2"></i> Descargar Credenciales
                            </button>
                        </div>

                        <div class="table-responsive rounded-4 border mb-5">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 border-0">Nombre Completo</th>
                                        <th class="px-4 py-3 border-0">Correo Electrónico</th>
                                        <th class="px-4 py-3 border-0">Contraseña (CC + Random)</th>
                                        <th class="px-4 py-3 border-0 text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="resultsTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Historial Section -->
                    <div class="mt-5 pt-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                                <i class="fas fa-history"></i>
                            </div>
                            <h4 class="fw-bold mb-0">Historial de Cargas</h4>
                        </div>
                        
                        <div class="table-responsive rounded-4 border shadow-sm">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 border-0">Fecha y Hora</th>
                                        <th class="px-4 py-3 border-0">Archivo Original</th>
                                        <th class="px-4 py-3 border-0 text-center">Total</th>
                                        <th class="px-4 py-3 border-0 text-center text-success">Éxito</th>
                                        <th class="px-4 py-3 border-0 text-center text-danger">Errores</th>
                                        <th class="px-4 py-3 border-0">Responsable</th>
                                        <th class="px-4 py-3 border-0 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($historial as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-muted small">
                                            <i class="far fa-calendar-alt me-1"></i> {{ $item->created_at->format('d/m/Y') }}<br>
                                            <i class="far fa-clock me-1"></i> {{ $item->created_at->format('H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="fw-bold text-dark d-block">{{ $item->nombre_archivo }}</span>
                                            <span class="text-muted extra-small" style="font-size: 0.7rem;">ID Carga: #{{ $item->id }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center fw-bold">{{ $item->total_registros }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ $item->total_exito }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($item->total_errores > 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">{{ $item->total_errores }}</span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs bg-light rounded-circle text-dark me-2 d-flex align-items-center justify-content-center" style="width: 25px; height: 25px; font-size: 0.7rem;">
                                                    {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                                </div>
                                                <span class="small">{{ explode(' ', $item->user->name)[0] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('admin.carga-masiva.descargar_historial', $item->id) }}" class="btn btn-sm btn-white border rounded-pill shadow-sm hover-elevate">
                                                <i class="fas fa-download text-primary me-1"></i> Reporte
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted italic">
                                            <i class="fas fa-info-circle me-2"></i> No se han realizado cargas masivas todavía.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .upload-area {
        background-color: #f8fff5;
        border-color: #39a900 !important;
        transition: all 0.3s ease;
    }

    .upload-area:hover {
        background-color: #f0fdf4;
        transform: translateY(-2px);
    }

    .upload-area.dragover {
        background-color: #dcfce7;
        border-style: solid !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .card {
        border: none;
        transition: box-shadow 0.3s ease;
    }

    .table thead th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }

    .btn-success {
        background-color: #39a900;
        border-color: #39a900;
    }

    .btn-success:hover {
        background-color: #2e8b00;
        border-color: #2e8b00;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    const dropzone = $('#dropzone');
    const fileInput = $('#fileInput');
    const uploadPlaceholder = $('#uploadPlaceholder');
    const filePreview = $('#filePreview');
    const fileNameDisplay = $('#fileName');
    const fileSizeDisplay = $('#fileSize');
    const btnImport = $('#btnImport');
    const resultsSection = $('#resultsSection');
    const resultsTableBody = $('#resultsTableBody');

    // Handle drag events on the input itself for visual feedback
    fileInput.on('dragover', function() {
        dropzone.addClass('dragover');
    });

    fileInput.on('dragleave drop', function() {
        dropzone.removeClass('dragover');
    });

    // Handle file selection (both click and native drop)
    fileInput.on('change', function() {
        if (this.files.length > 0) {
            handleFile(this.files[0]);
        }
    });

    function handleFile(file) {
        // Simple extension check
        const ext = file.name.split('.').pop().toLowerCase();
        if (!['xlsx', 'xls', 'csv'].includes(ext)) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo no válido',
                text: 'Por favor, sube un archivo Excel (.xlsx, .xls) o CSV.',
            });
            return;
        }

        fileNameDisplay.text(file.name);
        fileSizeDisplay.text((file.size / 1024).toFixed(2) + ' KB');
        
        uploadPlaceholder.addClass('d-none');
        filePreview.removeClass('d-none');
        btnImport.removeClass('d-none');
        resultsSection.addClass('d-none');
    }

    // Remove file
    $('#removeFile').on('click', function(e) {
        e.stopPropagation();
        fileInput.val('');
        uploadPlaceholder.removeClass('d-none');
        filePreview.addClass('d-none');
        btnImport.addClass('d-none');
        resultsSection.addClass('d-none');
    });

    // Import logic (Mock for now)
    btnImport.on('click', function() {
        const formData = new FormData();
        formData.append('archivo', fileInput[0].files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        btnImport.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Procesando...');

        $.ajax({
            url: "{{ route('admin.carga-masiva.importar') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btnImport.addClass('d-none').prop('disabled', false).html('<i class="fas fa-rocket me-2"></i> Procesar Carga Masiva');
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Carga Exitosa!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    renderResults(response.data);
                }
            },
            error: function(xhr) {
                btnImport.prop('disabled', false).html('<i class="fas fa-rocket me-2"></i> Procesar Carga Masiva');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Hubo un problema al procesar el archivo.'
                });
            }
        });
    });

    function renderResults(data) {
        resultsTableBody.empty();
        data.forEach(user => {
            resultsTableBody.append(`
                <tr>
                    <td class="px-4 py-3 fw-bold">${user.nombre}</td>
                    <td class="px-4 py-3 text-muted">${user.correo}</td>
                    <td class="px-4 py-3">
                        <code class="bg-light p-2 rounded text-dark border">${user.password}</code>
                        <button class="btn btn-sm btn-link text-muted copy-btn" data-pass="${user.password}">
                            <i class="far fa-copy"></i>
                        </button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Registrado</span>
                    </td>
                </tr>
            `);
        });

        resultsSection.removeClass('d-none');
        
        // Scroll to results
        $('html, body').animate({
            scrollTop: resultsSection.offset().top - 100
        }, 800);
    }

    // Copy to clipboard
    $(document).on('click', '.copy-btn', function() {
        const pass = $(this).data('pass');
        navigator.clipboard.writeText(pass).then(() => {
            const icon = $(this).find('i');
            icon.removeClass('far fa-copy').addClass('fas fa-check text-success');
            setTimeout(() => {
                icon.removeClass('fas fa-check text-success').addClass('far fa-copy');
            }, 2000);
        });
    });

    // Download Report (Mock)
    $('#btnDownload').on('click', function() {
        window.location.href = "{{ route('admin.carga-masiva.descargar') }}";
    });
});
</script>
@endpush
@endsection
