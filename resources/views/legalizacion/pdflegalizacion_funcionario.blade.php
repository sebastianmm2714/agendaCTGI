<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Labores - SENA</title>
    <link rel="icon" href="{{ asset('images/sena/logo-sena-verde.png') }}" type="image/png">
    <style>
        :root {
            --sena-green: #39A900;
            --sena-blue: #00324d;
            --border-color: #000;
            --header-bg: #d9d9d9;
            --text-color: #000;
        }

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

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 20px;
        }

        td {
            border: 1.5px solid #000;
            padding: 6px 10px;
            font-size: 12px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .bg-gray {
            background-color: #ffffff !important;
            color: #000 !important;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 14px;
            padding: 8px !important;
        }

        .label-cell {
            font-weight: bold;
            font-size: 12px;
            line-height: 1.2;
        }

        .value-cell {
            font-weight: bold;
            font-size: 13px;
            line-height: 1.2;
        }

        .label-subtext {
            font-weight: bold;
            font-size: 10px;
            display: block;
            margin-top: 2px;
        }

        .activities-container {
            padding: 10px;
            line-height: 1.4;
            font-size: 13px;
        }

        .signature-header {
            background-color: #ffffff;
            color: #000;
            font-weight: bold;
            text-align: left;
            padding-left: 10px;
            font-size: 14px;
        }

        /* Botón de descarga */
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

        /* Optimización de saltos de página idéntica a contratistas */
        tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .no-break {
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
    {{-- Librería para generar el PDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
</head>

<body>
    @php
        // Logo logic - Búsqueda robusta para cPanel/Producción
        $logoBase64 = '';
        $posiblesRutas = [
            public_path('images/sena/logoSena.png'),
            public_path('images/sena/logo-sena-verde.png'),
            public_path('images/sena/agenda_labores.png'),
            base_path('../public_html/images/sena/logoSena.png'),
            base_path('../public_html/images/sena/logo-sena-verde.png'),
            base_path('../public_html/images/sena/agenda_labores.png'),
            $_SERVER['DOCUMENT_ROOT'] . '/images/sena/logoSena.png',
            $_SERVER['DOCUMENT_ROOT'] . '/images/sena/logo-sena-verde.png',
            $_SERVER['DOCUMENT_ROOT'] . '/images/sena/agenda_labores.png'
        ];

        foreach ($posiblesRutas as $ruta) {
            if (file_exists($ruta)) {
                $logoData = file_get_contents($ruta);
                $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                break;
            }
        }

        // Fallback si falla la lectura física
        if (empty($logoBase64)) {
            $logoBase64 = asset('images/sena/logoSena.png');
        }

        // Closures and standard imports for firmas base64 to prevent global name clash
        $getFirmaBase64 = function ($path) {
            if (empty($path))
                return null;
            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath)) {
                return 'data:image/png;base64,' . base64_encode(file_get_contents($fullPath));
            }
            return null;
        };

        // Computed dynamic values
        $fecha_elaboracion = $agenda->legalizado_at
            ? \Carbon\Carbon::parse($agenda->legalizado_at)->translatedFormat('d \d\e F \d\e Y')
            : ($agenda->fecha_elaboracion
                ? \Carbon\Carbon::parse($agenda->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y')
                : now()->translatedFormat('d \d\e F \d\e Y'));

        $comisionado_nombre = strtoupper($agenda->user->name ?? '');
        $comisionado_cargo = strtoupper($agenda->user->cargo ?? 'SERVIDOR PÚBLICO');
        $dependencia = strtoupper($agenda->centro ?? 'CENTRO TEXTIL Y DE GESTIÓN INDUSTRIAL');
        $orden_viaje = $agenda->orden_viaje ?? '';

        $fecha_inicio_corta = $agenda->fecha_inicio ? \Carbon\Carbon::parse($agenda->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') : '';
        $fecha_fin_corta = $agenda->fecha_fin ? \Carbon\Carbon::parse($agenda->fecha_fin)->translatedFormat('d \d\e F \d\e Y') : '';

        $destinosArr = [];
        if ($agenda->destinos) {
            $destinosArr = array_unique(array_filter(array_map(fn($d) => ($d['nombre'] ?? ''), is_array($agenda->destinos) ? $agenda->destinos : json_decode($agenda->destinos, true))));
        }
        $ciudad_destino = !empty($destinosArr) ? implode(', ', $destinosArr) : ($agenda->ciudad_destino ?: '');
        $ciudad_destino = strtoupper($ciudad_destino);
        $regional = strtoupper($agenda->regional ?? 'ANTIOQUIA');
        $objetivo = $agenda->objetivo_desplazamiento ?? '';

        $supervisor_nombre = strtoupper($agenda->supervisor->nombre ?? '');
        $supervisor_cargo = strtoupper($agenda->supervisor->cargo ?? 'COORDINADOR ACADÉMICO / SUPERVISOR');
        $ciudad_fecha = 'Medellín ' . ($agenda->legalizado_at ? \Carbon\Carbon::parse($agenda->legalizado_at)->translatedFormat('d \d\e F \d\e Y') : ($agenda->fecha_elaboracion ? \Carbon\Carbon::parse($agenda->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') : now()->translatedFormat('d \d\e F \d\e Y')));

        // Resultados
        $resultados = [];
        if (!empty($agenda->legalizacion_resultados)) {
            $resultados = is_array($agenda->legalizacion_resultados)
                ? $agenda->legalizacion_resultados
                : json_decode($agenda->legalizacion_resultados, true);
        }
        if (empty($resultados)) {
            $resultados = ['SE CUMPLIERON LAS SESIONES PLANIFICADAS Y SE ASIGNARON ACTIVIDADES DE MANERA SATISFACTORIA.'];
        }

        // Fotos
        $fotosBase64 = [];
        if (!empty($agenda->legalizacion_fotos)) {
            $fotos = is_array($agenda->legalizacion_fotos)
                ? $agenda->legalizacion_fotos
                : json_decode($agenda->legalizacion_fotos, true);
            if (is_array($fotos)) {
                foreach ($fotos as $foto) {
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
        }

        // Planillas
        $planillasBase64 = [];
        if (!empty($agenda->legalizacion_planillas)) {
            $planillas = is_array($agenda->legalizacion_planillas)
                ? $agenda->legalizacion_planillas
                : json_decode($agenda->legalizacion_planillas, true);
            if (is_array($planillas)) {
                foreach ($planillas as $planilla) {
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
        }

        // Tiquetes
        $tiquetesBase64 = [];
        if (!empty($agenda->legalizacion_tiquetes)) {
            $tiquetes = is_array($agenda->legalizacion_tiquetes)
                ? $agenda->legalizacion_tiquetes
                : json_decode($agenda->legalizacion_tiquetes, true);
            if (is_array($tiquetes)) {
                foreach ($tiquetes as $tiquete) {
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

        $formatHora = function ($hora) {
            if (empty($hora))
                return '';
            try {
                if (str_contains(strtolower($hora), 'meridiano') || str_contains(strtolower($hora), 'm.')) {
                    return $hora;
                }

                $horaClean = trim($hora);
                $carbon = \Carbon\Carbon::parse($horaClean);
                $timeStr = $carbon->format('g i a');
                $parts = explode(' ', $timeStr);
                $h = $parts[0];
                $m = $parts[1];
                $ampm = strtolower($parts[2]);

                if ($h == '12' && $ampm == 'pm') {
                    return '12 meridiano.';
                }
                if ($m == '00') {
                    return $h . ' ' . $ampm;
                } else {
                    return $h . ':' . $m . ' ' . $ampm;
                }
            } catch (\Exception $e) {
                return $hora;
            }
        };
    @endphp

    <script>
        window.logoBase64 = "{{ $logoBase64 }}";
    </script>

    <button class="no-print" onclick="descargarPDF()">
        <span>Descargar Formato</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
    </button>

    <div class="hoja" id="hoja-agenda">
        <!-- LOGO: Solo visible en web, se oculta en PDF (el logo se dibuja por JS en el margen) -->
        <div id="logo-header" style="text-align: center; padding: 10px 0 15px 0;">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="SENA Logo"
                    style="max-height: 80px; display: block; margin: 0 auto; object-fit: contain;">
            @else
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Sena_Colombia_logo.svg/1200px-Sena_Colombia_logo.svg.png"
                    style="max-height: 80px; display: block; margin: 0 auto;">
            @endif
        </div>

        <!-- CLASIFICACIÓN: Se mantiene visible tanto en web como en PDF -->
        <table id="clasificacion-tabla" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
            <tr>
                @for ($i = 0; $i < 48; $i++)
                    <td style="height:0; padding:0; border:none; width:2.0833%;"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="48"
                    style="background-color: #000; color: #fff; text-align: center; font-weight: bold; font-size: 14px; padding: 8px; border: 1.5px solid #000; text-transform: uppercase;">
                    CLASIFICACIÓN DE LA INFORMACIÓN
                </td>
            </tr>
            <tr>
                <td colspan="13"
                    style="font-weight: bold; font-size: 12px; border: 1.5px solid #000; padding: 8px; background-color: #fff; color: #000;">
                    Pública</td>
                <td colspan="3"
                    style="text-align: center; border: 1.5px solid #000; padding: 8px; font-weight: bold; font-size: 14px; vertical-align: middle; background-color: #fff; color: #000;">
                    {{ ($agenda->clasificacion_id == 1) ? 'X' : '' }}
                </td>
                <td colspan="13"
                    style="font-weight: bold; font-size: 12px; border: 1.5px solid #000; padding: 8px; background-color: #fff; color: #000;">
                    Pública Clasificada</td>
                <td colspan="3"
                    style="text-align: center; border: 1.5px solid #000; padding: 8px; font-weight: bold; font-size: 14px; vertical-align: middle; background-color: #fff; color: #000;">
                    {{ ($agenda->clasificacion_id == 2) ? 'X' : '' }}
                </td>
                <td colspan="13"
                    style="font-weight: bold; font-size: 12px; border: 1.5px solid #000; padding: 8px; background-color: #fff; color: #000;">
                    Pública Reservada</td>
                <td colspan="3"
                    style="text-align: center; border: 1.5px solid #000; padding: 8px; font-weight: bold; font-size: 14px; vertical-align: middle; background-color: #fff; color: #000;">
                    {{ ($agenda->clasificacion_id == 3) ? 'X' : '' }}
                </td>
            </tr>
        </table>

        <!-- TABLA 2: CUERPO (Datos, Lugar, Actividades, Firmas) -->
        <table style="margin-top: -1.5px;">
            <!-- GRID CONTROL ROW (48 columns) -->
            <tr>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <td colspan="1" style="height:0; padding:0; border:none; width:2.0833%;"></td>
                <!-- DATOS DE LA AGENDA SECTION -->

            <tr>
                <td colspan="18" class="label-cell">
                    FECHA DE ELABORACIÓN DEL INFORME.
                    <span class="label-subtext" style="font-weight: normal; font-size: 10px; margin-top: 2px;">Según la
                        Resolución 2838/2016 art.14</span>
                </td>
                <td colspan="30" class="value-cell" style="vertical-align: middle;">
                    {{ $fecha_elaboracion }}
                </td>
            </tr>
            <tr>
                <td colspan="18" class="label-cell">NOMBRES Y APELLIDOS DEL COMISIONADO</td>
                <td colspan="30" class="value-cell text-uppercase" style="vertical-align: middle;">{{ $comisionado_nombre }}</td>
            </tr>
            <tr>
                <td colspan="18" class="label-cell">DEPENDENCIA</td>
                <td colspan="30" class="value-cell text-uppercase" style="vertical-align: middle;">{{ $dependencia }}</td>
            </tr>
            <tr>
                <td colspan="48" class="bg-gray" style="padding: 12px 10px !important;">INFORMACIÓN DE LA COMISIÓN</td>
            </tr>
            <tr>
                <td colspan="14" class="label-cell">No. COMISIÓN DE SERVICIOS</td>
                <td colspan="34" class="value-cell text-uppercase" style="vertical-align: middle;">{{ $orden_viaje }}</td>
            </tr>
            <tr>
                <td colspan="14" class="label-cell" style="white-space: nowrap; font-size: 11px;">FECHA INICIO DE COMISIÓN</td>
                <td colspan="10" class="value-cell text-center" style="vertical-align: middle;">{{ $fecha_inicio_corta }}</td>
                <td colspan="14" class="label-cell" style="white-space: nowrap; font-size: 11px;">FECHA FIN DE LA COMISIÓN</td>
                <td colspan="10" class="value-cell text-center" style="vertical-align: middle;">{{ $fecha_fin_corta }}</td>
            </tr>

            <!-- LUGAR DE LA COMISIÓN SECTION -->
            <tr>
                <td colspan="48" class="bg-gray" style="padding: 12px 10px !important;">LUGAR DONDE SE REALIZÓ LA COMISIÓN</td>
            </tr>
            <tr>
                <td colspan="10" class="label-cell text-center" style="font-size: 11px; text-align: center;">CIUDAD O MUNICIPIO</td>
                <td colspan="12" class="label-cell text-center" style="font-size: 11px; text-align: center;">DIRECCIÓN GENERAL / REGIONAL</td>
                <td colspan="26" class="label-cell text-center" style="font-size: 11px; text-align: center;">DEPENDENCIA /CENTRO DE FORMACIÓN/SEDE/INSTITUCION VISITADA</td>
            </tr>
            <tr>
                <td colspan="10" class="value-cell text-center" style="padding: 10px; font-weight: normal; text-transform: uppercase; vertical-align: middle;">
                    {{ ucwords(strtolower($ciudad_destino)) }}
                </td>
                <td colspan="12" class="value-cell text-center" style="font-weight: normal; text-transform: uppercase; vertical-align: middle;">
                    {{ ucwords(strtolower($regional)) }}
                </td>
                <td colspan="26" class="value-cell text-center" style="font-weight: bold; text-transform: uppercase; vertical-align: middle;">
                    {{ $dependencia }}
                </td>
            </tr>

            <!-- OBJETO SECTION -->
            <tr>
                <td colspan="48" class="value-cell" style="padding: 12px; font-size: 11px; line-height: 1.4; font-weight: normal;">
                    <span style="font-weight: bold;">OBJETO DE LA COMISIÓN:</span>
                    @php
                        $cleanObjetivo = $objetivo;
                        if (str_starts_with(strtoupper($cleanObjetivo), 'OBJETO DE LA COMISIÓN:')) {
                            $cleanObjetivo = substr($cleanObjetivo, strlen('OBJETO DE LA COMISIÓN:'));
                        }
                        $cleanObjetivo = trim($cleanObjetivo);
                    @endphp
                    {{ strtoupper($cleanObjetivo) }}
                </td>
            </tr>

            <!-- ACTIVIDADES SECTION -->
            <tr>
                <td colspan="48" class="bg-gray" style="padding: 10px !important; text-align: center; line-height: 1.3; font-size: 12px;">
                    ACTIVIDADES DESARROLLADAS DURANTE LA COMISIÓN RESOLUCIÓN 2838/2016 Art.17:
                    <div style="font-weight: bold; font-size: 12px; margin-top: 20px; text-transform: none;">
                        (Deberá contener información detallada de las tareas realizadas día a día)
                    </div>
                </td>
            </tr>
            @forelse($agenda->actividades->sortBy('fecha') as $index => $act)
                @php
                    $items = is_array($act->actividad) ? $act->actividad : json_decode($act->actividad, true);
                    if (!is_array($items)) {
                        $items = [['hora' => '', 'actividad' => $act->actividad]];
                    }

                    $actividadesEjecutar = [];
                    $returnTime = null;

                    foreach ($items as $item) {
                        $text = $item['actividad'] ?? '';
                        $horaStr = $item['hora'] ?? '';

                        if (
                            $act->ruta_regreso && (
                                str_contains(strtolower($text), 'regreso') ||
                                str_contains(strtolower($text), 'retorno') ||
                                str_contains(strtolower($text), 'desplazamiento ruta de regreso') ||
                                str_contains(strtolower($text), 'desplazamiento de regreso')
                            )
                        ) {
                            $returnTime = $horaStr;
                        } else {
                            $actividadesEjecutar[] = $item;
                        }
                    }

                    // Fallback for return time if not explicitly found in text
                    if ($act->ruta_regreso && empty($returnTime) && count($items) > 1) {
                        $lastItem = end($items);
                        $lastText = $lastItem['actividad'] ?? '';
                        if (str_contains(strtolower($lastText), 'viaje') || str_contains(strtolower($lastText), 'desplazamiento') || str_contains(strtolower($lastText), 'regreso') || str_contains(strtolower($lastText), 'retorno')) {
                            $returnTime = $lastItem['hora'] ?? null;
                            array_pop($actividadesEjecutar);
                        }
                    }
                @endphp
                <tbody>
                    <!-- ENCABEZADO DÍA -->
                    <tr>
                        <td colspan="48" style="padding: 15px 20px 5px 20px; border-top: none; border-bottom: none; border-left: 1.5px solid #000; border-right: 1.5px solid #000;">
                            <div style="font-weight: bold; font-size: 14px; text-decoration: underline;">
                                Día {{ $index + 1 }}:
                            </div>
                        </td>
                    </tr>

                    @if($act->ruta_ida || (count(is_array($act->transporte_ida) ? $act->transporte_ida : []) > 0))
                        <tr>
                            <td colspan="48" style="padding: 10px 20px 5px 20px; border-top: none; border-bottom: none; border-left: 1.5px solid #000; border-right: 1.5px solid #000;">
                                @if($act->ruta_ida)
                                    <div style="margin-bottom: 4px; font-size: 13px;">
                                        <span style="font-weight: bold;">Desplazamiento ruta de ida:</span><br>
                                        {{ strtoupper($act->ruta_ida) }}
                                    </div>
                                @endif

                                @if(count(is_array($act->transporte_ida) ? $act->transporte_ida : []) > 0)
                                    <div style="margin-bottom: 4px; font-size: 13px;">
                                        <span style="font-weight: bold;">Medio de transporte:</span> 
                                        @php 
                                                                    $medios = is_array($act->transporte_ida) ? $act->transporte_ida : [];
                                            $mediosStr = implode(', ', array_map('strtoupper', $medios));
                                        @endphp
                                        <strong><u>{{ $mediosStr }}</u></strong>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="48" style="padding: 10px 20px 0px 20px; border-top: none; border-bottom: none; border-left: 1.5px solid #000; border-right: 1.5px solid #000;">
                            <div style="font-weight: bold; font-size: 13px;">
                                Actividades para ejecutar:
                            </div>
                        </td>
                    </tr>

                    @foreach($actividadesEjecutar as $item)
                        <tr>
                            <td colspan="48" style="padding: 8px 20px; border-top: none; border-bottom: none; border-left: 1.5px solid #000; border-right: 1.5px solid #000;">
                                <div style="font-size: 13px; line-height: 1.4;">
                                    {{ strtoupper($item['actividad'] ?? '') }}<br>
                                    <span style="font-weight: bold;">Hora: {{ $formatHora($item['hora'] ?? '') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @php 
                        $esUltimoDia = $loop->last; 
                    @endphp

                    <tr>
                        <td colspan="48" style="padding: 10px 20px 15px 20px; border-top: none; border-left: 1.5px solid #000; border-right: 1.5px solid #000; {{ $esUltimoDia ? 'border-bottom: 1.5px solid #000;' : 'border-bottom: none;' }}">
                            @if($act->ruta_regreso)
                                <div style="margin-top: 10px; padding-top: 0;">
                                    <div style="font-size: 13px; line-height: 1.4;">
                                        <span style="font-weight: bold;">Ruta de regreso</span> {{ $act->ruta_regreso }}<br>
                                        @if($returnTime)
                                            <span style="font-weight: bold;">Hora: {{ $formatHora($returnTime) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                </tbody>
            @empty
                <tbody>
                    <tr>
                        <td colspan="48" style="text-align: center; padding: 20px; color: #666; font-style: italic; border-bottom: 1.5px solid #000;">
                            No hay actividades reportadas para esta comisión.
                        </td>
                    </tr>
                </tbody>
            @endforelse
            <!-- SECCIÓN H: RESULTADOS Y EVIDENCIAS DE LA COMISIÓN -->
            <tbody>
                <tr>
                    <td colspan="48" class="bg-gray" style="padding: 10px !important; font-size: 14px; font-weight: bold;">CONCLUSIONES</td>
                </tr>
                <tr>
                    <td colspan="48" style="padding: 12px 15px; vertical-align: top;">
                        <div style="font-weight: bold; font-size: 13px; margin-bottom: 8px;">
                            Relacione los resultados de la comisión
                        </div>
                        @forelse($resultados as $idx => $res)
                            <div style="font-size: 13px; line-height: 1.5; margin-left: 15px; margin-bottom: 4px;">
                                <span style="font-weight: bold;">{{ $idx + 1 }}.</span> {{ strtoupper($res) }}
                            </div>
                        @empty
                            <div style="font-size: 13px; color: #666; font-style: italic; margin-left: 15px;">
                                No se reportaron resultados para esta comisión.
                            </div>
                        @endforelse
                    </td>
                </tr>
                <tr>
                    <td colspan="48" style="padding: 10px 15px; font-weight: bold; font-size: 12px; text-transform: none; line-height: 1.3;">
                        EVIDENCIAS O SOPORTES: Enuncie y anexe los documentos que soportan los resultados de la comisión.
                        <div style="font-weight: bold; font-size: 12px; margin-top: 25px; text-transform: none; text-align: center;">
                            (Actas, registro fotográfico, listas de asistencia, invitaciones)
                        </div>
                    </td>
                </tr>
            </tbody>

            <!-- FOTOGRAFÍAS -->
            @if(!empty($fotosBase64))
                @foreach($fotosBase64 as $foto)
                    <tbody>
                        <tr>
                            <td colspan="48" style="padding: 15px; text-align: center; border: 1.5px solid #000;">
                                <div style="font-weight: bold; font-size: 13px; margin-bottom: 8px; text-align: left; text-transform: uppercase;">
                                    1. Evidencia Fotográfica {{ $loop->iteration }}
                                </div>
                                <img src="{{ $foto }}" style="width: auto; max-width: 100%; height: auto; max-height: 280px; object-fit: contain; display: block; margin: 0 auto; border: 1px solid #ddd; border-radius: 4px; padding: 2px;" alt="Evidencia fotográfica">
                            </td>
                        </tr>
                    </tbody>
                @endforeach
            @else
                <tbody>
                    <tr>
                        <td colspan="48" style="padding: 15px; color: #777; font-style: italic; border: 1.5px solid #000;">
                            1. Fotografías: No se adjuntaron evidencias fotográficas.
                        </td>
                    </tr>
                </tbody>
            @endif

            <!-- FORMATOS DE ASISTENCIA -->
            @if(!empty($planillasBase64))
                @foreach($planillasBase64 as $planilla)
                    <tbody>
                        <tr>
                            <td colspan="48" style="padding: 15px; text-align: center; border: 1.5px solid #000;">
                                <div style="font-weight: bold; font-size: 13px; margin-bottom: 8px; text-align: left; text-transform: uppercase;">
                                    2. Formato de Asistencia {{ $loop->iteration }}
                                </div>
                                <img src="{{ $planilla }}" style="width: auto; max-width: 100%; height: auto; max-height: 280px; object-fit: contain; display: block; margin: 0 auto; border: 1px solid #ddd; border-radius: 4px; padding: 2px;" alt="Planilla de asistencia">
                            </td>
                        </tr>
                    </tbody>
                @endforeach
            @else
                <tbody>
                    <tr>
                        <td colspan="48" style="padding: 15px; color: #777; font-style: italic; border: 1.5px solid #000;">
                            2. Formatos de asistencia: No se adjuntaron formatos de asistencia.
                        </td>
                    </tr>
                </tbody>
            @endif

            <!-- TIQUETES DE VIAJE -->
            @if(!$agenda->realiza_declaracion)
                @if(!empty($tiquetesBase64))
                    @foreach($tiquetesBase64 as $tiquete)
                        <tbody>
                            <tr>
                                <td colspan="48" style="padding: 15px; text-align: center; border: 1.5px solid #000;">
                                    <div style="font-weight: bold; font-size: 13px; margin-bottom: 8px; text-align: left; text-transform: uppercase;">
                                        3. Tiquete de Viaje {{ $loop->iteration }}
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
                        </tbody>
                    @endforeach
                @else
                    <tbody>
                        <tr>
                            <td colspan="48" style="padding: 15px; color: #777; font-style: italic; border: 1.5px solid #000;">
                                3. Tiquetes de viaje: No se adjuntaron tiquetes de viaje.
                            </td>
                        </tr>
                    </tbody>
                @endif
            @endif

            <!-- FIRMAS SECTION -->
            <tbody>
                <tr>
                    <td colspan="24" class="text-bold text-center" style="font-size: 12px; font-weight: bold; text-transform: uppercase; padding: 6px 10px; background-color: #fff; text-align: center; border-right: 1.5px solid #000;">
                        NOMBRES Y APELLIDOS DEL COMISIONADO
                    </td>
                    <td colspan="24" class="text-bold text-center" style="font-size: 12px; font-weight: bold; text-transform: uppercase; padding: 6px 10px; background-color: #fff; text-align: center;">
                        FIRMA
                    </td>
                </tr>
                <tr>
                    <td colspan="24" style="height: 120px; vertical-align: top; padding: 12px; font-size: 13px; line-height: 1.4; background-color: #fff; border-right: 1.5px solid #000;">
                        @php
                            $nombreCompleto = $agenda->user->name ?? '';
                            $nombreCompleto = ucwords(mb_strtolower($nombreCompleto, 'UTF-8'));
                        @endphp
                        {{ $nombreCompleto }}.
                    </td>
                    <td colspan="24" style="height: 120px; vertical-align: middle; text-align: center; padding: 10px; background-color: #fff;">
                        @php
                            $pathFirmaComisionado = $agenda->firma_contratista_path ?? $agenda->user->firma;
                            $fBase64 = $getFirmaBase64($pathFirmaComisionado);
                        @endphp
                        @if($fBase64)
                            <img src="{{ $fBase64 }}" style="max-height: 90px; max-width: 280px; width: auto; height: auto; object-fit: contain; display: inline-block; vertical-align: middle;" alt="Firma comisionado">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="48" style="padding: 12px 15px; vertical-align: top; border: 1.5px solid #000; background-color: #fff; color: #000;">
                        <div style="font-weight: bold; font-size: 12px; margin-bottom: 8px; text-align: center; text-transform: uppercase;">
                            SOPORTES DE DESPLAZAMIENTO
                        </div>
                        @php
                            $soportes = [];
                            if (!empty($agenda->legalizacion_soportes_desplazamiento)) {
                                $soportes = is_array($agenda->legalizacion_soportes_desplazamiento)
                                    ? $agenda->legalizacion_soportes_desplazamiento
                                    : json_decode($agenda->legalizacion_soportes_desplazamiento, true);
                            }
                            if (empty($soportes)) {
                                $soportes = [
                                    'Anexe los pasabordos y/o tiquetes terrestres.',
                                    'En caso de prórroga de la comisión adjunte la comunicación Interna, radicado, el cual debe ser tramitado dentro del periodo de la comisión.',
                                    'En caso de hacer reintegros adjunte los soportes de consignación.'
                                ];
                            }
                        @endphp
                        @foreach($soportes as $sop)
                            <div style="font-size: 11px; line-height: 1.5; margin-left: 15px; margin-bottom: 6px; position: relative; padding-left: 15px;">
                                <span style="position: absolute; left: 0; font-weight: bold;">•</span>
                                {{ $sop }}
                            </div>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>

        @if($agenda->realiza_declaracion && !empty($agenda->legalizacion_declaracion))
            <!-- Page break before Declaración No Juramentada in print/PDF -->
            <div style="page-break-before: always; break-before: always; height: 1px; clear: both;"></div>

            <!-- ANEXO: DECLARACIÓN NO JURAMENTADA -->
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
                            <div style="margin-top: 10px; font-size: 11px; font-weight: bold; color: #333; text-transform: uppercase;">
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
                <table style="width: 100%; border-collapse: collapse; border: 1.5px solid #000; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; table-layout: fixed;">
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
                        <td colspan="16" rowspan="2" style="border: 1.5px solid #000; border-left: none; text-align: center; vertical-align: middle; padding: 6px; background-color: #fff;">
                            @if($logoBase64)
                                <img src="{{ $logoBase64 }}" alt="SENA Logo" style="max-height: 42px; display: inline-block; margin: 0 auto; vertical-align: middle;">
                            @else
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Sena_Colombia_logo.svg/1200px-Sena_Colombia_logo.svg.png" style="max-height: 42px; display: inline-block; margin: 0 auto; vertical-align: middle;">
                            @endif
                        </td>
                        <td colspan="4" style="border: 1.5px solid #000; text-align: center; vertical-align: middle; font-size: 9px; padding: 4px; font-weight: normal; background-color: #fff; height: 30px;">
                            Versión: 02
                        </td>
                    </tr>
                    <tr style="height: 30px;">
                        <td colspan="4" style="border: 1.5px solid #000; text-align: center; vertical-align: middle; font-size: 9px; padding: 4px; font-weight: normal; background-color: #fff; height: 30px;">
                            Código:<br>GRF-F-057
                        </td>
                    </tr>

                    <!-- HEADER BARS -->
                    <tr>
                        <td colspan="24" style="border: 1.5px solid #464646ff; text-align: center; vertical-align: middle; font-weight: bold; font-size: 12px; padding: 10px; background-color: #52525b; color: #fff; text-transform: uppercase; letter-spacing: 0.5px;">
                            COMISIONES DE SERVICIOS
                        </td>
                    </tr>
                    <tr>
                        <td colspan="24" style="border: 1.5px solid #000; text-align: center; vertical-align: middle; font-weight: bold; font-size: 11px; padding: 10px; background-color: #52525b; color: #fff; text-transform: uppercase; letter-spacing: 0.5px;">
                            COMPROBANTE LEGALIZACION GASTOS TRANSPORTE<br>INFORMAL - FUNCIONARIOS
                        </td>
                    </tr>

                    <!-- METADATA ROWS -->
                    <tr>
                        <td colspan="6" style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                            CIUDAD / MUNICIPIO - FECHA:
                        </td>
                        <td colspan="9" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: normal; background-color: #fff;">
                            {{ strtoupper($ciudad_fecha ?? 'Medellín') }}
                        </td>
                        <td colspan="5" style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                            Código Regional :
                        </td>
                        <td colspan="4" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #fff;">
                            {{ $regional_codigo }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                            NOMBRE DEL COMISIONADO:
                        </td>
                        <td colspan="9" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: normal; background-color: #fff;">
                            {{ $comisionado_nombre }}
                        </td>
                        <td colspan="5" style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                            Código Centro:
                        </td>
                        <td colspan="4" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #fff;">
                            {{ $centro_codigo }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                            No. DOCUMENTO IDENTIDAD:
                        </td>
                        <td colspan="9" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: normal; background-color: #fff;">
                            {{ $agenda->user->numero_documento ?? '' }}
                        </td>
                        <td colspan="5" style="border: 1.5px solid #000; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; text-align: center; vertical-align: middle; background-color: #fff;">
                            Fecha de elaboración:
                        </td>
                        <td colspan="4" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; vertical-align: middle; font-weight: normal; background-color: #fff;">
                            {{ $fecha_elaboracion }}
                        </td>
                    </tr>

                    <!-- COMISION DESCRIPTION ROW -->
                    <tr>
                        <td colspan="24" style="border: 1.5px solid #000; padding: 4px 6px; font-size: 8px; text-align: justify; line-height: 1.4; background-color: #fff;">
                            En desarrollo de la comisión No. <span style="border-bottom: 1px solid #000; font-weight: bold; min-width: 60px; text-align: center; display: inline-block; padding: 0 2px;">{{ $orden_viaje }}</span>
                            durante los días <span style="border-bottom: 1px solid #000; font-weight: bold; min-width: 40px; text-align: center; display: inline-block; padding: 0 2px;">{{ $dias_comision }}</span>
                            del mes de <span style="border-bottom: 1px solid #000; font-weight: bold; min-width: 50px; text-align: center; display: inline-block; padding: 0 2px;">{{ $mes_comision }}</span>
                            de <span style="border-bottom: 1px solid #000; font-weight: bold; min-width: 30px; text-align: center; display: inline-block; padding: 0 2px;">{{ $anio_comision }}</span>
                            se informa que en los tramos detallados a continuación, fue necesario utilizar transporte
                            informal sin generación de documento soporte de pago por parte del prestador del servicio:
                        </td>
                    </tr>

                    <!-- TABLE GASTOS HEADERS -->
                    <tr style="background-color: #fff;">
                        <td colspan="4" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase;">FECHA</td>
                        <td colspan="11" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase;">TRAYECTO GENERADOR DEL PAGO</td>
                        <td colspan="5" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase;">MEDIO DE TRANSPORTE EMPLEADO</td>
                        <td colspan="4" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase;">VALOR PAGADO</td>
                    </tr>

                    <!-- TABLE GASTOS BODY -->
                    @if(!empty($gastos_transporte))
                        @foreach($gastos_transporte as $gasto)
                            <tr>
                                <td colspan="4" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; background-color: #fff;">
                                    {{ !empty($gasto['fecha']) ? \Carbon\Carbon::parse($gasto['fecha'])->format('d/m/Y') : '' }}
                                </td>
                                <td colspan="11" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; text-transform: uppercase; background-color: #fff;">
                                    {{ $gasto['trayecto'] ?? '' }}
                                </td>
                                <td colspan="5" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; text-transform: uppercase; background-color: #fff;">
                                    {{ $gasto['medio'] ?? '' }}
                                </td>
                                <td colspan="4" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; background-color: #fff;">
                                    $ {{ number_format(floatval($gasto['valor'] ?? 0), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="24" style="border: 1.5px solid #000; padding: 15px; font-size: 10px; text-align: center; color: #777; font-style: italic; background-color: #fff;">
                                No se registraron gastos de transporte informal.
                            </td>
                        </tr>
                    @endif

                    <!-- RAZON Y TOTAL -->
                    <tr>
                        <td colspan="20" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; text-transform: uppercase; background-color: #fff; vertical-align: middle;">
                            RAZON POR LA CUAL, SE AUTORIZA EL GASTO INCURRIDO CUYO VALOR PAGADO EQUIVALE A:
                        </td>
                        <td colspan="4" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; font-weight: bold; text-align: center; background-color: #fff; vertical-align: middle;">
                            $ {{ number_format($total_gastos, 0, ',', '.') }}
                        </td>
                    </tr>

                    <!-- CERTIFICACION LEGAL -->
                    <tr>
                        <td colspan="24" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; line-height: 1.3; background-color: #fff;">
                            Para efectos legales el comisionado certifica bajo la gravedad del juramento, que las
                            actividades objeto del desplazamiento se cumplieron a cabalidad y el valor cobrado corresponde
                            al valor efectivamente pagado al prestador del servicio de transporte informal.
                        </td>
                    </tr>

                    <!-- YELLOW WARNING BANNER -->
                    <tr>
                        <td colspan="24" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; font-weight: bold; background-color: #ffc107; color: #000;">
                            Este formato aplica únicamente para justificar gastos de transporte en aquellos sitios donde no
                            se cuenta con transporte formal.
                        </td>
                    </tr>

                    <!-- FOOTER AUTHORIZATION -->
                    <tr>
                        <td colspan="24" style="border: 1.5px solid #000; padding: 2px 4px; font-size: 8px; text-align: center; font-weight: bold; line-height: 1.3; background-color: #fff;">
                            Teniendo en cuenta las certificaciones suscritas por el comisionado y su jefe inmediato, se
                            autoriza el presente gasto.
                        </td>
                    </tr>

                    <!-- SIGNATURES SECTION GRF-F-076 -->
                    <!-- Section Headers -->
                    <tr style="background-color: #fff;">
                        <td colspan="8" style="border: 1.5px solid #000; text-align: center; font-weight: bold; font-size: 9px; padding: 6px 8px; text-transform: uppercase;">
                            COMISIONADO SERVIDOR PÚBLICO</td>
                        <td colspan="8" style="border: 1.5px solid #000; text-align: center; font-weight: bold; font-size: 9px; padding: 6px 8px; text-transform: uppercase;">
                            SUPERVISOR / JEFE INMEDIATO</td>
                        <td colspan="8" style="border: 1.5px solid #000; text-align: center; font-weight: bold; font-size: 9px; padding: 6px 8px; text-transform: uppercase;">
                            ORDENADOR DEL GASTO</td>
                    </tr>
                    <!-- Signature Sub-row 1 (Nombre Completo) -->
                    <tr>
                        <td colspan="3" style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                            Nombre completo:</td>
                        <td colspan="5" style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; background-color: #fff;">
                            {{ $comisionado_nombre }}
                        </td>
                        <td colspan="3" style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                            Nombre completo:</td>
                        <td colspan="5" style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; background-color: #fff;">
                            {{ $supervisor_nombre }}
                        </td>
                        <td colspan="3" style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                            Nombre completo:</td>
                        <td colspan="5" style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; background-color: #fff;">
                            {{ strtoupper($agenda->ordenador->nombre ?? '') }}
                        </td>
                    </tr>
                    <!-- Signature Sub-row 2 (Cargo) -->
                    <tr>
                        <td colspan="3" style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                            Cargo:</td>
                        <td colspan="5" style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; text-transform: uppercase; background-color: #fff;">
                            {{ $comisionado_cargo }}
                        </td>
                        <td colspan="3" style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                            Cargo:</td>
                        <td colspan="5" style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; text-transform: uppercase; background-color: #fff;">
                            {{ $supervisor_cargo }}
                        </td>
                        <td colspan="3" style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; background-color: #fff;">
                            Cargo:</td>
                        <td colspan="5" style="border: 1.5px solid #000; font-size: 7px; padding: 2px 4px; text-align: center; text-transform: uppercase; background-color: #fff;">
                            {{ $agenda->ordenador->cargo ?? 'SUBDIRECTOR DE CENTRO' }}
                        </td>
                    </tr>
                    <!-- Signature Sub-row 3 (Firma) -->
                    <tr>
                        <td colspan="3" style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; vertical-align: middle; background-color: #fff;">
                            Firma:</td>
                        <td colspan="5" style="border: 1.5px solid #000; padding: 2px; text-align: center; vertical-align: middle; height: 35px; background-color: #fff;">
                            @if($contratista_firma_base64)
                                <img src="{{ $contratista_firma_base64 }}" style="max-height: 30px; max-width: 100%; object-fit: contain;">
                            @endif
                        </td>
                        <td colspan="3" style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; vertical-align: middle; background-color: #fff;">
                            Firma:</td>
                        <td colspan="5" style="border: 1.5px solid #000; padding: 2px; text-align: center; vertical-align: middle; height: 35px; background-color: #fff;">
                            @if($supervisor_firma_base64 && in_array($agenda->legalizacion_estado, ['APROBADA_SUPERVISOR', 'APROBADA_LEGALIZACION', 'APROBADA_ORDENADOR']))
                                <img src="{{ $supervisor_firma_base64 }}" style="max-height: 30px; max-width: 100%; object-fit: contain;" alt="Firma Supervisor">
                            @endif
                        </td>
                        <td colspan="3" style="border: 1.5px solid #000; font-weight: bold; font-size: 7px; padding: 2px 4px; vertical-align: middle; background-color: #fff;">
                            Firma:</td>
                        <td colspan="5" style="border: 1.5px solid #000; padding: 2px; text-align: center; vertical-align: middle; height: 35px; background-color: #fff;">
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

            const element = document.getElementById('hoja-agenda');
            const headerTabla = document.getElementById('header-tabla');
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

            // 3. Ocultar SOLO el logo HTML (el logo se dibuja por JS en el margen; la clasificación queda en el PDF)
            const logoHeader = document.getElementById('logo-header');
            if (logoHeader) logoHeader.style.display = 'none';
            element.style.paddingTop = '0';
            
            // 4. Intercambiar bloques web-only y print-only
            const webBlocks = document.querySelectorAll('.web-only-block');
            const printBlocks = document.querySelectorAll('.print-only-block');
            webBlocks.forEach(b => b.style.setProperty('display', 'none', 'important'));
            printBlocks.forEach(b => b.style.setProperty('display', 'block', 'important'));
            
            const opt = {
                margin: [30, 0, 20, 0], // Margen superior: 30mm para logo pequeño centrado
                filename: 'Comision_Funcionario_{{ $agenda->id }}.pdf',
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
            
            // Esperar 500ms a que el navegador termine el reflow del diseño a 816px antes de generar el PDF
            setTimeout(() => {
                let promise = html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                    const totalPages = pdf.internal.getNumberOfPages();
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const pageHeight = pdf.internal.pageSize.getHeight();
                    
                    for (let i = 1; i <= totalPages; i++) {
                        pdf.setPage(i);
                        
                        // --- ENCABEZADO: solo logo SENA pequeño centrado en el margen superior ---
                        if (window.logoBase64) {
                            const logoW = 14;
                            const logoH = 14;
                            const logoX = (pageWidth - logoW) / 2;
                            pdf.addImage(window.logoBase64, 'PNG', logoX, 8, logoW, logoH);
                        }
     
                        // --- LÍNEAS DE CIERRE DEL CUADRO EN SALTOS DE PÁGINA ---
                        pdf.setDrawColor(0);
                        pdf.setLineWidth(0.4);

                        // Línea que CIERRA el cuadro al fondo de la hoja (cuando el contenido continúa)
                        if (i < totalPages) {
                            pdf.line(10, pageHeight - 20, pageWidth - 10, pageHeight - 20);
                        }

                        // Línea que ABRE el cuadro al inicio del contenido en hojas 2+
                        if (i > 1) {
                            pdf.line(10, 30, pageWidth - 10, 30);
                        }

                        // --- PIE DE PÁGINA ---
                        pdf.setFontSize(10);
                        pdf.setTextColor(0);
                        pdf.text(i.toString(), pageWidth - 15, pageHeight - 15);
                        pdf.text('GTH-F-087 V.02', pageWidth - 45, pageHeight - 10);
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
                        // Restaurar vista original
                        if (logoHeader) logoHeader.style.display = '';
                        element.style.width = originalWidth;
                        element.style.margin = originalMargin;
                        element.style.padding = originalPadding;
                        element.style.paddingTop = '10mm';
                        
                        // Restaurar bloques web-only y print-only
                        webBlocks.forEach(b => b.style.display = '');
                        printBlocks.forEach(b => b.style.setProperty('display', 'none', 'important'));

                        // Restaurar botón de descarga
                        if (btn) {
                            btn.innerHTML = originalBtnText;
                            btn.disabled = false;
                        }

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
                    });
                }
            }, 500);
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