@php
    // Logo detection logic (preserve existing $logoBase64 if passed from controller)
    if (empty($logoBase64)) {
        $logoBase64 = '';
        $posiblesRutas = [
            public_path('images/sena/logoSena.png'),
            public_path('images/sena/logo-sena-verde.png'),
            public_path('images/sena/agenda_labores.png'),
            base_path('../public_html/images/sena/logoSena.png'),
            base_path('../public_html/images/sena/logo-sena-verde.png'),
            $_SERVER['DOCUMENT_ROOT'] . '/images/sena/logoSena.png',
            $_SERVER['DOCUMENT_ROOT'] . '/images/sena/logo-sena-verde.png'
        ];

        foreach ($posiblesRutas as $ruta) {
            if (file_exists($ruta)) {
                $logoData = file_get_contents($ruta);
                $mimeType = mime_content_type($ruta) ?: 'image/png';
                $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($logoData);
                break;
            }
        }
    }

    if (empty($logoBase64)) {
        $logoBase64 = asset('images/sena/logoSena.png');
    }

    // Dynamic values from the agenda model
    $ciudad_fecha = 'Medellín ' . ($agenda->legalizado_at ? \Carbon\Carbon::parse($agenda->legalizado_at)->translatedFormat('d \d\e F \d\e Y') : ($agenda->fecha_elaboracion ? \Carbon\Carbon::parse($agenda->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') : now()->translatedFormat('d \d\e F \d\e Y')));
    $presentado_a = strtoupper(($agenda->ordenador->nombre ?? '') . ', ' . ($agenda->ordenador->cargo ?? 'SUBDIRECTOR DE CENTRO'));

    $orden_viaje = $agenda->orden_viaje ?? 'N/A';
    $fecha_inicio = $agenda->fecha_inicio ? \Carbon\Carbon::parse($agenda->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') : '';
    $fecha_fin = $agenda->fecha_fin ? \Carbon\Carbon::parse($agenda->fecha_fin)->translatedFormat('d \d\e F \d\e Y') : '';

    $lugar_desplazamiento = strtoupper($agenda->ruta ?? '');
    $centro_formacion = strtoupper($agenda->centro ?? 'CENTRO TEXTIL Y DE GESTIÓN INDUSTRIAL');

    $destinosArr = [];
    if ($agenda->destinos) {
        $destinosArr = array_unique(array_filter(array_map(fn($d) => ($d['nombre'] ?? ''), $agenda->destinos)));
    }
    $otra_ciudad = !empty($destinosArr) ? implode(', ', $destinosArr) : ($agenda->ciudad_destino ?: '');
    $otra_ciudad = strtoupper($otra_ciudad);

    $objetivo = $agenda->objetivo_desplazamiento ?? '';

    $actividades = [];
    foreach ($agenda->actividades->sortBy('fecha') as $act) {
        if (is_array($act->actividad)) {
            foreach ($act->actividad as $item) {
                if (!empty(trim($item['actividad'] ?? ''))) {
                    $actividades[] = strtoupper($item['actividad']);
                }
            }
        } else if (!empty(trim($act->actividad))) {
            $actividades[] = strtoupper($act->actividad);
        }
    }
    if (empty($actividades)) {
        $actividades = ['DESARROLLO DE LAS SESIONES DE FORMACIÓN SEGÚN LA PROGRAMACIÓN DE LA AGENDA.'];
    }

    // Resultados
    if (!empty($agenda->legalizacion_resultados)) {
        $resultados = $agenda->legalizacion_resultados;
    } else {
        $resultados = [];
        foreach ($actividades as $act) {
            $resultados[] = 'SE DESARROLLÓ SIN NINGUNA NOVEDAD LA ACTIVIDAD DE APRENDIZAJE: ' . $act;
        }
        if (empty($resultados)) {
            $resultados = ['SE DESARROLLARON LAS SESIONES DE FORMACIÓN PLANIFICADAS Y SE COMPILARON LOS REGISTROS RESPECTIVOS DE MANERA SATISFACTORIA.'];
        }
    }

    $evidencias = [
        'REGISTRO DE ASISTENCIA (PLANILLAS DE ASISTENCIA)',
        'EVIDENCIAS FOTOGRÁFICAS DE LAS SESIONES DE FORMACIÓN/ACTIVIDADES'
    ];

    // Compromisos
    if (!empty($agenda->legalizacion_compromisos)) {
        $compromisos = [];
        foreach ($agenda->legalizacion_compromisos as $comp) {
            $compromisos[] = [
                'actividad' => $comp['actividad'] ?? '',
                'responsable' => $comp['responsable'] ?? '',
                'fecha' => !empty($comp['fecha']) ? \Carbon\Carbon::parse($comp['fecha'])->translatedFormat('j \d\e F \d\e Y') : ''
            ];
        }
    } else {
        $compromisos = [];
        foreach ($agenda->actividades->sortBy('fecha') as $act) {
            $fechaAct = \Carbon\Carbon::parse($act->fecha)->translatedFormat('j \d\e F \d\e Y');
            if (is_array($act->actividad)) {
                foreach ($act->actividad as $item) {
                    if (!empty(trim($item['actividad'] ?? ''))) {
                        $compromisos[] = [
                            'actividad' => 'SE DESARROLLÓ ACTIVIDAD DE SOCIALIZACIÓN, TRANSFERENCIA DEL CONOCIMIENTO Y PRODUCTO EN RELACIÓN A: ' . strtoupper($item['actividad']),
                            'responsable' => $agenda->user->name ?? '',
                            'fecha' => $fechaAct
                        ];
                    }
                }
            } else if (!empty(trim($act->actividad))) {
                $compromisos[] = [
                    'actividad' => 'SE DESARROLLÓ ACTIVIDAD DE SOCIALIZACIÓN, TRANSFERENCIA DEL CONOCIMIENTO Y PRODUCTO EN RELACIÓN A: ' . strtoupper($act->actividad),
                    'responsable' => $agenda->user->name ?? '',
                    'fecha' => $fechaAct
                ];
            }
        }
        if (empty($compromisos)) {
            $compromisos[] = [
                'actividad' => 'SE DESARROLLÓ ACTIVIDAD DE SOCIALIZACIÓN Y TRANSFERENCIA DEL CONOCIMIENTO DE MANERA SATISFACTORIA.',
                'responsable' => $agenda->user->name ?? '',
                'fecha' => $fecha_fin
            ];
        }
    }

    // Conclusiones
    if (!empty($agenda->legalizacion_conclusiones)) {
        $conclusiones = $agenda->legalizacion_conclusiones;
    } else {
        $conclusiones = [];
        foreach ($actividades as $act) {
            $conclusiones[] = 'SE CUMPLIERON LAS SESIONES PLANIFICADAS Y SE ASIGNARON ACTIVIDADES DE PRODUCTO SOBRE ' . $act . '.';
        }
        if (empty($conclusiones)) {
            $conclusiones = [
                'SE DESARROLLARON LAS SESIONES DE FORMACIÓN PLANIFICADAS CON LAS FICHAS PROGRAMADAS.',
                'SE ASIGNARON ACTIVIDADES DE PRODUCTO Y TRANSFERENCIA DE CONOCIMIENTOS A LOS PARTICIPANTES.',
                'SE COMPILÓ EL REGISTRO DE ASISTENCIA Y EVIDENCIAS FOTOGRÁFICAS DE MANERA EXITOSA.'
            ];
        }
    }

    $contratista_nombre = strtoupper($agenda->user->name ?? '');
    $contratista_firma_base64 = $contratista_firma_base64 ?? null;

    $contratista_contrato = $agenda->user->numero_contrato ?? '';
    if (strpos($contratista_contrato, '.') !== false) {
        $partes_contrato = explode('.', $contratista_contrato);
        $contratista_contrato = end($partes_contrato);
    }

    $supervisor_cargo = strtoupper($agenda->supervisor->cargo ?? 'COORDINADOR ACADÉMICO / SUPERVISOR');
    $supervisor_nombre = strtoupper($agenda->supervisor->nombre ?? '');
    $supervisor_firma_base64 = $supervisor_firma_base64 ?? null;

    $fotosBase64 = [];
    if (!empty($agenda->legalizacion_fotos)) {
        foreach ($agenda->legalizacion_fotos as $foto) {
            $pathFoto = storage_path('app/public/' . $foto);
            if (file_exists($pathFoto)) {
                $fotoData = file_get_contents($pathFoto);
                $mimeType = mime_content_type($pathFoto) ?: 'image/jpeg';
                $fotosBase64[] = 'data:' . $mimeType . ';base64,' . base64_encode($fotoData);
            } else {
                $fotosBase64[] = asset('storage/' . $foto);
            }
        }
    }

    $planillasBase64 = [];
    if (!empty($agenda->legalizacion_planillas)) {
        foreach ($agenda->legalizacion_planillas as $planilla) {
            $pathPlanilla = storage_path('app/public/' . $planilla);
            if (file_exists($pathPlanilla)) {
                $planillaData = file_get_contents($pathPlanilla);
                $mimeType = mime_content_type($pathPlanilla) ?: 'image/jpeg';
                $planillasBase64[] = 'data:' . $mimeType . ';base64,' . base64_encode($planillaData);
            } else {
                $planillasBase64[] = asset('storage/' . $planilla);
            }
        }
    }

    $declaracionBase64 = null;
    $declaracionType = null;
    if (!empty($agenda->legalizacion_declaracion)) {
        $ext = strtolower(pathinfo($agenda->legalizacion_declaracion, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            $declaracionType = 'pdf';
        } else {
            $declaracionType = 'image';
            $pathDeclaracion = storage_path('app/public/' . $agenda->legalizacion_declaracion);
            if (file_exists($pathDeclaracion)) {
                $declaracionData = file_get_contents($pathDeclaracion);
                $mimeType = mime_content_type($pathDeclaracion) ?: 'image/jpeg';
                $declaracionBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($declaracionData);
            } else {
                $declaracionBase64 = asset('storage/' . $agenda->legalizacion_declaracion);
            }
        }
    }

    // Gastos de Transporte Informal (GRF-F-076)
    $gastos_transporte = $agenda->legalizacion_gastos_transporte ?? [];
    $regional_codigo = $agenda->legalizacion_codigo_regional ?? '';
    $centro_codigo = $agenda->legalizacion_codigo_centro ?? '';

    $diasArr = [];
    foreach ($agenda->actividades->sortBy('fecha') as $act) {
        if ($act->fecha) {
            $diasArr[] = \Carbon\Carbon::parse($act->fecha)->format('j');
        }
    }
    $diasArr = array_unique($diasArr);
    sort($diasArr);
    $dias_comision = implode('-', $diasArr);

    $mes_comision = '';
    if ($agenda->fecha_inicio) {
        $mes_comision = strtoupper(\Carbon\Carbon::parse($agenda->fecha_inicio)->translatedFormat('F'));
    }
    $anio_comision = '';
    if ($agenda->fecha_inicio) {
        $anio_comision = \Carbon\Carbon::parse($agenda->fecha_inicio)->format('Y');
    }

    $total_gastos = 0;
    foreach ($gastos_transporte as $g) {
        $total_gastos += floatval($g['valor'] ?? 0);
    }

    $tiquetesBase64 = [];
    if (!empty($agenda->legalizacion_tiquetes)) {
        foreach ($agenda->legalizacion_tiquetes as $tiquete) {
            $pathTiquete = storage_path('app/public/' . $tiquete);
            $ext = strtolower(pathinfo($tiquete, PATHINFO_EXTENSION));
            if (file_exists($pathTiquete)) {
                $tiqueteData = file_get_contents($pathTiquete);
                if ($ext === 'pdf') {
                    $tiquetesBase64[] = [
                        'type' => 'pdf',
                        'path' => $tiquete,
                        'base64' => 'data:application/pdf;base64,' . base64_encode($tiqueteData)
                    ];
                } else {
                    $mimeType = mime_content_type($pathTiquete) ?: 'image/jpeg';
                    $tiquetesBase64[] = [
                        'type' => 'image',
                        'path' => $tiquete,
                        'base64' => 'data:' . $mimeType . ';base64,' . base64_encode($tiqueteData)
                    ];
                }
            } else {
                $tiquetesBase64[] = [
                    'type' => $ext === 'pdf' ? 'pdf' : 'image',
                    'path' => $tiquete,
                    'base64' => asset('storage/' . $tiquete)
                ];
            }
        }
    }
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formato Informe Legalización Desplazamiento - Contratista</title>
    <link rel="icon" href="{{ asset('images/sena/logo-sena-verde.png') }}" type="image/png">
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #000;
        }

        .hoja {
            width: 215.9mm;
            min-height: 279.4mm;
            margin: 20px auto;
            background: #fff;
            padding: 10mm;
            position: relative;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #39A900;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .no-print:hover {
            transform: scale(1.05);
            background: #2d8500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 20px;
        }

        td {
            border: 1.5px solid #000;
            padding: 6px 10px;
            font-size: 11px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-normal {
            font-weight: normal !important;
        }

        .title-cell {
            font-weight: bold !important;
            font-size: 13px;
            text-align: center;
            padding: 12px 10px;
            text-transform: uppercase;
        }

        .section-title-cell {
            font-weight: bold !important;
            font-size: 11px;
            padding: 10px;
            text-transform: uppercase;
            vertical-align: middle;
        }

        .label-cell {
            font-weight: bold !important;
            font-size: 11px;
            padding: 10px;
            text-transform: uppercase;
            vertical-align: middle;
        }

        .cell-label {
            font-weight: bold !important;
            font-size: 11px;
            display: block;
            text-transform: uppercase;
        }

        .cell-value {
            font-size: 11px;
            display: block;
            margin-top: 4px;
        }

        .list-container {
            margin: 6px 0 0 0;
            padding-left: 20px;
            font-size: 11px;
            line-height: 1.4;
        }

        .list-container li {
            margin-bottom: 4px;
        }

        .signature-img {
            max-height: 55px;
            max-width: 100%;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        tr, td, .no-break {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Print optimization */
        @media print {
            body {
                background-color: #fff;
            }

            .hoja {
                margin: 0;
                box-shadow: none;
                width: 215.9mm;
                padding: 10mm;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
    {{-- html2pdf library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
</head>

<body>

    <script>
        window.logoBase64 = "{{ $logoBase64 }}";
    </script>

    <button class="no-print" onclick="descargarPDF()">
        <span>Descargar PDF</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
    </button>

    <div class="hoja" id="hoja-legalizacion">
        <!-- Main Logo (For Web View) -->
        <div class="text-center mb-3" id="web-logo-header">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="SENA Logo" style="max-height: 80px;">
            @else
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Sena_Colombia_logo.svg/1200px-Sena_Colombia_logo.svg.png"
                    style="max-height: 80px;">
            @endif
        </div>

        <table>
            <colgroup>
                @for ($i = 0; $i < 24; $i++)
                    <col style="width: 4.1666%;">
                @endfor
            </colgroup>
            <!-- GRID CONTROL ROW (24 columns) -->
            <tr>
                @for ($i = 0; $i < 24; $i++)
                    <td style="height:0; padding:0; border:none; width:4.1666%;"></td>
                @endfor
            </tr>

            <!-- Title -->
            <tr>
                <td colspan="24" class="title-cell">
                    FORMATO INFORME LEGALIZACION DESPLAZAMIENTO - CONTRATISTA
                </td>
            </tr>

            <!-- Ciudad y Fecha -->
            <tr>
                <td colspan="24" style="padding: 10px;">
                    <span class="cell-label">CIUDAD Y FECHA</span>
                    <span class="cell-value">{{ $ciudad_fecha }}</span>
                </td>
            </tr>

            <!-- Presentado A -->
            <tr>
                <td colspan="24" style="padding: 10px; vertical-align: middle;">
                    <span class="text-bold">PRESENTADO A:</span> <span class="text-normal">{{ $presentado_a }}</span>
                </td>
            </tr>

            <!-- Orden, Inicio, Fin -->
            <tr>
                <td colspan="8" rowspan="2" style="vertical-align: top; padding: 10px;">
                    <span class="cell-label">ORDEN DE VIAJE No:</span>
                    <span class="cell-value">{{ $orden_viaje }}</span>
                </td>
                <td colspan="8" class="label-cell">
                    FECHA DE INICIO:
                </td>
                <td colspan="8" class="label-cell">
                    FECHA DE FINALIZACION:
                </td>
            </tr>
            <tr>
                <td colspan="8" class="text-normal" style="padding: 10px;">
                    {{ $fecha_inicio }}
                </td>
                <td colspan="8" class="text-normal" style="padding: 10px;">
                    {{ $fecha_fin }}
                </td>
            </tr>

            <!-- Lugar, Regional, Otra -->
            <tr>
                <td colspan="8" rowspan="2" style="vertical-align: top; padding: 10px;">
                    <span class="cell-label">LUGAR A DONDE REALIZÓ EL DESPLAZAMIENTO</span>
                    <span class="cell-value">{{ $lugar_desplazamiento }}</span>
                </td>
                <td colspan="8" class="label-cell">
                    REGIONAL / CENTRO DE FORMACION
                </td>
                <td colspan="8" class="label-cell">
                    OTRA : ( ciudad)
                </td>
            </tr>
            <tr>
                <td colspan="8" class="text-normal" style="padding: 10px;">
                    {{ $centro_formacion }}
                </td>
                <td colspan="8" class="text-normal" style="padding: 10px;">
                    {{ $otra_ciudad }}
                </td>
            </tr>

            <!-- Objetivo -->
            <tr>
                <td colspan="24" style="padding: 10px; vertical-align: middle;">
                    <span class="text-bold">OBJETIVO DEL DESPLAZAMIENTO:</span> <span
                        class="text-normal">{{ $objetivo }}</span>
                </td>
            </tr>

            <!-- Actividades Desarrolladas -->
            <tr>
                <td colspan="24" class="section-title-cell" style="padding: 10px !important; text-align: center; line-height: 1.3; font-size: 12px;">
                    ACTIVIDADES DESARROLLADAS DURANTE LA COMISIÓN RESOLUCIÓN 2838/2016 Art.17:
                    <div style="font-weight: bold; font-size: 12px; margin-top: 8px; text-transform: none;">
                        (Deberá contener información detallada de las tareas realizadas día a día)
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="24" style="padding: 10px;">
                    <ol class="list-container">
                        @foreach ($actividades as $act)
                            <li>{{ $act }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>

            <!-- Resultados -->
            <tr>
                <td colspan="24" class="section-title-cell">
                    RESULTADOS:
                </td>
            </tr>
            <tr>
                <td colspan="24" style="padding: 10px;">
                    <ol class="list-container">
                        @foreach ($resultados as $res)
                            <li>{{ $res }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>

            <!-- Evidencias -->
            <tr>
                <td colspan="24" class="section-title-cell" style="padding: 10px 15px !important; line-height: 1.3; font-size: 12px; text-transform: none !important;">
                    EVIDENCIAS O SOPORTES: Enuncie y anexe los documentos que soportan los resultados de la comisión.
                    <div style="font-weight: bold; font-size: 12px; margin-top: 20px; text-transform: none; text-align: center;">
                        (Actas, registro fotográfico, listas de asistencia, invitaciones)
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="24" style="padding: 10px;">
                    <ol class="list-container">
                        @foreach ($evidencias as $ev)
                            <li>{{ $ev }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>

            <!-- Compromisos Title -->
            <tr>
                <td colspan="24" class="title-cell">
                    COMPROMISOS
                </td>
            </tr>

            <!-- Compromisos Headers -->
            <tr>
                <td colspan="12" class="label-cell text-center">ACTIVIDAD</td>
                <td colspan="6" class="label-cell text-center">RESPONSABLE</td>
                <td colspan="6" class="label-cell text-center">FECHA</td>
            </tr>

            <!-- Compromisos Rows -->
            @foreach ($compromisos as $index => $comp)
                <tr>
                    <td colspan="12" class="text-normal" style="text-align: left; padding: 10px;">
                        {{ $index + 1 }}. {{ $comp['actividad'] }}
                    </td>
                    <td colspan="6" class="text-normal text-center" style="vertical-align: middle; padding: 10px;">
                        {{ $comp['responsable'] }}
                    </td>
                    <td colspan="6" class="text-normal text-center" style="vertical-align: middle; padding: 10px;">
                        {{ $comp['fecha'] }}
                    </td>
                </tr>
            @endforeach

            <!-- Conclusiones Title -->
            <tr>
                <td colspan="24" class="section-title-cell">
                    CONCLUSIONES:
                </td>
            </tr>
            <tr>
                <td colspan="24" style="padding: 10px;">
                    <ol class="list-container">
                        @foreach ($conclusiones as $concl)
                            <li>{{ $concl }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>

            <!-- Datos del Contratista Title -->
            <tr>
                <td colspan="24" class="title-cell">
                    DATOS DEL CONTRATISTA
                </td>
            </tr>

            <!-- Datos del Contratista Labels -->
            <tr>
                <td colspan="16" class="label-cell">
                    NOMBRE Y APELLIDO
                </td>
                <td colspan="8" class="label-cell text-center">
                    FIRMA
                </td>
            </tr>
            <!-- Datos del Contratista Values -->
            <tr>
                <td colspan="16" class="text-bold" style="height: 65px; vertical-align: top; padding: 10px;">
                    {{ $contratista_nombre }}
                </td>
                <td colspan="8" style="height: 65px; vertical-align: middle; padding: 2px; text-align: center;">
                    @if($contratista_firma_base64)
                        <img src="{{ $contratista_firma_base64 }}" class="signature-img" alt="Firma Contratista">
                    @endif
                </td>
            </tr>

            <!-- Visto Bueno Supervisor Title -->
            <tr>
                <td colspan="24" class="title-cell">
                    VISTO BUENO SUPERVISOR
                </td>
            </tr>

            <!-- Visto Bueno Supervisor Labels -->
            <tr>
                <td colspan="8" class="label-cell">
                    CARGO DEL SUPERVISOR
                </td>
                <td colspan="8" class="label-cell">
                    NOMBRE Y APELLIDO SUPERVISOR
                </td>
                <td colspan="8" class="label-cell text-center">
                    FIRMA
                </td>
            </tr>
            <!-- Visto Bueno Supervisor Values -->
            <tr>
                <td colspan="8" class="text-bold" style="height: 65px; vertical-align: top; padding: 10px;">
                    {{ $supervisor_cargo }}
                </td>
                <td colspan="8" class="text-bold" style="height: 65px; vertical-align: top; padding: 10px;">
                    {{ $supervisor_nombre }}
                </td>
                <td colspan="8" style="height: 65px; vertical-align: middle; padding: 2px; text-align: center;">
                    @if($supervisor_firma_base64 && in_array($agenda->legalizacion_estado, ['APROBADA_SUPERVISOR', 'APROBADA_LEGALIZACION', 'APROBADA_ORDENADOR']))
                        <img src="{{ $supervisor_firma_base64 }}" class="signature-img" alt="Firma Supervisor" style="max-height: 55px; max-width: 100%; object-fit: contain;">
                    @endif
                </td>
            </tr>

            <!-- ANEXO 1: EVIDENCIAS FOTOGRÁFICAS -->
            @if(!empty($fotosBase64))
                @foreach($fotosBase64 as $foto)
                    <tr>
                        <td colspan="24" style="padding: 15px; text-align: center; border: 1.5px solid #000;">
                            <div style="font-weight: bold; font-size: 13px; margin-bottom: 8px; text-align: left; text-transform: uppercase;">
                                ANEXO 1: Registro Fotográfico {{ $loop->iteration }}
                            </div>
                            <img src="{{ $foto }}" style="width: auto; max-width: 100%; height: auto; max-height: 280px; object-fit: contain; display: block; margin: 0 auto; border: 1px solid #ddd; border-radius: 4px; padding: 2px;" alt="Evidencia fotográfica">
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="24" style="padding: 15px; color: #777; font-style: italic; border: 1.5px solid #000;">
                        ANEXO 1: No se registraron evidencias fotográficas.
                    </td>
                </tr>
            @endif

            <!-- ANEXO 2: PLANILLAS DE ASISTENCIA -->
            @if(!empty($planillasBase64))
                @foreach($planillasBase64 as $planilla)
                    <tr>
                        <td colspan="24" style="padding: 15px; text-align: center; border: 1.5px solid #000;">
                            <div style="font-weight: bold; font-size: 13px; margin-bottom: 8px; text-align: left; text-transform: uppercase;">
                                ANEXO 2: Formato de Asistencia {{ $loop->iteration }}
                            </div>
                            <img src="{{ $planilla }}" style="width: auto; max-width: 100%; height: auto; max-height: 280px; object-fit: contain; display: block; margin: 0 auto; border: 1px solid #ddd; border-radius: 4px; padding: 2px;" alt="Planilla de asistencia">
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="24" style="padding: 15px; color: #777; font-style: italic; border: 1.5px solid #000;">
                        ANEXO 2: No se registraron planillas de asistencia.
                    </td>
                </tr>
            @endif

            <!-- ANEXO TIQUETES DE VIAJE (Si no realiza declaración) -->
            @if(!$agenda->realiza_declaracion)
                @if(!empty($tiquetesBase64))
                    @foreach($tiquetesBase64 as $tiquete)
                        <tr>
                            <td colspan="24" style="padding: 15px; text-align: center; border: 1.5px solid #000;">
                                <div style="font-weight: bold; font-size: 13px; margin-bottom: 8px; text-align: left; text-transform: uppercase;">
                                    ANEXO: Tiquete de Viaje {{ $loop->iteration }}
                                </div>
                                @if($tiquete['type'] === 'pdf')
                                    <div class="pdf-render-container" data-pdf-url="{{ asset('storage/' . $tiquete['path']) }}" style="width: 100%;">
                                        <!-- Vista para descarga PDF (html2pdf no soporta iframes) -->
                                        <div class="print-only-block" style="display: none; width: 100%;"></div>
                                        <!-- Vista para Pantalla Web -->
                                        <div class="web-only-block" style="display: block; width: 100%; text-align: center;">
                                            <iframe src="{{ asset('storage/' . $tiquete['path']) }}#toolbar=0&navpanes=0"
                                                style="width: 100%; height: 500px; border: 1px solid #ddd; border-radius: 4px;"
                                                frameborder="0"></iframe>
                                        </div>
                                    </div>
                                @else
                                    <img src="{{ $tiquete['base64'] }}" style="width: auto; max-width: 100%; height: auto; max-height: 280px; object-fit: contain; display: block; margin: 0 auto; border: 1px solid #ddd; border-radius: 4px; padding: 2px;" alt="Tiquete de viaje">
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endif
        </table>

            <!-- Page break before Declaración No Juramentada in print/PDF -->
            @if($agenda->realiza_declaracion && !empty($agenda->legalizacion_declaracion))
                <div style="page-break-before: always; break-before: always; height: 1px; clear: both;"></div>

                <!-- ANEXO 3: DECLARACIÓN NO JURAMENTADA -->
                <div style="margin-top: 30px; margin-bottom: 30px;">

                    <div style="width: 100%; text-align: center; clear: both;">
                        @if($declaracionType === 'pdf')
                            <div class="pdf-render-container" data-pdf-url="{{ asset('storage/' . $agenda->legalizacion_declaracion) }}" style="width: 100%;">
                                <!-- Vista para descarga PDF (html2pdf no soporta iframes) -->
                                <div class="print-only-block" style="display: none; width: 100%; border: 1.5px solid #000; padding: 40px 20px; text-align: center; background: #fff; border-radius: 6px; box-sizing: border-box; margin: 15px auto;">
                                    <div style="font-size: 48px; margin-bottom: 12px; line-height: 1;">📄</div>
                                    <div style="font-size: 14px; font-weight: bold; color: #000; text-transform: uppercase; margin-bottom: 8px;">
                                        ARCHIVO PDF ADJUNTO: DECLARACIÓN NO JURAMENTADA
                                    </div>
                                    <div style="font-size: 12px; color: #555; max-width: 500px; margin: 0 auto; line-height: 1.4;">
                                        El archivo original de la declaración no juramentada se encuentra cargado de forma segura en el sistema en formato PDF.
                                    </div>
                                </div>
                                <!-- Vista para Pantalla Web -->
                                <div class="web-only-block" style="display: block; width: 100%; margin: 15px auto; text-align: center; background: #fff; box-sizing: border-box;">
                                    <iframe src="{{ asset('storage/' . $agenda->legalizacion_declaracion) }}#toolbar=0&navpanes=0"
                                        style="width: 100%; height: 900px; border: 1.5px solid #000; border-radius: 6px;"
                                        frameborder="0"></iframe>
                                </div>
                            </div>
                        @else
                            <div style="display: block; width: 100%; margin: 15px auto; border: 1.5px solid #000; padding: 15px; text-align: center; background: #fff; border-radius: 6px; box-sizing: border-box;"
                                class="no-break">
                                <img src="{{ $declaracionBase64 }}"
                                    style="width: 100%; height: auto; max-height: 900px; object-fit: contain; border-radius: 4px;"
                                    alt="Declaración No Juramentada">
                                <div
                                    style="margin-top: 10px; font-size: 11px; font-weight: bold; color: #333; text-transform: uppercase;">
                                    DECLARACIÓN NO JURAMENTADA</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        @if($agenda->realiza_declaracion)
            <!-- Page break before Gastos de Transporte Informal (GRF-F-076) -->
            <div style="page-break-before: always; break-before: always; height: 1px; clear: both;"></div>

            <!-- COMPROBANTE GASTOS DE TRANSPORTE INFORMAL (GRF-F-076) -->
            <div style="margin-top: 30px; margin-bottom: 30px;">
            <table
                style="width: 100%; border-collapse: collapse; border: 1.5px solid #000; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; table-layout: fixed;">
                <colgroup>
                    @for ($i = 0; $i < 24; $i++)
                        <col style="width: 4.1666%;">
                    @endfor
                </colgroup>
                <!-- GRID CONTROL ROW (24 columns) -->
                <tr>
                    @for ($i = 0; $i < 24; $i++)
                        <td style="height:0; padding:0; border:none; width:4.1666%;"></td>
                    @endfor
                </tr>

                <!-- HEADER LOGO & VERSION -->
                <tr style="height: 30px;">
                    <td colspan="4" rowspan="2" style="border: 1.5px solid #000; border-right: none; background-color: #fff;"></td>
                    <td colspan="16" rowspan="2"
                        style="border: 1.5px solid #000; border-left: none; text-align: center; vertical-align: middle; padding: 6px; background-color: #fff;">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="SENA Logo" style="max-height: 42px; display: inline-block; margin: 0 auto; vertical-align: middle;">
                        @else
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Sena_Colombia_logo.svg/1200px-Sena_Colombia_logo.svg.png"
                                style="max-height: 42px; display: inline-block; margin: 0 auto; vertical-align: middle;">
                        @endif
                    </td>
                    <td colspan="4"
                        style="border: 1.5px solid #000; text-align: center; vertical-align: middle; font-size: 9px; padding: 4px; font-weight: normal; background-color: #fff; height: 30px;">
                        Versión: 02
                    </td>
                </tr>
                <tr style="height: 30px;">
                    <td colspan="4"
                        style="border: 1.5px solid #000; text-align: center; vertical-align: middle; font-size: 9px; padding: 4px; font-weight: normal; background-color: #fff; height: 30px;">
                        Código:<br>GRF-F-076
                    </td>
                </tr>

                <!-- HEADER BARS -->
                <tr>
                    <td colspan="24"
                        style="border: 1.5px solid #464646ff; text-align: center; vertical-align: middle; font-weight: bold; font-size: 12px; padding: 10px; background-color: #52525b; color: #fff; text-transform: uppercase; letter-spacing: 0.5px;">
                        GASTOS DE DESPLAZAMIENTO
                    </td>
                </tr>
                <tr>
                    <td colspan="24"
                        style="border: 1.5px solid #000; text-align: center; vertical-align: middle; font-weight: bold; font-size: 11px; padding: 10px; background-color: #52525b; color: #fff; text-transform: uppercase; letter-spacing: 0.5px;">
                        COMPROBANTE LEGALIZACION GASTOS TRANSPORTE<br>INFORMAL - CONTRATISTAS
                    </td>
                </tr>

                <!-- METADATA ROWS -->
                <tr>
                    <td colspan="6"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                        CIUDAD / MUNICIPIO - FECHA:
                    </td>
                    <td colspan="9"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: normal; background-color: #fff;">
                        {{ strtoupper($ciudad_fecha) }}
                    </td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                        Código Regional :
                    </td>
                    <td colspan="4"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #fff;">
                        {{ $regional_codigo }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                        NOMBRE DEL CONTRATISTA:
                    </td>
                    <td colspan="9"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: normal; background-color: #fff;">
                        {{ $contratista_nombre }}
                    </td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                        Código Centro:
                    </td>
                    <td colspan="4"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #fff;">
                        {{ $centro_codigo }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                        No. DOCUMENTO IDENTIDAD:
                    </td>
                    <td colspan="9"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: normal; background-color: #fff;">
                        {{ $agenda->user->numero_documento ?? '' }}
                    </td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                        Fecha de elaboración:
                    </td>
                    <td colspan="4"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: normal; background-color: #fff;">
                        {{ $agenda->legalizado_at ? \Carbon\Carbon::parse($agenda->legalizado_at)->translatedFormat('d \d\e F \d\e Y') : ($agenda->fecha_elaboracion ? \Carbon\Carbon::parse($agenda->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') : '') }}
                    </td>
                </tr>

                <!-- COMISION DESCRIPTION ROW -->
                <tr>
                    <td colspan="24"
                        style="border: 1.5px solid #000; padding: 4px 6px; font-size: 8px; text-align: justify; line-height: 1.4; background-color: #fff;">
                        En desarrollo de la comisión No. <span
                            style="border-bottom: 1px solid #000; font-weight: bold; min-width: 60px; text-align: center; display: inline-block; padding: 0 2px;">{{ $orden_viaje }}</span>
                        durante los días <span
                            style="border-bottom: 1px solid #000; font-weight: bold; min-width: 40px; text-align: center; display: inline-block; padding: 0 2px;">{{ $dias_comision }}</span>
                        del mes de <span
                            style="border-bottom: 1px solid #000; font-weight: bold; min-width: 50px; text-align: center; display: inline-block; padding: 0 2px;">{{ $mes_comision }}</span>
                        de <span
                            style="border-bottom: 1px solid #000; font-weight: bold; min-width: 30px; text-align: center; display: inline-block; padding: 0 2px;">{{ $anio_comision }}</span>
                        se informa que en los tramos detallados a continuación, fue necesario utilizar transporte
                        informal sin generación de documento soporte de pago por parte del prestador del servicio:
                    </td>
                </tr>

                <!-- TABLE GASTOS HEADERS -->
                <tr style="background-color: #fff;">
                    <td colspan="4"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase;">
                        FECHA</td>
                    <td colspan="11"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase;">
                        TRAYECTO GENERADOR DEL PAGO</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase;">
                        MEDIO DE TRANSPORTE EMPLEADO</td>
                    <td colspan="4"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase;">
                        VALOR PAGADO</td>
                </tr>

                <!-- TABLE GASTOS BODY -->
                @if(!empty($gastos_transporte))
                    @foreach($gastos_transporte as $gasto)
                        <tr>
                            <td colspan="4"
                                style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; background-color: #fff;">
                                {{ !empty($gasto['fecha']) ? \Carbon\Carbon::parse($gasto['fecha'])->format('d/m/Y') : '' }}
                            </td>
                            <td colspan="11"
                                style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; text-transform: uppercase; background-color: #fff;">
                                {{ $gasto['trayecto'] ?? '' }}
                            </td>
                            <td colspan="5"
                                style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; text-transform: uppercase; background-color: #fff;">
                                {{ $gasto['medio'] ?? '' }}
                            </td>
                            <td colspan="4"
                                style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; background-color: #fff;">
                                $ {{ number_format(floatval($gasto['valor'] ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="24"
                            style="border: 1.5px solid #000; padding: 15px; font-size: 10px; text-align: center; color: #777; font-style: italic; background-color: #fff;">
                            No se registraron gastos de transporte informal.
                        </td>
                    </tr>
                @endif

                <!-- RAZON Y TOTAL -->
                <tr>
                    <td colspan="20"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #fff; vertical-align: middle;">
                        RAZON POR LA CUAL, SE AUTORIZA EL GASTO INCURRIDO CUYO VALOR PAGADO EQUIVALE A:
                    </td>
                    <td colspan="4"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; background-color: #fff; vertical-align: middle;">
                        $ {{ number_format($total_gastos, 0, ',', '.') }}
                    </td>
                </tr>

                <!-- CERTIFICACION LEGAL -->
                <tr>
                    <td colspan="24"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; line-height: 1.3; background-color: #fff;">
                        Para efectos legales el contratista certifica bajo la gravedad del juramento, que las
                        actividades objeto del desplazamiento se cumplieron a cabalidad y el valor cobrado corresponde
                        al valor efectivamente pagado al prestador del servicio de transporte informal.
                    </td>
                </tr>

                <!-- YELLOW WARNING BANNER -->
                <tr>
                    <td colspan="24"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; font-weight: bold; background-color: #ffc107; color: #000;">
                        Este formato aplica únicamente para justificar gastos de transporte en aquellos sitios donde no
                        se cuenta con transporte formal.
                    </td>
                </tr>

                <!-- FOOTER AUTHORIZATION -->
                <tr>
                    <td colspan="24"
                        style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; font-weight: bold; line-height: 1.3; background-color: #fff;">
                        Teniendo en cuenta las certificaciones suscritas por el comisionado y su jefe inmediato, se
                        autoriza el presente gasto.
                    </td>
                </tr>

                <!-- SIGNATURES SECTION GRF-F-076 -->
                <!-- Section Headers -->
                <tr style="background-color: #fff;">
                    <td colspan="8"
                        style="border: 1.5px solid #000; text-align: center; font-weight: bold; font-size: 9px; padding: 6px 8px; text-transform: uppercase;">
                        COMISIONADO CONTRATISTA</td>
                    <td colspan="8"
                        style="border: 1.5px solid #000; text-align: center; font-weight: bold; font-size: 9px; padding: 6px 8px; text-transform: uppercase;">
                        SUPERVISOR DE CONTRATO</td>
                    <td colspan="8"
                        style="border: 1.5px solid #000; text-align: center; font-weight: bold; font-size: 9px; padding: 6px 8px; text-transform: uppercase;">
                        ORDENADOR DEL GASTO</td>
                </tr>
                <!-- Signature Sub-row 1 (Nombre Completo) -->
                <tr>
                    <td colspan="3"
                        style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                        Nombre completo:</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; background-color: #fff;">
                        {{ $contratista_nombre }}
                    </td>
                    <td colspan="3"
                        style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                        Nombre completo:</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; background-color: #fff;">
                        {{ $supervisor_nombre }}
                    </td>
                    <td colspan="3"
                        style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                        Nombre completo:</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; background-color: #fff;">
                        {{ strtoupper($agenda->ordenador->nombre ?? '') }}
                    </td>
                </tr>
                <!-- Signature Sub-row 2 (Contrato / Cargo) -->
                <tr>
                    <td colspan="3"
                        style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                        Numero de Contrato:</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; background-color: #fff;">
                        {{ $contratista_contrato }}
                    </td>
                    <td colspan="3"
                        style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                        Cargo:</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; text-transform: uppercase; background-color: #fff;">
                        {{ $supervisor_cargo }}
                    </td>
                    <td colspan="3"
                        style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                        Cargo:</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; text-transform: uppercase; background-color: #fff;">
                        {{ $agenda->ordenador->cargo ?? 'SUBDIRECTOR DE CENTRO' }}
                    </td>
                </tr>
                <!-- Signature Sub-row 3 (Firma) -->
                <tr>
                    <td colspan="3"
                        style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; vertical-align: middle; background-color: #fff;">
                        Firma:</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; padding: 2px; text-align: center; vertical-align: middle; height: 35px; background-color: #fff;">
                        @if($contratista_firma_base64)
                            <img src="{{ $contratista_firma_base64 }}"
                                style="max-height: 30px; max-width: 100%; object-fit: contain;">
                        @endif
                    </td>
                    <td colspan="3"
                        style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; vertical-align: middle; background-color: #fff;">
                        Firma:</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; padding: 2px; text-align: center; vertical-align: middle; height: 35px; background-color: #fff;">
                        @if($supervisor_firma_base64 && in_array($agenda->legalizacion_estado, ['APROBADA_SUPERVISOR', 'APROBADA_LEGALIZACION', 'APROBADA_ORDENADOR']))
                            <img src="{{ $supervisor_firma_base64 }}" style="max-height: 30px; max-width: 100%; object-fit: contain;" alt="Firma Supervisor">
                        @endif
                    </td>
                    <td colspan="3"
                        style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; vertical-align: middle; background-color: #fff;">
                        Firma:</td>
                    <td colspan="5"
                        style="border: 1.5px solid #000; padding: 2px; text-align: center; vertical-align: middle; height: 35px; background-color: #fff;">
                        @if($ordenador_firma_base64 && $agenda->legalizacion_estado === 'APROBADA_ORDENADOR' && $agenda->realiza_declaracion)
                            <img src="{{ $ordenador_firma_base64 }}" style="max-height: 30px; max-width: 100%; object-fit: contain;" alt="Firma Ordenador">
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        @endif
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        async function descargarPDF() {
            // Set pdf.js worker URL
            if (typeof pdfjsLib !== 'undefined') {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
            }

            const element = document.getElementById('hoja-legalizacion');
            const webHeader = document.getElementById('web-logo-header');
            const btn = document.querySelector('.no-print');

            // 1. Mostrar estado de carga en el botón
            const originalBtnText = btn ? btn.innerHTML : '';
            if (btn) {
                btn.innerHTML = '<span>Generando... (Renderizando PDFs adjuntos)</span>';
                btn.disabled = true;
            }

            // 2. Renderizar contenedores PDF a imágenes canvas reales
            const containers = document.querySelectorAll('.pdf-render-container');
            const renderedCanvases = [];

            for (const container of containers) {
                const pdfUrl = container.getAttribute('data-pdf-url');
                if (!pdfUrl) continue;
                
                try {
                    const loadingTask = pdfjsLib.getDocument(pdfUrl);
                    const pdf = await loadingTask.promise;
                    
                    const printBlock = container.querySelector('.print-only-block');
                    if (printBlock) {
                        printBlock.innerHTML = ''; // Limpiar mensaje de carga
                        printBlock.style.padding = '0';
                        printBlock.style.border = 'none';
                        printBlock.style.background = 'transparent';
                        
                        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                            const page = await pdf.getPage(pageNum);
                            
                            const canvas = document.createElement('canvas');
                            canvas.style.display = 'block';
                            canvas.style.margin = '10px auto';
                            canvas.style.border = '1.5px solid #000';
                            canvas.style.borderRadius = '6px';
                            canvas.className = 'no-break';
                            
                            // Evitar saltos de página fantasmas limitando la altura de las páginas completas (Declaración)
                            if (container.style.width === '100%') {
                                canvas.style.maxHeight = '190mm';
                                canvas.style.width = 'auto';
                            } else {
                                canvas.style.width = '100%';
                                canvas.style.height = 'auto';
                            }
                            
                            const viewport = page.getViewport({ scale: 2.0 }); // Calidad HD
                            canvas.width = viewport.width;
                            canvas.height = viewport.height;
                            
                            const context = canvas.getContext('2d');
                            await page.render({
                                canvasContext: context,
                                viewport: viewport
                            }).promise;
                            
                            printBlock.appendChild(canvas);
                            renderedCanvases.push({ container, canvas });
                        }
                    }
                } catch (err) {
                    console.error("Error al renderizar el PDF adjunto: " + pdfUrl, err);
                }
            }

            // Guardar estilos originales para restaurar después
            const originalWidth = element.style.width;
            const originalMargin = element.style.margin;
            const originalPadding = element.style.padding;

            let parentIframe = null;
            let originalIframeWidth = '';
            let originalIframeMaxWidth = '';
            let originalIframeMinWidth = '';

            try {
                if (window.frameElement) {
                    parentIframe = window.frameElement;
                    originalIframeWidth = parentIframe.style.width;
                    originalIframeMaxWidth = parentIframe.style.maxWidth;
                    originalIframeMinWidth = parentIframe.style.minWidth;

                    parentIframe.style.setProperty('width', '850px', 'important');
                    parentIframe.style.setProperty('min-width', '850px', 'important');
                    parentIframe.style.setProperty('max-width', 'none', 'important');
                }
            } catch (e) {
                console.warn("Could not access parent iframe: ", e);
            }

            // Guardar viewport original para restaurar después en celulares
            let viewport = document.querySelector('meta[name="viewport"]');
            window.pdfOriginalViewport = '';
            window.pdfCreatedViewport = false;
            
            if (!viewport) {
                viewport = document.createElement('meta');
                viewport.name = 'viewport';
                document.head.appendChild(viewport);
                window.pdfCreatedViewport = true;
            } else {
                window.pdfOriginalViewport = viewport.getAttribute('content');
            }
            viewport.setAttribute('content', 'width=816, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');

            const styleTag = document.createElement('style');
            styleTag.id = 'pdf-forced-styles';
            styleTag.innerHTML = `
                html, body {
                    width: 816px !important;
                    min-width: 816px !important;
                    overflow: visible !important;
                }
                .hoja {
                    width: 816px !important;
                    min-width: 816px !important;
                    margin: 0 auto !important;
                    padding: 10mm !important;
                    min-height: unset !important;
                }
            `;
            document.head.appendChild(styleTag);

            if (webHeader) webHeader.style.display = 'none';
            element.style.paddingTop = '0';

            const webBlocks = document.querySelectorAll('.web-only-block');
            const printBlocks = document.querySelectorAll('.print-only-block');
            webBlocks.forEach(b => b.style.setProperty('display', 'none', 'important'));
            printBlocks.forEach(b => b.style.setProperty('display', 'block', 'important'));

            setTimeout(function() {
                const opt = {
                    margin: [30, 0, 20, 0],
                    filename: 'Legalizacion_Contratista.pdf',
                    image: { type: 'jpeg', quality: 0.80 },
                    html2canvas: {
                        scale: 1.5,
                        useCORS: true,
                        letterRendering: true,
                        scrollY: 0,
                        scrollX: 0,
                        x: 0,
                        y: 0,
                        windowWidth: 816
                    },
                    jsPDF: { unit: 'mm', format: 'letter', orientation: 'portrait' },
                    pagebreak: {
                        mode: ['css', 'legacy'],
                        avoid: ['tr', '.no-break']
                    }
                };

                let promise = html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                    const totalPages = pdf.internal.getNumberOfPages();
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const pageHeight = pdf.internal.pageSize.getHeight();

                    for (let i = 1; i <= totalPages; i++) {
                        pdf.setPage(i);
                        try {
                            if (window.logoBase64) {
                                const logoWidth = 14;
                                const logoHeight = 14;
                                const logoX = (pageWidth - logoWidth) / 2;
                                pdf.addImage(window.logoBase64, 'PNG', logoX, 8, logoWidth, logoHeight);
                            }
                        } catch (logoErr) {
                            console.warn("Could not render logo on PDF page " + i, logoErr);
                        }

                        // Línea que CIERRA el cuadro al fondo de la hoja (cuando el contenido continúa)
                        pdf.setDrawColor(0);
                        pdf.setLineWidth(0.4);
                        if (i < totalPages) {
                            pdf.line(10, pageHeight - 20, pageWidth - 10, pageHeight - 20);
                        }
                        // Línea que ABRE el cuadro al inicio del contenido en hojas 2+
                        if (i > 1) {
                            pdf.line(10, 30, pageWidth - 10, 30);
                        }

                        pdf.setFont("helvetica", "normal");
                        pdf.setFontSize(9);
                        pdf.setTextColor(0);
                        pdf.text(i.toString(), pageWidth - 15, pageHeight - 15);
                        pdf.text("GTH-F-087 V.02", pageWidth - 45, pageHeight - 10);
                    }
                    return pdf;
                });

                if (window.autoSaveRoute) {
                    promise = promise.output('blob').then(async function (pdfBlob) {
                        const formData = new FormData();
                        formData.append('pdf', pdfBlob, 'legalizacion_{{ $agenda->id }}.pdf');
                        formData.append('_token', '{{ csrf_token() }}');
                        
                        try {
                            const response = await fetch(window.autoSaveRoute, {
                                method: 'POST',
                                body: formData
                            });
                            const resData = await response.json();
                            if (resData.success) {
                                window.location.reload();
                            } else {
                                console.error('Error auto-saving:', resData);
                                alert('Error al guardar el PDF en el servidor.');
                            }
                        } catch (err) {
                            console.error('Error sending fetch:', err);
                            alert('Error de red al guardar el PDF.');
                        }
                    });
                } else {
                    promise = promise.save().then(() => {
                        restaurarTodo();
                    }).catch(err => {
                        console.error("Error generating PDF: ", err);
                        restaurarTodo();
                    });
                }
            }, 250);

            function restaurarTodo() {
                if (webHeader) webHeader.style.display = 'block';
                element.style.width = originalWidth;
                element.style.margin = originalMargin;
                element.style.padding = originalPadding;
                element.style.paddingTop = '10mm';

                webBlocks.forEach(b => b.style.display = '');
                printBlocks.forEach(b => b.style.display = 'none');

                btn.innerHTML = originalBtnText;
                btn.disabled = false;

                const styleTagRemoved = document.getElementById('pdf-forced-styles');
                if (styleTagRemoved) styleTagRemoved.remove();

                // Restaurar viewport original en celulares
                let viewport = document.querySelector('meta[name="viewport"]');
                if (viewport) {
                    if (window.pdfCreatedViewport) {
                        viewport.remove();
                    } else if (window.pdfOriginalViewport) {
                        viewport.setAttribute('content', window.pdfOriginalViewport);
                    }
                }

                if (parentIframe) {
                    parentIframe.style.width = originalIframeWidth;
                    parentIframe.style.minWidth = originalIframeMinWidth;
                    parentIframe.style.maxWidth = originalIframeMaxWidth;
                }
            }
        }
    </script>

    @if(isset($isFinalState) && $isFinalState)
    <!-- Capa de carga para guardar el PDF firmado -->
    <div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(255,255,255,0.95); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; font-family: sans-serif;">
        <div style="border: 6px solid #f3f3f3; border-top: 6px solid #39A900; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite;"></div>
        <p style="margin-top: 20px; font-weight: bold; color: #333; font-size: 16px;">Generando y guardando documento firmado...</p>
        <p style="color: #666; font-size: 14px;">Este proceso se realiza solo una vez.</p>
    </div>
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        window.autoSaveRoute = "{{ route('legalizacion.save-pdf', $agenda->id) }}";
        window.addEventListener('DOMContentLoaded', () => {
            // Asegurar que todas las firmas y logotipos carguen antes de proceder
            const elemento = document.getElementById('hoja-agenda') || document.querySelector('.hoja');
            const images = elemento.querySelectorAll('img');
            const promises = Array.from(images).map(img => {
                if (img.complete) return Promise.resolve();
                return new Promise(resolve => {
                    img.onload = resolve;
                    img.onerror = resolve;
                });
            });

            Promise.all(promises).then(() => {
                // Desplazar al tope para evitar bug de scrollY y ejecutar
                setTimeout(() => {
                    window.scrollTo(0, 0);
                    descargarPDF();
                }, 300);
            });
        });
    </script>
    @endif
</body>

</html>