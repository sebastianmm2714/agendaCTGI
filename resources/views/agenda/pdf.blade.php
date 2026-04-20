<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Agenda CTGI - PDF</title>
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
      padding: 5mm;
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
    
    // Logo logic
    $logoPath = public_path('images/sena/logoSena.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
      $logoData = file_get_contents($logoPath);
      $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
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
        <td colspan="3" class="content-value">{{ $agenda->user->numero_contrato ?? '' }}</td>
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
        <td colspan="13" class="content-value">{{ $agenda->ordenador->nombre ?? '' }}</td>
        <td colspan="8" class="text-bold">CARGO</td>
        <td colspan="16" class="content-value">{{ $agenda->ordenador->cargo ?? '' }}</td>
      </tr>

      <tr>
        <td colspan="11" class="text-bold" style="font-size: 10px;">NOMBRE DEL SUPERVISOR(A) DEL CONTRATO</td>
        <td colspan="13" class="content-value">{{ $agenda->supervisor->nombre ?? '' }}</td>
        <td colspan="8" class="text-bold">CARGO</td>
        <td colspan="16" class="content-value">{{ $agenda->supervisor->cargo ?? '' }}</td>
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
          <td colspan="30" class="content-value" style="text-align: center;">{{ strtoupper($actividad->ruta_regreso ?: $agenda->ruta) }}</td>
        </tr>
        <tr>
          <td colspan="18" class="text-bold" style="text-align: left; padding-left: 5px; font-size: 10px;">Medio de transporte: aéreo, terrestre, fluvial:</td>
          <td colspan="30" class="content-value" style="text-align: center;">
            {{ strtoupper(is_array($actividad->transporte_regreso) ? implode(', ', $actividad->transporte_regreso) : ($actividad->transporte_regreso ?: 'TERRESTRE')) }}
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
        $pathS = $agenda->firma_supervisor_path ?? ($agenda->supervisor->firma ?? null);
        $pathO = $agenda->firma_ordenador_path ?? ($agenda->ordenador->nombre ? ($agenda->ordenador->firma ?? null) : null);

        if (!function_exists('getFirmaBase64')) {
          function getFirmaBase64($path) {
            if (!$path) return '';
            $full = storage_path('app/public/' . $path);
            if (file_exists($full)) {
              return 'data:image/' . pathinfo($full, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($full));
            }
            return '';
          }
        }

        $fC = getFirmaBase64($pathC);
        $fS = (in_array($estado, ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA'])) ? getFirmaBase64($pathS) : '';
        $fO = (in_array($estado, ['APROBADA'])) ? getFirmaBase64($pathO) : '';
      @endphp

      <tr>
        <td colspan="16" class="text-bold" style="font-size: 9px; vertical-align: top; text-align: left; height: 65px; border: 1px solid black; padding: 2px;">
          FIRMA ORDENADOR DE GASTO:
          <div style="text-align: center; margin-top: 5px;">
             @if($fO) <img src="{{ $fO }}" style="max-height: 45px;"> @endif
          </div>
        </td>
        <td colspan="16" class="text-bold" style="font-size: 9px; vertical-align: top; text-align: left; border: 1px solid black; padding: 2px;">
          FIRMA SUPERVISOR DEL CONTRATO :
          <div style="text-align: center; margin-top: 5px;">
             @if($fS) <img src="{{ $fS }}" style="max-height: 45px;"> @endif
          </div>
        </td>
        <td colspan="16" rowspan="2" style="height: 60px; text-align: center; vertical-align: middle; border: 1px solid black; padding: 2px;">
          @if($fC) <img src="{{ $fC }}" style="max-height: 60px;"> @endif
        </td>
      </tr>
      <tr>
        <td colspan="16" style="font-size: 9px; text-align: left; border: 1px solid black; padding: 2px; height: 32px; vertical-align: top;">
          <div class="text-bold">Nombres y Apellidos:</div>
          <div style="margin-top: 1px;">{{ strtoupper($agenda->ordenador->nombre ?? '') }}</div>
        </td>
        <td colspan="16" style="font-size: 9px; text-align: left; border: 1px solid black; padding: 2px; vertical-align: top;">
          <div class="text-bold">Nombres y Apellidos:</div>
          <div style="margin-top: 1px;">{{ strtoupper($agenda->supervisor->nombre ?? '') }}</div>
        </td>
      </tr>
      <tr>
        <td colspan="16" style="font-size: 9px; height: 32px; vertical-align: top; text-align: left; border: 1px solid black; padding: 2px;">
          <div class="text-bold">Cargo:</div>
          <div style="margin-top: 1px;">{{ strtoupper($agenda->ordenador->cargo ?? '') }}</div>
        </td>
        <td colspan="16" style="font-size: 9px; height: 32px; vertical-align: top; text-align: left; border: 1px solid black; padding: 2px;">
          <div class="text-bold">Cargo:</div>
          <div style="margin-top: 1px;">{{ strtoupper($agenda->supervisor->cargo ?? '') }}</div>
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
</body>

</html>