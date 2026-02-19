<x-dashboard-layout>
    <div class="container-fluid px-4 py-5">
        <div class="row justify-content-center">
            <div class="col-xxl-9 col-xl-10">

                {{-- Encabezado de Página --}}
                <div class="d-flex align-items-center mb-5">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4 me-4">
                        <i class="fas fa-file-signature fa-2x text-success"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1 text-dark">Nueva Agenda de Desplazamiento</h2>
                        <p class="text-muted mb-0">Complete todos los campos para registrar su comisión de servicio.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-exclamation-circle text-danger me-2"></i>
                            <h6 class="fw-bold mb-0">Por favor corrija los siguientes errores:</h6>
                        </div>
                        <ul class="mb-0 ps-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div
                        class="alert alert-success border-0 shadow-sm rounded-4 mb-5 p-4 animate__animated animate__fadeIn">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success me-2 fa-lg"></i>
                            <h5 class="fw-bold mb-0">{{ session('success') }}</h5>
                        </div>
                        @if (isset($agenda))
                            <div class="d-flex gap-2 mt-2">
                                <a href="{{ route('agenda.pdf', $agenda->id) }}"
                                    class="btn btn-dark rounded-pill px-4 shadow-sm" target="_blank">
                                    <i class="fas fa-file-pdf me-2"></i>Ver PDF
                                </a>
                                <a href="{{ route('reportar-dia.show', $agenda->id) }}"
                                    class="btn btn-success rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-calendar-check me-2"></i>Reportar Actividades
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <form method="POST" action="{{ route('formulario.store') }}" enctype="multipart/form-data"
                    id="agenda-form">
                    @csrf

                    <div class="row g-4">
                        {{-- SECCIÓN 0: CLASIFICACIÓN DE LA INFORMACIÓN --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                                    <div class="bg-dark p-2 rounded-3 me-3">
                                        <i class="fas fa-shield-alt text-white"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-dark">Clasificación de la Información</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <input type="radio" name="clasificacion_informacion" value="publica"
                                                id="c-publica" class="btn-check" checked required>
                                            <label
                                                class="btn btn-outline-success w-100 rounded-3 p-3 text-start border-2 shadow-sm"
                                                for="c-publica">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-eye fa-lg me-3 opacity-75"></i>
                                                    <div>
                                                        <div class="fw-bold">Información Pública</div>
                                                        <div class="small opacity-75">Acceso general para todos</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" name="clasificacion_informacion" value="clasificada"
                                                id="c-clasificada" class="btn-check" required>
                                            <label
                                                class="btn btn-outline-success w-100 rounded-3 p-3 text-start border-2 shadow-sm"
                                                for="c-clasificada">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-shield fa-lg me-3 opacity-75"></i>
                                                    <div>
                                                        <div class="fw-bold">Información Clasificada</div>
                                                        <div class="small opacity-75">Acceso restringido por ley</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" name="clasificacion_informacion" value="reservada"
                                                id="c-reservada" class="btn-check" required>
                                            <label
                                                class="btn btn-outline-success w-100 rounded-3 p-3 text-start border-2 shadow-sm"
                                                for="c-reservada">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-lock fa-lg me-3 opacity-75"></i>
                                                    <div>
                                                        <div class="fw-bold">Información Reservada</div>
                                                        <div class="small opacity-75">Reserva por defensa nacional</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 1: DATOS PERSONALES --}}
                        <div class="col-12">
                            <div class="form-section card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                                    <span class="step-badge me-3">1</span>
                                    <h5 class="fw-bold mb-0 text-dark">Información del Contratista</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label
                                                class="form-label fw-semibold text-muted small text-uppercase">Nombres y
                                                Apellidos Completos</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i
                                                        class="fas fa-user text-muted"></i></span>
                                                <input type="text" name="nombre_completo"
                                                    class="form-control custom-input"
                                                    value="{{ old('nombre_completo') }}"
                                                    placeholder="Ej: Juan Pablo Pérez" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Tipo
                                                de Documento</label>
                                            <select name="tipo_documento" class="form-select custom-input" required>
                                                <option value="" disabled selected>Seleccione...</option>
                                                <option value="CC" {{ old('tipo_documento') == 'CC' ? 'selected' : '' }}>
                                                    Cédula de Ciudadanía</option>
                                                <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }}>
                                                    Cédula de Extranjería</option>
                                                <option value="PAS" {{ old('tipo_documento') == 'PAS' ? 'selected' : '' }}>Pasaporte</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Número
                                                de Documento</label>
                                            <input type="text" name="numero_documento" class="form-control custom-input"
                                                value="{{ old('numero_documento') }}" placeholder="1234567890" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label
                                                class="form-label fw-semibold text-muted small text-uppercase">Cargo</label>
                                            <select name="cargo" id="cargo" class="form-select custom-input" required>
                                                <option value="" disabled {{ old('cargo') ? '' : 'selected' }}>
                                                    Seleccione un cargo...</option>
                                                <option value="Contratista" {{ old('cargo') == 'Contratista' ? 'selected' : '' }}>Contratista (Planta)</option>
                                                <option value="Servidor_Publico" {{ old('cargo') == 'Servidor_Publico' ? 'selected' : '' }}>Servidor Público </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: DATOS DEL CONTRATO --}}
                        <div class="col-12">
                            <div class="form-section card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                                    <span class="step-badge me-3">2</span>
                                    <h5 class="fw-bold mb-0 text-dark">Detalles del Contrato</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Número
                                                de Contrato</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i
                                                        class="fas fa-hashtag text-muted"></i></span>
                                                <input type="number" name="numero_contrato"
                                                    class="form-control custom-input"
                                                    value="{{ old('numero_contrato') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label
                                                class="form-label fw-semibold text-muted small text-uppercase">Año</label>
                                            <select name="anio_contrato" class="form-select custom-input" required>
                                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                                    <option value="{{ $i }}" {{ old('anio_contrato') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Fecha
                                                de Elaboración Agenda</label>
                                            <input type="date" name="fecha_elaboracion"
                                                class="form-control custom-input bg-light"
                                                value="{{ old('fecha_elaboracion', date('Y-m-d')) }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label
                                                class="form-label fw-semibold text-muted small text-uppercase">Vencimiento
                                                del Contrato</label>
                                            <input type="date" name="fecha_vencimiento"
                                                class="form-control custom-input" min="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label
                                                class="form-label fw-semibold text-muted small text-uppercase">Objetivo
                                                Contractual</label>
                                            <select id="objetivo_contractual" name="objetivo_contractual_display"
                                                class="form-select custom-input" required>
                                                <option value="" disabled selected>Seleccione un objetivo...</option>
                                            </select>
                                            <input type="hidden" name="objetivo_contractual"
                                                id="objetivo_contractual_hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: INFORMACIÓN DEL DESPLAZAMIENTO --}}
                        <div class="col-12">
                            <div class="form-section card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                                    <span class="step-badge me-3">3</span>
                                    <h5 class="fw-bold mb-0 text-dark">Información del Desplazamiento</h5>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="bg-light p-4 rounded-4">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label
                                                            class="form-label fw-semibold text-muted small text-uppercase">Origen</label>
                                                        <div class="bg-white p-3 rounded-3 border fw-bold text-center">
                                                            MEDELLÍN</div>
                                                        <input type="hidden" name="origen" value="MEDELLIN">
                                                    </div>
                                                    <div
                                                        class="col-md-4 d-flex align-items-center justify-content-center py-2">
                                                        <div class="text-success bg-white rounded-circle shadow-sm p-2">
                                                            <i class="fas fa-long-arrow-alt-right fa-2x"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label
                                                            class="form-label fw-semibold text-muted small text-uppercase">Municipio
                                                            Destino</label>
                                                        <select id="destino" name="destino_select"
                                                            class="form-select custom-input" required>
                                                            <option value="" selected>Seleccione...</option>
                                                        </select>
                                                        <input type="hidden" name="destino_departamento_id"
                                                            id="destino_departamento_id">
                                                        <input type="hidden" name="destino_municipio_id"
                                                            id="destino_municipio_id">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Fecha
                                                Inicio</label>
                                            <input type="date" name="fecha_inicio_desplazamiento"
                                                class="form-control custom-input" min="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Fecha
                                                Regreso</label>
                                            <input type="date" name="fecha_fin_desplazamiento"
                                                class="form-control custom-input" required>
                                        </div>

                                        <div class="col-12">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label
                                                        class="form-label fw-semibold text-muted small text-uppercase">Entidad
                                                        / Empresa</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-0"><i
                                                                class="fas fa-building text-muted"></i></span>
                                                        <input type="text" name="entidad_empresa"
                                                            class="form-control custom-input"
                                                            placeholder="Ej: Sena CTGI"
                                                            value="{{ old('entidad_empresa') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label
                                                        class="form-label fw-semibold text-muted small text-uppercase">Nombre
                                                        del Contacto</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-0"><i
                                                                class="fas fa-user-tie text-muted"></i></span>
                                                        <input type="text" name="contacto"
                                                            class="form-control custom-input"
                                                            placeholder="Ej: Coordinador de área"
                                                            value="{{ old('contacto') }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label
                                                class="form-label fw-semibold text-muted small text-uppercase">Objetivo
                                                del Desplazamiento</label>
                                            <textarea name="objetivo_desplazamiento" class="form-control custom-input"
                                                rows="3" maxlength="160"
                                                placeholder="Describa brevemente el motivo del viaje..."
                                                required>{{ old('objetivo_desplazamiento') }}</textarea>
                                        </div>

                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <label
                                                    class="form-label fw-semibold text-muted small text-uppercase mb-0">Obligaciones
                                                    del Contrato Relacionadas</label>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 add-obligacion">
                                                    <i class="fas fa-plus me-1"></i> Agregar Obligación
                                                </button>
                                            </div>
                                            <div id="obligaciones-container">
                                                <div
                                                    class="input-group mb-3 obligation-item shadow-sm rounded-3 overflow-hidden animate__animated animate__fadeInUp animate__faster">
                                                    <textarea name="obligaciones_contrato[]"
                                                        class="form-control border-0 p-3" rows="2"
                                                        placeholder="Describa la obligación..." required></textarea>
                                                    <button type="button"
                                                        class="btn btn-danger border-0 remove-obligacion p-3"
                                                        style="width: 50px;">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Firma
                                                Digital del Contratista</label>
                                            <div
                                                class="signature-container bg-light rounded-4 border p-3 position-relative">
                                                <canvas id="signature-pad" class="signature-pad w-100"
                                                    style="height: 200px; touch-action: none; background: #fff; border-radius: 0.5rem;"></canvas>
                                                <div class="signature-actions mt-2 d-flex gap-2">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                                        id="clear-signature">
                                                        <i class="fas fa-eraser me-1"></i> Limpiar
                                                    </button>
                                                    <span class="text-muted small align-self-center ms-auto">Firme
                                                        dentro del recuadro</span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="firma_base64" id="firma_base64">

                                            <div class="mt-4">
                                                <label class="form-label fw-semibold text-muted small text-uppercase">O
                                                    cargar imagen de firma</label>
                                                <div class="file-upload-wrapper">
                                                    <input type="file" name="firma_contratista" id="firma_contratista"
                                                        accept="image/*" class="file-upload-input">
                                                    <label for="firma_contratista"
                                                        class="file-upload-label rounded-4 border-dashed p-3 text-center d-block cursor-pointer">
                                                        <i class="fas fa-cloud-upload-alt me-2 text-muted"></i>
                                                        <span id="file-name-preview" class="text-muted small">Haga clic
                                                            si prefiere subir un archivo</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BOTÓN DE ENVÍO --}}
                        <div class="col-12 mt-4 mb-5">
                            <button type="submit"
                                class="btn btn-success btn-lg w-100 rounded-4 py-3 fw-bold shadow hover-grow">
                                <i class="fas fa-paper-plane me-2"></i> Crear y Guardar Agenda
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .step-badge {
            width: 32px;
            height: 32px;
            background-color: #39a900;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .custom-input {
            border: 2px solid #f1f5f9;
            border-radius: 0.85rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .custom-input:focus {
            border-color: #39a900;
            box-shadow: 0 0 0 4px rgba(57, 169, 0, 0.1);
            background-color: #fff;
        }

        .form-section {
            transition: transform 0.3s ease;
        }

        .border-dashed {
            border: 2px dashed #cbd5e1;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        .border-dashed:hover {
            border-color: #39a900;
            background-color: #f0fdf4;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .hover-grow {
            transition: all 0.2s ease;
        }

        .hover-grow:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(57, 169, 0, 0.3) !important;
        }

        .file-upload-input {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }

        .input-group-text {
            border-radius: 0.85rem 0 0 0.85rem;
        }

        .custom-input.form-control:not(.obligation-item textarea) {
            border-left: none;
        }

        /* Animaciones para los items de obligaciones */
        .obligation-item {
            border: 2px solid #f1f5f9;
        }

        .btn-success {
            background-color: #39a900;
            border-color: #39a900;
        }

        .btn-success:hover {
            background-color: #2d8600;
            border-color: #2d8600;
        }

        .text-success {
            color: #39a900 !important;
        }

        .bg-success {
            background-color: #39a900 !important;
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
        <script>
            $(document).ready(function () {
                // Inicialización Signature Pad
                const canvas = document.getElementById('signature-pad');
                const signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(0, 0, 0)'
                });

                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    signaturePad.clear();
                }

                window.addEventListener("resize", resizeCanvas);
                resizeCanvas();

                $('#clear-signature').click(function () {
                    signaturePad.clear();
                    $('#firma_base64').val('');
                });

                // Antes de enviar el formulario, capturar la firma
                $('#agenda-form').submit(function () {
                    if (!signaturePad.isEmpty()) {
                        $('#firma_base64').val(signaturePad.toDataURL());
                    }
                });

                // Manejo de carga de archivos (mostrar nombre del archivo)
                $('#firma_contratista').on('change', function (e) {
                    const fileName = e.target.files[0]?.name;
                    if (fileName) {
                        $('#file-name-preview').text('Archivo seleccionado: ' + fileName).addClass('text-success fw-bold');
                        signaturePad.clear(); // Si sube archivo, limpiamos el pad
                    }
                });

                // Logica para agregar/quitar obligaciones
                $('.add-obligacion').click(function () {
                    const newItem = `
                                                <div class="input-group mb-3 obligation-item shadow-sm rounded-3 overflow-hidden animate__animated animate__fadeInUp animate__faster">
                                                    <textarea name="obligaciones_contrato[]" class="form-control border-0 p-3" rows="2" placeholder="Describa la obligación..." required></textarea>
                                                    <button type="button" class="btn btn-danger border-0 remove-obligacion p-3" style="width: 50px;">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            `;
                    $('#obligaciones-container').append(newItem);
                });

                $(document).on('click', '.remove-obligacion', function () {
                    if ($('.obligation-item').length > 1) {
                        const parent = $(this).closest('.obligation-item');
                        parent.removeClass('animate__fadeInUp').addClass('animate__fadeOutDown');
                        setTimeout(() => parent.remove(), 400);
                    } else {
                        alert('Debe registrar al menos una obligación.');
                    }
                });

                // Logica para objetivos contractuales dinámicos
                const objetivosContratista = [
                    { val: "OBJETO REGULAR", text: "CONTRATAR LOS SERVICIOS PERSONALES DE CARÁCTER TEMPORAL COMO CONTRATISTA PARA IMPARTIR FORMACIÓN TITULADA Y COMPLEMENTARIA EN MODALIDAD PRESENCIAL Y VIRTUAL PARA LOS PROGRAMAS DE OFERTA REGULAR DEL CTGI" },
                    { val: "OBJETO DESPLAZADOS", text: "CONTRATAR LOS SERVICIOS PERSONALES DE CARÁCTER TEMPORAL COMO CONTRATISTA PARA IMPARTIR FORMACIÓN TITULADA Y COMPLEMENTARIA EN MODALIDAD PRESENCIAL Y VIRTUAL PARA LOS PROGRAMAS DE OFERTA DESPLAZADOS DEL CTGI" },
                    { val: "OBJETO DE MEDIA TECNICA", text: "CONTRATAR LOS SERVICIOS PERSONALES DE CARÁCTER TEMPORAL COMO CONTRATISTA PARA IMPARTIR FORMACIÓN TITULADA EN MODALIDAD PRESENCIAL PARA LOS PROGRAMAS DE ARTICULACIÓN CON LA EDUCACIÓN MEDIA DEL CTGI" },
                    { val: "OBJETO ECONOMIA POPULAR", text: "PRESTAR SERVICIOS PROFESIONALES Y/O DE APOYO A LA GESTIÓN, EN LA PLANEACIÓN Y EJECUCIÓN DE LA FORMACIÓN, ASÍ COMO LA EVALUACIÓN DE LOS RESULTADOS DE APRENDIZAJE DEFINIDOS EN LOS DISEÑOS CURRICULARES ASIGNADOS, PARA EL DESARROLLO DE HABILIDADES Y COMPETENCIAS TÉCNICAS DE LA POBLACIÓN DE TRABAJADORES DE LA ECONOMIA POPULAR, APORTANDO AL FORTALECIMIENTO DE LA ECONOMÍA POPULAR EN  CONCORDANCIA CON LOS LINEAMIENTOS ESTABLECIDOS POR LA DIRECCIÓN DEL SISTEMA NACIONAL DE FORMACIÓN PARA EL TRABAJO Y LA COORDINACIÓN NACIONAL DE ATENCIÓN INTEGRAL, DIFERENCIAL E INCLUYENTE A LA ECONOMÍA POPULAR – CAMPESEN       " },
                    { val: "OBJETO TIC", text: "CONTRATAR LOS SERVICIOS PERSONALES DE CARÁCTER TEMPORAL COMO CONTRATISTA PARA IMPARTIR FORMACIÓN TITULADA Y COMPLEMENTARIA EN MODALIDAD PRESENCIAL Y VIRTUAL PARA LOS PROGRAMAS DE OFERTA FIC DEL CTGI       " },
                    { val: "OBJETO CAMPESENA", text: "PRESTAR SERVICIOS PROFESIONALES Y/O DE APOYO A LA GESTIÓN, EN LA PLANEACIÓN Y EJECUCIÓN DE LA FORMACIÓN, ASÍ COMO LA EVALUACIÓN DE LOS RESULTADOS DE APRENDIZAJE DEFINIDOS EN LOS DISEÑOS CURRICULARES ASIGNADOS, PARA EL DESARROLLO DE HABILIDADES Y COMPETENCIAS TÉCNICAS DE LA POBLACIÓN CAMPESINA, APORTANDO AL FORTALECIMIENTO DE LA ECONOMÍA POPULAR, FAMILIAR, ÉTNICA Y COMUNITARIA, EN CONCORDANCIA CON LINEAMIENTOS ESTABLECIDOS POR LA DIRECCIÓN DEL SISTEMA NACIONAL DE FORMACIÓN PARA EL TRABAJO Y LA COORDINACIÓN NACIONAL DE ATENCIÓN INTEGRAL, DIFERENCIAL E INCLUYENTE A LA ECONOMÍA POPULAR – CAMPESENA" }
                ];

                const objetivosPublico = [
                    { val: "MISIONAL", text: "Cumplimiento de Funciones Misionales según la Ley..." },
                    { val: "TECNICO", text: "Apoyo a la Gestión Técnica del Centro..." },
                    { val: "ADMINISTRATIVO", text: "Desarrollo de Actividades Administrativas y de Gestión..." }
                ];

                function filterObjectives(cargoValue, selectedObjective = null) {
                    let options = '<option value="" disabled ' + (!selectedObjective ? 'selected' : '') + '>Seleccione un objetivo...</option>';
                    const lista = (cargoValue === 'Contratista') ? objetivosContratista : (cargoValue === 'Servidor_Publico' ? objetivosPublico : []);

                    lista.forEach(item => {
                        const isSelected = (selectedObjective && (selectedObjective === item.val || selectedObjective.startsWith(item.val))) ? 'selected' : '';
                        options += `<option value="${item.val}" ${isSelected}>${item.val}</option>`;
                    });

                    $('#objetivo_contractual').html(options);

                    // Actualizar el input hidden si ya hay algo seleccionado
                    if ($('#objetivo_contractual').val()) {
                        updateHiddenObjective();
                    }
                }

                function updateHiddenObjective() {
                    const valSelected = $('#objetivo_contractual').val();
                    const cargo = $('#cargo').val();
                    if (!valSelected || !cargo) return;

                    const lista = (cargo === 'Contratista') ? objetivosContratista : objetivosPublico;
                    const fullText = lista.find(i => i.val === valSelected)?.text || valSelected;
                    $('#objetivo_contractual_hidden').val(fullText);
                }

                $('#cargo').change(function () {
                    filterObjectives($(this).val());
                });

                $('#objetivo_contractual').change(function () {
                    updateHiddenObjective();
                });

                // Inicialización al cargar la página (para persistencia de errores de validación)
                const initialCargo = $('#cargo').val();
                if (initialCargo) {
                    const initialObjectiveValue = "{{ old('objetivo_contractual_display') }}";
                    filterObjectives(initialCargo, initialObjectiveValue);
                }
            });







            document.addEventListener('DOMContentLoaded', async () => {
                const destino = document.getElementById('destino');
                const depInput = document.getElementById('destino_departamento_id');
                const munInput = document.getElementById('destino_municipio_id');

                const res = await fetch('/api/destinos');
                const departamentos = await res.json();

                destino.innerHTML = '<option value="">Seleccione...</option>';

                departamentos.forEach(dep => {
                    // Departamento
                    destino.innerHTML += `
                <option value="dep-${dep.id}">
                    ${dep.nombre}
                </option>
            `;

                    // Municipios
                    dep.municipios.forEach(mun => {
                        destino.innerHTML += `
                    <option value="mun-${mun.id}" data-dep="${dep.id}">
                        — ${mun.nombre}
                    </option>
                `;
                    });
                });

                destino.addEventListener('change', () => {
                    const value = destino.value;

                    if (value.startsWith('dep-')) {
                        depInput.value = value.replace('dep-', '');
                        munInput.value = '';
                    }

                    if (value.startsWith('mun-')) {
                        munInput.value = value.replace('mun-', '');
                        depInput.value = destino.selectedOptions[0].dataset.dep;
                    }
                });
            });
        </script>
    @endpush
</x-dashboard-layout>