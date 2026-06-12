<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Agenda CTGI - PDF</title>
  <link rel="icon" href="{{ asset('images/sena/logo-sena-verde.png') }}" type="image/png">
  <style>
    /* Reset y fuentes */
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

    /* Estilos de Tabla */
    table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
    }

    td {
      border: 1.5px solid #000;
      padding: 4px 6px;
      font-size: 11px;
      vertical-align: middle;
      word-wrap: break-word;
    }

    /* Evita que las filas se corten entre páginas */
    tr {
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }

    thead {
      display: table-header-group;
    }

    /* Clases de utilidad */
    .bg-black {
      background-color: #000 !important;
      color: #fff !important;
      text-align: center;
      font-weight: bold;
      text-transform: uppercase;
      padding: 2px !important;
      font-size: 10px;
    }

    .bg-black-header {
      background-color: #000 !important;
      color: #fff !important;
      text-align: center;
      font-weight: bold;
      text-transform: uppercase;
      padding: 10px !important;
      font-size: 10px;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .text-bold {
      font-weight: bold;
    }

    .header-label {
      font-weight: bold;
      font-size: 10px;
    }

    .content-value {
      text-align: center;
      font-size: 11px;
    }

    .checkbox-container {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .checkbox {
      width: 18px;
      height: 18px;
      border: 1.5px solid #000;
      display: inline-block;
      text-align: center;
      line-height: 18px;
      font-weight: bold;
      font-size: 14px;
      margin-left: 10px;
    }

    /* Ajustes específicos para impresión o conversión a PDF */
    @media print {
      body {
        background-color: #fff;
      }
      .hoja {
        margin: 0;
        box-shadow: none;
      }
      #btnPdf {
        display: none !important;
      }
    }

    #btnPdf {
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
    }
  </style>
</head>

