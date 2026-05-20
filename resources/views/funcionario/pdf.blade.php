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
            background-color: #f2f2f2 !important;
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
            background-color: #f2f2f2;
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
</head>

<body>
    @php
        // Logo logic - Búsqueda robusta para cPanel/Producción
        $logoBase64 = '';
        $posiblesRutas = [
            public_path('images/sena/agenda_labores.png'),
            base_path('../public_html/images/sena/agenda_labores.png'),
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
            $logoBase64 = asset('images/sena/agenda_labores.png');
        }
    @endphp

    <script>
        window.logoBase64 = "{{ $logoBase64 }}";
    </script>

    <button class="no-print" onclick="descargarPDF()">
        <span>Descargar Formato</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
    </button>

    <div class="hoja" id="hoja-agenda">
        <!-- TABLA 1: ENCABEZADO (Logo y Título) - Se mantiene para vista web, pero se maneja en JS para el PDF -->
        <table id="header-tabla">
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
            </tr>
            <tr>
                <td colspan="10" class="text-center"
                    style="height: 100px; padding: 12px; vertical-align: middle; border: 1.5px solid #000;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="SENA Logo"
                            style="max-height: 70px; max-width: 100%; display: block; margin: 0 auto; object-fit: contain;">
                    @else
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Sena_Colombia_logo.svg/1200px-Sena_Colombia_logo.svg.png"
                            style="max-height: 70px;">
                    @endif
                </td>
                <td colspan="38" class="text-center text-bold"
                    style="font-size: 18px; padding: 10px; line-height: 1.2; color: #000; vertical-align: middle; border: 1.5px solid #000;">
                    AGENDA DE LABORES PARA COMISIÓN SERVIDORES<br>PÚBLICOS
                </td>
            </tr>
        </table>

        <!-- TABLA 2: CUERPO (Datos, Lugar, Actividades, Firmas) -->
        <table>
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
            </tr>

            <!-- DATOS DE LA AGENDA SECTION -->
            <tr>
                <td colspan="48" class="bg-gray" style="padding: 12px 10px !important;">DATOS DE LA AGENDA DE COMISIÓN
                </td>
            </tr>
            <tr>
                <td colspan="18" class="label-cell">
                    FECHA DE ELABORACIÓN DE AGENDA
                    <span class="label-subtext">Según la Resolución 2838/2016 art.14</span>
                </td>
                <td colspan="30" class="value-cell">
                    {{ \Carbon\Carbon::parse($agenda->fecha_elaboracion)->translatedFormat('d \d\e F \d\e Y') }}</td>
            </tr>
            <tr>
                <td colspan="18" class="label-cell">NOMBRES Y APELLIDOS DEL <br>COMISIONADO</td>
                <td colspan="30" class="value-cell text-uppercase">{{ $agenda->user->name }}</td>
            </tr>
            <tr>
                <td colspan="13" class="label-cell" style="white-space: nowrap; font-size: 11px;">FECHA INICIO DE
                    COMISIÓN</td>
                <td colspan="11" class="value-cell text-center">
                    {{ \Carbon\Carbon::parse($agenda->fecha_inicio)->format('d/m/Y') }}</td>
                <td colspan="13" class="label-cell" style="white-space: nowrap; font-size: 11px;">FECHA FIN DE LA
                    COMISIÓN</td>
                <td colspan="11" class="value-cell text-center">
                    {{ \Carbon\Carbon::parse($agenda->fecha_fin)->format('d/m/Y') }}</td>
            </tr>

            <!-- LUGAR DE LA COMISIÓN SECTION -->
            <tr>
                <td colspan="48" class="bg-gray" style="padding: 2px 10px !important;">LUGAR DE LA COMISIÓN</td>
            </tr>
            <tr>
                <td colspan="9" class="label-cell text-center" style="font-size: 13px;">CIUDAD O<br> MUNICIPIO</td>
                <td colspan="11" class="label-cell text-center" style="font-size: 13px;">DIRECCIÓN GENERAL <br>/
                    REGIONAL</td>
                <td colspan="28" class="label-cell text-center" style="font-size: 13px;">DEPENDENCIA /CENTRO DE<br>
                    FORMACIÓN/SEDE/INSTITUCIÓN A
                    VISITAR</td>
            </tr>
            <tr>
                <td colspan="9" class="value-cell text-center"
                    style="padding: 10px; font-weight: normal; text-transform: uppercase;">
                    @php
                        $destinosArray = is_array($agenda->destinos) ? $agenda->destinos : json_decode($agenda->destinos, true);
                        $municipios = array_column($destinosArray ?? [], 'nombre');
                        echo implode(', ', $municipios);
                    @endphp
                </td>
                <td colspan="11" class="value-cell text-center" style="font-weight: normal; text-transform: uppercase;">
                    {{ $agenda->regional }}</td>
                <td colspan="28" class="value-cell text-center" style="font-weight: normal; text-transform: uppercase;">
                    {{ $agenda->centro }}</td>
            </tr>

            <!-- OBJETO SECTION -->
            <tr>
                <td colspan="48" class="value-cell"
                    style="padding: 10px; font-weight: normal; text-transform: uppercase;">
                    <span style="font-weight: bold; font-size: 14px;">OBJETO DE LA COMISIÓN:</span>
                    {{ $agenda->objetivo_desplazamiento }}
                </td>
            </tr>

            <!-- ACTIVIDADES SECTION -->
            <tr>
                <td colspan="48" class="bg-gray" style="padding: 2px 10px 2px 10px !important; background-color: #f2f2f2 !important; color: #000 !important; border-bottom: 2px solid #000;">
                    ACTIVIDADES PARA DESARROLLAR DURANTE LA COMISIÓN RESOLUCIÓN 2838/2016 Art.17:
                    <div style="text-transform: none; font-weight: bold; font-size: 12px; margin-top: 5px;">
                        (Deberá contener información detallada de las tareas a realizar día a día)
                    </div>
                </td>
            </tr>
            @forelse($agenda->actividades as $index => $act)
                <tbody>
                    <!-- ENCABEZADO DÍA -->
                    <tr>
                        <td colspan="48" style="padding: 15px 20px 5px 20px; border-top: none; border-bottom: none;">
                            <div style="font-weight: bold; font-size: 14px; text-decoration: underline;">
                                Día {{ $index + 1 }}: {{ \Carbon\Carbon::parse($act->fecha)->format('d/m/Y') }}
                            </div>
                        </td>
                    </tr>

                    @if($act->ruta_ida || (count(is_array($act->transporte_ida) ? $act->transporte_ida : []) > 0))
                    <tr>
                        <td colspan="48" style="padding: 0 20px 5px 20px; border-top: none; border-bottom: none;">
                            @if($act->ruta_ida)
                            <div style="margin-bottom: 4px; font-size: 13px;">
                                <span style="font-weight: bold;">Desplazamientos ruta de ida:</span> {{ $act->ruta_ida }}
                            </div>
                            @endif
                            
                            @if(count(is_array($act->transporte_ida) ? $act->transporte_ida : []) > 0)
                            <div style="margin-bottom: 4px; font-size: 13px;">
                                <span style="font-weight: bold;">Medio de transporte:</span> 
                                @php 
                                    $medios = is_array($act->transporte_ida) ? $act->transporte_ida : [];
                                    echo implode(', ', array_map('ucfirst', $medios));
                                @endphp
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td colspan="48" style="padding: 5px 20px 0px 20px; border-top: none; border-bottom: none;">
                            <div style="font-weight: bold; font-size: 13px;">
                                Actividades a ejecutar:
                            </div>
                        </td>
                    </tr>
                    
                    @php $items = is_array($act->actividad) ? $act->actividad : []; @endphp
                    @foreach($items as $item)
                    <tr>
                        <td colspan="48" style="padding: 2px 20px; border-top: none; border-bottom: none;">
                            <div style="display: flex; line-height: 1.3; width: 100%;">
                                <div style="width: 70px; font-weight: bold; font-size: 13px; flex-shrink: 0;">
                                    {{ $item['hora'] ?? '' }}
                                </div>
                                <div style="font-size: 13px; flex-grow: 1;">
                                    - {{ $item['actividad'] ?? '' }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    @php 
                        $esUltimoDia = $loop->last; 
                    @endphp

                    <tr>
                        <td colspan="48" style="padding: 5px 20px 15px 20px; border-top: none; {{ $esUltimoDia ? 'border-bottom: 1.5px solid #000;' : 'border-bottom: none;' }}">
                            @if($act->ruta_regreso)
                            <div style="margin-top: 10px; padding-top: 0;">
                                <div style="font-size: 13px;">
                                    <span style="font-weight: bold;">Desplazamiento ruta de regreso:</span> {{ $act->ruta_regreso }}
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

            <!-- FIRMAS SECTION -->
            <tbody class="no-break">
                <tr>
                    <td colspan="24" class="signature-header">COMISIONADO</td>
                    <td colspan="24" class="signature-header">AUTORIZA LA COMISIÓN</td>
                </tr>
                <tr>
                    <td colspan="24"
                        style="min-height: 100px; vertical-align: bottom; padding: 10px; position: relative;">
                        @php
                            $pathFirmaComisionado = $agenda->firma_contratista_path ?? $agenda->user->firma;
                            $fBase64 = $getFirmaBase64($pathFirmaComisionado);
                        @endphp
                        @if($fBase64)
                            <div style="min-height: 70px; display: flex; align-items: center; justify-content: center; margin-bottom: 5px; padding: 2px;">
                                <img src="{{ $fBase64 }}" style="max-height: 90px; max-width: 280px; width: auto; height: auto;">
                            </div>
                        @endif
                        <div style="font-size: 13px; margin-bottom: 2px; font-weight: bold;">Firma</div>
                        <div style="font-size: 14px; font-weight: bold; text-transform: uppercase;">{{ $agenda->user->name }}</div>
                        <div style="font-size: 12px; font-weight: bold; text-transform: uppercase;">
                            {{ $agenda->user->cargo ?? ($agenda->user->role == 'funcionario' ? 'SERVIDOR PÚBLICO' : 'CONTRATISTA') }}
                        </div>
                    </td>
                    <td colspan="24"
                        style="min-height: 100px; vertical-align: bottom; padding: 10px; position: relative;">
                        @php
                            $oBase64 = '';
                            $isAprobada = ($agenda->estado->nombre === 'APROBADA');
                            
                            // Buscar ordenador: prioridad agenda, luego usuario asignado
                            $ordenadorObj = $agenda->ordenador ?? $agenda->user->ordenador;
                            
                            if ($isAprobada && $agenda->firma_ordenador_path) {
                                $oBase64 = $getFirmaBase64($agenda->firma_ordenador_path);
                            }
                        @endphp
                        @if($oBase64)
                            <div style="min-height: 70px; display: flex; align-items: center; justify-content: center; margin-bottom: 5px; padding: 2px;">
                                <img src="{{ $oBase64 }}" style="max-height: 90px; max-width: 280px; width: auto; height: auto;">
                            </div>
                        @endif
                        <div style="font-size: 13px; margin-bottom: 2px; font-weight: bold;">Firma</div>
                        <div style="font-size: 14px; font-weight: bold; text-transform: uppercase;">
                            {{ strtoupper($ordenadorObj->nombre ?? 'PENDIENTE ASIGNACIÓN') }}
                        </div>
                        <div style="font-size: 12px; font-weight: bold; text-transform: uppercase;">
                            {{ strtoupper($ordenadorObj->cargo ?? 'ORDENADOR DEL GASTO') }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        
    </div>
    <script>
        function descargarPDF() {
            const element = document.getElementById('hoja-agenda');
            const headerTabla = document.getElementById('header-tabla');
            
            // 1. Ocultar el encabezado HTML para que no se duplique en la primera página
            // ya que lo dibujaremos manualmente en todas las páginas para mantener consistencia
            if (headerTabla) headerTabla.style.display = 'none';
            element.style.paddingTop = '0';
            
            const opt = {
                margin: [40, 0, 20, 0], // Margen superior amplio (40mm) para el encabezado recurrente
                filename: 'Comision_Funcionario_{{ $agenda->id }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2, 
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
            
            html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                const totalPages = pdf.internal.getNumberOfPages();
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                
                for (let i = 1; i <= totalPages; i++) {
                    pdf.setPage(i);
                    
                    // --- DIBUJAR ENCABEZADO INSTITUCIONAL ---
                    const marginX = 10;
                    const marginY = 10;
                    const headerWidth = pageWidth - 20;
                    const headerHeight = 25;
                    const separatorX = marginX + (headerWidth * (10/48)); // Proporción 10/48 como en el HTML

                    // 1. Recuadro principal
                    pdf.setDrawColor(0);
                    pdf.setLineWidth(0.4);
                    pdf.rect(marginX, marginY, headerWidth, headerHeight);

                    // 2. Línea divisoria vertical
                    pdf.line(separatorX, marginY, separatorX, marginY + headerHeight);

                    // 3. Logo (SIGA/SENA)
                    if (window.logoBase64) {
                        pdf.addImage(window.logoBase64, 'PNG', marginX + 2, marginY + 2, (separatorX - marginX) - 4, headerHeight - 4);
                    }

                    // 4. Texto del Título
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(14);
                    const textX = separatorX + (headerWidth - (separatorX - marginX)) / 2;
                    pdf.text("AGENDA DE LABORES PARA COMISIÓN SERVIDORES", textX, marginY + 10, { align: "center" });
                    pdf.text("PÚBLICOS", textX, marginY + 17, { align: "center" });

                    // --- PIE DE PÁGINA Y CIERRES ---
                    pdf.setDrawColor(0);
                    pdf.setLineWidth(0.4);

                    // Línea superior de cierre del recuadro de contenido (Solo a partir de la hoja 2)
                    // En la hoja 1 no es necesaria porque el HTML ya trae su propio borde superior en la sección de DATOS
                    if (i > 1) {
                        pdf.line(10, 40, pageWidth - 10, 40);
                    }

                    // Línea inferior de cierre (Solo si la tabla continúa en otra hoja)
                    if (i < totalPages) {
                        pdf.line(10, pageHeight - 20, pageWidth - 10, pageHeight - 20);
                    }

                    // Pie de página institucional
                    pdf.setFontSize(10);
                    pdf.setTextColor(0);
                    pdf.text(i.toString(), pageWidth - 15, pageHeight - 15);
                    pdf.text('GTH-F-188 V01', pageWidth - 45, pageHeight - 10);
                }
            }).save().then(() => {
                // Restaurar vista original
                if (headerTabla) headerTabla.style.display = 'table';
                element.style.paddingTop = '10mm';
            });
        }
    </script>
</body>

</html>