<body>
  @php
    $cNombre = $agenda->clasificacion->nombre ?? '';
    
    // Logo logic - Búsqueda robusta para cPanel/Producción
    $logoBase64 = '';
    $posiblesRutas = [
        public_path('images/sena/logoSena.png'),
        base_path('../public_html/images/sena/logoSena.png'),
        $_SERVER['DOCUMENT_ROOT'] . '/images/sena/logoSena.png'
    ];

    foreach ($posiblesRutas as $ruta) {
        if (file_exists($ruta)) {
            $logoData = file_get_contents($ruta);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
            break;
        }
    }

    // Fallback: Si no se encuentra en disco, usar URL (con precaución por CORS en html2pdf)
    if (empty($logoBase64)) {
        $logoBase64 = asset('images/sena/logoSena.png');
    }

    use Carbon\Carbon;
  @endphp

  <div class="hoja" id="hoja-agenda">
    <!-- ESTRUCTURA PRINCIPAL UNIFICADA (UNA SOLA TABLA PARA EVITAR VACÍOS) -->
    <table>
      <!-- HEADER CON LOGO Y VERSION -->
      <tr>
        <td colspan="8" rowspan="2" style="border-bottom: 1.5px solid black; border-right: none;"></td>
        <td colspan="32" rowspan="2" style="text-align: center; padding: 6px; border-bottom: 1.5px solid black; border-left: none; border-right: none;">
          <img src="{{ $logoBase64 ?: asset('images/sena/logoSena.png') }}" alt="SENA" style="width: 65px;">
        </td>
        <td colspan="8" class="text-center text-bold" style="height: 28px; border-bottom: 1.5px solid black; font-size: 10px; border-left: 1.5px solid black;">
          Versión: 01
        </td>
      </tr>
      <tr>
        <td colspan="8" class="text-center text-bold" style="height: 28px; border-bottom: 1.5px solid black; font-size: 10px; border-left: 1.5px solid black;">
          Código: <br> GCCON-F-095
        </td>
      </tr>

      <!-- BARRAS DE TITULO -->
      <tr>
        <td colspan="48" class="bg-black-header">PROCESO</td>
      </tr>
      <tr>
        <td colspan="48" class="text-center text-bold" style="padding: 8px; font-size: 10px;">GESTIÓN CONTRACTUAL</td>
      </tr>
      <tr>
        <td colspan="48" class="bg-black-header">NOMBRE DEL FORMATO</td>
      </tr>
      <tr>
        <td colspan="48" class="text-center text-bold" style="padding: 8px; font-size: 10px;">FORMATO AGENDA DESPLAZAMIENTO CONTRATISTA</td>
      </tr>
      <tr>
        <td colspan="48" class="bg-black-header">CLASIFICACIÓN DE LA INFORMACIÓN</td>
      </tr>

      <!-- CLASIFICACION -->
      <tr>
        <td colspan="16" style="padding: 6px; border-right: none;">
          <div class="checkbox-container text-bold">
            Pública <div class="checkbox">{{ trim(strtoupper($cNombre)) == 'PÚBLICA' ? 'X' : '' }}</div>
          </div>
        </td>
        <td colspan="16" style="padding: 6px; border-left: none; border-right: none;">
          <div class="checkbox-container text-bold">
            Pública Clasificada 
            <div class="checkbox">
              @php 
                $strnum = trim(strtoupper($cNombre)); 
                $isInterna = ($strnum == 'INTERNA' || $strnum == 'PÚBLICA CLASIFICADA'); 
                echo $isInterna ? 'X' : ''; 
              @endphp
            </div>
          </div>
        </td>
        <td colspan="16" style="padding: 6px; border-left: none;">
          <div class="checkbox-container text-bold">
            Pública Reservada <div class="checkbox">{{ trim(strtoupper($cNombre)) == 'CONFIDENCIAL' ? 'X' : '' }}</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="48" class="text-center text-bold" style="background-color: #ffffffff;"></td>
      </tr>

      <!-- SECCION: DATOS DEL CONTRATISTA -->
      <tr>
        <td colspan="48" class="text-center text-bold" style="background-color: #ffffffff;">DATOS DEL CONTRATISTA QUE SE DESPLAZA</td>
      </tr>
      <tr>
        <td colspan="24" class="text-bold">FECHA DE ELABORACIÓN DE AGENDA</td>
        <td colspan="24" class="content-value">{{ $agenda->fecha_elaboracion ? date('j/m/Y', strtotime($agenda->fecha_elaboracion)) : '' }}</td>
      </tr>
      <tr>
        <td colspan="24" class="text-center text-bold" style="background-color: #fff;">NOMBRES Y APELLIDOS</td>
        <td colspan="24" class="text-center text-bold" style="background-color: #fff;">IDENTIFICACIÓN:</td>
      </tr>
      <tr>
        <td colspan="24" class="content-value" style="height: 35px; vertical-align: middle;">{{ $agenda->user->name ?? '' }}</td>
        <td colspan="10" class="text-bold" style="text-align: center;">Tipo:</td>
        <td colspan="4" class="content-value">{{ $agenda->user->tipo_documento ?? 'C.C.' }}</td>
        <td colspan="4" class="text-bold text-center">No.</td>
        <td colspan="6" class="content-value" style="font-size: 13px;">{{ $agenda->user->numero_documento ?? '' }}</td>
      </tr>
      
      <!-- FILA CONTRATO -->
      <tr>
        <td colspan="11" class="text-bold" style="text-align: left;">CONTRATO</td>
        <td colspan="3" class="text-bold text-center">No.</td>
        <td colspan="3" class="content-value">{{ last(explode('.', $agenda->user->numero_contrato ?? '')) }}</td>
        <td colspan="3" class="text-bold text-center">AÑO</td>
        <td colspan="4" class="content-value">{{ $agenda->user->anio_contrato ?? '' }}</td>
        
        <td colspan="10" class="text-bold" style="font-size: 8px; line-height: 1; padding-left: 5px; text-align: left; text-transform: uppercase;">FECHA VENCIMIENTO<br>DEL CONTRATO</td>
        @php
          $venc = $agenda->user->fecha_vencimiento ? Carbon::parse($agenda->user->fecha_vencimiento) : null;
        @endphp
        <td colspan="4" class="content-value">{{ $venc ? $venc->day : '' }}</td>
        <td colspan="4" class="content-value">{{ $venc ? $venc->month : '' }}</td>
        <td colspan="6" class="content-value">{{ $venc ? $venc->year : '' }}</td>
      </tr>

      <tr>
        <td colspan="11" class="text-bold">OBJETO CONTRACTUAL:</td>
        <td colspan="37" class="content-value" style="text-align: left; padding: 6px; font-size: 10px;">{{ $agenda->user->objeto_contractual ?? '' }}</td>
      </tr>

      <tr>
        <td colspan="11" class="text-bold">DIRECCIÓN GENERAL/ REGIONAL</td>
        <td colspan="13" class="content-value">ANTIOQUIA</td>
        <td colspan="8" class="text-bold">DEPENDENCIA/ CENTRO</td>
        <td colspan="16" class="content-value">CENTRO TEXTIL Y DE GESTION INDUSTRIAL</td>
      </tr>

      <tr>
        <td colspan="11" class="text-bold" style="font-size: 10px;">NOMBRE DEL ORDENADOR DEL GASTO (de la Movilización)</td>
        <td colspan="13" class="content-value">
          {{ $agenda->ordenador->nombre ?? ($agenda->user->ordenador->nombre ?? '') }}
        </td>
        <td colspan="8" class="text-bold">CARGO</td>
        <td colspan="16" class="content-value">
          {{ $agenda->ordenador->cargo ?? ($agenda->user->ordenador->cargo ?? 'ORDENADOR DEL GASTO') }}
        </td>
      </tr>

      <tr>
        <td colspan="11" class="text-bold" style="font-size: 10px;">NOMBRE DEL SUPERVISOR(A) DEL CONTRATO</td>
        <td colspan="13" class="content-value">
          {{ $agenda->supervisor->nombre ?? ($agenda->user->supervisor->nombre ?? '') }}
        </td>
        <td colspan="8" class="text-bold">CARGO</td>
        <td colspan="16" class="content-value">
          {{ $agenda->supervisor->cargo ?? ($agenda->user->supervisor->cargo ?? 'SUPERVISOR(A) DEL CONTRATO') }}
        </td>
      </tr>

      <!-- SECCION: INFORMACION DEL DESPLAZAMIENTO -->
      <tr>
        <td colspan="48" class="bg-black">INFORMACIÓN DEL DESPLAZAMIENTO</td>
      </tr>
      <tr>
        <td colspan="11" class="text-bold" style="text-align: left;">RUTA</td>
        <td colspan="37" class="content-value" style="text-align: center;">{{ strtoupper($agenda->ruta) }}</td>
      </tr>
      <tr>
        <td colspan="11" class="text-bold" style="text-align: left;">DIRECCIÓN GENERAL/ REGIONAL</td>
        <td colspan="13" class="content-value" style="text-align: center;">{{ strtoupper($agenda->regional ?? 'ANTIOQUIA') }}</td>
        <td colspan="8" class="text-bold" style="text-align: left; font-size: 10px;">DEPENDENCIA/ CENTRO</td>
        <td colspan="16" class="content-value" style="text-align: center;">{{ strtoupper($agenda->centro ?? 'CENTRO TEXTIL Y DE GESTION INDUSTRIAL') }}</td>
      </tr>

      <tr>
        <td colspan="11" class="text-bold" style="font-size: 11px; line-height: 1.1; text-align: left; vertical-align: middle; padding: 5px; height: 60px;">CIUDAD/DEPARTAMENTO O<br>MUNICIPIO/DEPARTAMENTO O<br>CIUDAD/PAIS</td>
        <td colspan="9" class="content-value" style="text-align: center; vertical-align: middle; font-size: 12px; padding: 5px;">
          @php
            $destinosArr = [];
            if($agenda->destinos) {
              $destinosArr = array_unique(array_filter(array_map(fn($d) => ($d['nombre'] ?? ''), $agenda->destinos)));
            }
            $destTexto = !empty($destinosArr) ? implode(', ', $destinosArr) : ($agenda->ciudad_destino ?: '');
          @endphp
          {{ strtoupper($destTexto) }}
        </td>
        <td colspan="4" class="text-bold" style="font-size: 10px; text-align: left; vertical-align: middle; padding: 5px;">ENTIDAD O EMPRESA:</td>
        <td colspan="13" class="content-value" style="text-align: center; vertical-align: middle; font-size: 12px; padding: 5px;">{{ strtoupper($agenda->entidad_empresa) }}</td>
        <td colspan="4" class="text-bold" style="text-align: left; vertical-align: middle; padding: 2px; font-size: 11px;">CONTACTO</td>
        <td colspan="7" class="content-value" style="font-size: 11px; text-align: center; vertical-align: middle; padding: 5px;">{{ strtoupper($agenda->contacto) }}</td>
      </tr>
      
      @php
        $inicio = Carbon::parse($agenda->fecha_inicio);
        $fin = Carbon::parse($agenda->fecha_fin);
      @endphp
      <tr>
        <td colspan="11" class="font-size: 11px; line-height: 1.1; text-align: left; vertical-align: middle; padding: 5px; height: 60px;">FECHA INICIO DEL<br>DESPLAZAMIENTO</td>
        <td colspan="3" class="content-value" style="text-align: center; vertical-align: middle;">{{ $inicio->day }}</td>
        <td colspan="3" class="content-value" style="text-align: center; vertical-align: middle;">{{ $inicio->month }}</td>
        <td colspan="3" class="content-value" style="text-align: center; vertical-align: middle;">{{ $inicio->year }}</td>
        <td colspan="13" class="font-size: 11px; line-height: 1.1; text-align: left; vertical-align: middle; padding: 5px; height: 60px;">FECHA FIN DESPLAZAMIENTO</td>
        <td colspan="4" class="content-value" style="text-align: center; vertical-align: middle; font-size: 10px;">{{ $fin->day }}</td>
        <td colspan="4" class="content-value" style="text-align: center; vertical-align: middle; font-size: 10px;">{{ $fin->month }}</td>
        <td colspan="7" class="content-value" style="text-align: center; vertical-align: middle; font-size: 10px;">{{ $fin->year }}</td>
      </tr>

      <tr>
        <td colspan="11" class="text-bold" style="font-size: 11px; line-height: 1.1; text-align: left; vertical-align: middle; padding: 4px 5px; height: 28px;">OBJETIVO DEL DESPLAZAMIENTO</td>
        <td colspan="37" class="content-value" style="text-align: center; vertical-align: middle; padding: 4px 5px; font-size: 11px;">{{ strtoupper($agenda->objetivo_desplazamiento) }}</td>
      </tr>

      <!-- OBLIGACIONES -->
      <tr>
        <td colspan="48" class="text-bold text-center" style="background-color: #fff; color: #000; padding: 5px; font-size: 10px; border-top: 1px solid black; border-bottom: 1px solid black; text-transform: uppercase;">OBLIGACIONES DEL CONTRATO</td>
      </tr>
      @if($agenda->obligaciones->count() > 0)
        @foreach ($agenda->obligaciones as $index => $obligacion)
          <tr>
            <td colspan="3" class="text-bold" style="text-align: center; vertical-align: middle; padding: 4px;">{{ $index + 1 }}</td>
            <td colspan="45" style="font-size: 10px; text-align: left; vertical-align: middle; padding: 4px; line-height: 1.1;">{{ $obligacion->nombre }}</td>
          </tr>
        @endforeach
      @endif

      <!-- AGENDA BARRA -->
      <tr>
        <td colspan="48" class="bg-black">AGENDA</td>
      </tr>
      <tr>
        <td colspan="48" class="text-bold text-center" style="font-size: 10px;">ACTIVIDADES (Deberá contener información detallada de las tareas a realizar día a día)</td>
      </tr>

      @php
        $returnRoute = '';
        $returnTransport = [];
        foreach($agenda->actividades as $act) {
            if(!empty($act->ruta_regreso)) $returnRoute = $act->ruta_regreso;
            if(!empty($act->transporte_regreso)) {
                $returnTransport = is_array($act->transporte_regreso) ? $act->transporte_regreso : [$act->transporte_regreso];
            }
        }
      @endphp

      @foreach ($agenda->actividades->sortBy('fecha') as $index => $actividad)
        @php
          $fechaAct = Carbon::parse($actividad->fecha);
          $label = ($loop->first) ? 'Dia Inicio' : (($loop->last) ? 'Dia Fin' : 'Dia ' . ($index + 1));
          $dailyActs = is_array($actividad->actividad) ? array_filter($actividad->actividad, fn($i) => !empty(trim($i['actividad'] ?? ''))) : [['hora' => 'AM/PM', 'actividad' => $actividad->actividad]];
          if(empty($dailyActs)) $dailyActs = [['hora' => 'AM/PM', 'actividad' => '']];
          $rowspan = count($dailyActs);
        @endphp
        
        <!-- ENCABEZADO DIA -->
        <tr>
          <td colspan="6" class="text-bold" style="background-color: black; color: white; text-align: center; font-size: 10px; height: 18px; vertical-align: middle;">{{ $label }}</td>
          <td colspan="6" class="content-value text-bold" style="text-align: center; vertical-align: middle;">{{ $fechaAct->day }}</td>
          <td colspan="6" class="content-value text-bold" style="text-align: center; vertical-align: middle;">{{ $fechaAct->month }}</td>
          <td colspan="6" class="content-value text-bold" style="text-align: center; vertical-align: middle;">{{ $fechaAct->year }}</td>
          <td colspan="24" style="background-color: #fff;"></td>
        </tr>
        
        @if($loop->first)
        <tr>
          <td colspan="18" class="text-bold" style="text-align: left; padding-left: 5px; font-size: 10px;">Desplazamiento ruta de ida:</td>
          <td colspan="30" class="content-value" style="text-align: center;">{{ strtoupper($actividad->ruta_ida ?: $agenda->ruta) }}</td>
        </tr>
        <tr>
          <td colspan="18" class="text-bold" style="text-align: left; padding-left: 5px; font-size: 10px;">Medio de transporte: aéreo, terrestre, fluvial:</td>
          <td colspan="30" class="content-value" style="text-align: center;">
            {{ strtoupper(is_array($actividad->transporte_ida) ? implode(', ', $actividad->transporte_ida) : ($actividad->transporte_ida ?: 'TERRESTRE')) }}
          </td>
        </tr>
        @endif

        <tr>
          <td colspan="48" class="text-bold text-center" style="font-size: 10px; padding: 2px;">Actividades a ejecutar:</td>
        </tr>

        @foreach($dailyActs as $actIdx => $item)
        <tr>
          @if($actIdx === 0)
          <td colspan="4" rowspan="{{ $rowspan }}" class="content-value text-bold" style="text-align: center; vertical-align: middle; font-size: 11px; border: 1px solid black;">
            {{ $fechaAct->day }}
          </td>
          @endif
          <td colspan="6" class="text-bold" style="text-align: left; padding-left: 5px; font-size: 9px; vertical-align: middle; border: 1px solid black;">HORA: {{ $item['hora'] ?: 'AM/PM' }}</td>
          <td colspan="38" style="font-size: 10px; text-align: left; padding: 5px; vertical-align: middle; border: 1px solid black; line-height: 1.1;">{{ $item['actividad'] }}</td>
        </tr>
        @endforeach

        @if($loop->last)
        <tr>
          <td colspan="18" class="text-bold" style="text-align: left; padding-left: 5px; font-size: 10px;">Desplazamiento ruta de regreso:</td>
          <td colspan="30" class="content-value" style="text-align: center;">{{ strtoupper($returnRoute ?: $agenda->ruta) }}</td>
        </tr>
        <tr>
          <td colspan="18" class="text-bold" style="text-align: left; padding-left: 5px; font-size: 10px;">Medio de transporte: aéreo, terrestre, fluvial:</td>
          <td colspan="30" class="content-value" style="text-align: center;">
            {{ strtoupper(!empty($returnTransport) ? implode(', ', $returnTransport) : 'TERRESTRE') }}
          </td>
        </tr>
        @endif
      @endforeach

      <!-- OBSERVACIONES -->
      <tr>
        <td colspan="48" class="bg-black">Observaciones:</td>
      </tr>
      @php
        $vAereo = $agenda->actividades->sum('valor_aereo');
        $vTerrestre = $agenda->actividades->sum('valor_terrestre');
        $vInter = $agenda->actividades->sum('valor_intermunicipal');
      @endphp
      <tr>
        <td colspan="48" class="text-bold" style="font-size: 10px; text-align: left; padding: 4px 5px; height: 18px; border: 1px solid black;">
          Se liquidan gastos de transporte entre terminales aéreas por valor de &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="text-bold">$ {{ number_format($vAereo, 0, ',', '.') }}</span>
        </td>
      </tr>
      <tr>
        <td colspan="48" class="text-bold" style="font-size: 10px; text-align: left; padding: 4px 5px; height: 18px; border: 1px solid black;">
          Se liquidan gastos de transporte entre terminales terrestre por valor de &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="text-bold">$ {{ number_format($vTerrestre, 0, ',', '.') }}</span>
        </td>
      </tr>
      <tr>
        <td colspan="48" class="text-bold" style="font-size: 10px; text-align: left; padding: 4px 5px; height: 18px; border: 1px solid black;">
          Se liquidan gastos de transporte intermunicipal por valor de &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="text-bold">$ {{ number_format($vInter, 0, ',', '.') }}</span>
        </td>
      </tr>

      <!-- FIRMAS SECTION -->
      @php
        $estado = $agenda->estado->nombre ?? '';
        $pathC = $agenda->firma_contratista_path ?? ($agenda->user->firma ?? null);
        
        // Lógica de Supervisor: Si la agenda no tiene, buscar el del usuario
        $supervisorObj = $agenda->supervisor ?? $agenda->user->supervisor;
        $pathS = $agenda->firma_supervisor_path ?? ($supervisorObj->firma ?? null);
        
        // Lógica de Ordenador: Si la agenda no tiene, buscar el del usuario
        $ordenadorObj = $agenda->ordenador ?? $agenda->user->ordenador;
        $pathO = $agenda->firma_ordenador_path ?? ($ordenadorObj->firma ?? null);

        $isSupervisorAprobado = in_array($estado, ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA']);
        $isOrdenadorAprobado = ($estado === 'APROBADA');

        $fC = $getFirmaBase64($pathC);
        $fS = $isSupervisorAprobado ? $getFirmaBase64($pathS) : '';
        $fO = $isOrdenadorAprobado ? $getFirmaBase64($pathO) : '';
      @endphp

      <tr>
        <td colspan="16" class="text-bold" style="font-size: 9px; vertical-align: top; text-align: left; height: 65px; border: 1px solid black; padding: 2px;">
          FIRMA ORDENADOR DE GASTO:
          <div style="text-align: center; margin-top: 5px;">
             @if($fO) <img src="{{ $fO }}" style="max-height: 45px; max-width: 100%;"> @endif
          </div>
        </td>
        <td colspan="16" class="text-bold" style="font-size: 9px; vertical-align: top; text-align: left; border: 1px solid black; padding: 2px;">
          FIRMA SUPERVISOR DEL CONTRATO :
          <div style="text-align: center; margin-top: 5px;">
             @if($fS) <img src="{{ $fS }}" style="max-height: 45px; max-width: 100%;"> @endif
          </div>
        </td>
        <td colspan="16" rowspan="2" style="height: 60px; text-align: center; vertical-align: middle; border: 1px solid black; padding: 2px;">
          @if($fC) <img src="{{ $fC }}" style="max-height: 60px; max-width: 100%;"> @endif
        </td>
      </tr>
      <tr>
        <td colspan="16" style="font-size: 9px; text-align: left; border: 1.5px solid black; padding: 2px; height: 32px; vertical-align: top;">
          <div class="text-bold">Nombres y Apellidos:</div>
          <div style="margin-top: 1px;">
            {{ strtoupper($ordenadorObj->nombre ?? 'NO ASIGNADO') }}
          </div>
        </td>
        <td colspan="16" style="font-size: 9px; text-align: left; border: 1.5px solid black; padding: 2px; vertical-align: top;">
          <div class="text-bold">Nombres y Apellidos:</div>
          <div style="margin-top: 1px;">
            {{ strtoupper($supervisorObj->nombre ?? 'NO ASIGNADO') }}
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="16" style="font-size: 9px; height: 32px; vertical-align: top; text-align: left; border: 1.5px solid black; padding: 2px;">
          <div class="text-bold">Cargo:</div>
          <div style="margin-top: 1px;">{{ strtoupper($ordenadorObj->cargo ?? 'ORDENADOR DEL GASTO') }}</div>
        </td>
        <td colspan="16" style="font-size: 9px; height: 32px; vertical-align: top; text-align: left; border: 1.5px solid black; padding: 2px;">
          <div class="text-bold">Cargo:</div>
          <div style="margin-top: 1px;">{{ strtoupper($supervisorObj->cargo ?? 'SUPERVISOR(A) DEL CONTRATO') }}</div>
        </td>
        <td colspan="16" style="font-size: 9px; height: 32px; vertical-align: top; text-align: left; border: 1px solid black; padding: 2px;">
          <div class="text-bold">Nombres y Apellidos:</div>
          <div style="margin-top: 1px;">{{ strtoupper($agenda->user->name ?? '') }}</div>
        </td>
      </tr>
    </table>
  </div>

  <!-- Botón Generar (Solo vista navegador) -->
  <button id="btnPdf">
    <span>Descargar Formato</span>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
      <polyline points="7 10 12 15 17 10"></polyline>
      <line x1="12" y1="15" x2="12" y2="3"></line>
    </svg>
  </button>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="{{ asset('js/scripts.js') }}"></script>

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
      window.addEventListener('DOMContentLoaded', () => {
          // Asegurar que las firmas e imágenes estén completamente cargadas antes de proceder
          const elemento = document.querySelector('.hoja');
          const images = elemento.querySelectorAll('img');
          const promises = Array.from(images).map(img => {
              if (img.complete) return Promise.resolve();
              return new Promise(resolve => {
                  img.onload = resolve;
                  img.onerror = resolve;
              });
          });

          Promise.all(promises).then(async () => {
              try {
                  // Desplazar al tope para evitar bug de scrollY
                  window.scrollTo(0, 0);

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

                  // Forzar ancho rígido de 816px para emular vista desktop en móviles
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
                  elemento.style.paddingTop = '0';
                  
                  const opciones = {
                      margin: [10, 0, 20, 0],
                      filename: 'Agenda_Desplazamiento_SENA_{{ $agenda->id }}.pdf',
                      image: { type: 'jpeg', quality: 0.80 },
                      html2canvas: { 
                          scale: 1.5, 
                          useCORS: true, 
                          letterRendering: true,
                          scrollY: 0, scrollX: 0, x: 0, y: 0,
                          windowWidth: 816
                      },
                      jsPDF: { unit: 'mm', format: 'letter', orientation: 'portrait' },
                      pagebreak: { mode: ['css', 'legacy'] }
                  };

                  // Esperar 500ms a que el navegador termine el reflow del diseño a 816px antes de generar el PDF
                  setTimeout(async () => {
                      try {
                          const pdf = await html2pdf().set(opciones).from(elemento).toPdf().get('pdf');
                          const totalPages = pdf.internal.getNumberOfPages();
                          const pageWidth = pdf.internal.pageSize.getWidth();
                          const pageHeight = pdf.internal.pageSize.getHeight();
                          
                          for (let i = 1; i <= totalPages; i++) {
                              pdf.setPage(i);
                              
                              pdf.setDrawColor(0);
                              pdf.setLineWidth(0.4);

                              if (i > 1) {
                                  pdf.line(10, 10, pageWidth - 10, 10);
                              }
                              if (i < totalPages) {
                                  pdf.line(10, pageHeight - 20, pageWidth - 10, pageHeight - 20);
                              }

                              pdf.setFontSize(10);
                              pdf.setTextColor(100);
                              pdf.text('Página ' + i + ' de ' + totalPages, pageWidth - 25, pageHeight - 15);
                              pdf.text('GCCON-F-095', pageWidth - 45, pageHeight - 10);
                          }
                          
                          const pdfBlob = await pdf.output('blob');
                          
                          const formData = new FormData();
                          formData.append('pdf', pdfBlob, 'agenda_{{ $agenda->id }}.pdf');
                          formData.append('_token', '{{ csrf_token() }}');
                          
                          const response = await fetch('{{ route('agenda.save-pdf', $agenda->id) }}', {
                              method: 'POST',
                              body: formData
                          });
                          
                          const resData = await response.json();
                          if (resData.success) {
                              styleTag.remove();
                              if (viewport) {
                                  if (window.pdfCreatedViewport) {
                                      viewport.remove();
                                  } else if (window.pdfOriginalViewport) {
                                      viewport.setAttribute('content', window.pdfOriginalViewport);
                                  }
                              }
                              window.location.reload();
                          } else {
                              console.error('Error al guardar PDF:', resData);
                              document.getElementById('loading-overlay').innerHTML = '<p style="color:red;font-weight:bold;margin-top:20px;">Error al guardar el PDF. Por favor recargue.</p>';
                          }
                      } catch (err) {
                          console.error(err);
                          document.getElementById('loading-overlay').innerHTML = '<p style="color:red;font-weight:bold;margin-top:20px;">Error: ' + err.message + '</p>';
                      }
                  }, 500);
                } catch (outerErr) {
                    console.error("Error during PDF initialization:", outerErr);
                    document.getElementById('loading-overlay').innerHTML = '<p style="color:red;font-weight:bold;margin-top:20px;">Error de inicialización: ' + outerErr.message + '</p>';
                }
          });
      });
  </script>
  @endif
</body>

</html>