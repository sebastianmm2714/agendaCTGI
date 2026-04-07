<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Formato Agenda Desplazamiento</title>
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  <style>
    @media screen {
      body {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        margin: 0;
        padding: 20px 0;
        box-sizing: border-box;
      }
    }
  </style>
</head>

<body>
  @php
    $cNombre = $agenda->clasificacion->nombre ?? '';
  @endphp
  <div class="hoja" id="hoja-agenda" style="height: 100%; padding: 0; margin: 0;">

    <!-- ================= ENCABEZADO ================= -->
    <table class="encabezado">
      <!-- Fila Encabezado: Logo + Versión/Código sin rowspan para evitar artefactos en html2canvas -->
      <!-- Fila 1: Logo (con rowspan) + Versión -->
      <tr>
        <!-- Celda izquierda vacía (6 cols) -->
        <td rowspan="2" colspan="6" style="border: 1px solid black; border-right: none;"></td>

        <!-- Celda del Logo (12 cols) -->
        <td rowspan="2" colspan="12"
          style="text-align: center; vertical-align: middle; border: 1px solid black; border-left: none; padding: 8px;">
          @php
            $logoPath = public_path('images/sena/logoSena.png');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
              $logoData = file_get_contents($logoPath);
              $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
            }
          @endphp
          <img src="{{ $logoBase64 ?: asset('images/sena/logoSena.png') }}" alt="SENA" style="width: 130px;">
        </td>

        <!-- Celda de Versión (6 cols) -->
        <td colspan="6"
          style="border: 1px solid black; text-align: center; font-size: 11px; padding: 6px; vertical-align: middle; height: 65px;">
          Versión: 01
        </td>
      </tr>

      <!-- Fila 2: Solo la celda de Código (el resto está cubierto por el rowspan del logo) -->
      <tr>
        <td colspan="6"
          style="border: 1px solid black; text-align: center; font-size: 11px; padding: 6px; vertical-align: middle; height: 65px;">
          Código:<br>GCCON-F-095
        </td>
      </tr>

      <!-- Barras -->
      <tr class="barra">
        <td colspan="24">PROCESO</td>
      </tr>
      <tr class="resaltado">
        <td colspan="24">GESTIÓN DE TALENTO HUMANO</td>
      </tr>


      <tr class="barra">
        <td colspan="24">NOMBRE DEL FORMATO</td>
      </tr>
      <tr class="resaltado">
        <td colspan="24">FORMATO AGENDA DESPLAZAMIENTO CONTRATISTA</td>
      </tr>
      <tr class="barra">
        <td colspan="24">CLASIFICACIÓN DE LA INFORMACIÓN</td>
      </tr>

      <tr>
        <td colspan="24" style="padding: 0;">
          <table style="width: 100%; border-collapse: collapse; border: none; table-layout: fixed;">
            <tr>
              <td style="width: 33.33%; border: none; border-right: 1px solid black; padding: 0;">
                <table style="width: 100%; border: none;">
                  <tr>
                    <td
                      style="border: none; text-align: left; padding: 6px; font-weight: bold; font-family: Arial, sans-serif;">
                      Pública</td>
                    <td style="border: none; text-align: right; padding: 6px; width: 30px;">
                      <div
                        style="width: 15px; height: 15px; border: 1px solid black; display: inline-block; vertical-align: middle; text-align: center; line-height: 15px; font-weight: bold;">
                        {{ trim(strtoupper($cNombre)) == 'PÚBLICA' ? 'X' : '' }}
                      </div>
                    </td>
                  </tr>
                </table>
              </td>
              <td style="width: 33.33%; border: none; border-right: 1px solid black; padding: 0;">
                <table style="width: 100%; border: none;">
                  <tr>
                    <td
                      style="border: none; text-align: left; padding: 6px; font-weight: bold; font-family: Arial, sans-serif;">
                      Pública Clasificada</td>
                    <td style="border: none; text-align: right; padding: 6px; width: 30px;">
                      <div
                        style="width: 15px; height: 15px; border: 1px solid black; display: inline-block; vertical-align: middle; text-align: center; line-height: 15px; font-weight: bold;">
                        {{ trim(strtoupper($cNombre)) == 'INTERNA' ? 'X' : '' }}
                      </div>
                    </td>
                  </tr>
                </table>
              </td>
              <td style="width: 33.34%; border: none; padding: 0;">
                <table style="width: 100%; border: none;">
                  <tr>
                    <td
                      style="border: none; text-align: left; padding: 6px; font-weight: bold; font-family: Arial, sans-serif;">
                      Pública Reservada</td>
                    <td style="border: none; text-align: right; padding: 6px; width: 30px;">
                      <div
                        style="width: 15px; height: 15px; border: 1px solid black; display: inline-block; vertical-align: middle; text-align: center; line-height: 15px; font-weight: bold;">
                        {{ trim(strtoupper($cNombre)) == 'CONFIDENCIAL' ? 'X' : '' }}
                      </div>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- ================= DATOS CONTRATO ================= -->
      <table class="datos-contrato">
        <!-- Fila Título -->
        <tr class="enca">
          <td colspan="24">DATOS DEL CONTRATISTA QUE SE DESPLAZA</td>
        </tr>

        <!-- Fila Fecha Elaboración -->
        <tr class="fecha-venc" style="height: 40px;">
          <td colspan="12" style="font-weight: bold; text-align: center;">FECHA DE ELABORACIÓN DE AGENDA</td>
          <td colspan="12" style="text-align: center;">
            {{ $agenda->fecha_elaboracion ? date('Y-m-d', strtotime($agenda->fecha_elaboracion)) : '' }}
          </td>
        </tr>

        <!-- Fila Nombres -->
        <tr style="height: 30px;">
          <td colspan="12" style="font-weight: bold;">NOMBRES Y APELLIDOS</td>
          <td colspan="12" style="font-weight: bold;">IDENTIFICACIÓN</td>
        </tr>
        <!-- Fila 1 -->
        <tr style="height: 45px;">
          <td colspan="12" style="text-align: center; font-weight: bold;">
            {{ $agenda->user->name ?? '' }}
          </td>
          <td colspan="2" style="font-weight: bold;">Tipo</td>
          <td colspan="2" style="text-align: center;">{{ $agenda->user->tipo_documento ?? 'CC' }}</td>
          <td colspan="2" style="font-weight: bold;">No.</td>
          <td colspan="6" style="text-align: center;">{{ $agenda->user->numero_documento ?? '' }}</td>
        </tr>

        <!-- Fila 2 -->
        <tr class="fila-contrato" style="height: 45px;">
          <td colspan="4" style="font-weight: bold; text-align: left;">CONTRATO</td>
          <td colspan="1" style="font-weight: bold; text-align: center;">No.</td>
          <td colspan="3" style="text-align: center;">{{ $agenda->user->numero_contrato ?? '' }}</td>
          <td colspan="2" style="font-weight: bold; text-align: center;">AÑO</td>
          <td colspan="2" style="text-align: center;">{{ $agenda->user->anio_contrato ?? '' }}</td>

          <td colspan="4" style="font-weight: bold; text-align: left; font-size: 11px; line-height: 1.2;">
            FECHA<br>
            VENCIMIENTO<br>
            DEL CONTRATO
          </td>
          <td colspan="2" style="text-align: center;">
            {{ $agenda->user->fecha_vencimiento ? date('d', strtotime($agenda->user->fecha_vencimiento)) : '' }}
          </td>
          <td colspan="2" style="text-align: center;">
            {{ $agenda->user->fecha_vencimiento ? date('m', strtotime($agenda->user->fecha_vencimiento)) : '' }}
          </td>
          <td colspan="4" style="text-align: center;">
            {{ $agenda->user->fecha_vencimiento ? date('Y', strtotime($agenda->user->fecha_vencimiento)) : '' }}
          </td>
        </tr>


        <tr>
          <td colspan="4" style="font-weight: bold; text-align: left;">OBJETO CONTRACTUAL:</td>
          <td colspan="20" style="font-size: 11px; text-align: center; padding: 10px;">
            {{ $agenda->user->objeto_contractual ?? '' }}
          </td>
        </tr>

        <tr>
          <td colspan="4" style="font-weight: bold; text-align: left;">DIRECCIÓN GENERAL/<br>REGIONAL</td>
          <td colspan="8" style="text-align: center;">REGIONAL ANTIOQUIA</td>
          <td colspan="4" style="font-weight: bold; text-align: left;">DEPENDENCIA/<br>CENTRO</td>
          <td colspan="8" style="text-align: center;">CENTRO TEXTIL Y DE GESTIÓN INDUSTRIAL</td>
        </tr>

        <tr>
          <td colspan="4" style="font-weight: bold; text-align: left;">NOMBRE DEL ORDENADOR DEL GASTO (de la
            Movilización)</td>
          <td colspan="8">{{ $agenda->ordenador->nombre ?? 'N/A' }}</td>
          <td colspan="4" style="font-weight: bold; text-align: left;">CARGO</td>
          <td colspan="8">{{ $agenda->ordenador->cargo ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td colspan="4" style="font-weight: bold; text-align: left;">NOMBRE DEL SUPERVISOR(A) DEL CONTRATO</td>
          <td colspan="8">{{ $agenda->supervisor->nombre ?? 'N/A' }}</td>
          <td colspan="4" style="font-weight: bold; text-align: left;">CARGO</td>
          <td colspan="8">{{ $agenda->supervisor->cargo ?? 'N/A' }}</td>
        </tr>


      </table>

      <!-- ================= INFORMACIÓN DEL DESPLAZAMIENTO ================= -->
      <table class="datos-contrato">
        <tr class="barra">
          <td colspan="24">INFORMACIÓN DEL DESPLAZAMIENTO</td>
        </tr>

        <tr>
          <td colspan="4" style="font-weight: bold; text-align: left; vertical-align: top;">RUTA</td>
          <td colspan="20" style="text-align: center; font-weight: bold;">{{ $agenda->ruta }}</td>
        </tr>
        <tr>
          <td colspan="4" style="font-weight: bold; text-align: left; vertical-align: top;">DIRECCIÓN
            GENERAL/<br>REGIONAL</td>
          <td colspan="9" style="text-align: center;">{{ $agenda->regional }}</td>
          <td colspan="4" style="font-weight: bold; text-align: left; vertical-align: top;">DEPENDENCIA/<br>CENTRO</td>
          <td colspan="7" style="text-align: center;">{{ $agenda->centro }}</td>
        </tr>

        <tr>
          <td colspan="4"
            style="font-weight: bold; text-align: left; vertical-align: top; font-size: 10px; line-height: 1.1; letter-spacing: -0.2px;">
            CIUDAD/DEPARTAMENTO O<br>MUNICIPIO/DEPARTAMENTO<br>O CIUDAD/PAIS</td>
          <td colspan="7" style="text-align: center;">
            @if($agenda->destinos)
              {{ implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $agenda->destinos)))) }}
            @else
              {{ $agenda->ciudad_destino }}
            @endif
          </td>
          <td colspan="4"
            style="font-weight: bold; text-align: left; vertical-align: top; font-size: 10px; line-height: 1.1;">ENTIDAD
            O<br>EMPRESA:</td>
          <td colspan="3" style="text-align: center;">{{ $agenda->entidad_empresa }}</td>
          <td colspan="3" style="font-weight: bold; text-align: left; vertical-align: top; font-size: 10px;">CONTACTO
          </td>
          <td colspan="3" style="text-align: center;">{{ $agenda->contacto }}</td>
        </tr>


        @php
          use Carbon\Carbon;
          $inicio = Carbon::parse($agenda->fecha_inicio);
          $fin = Carbon::parse($agenda->fecha_fin);
        @endphp


        <tr>
          <td colspan="4" style="font-weight: bold; text-align: left; vertical-align: top;">FECHA INICIO
            DEL<br>DESPLAZAMIENTO</td>
          <td colspan="2" style="text-align: center;">{{ $inicio->day }}</td>
          <td colspan="2" style="text-align: center;">{{ $inicio->month }}</td>
          <td colspan="4" style="text-align: center;">{{ $inicio->year }}</td>
          <td colspan="4" style="font-weight: bold; text-align: left; vertical-align: top;">FECHA FIN<br>DESPLAZAMIENTO
          </td>
          <td colspan="2" style="text-align: center;">{{ $fin->day }}</td>
          <td colspan="2" style="text-align: center;">{{ $fin->month }}</td>
          <td colspan="4" style="text-align: center;">{{ $fin->year }}</td>
        </tr>

        <tr>
          <td colspan="4" style="font-weight: bold; text-align: left; vertical-align: top;">OBJETIVO DEL DESPLAZAMIENTO
          </td>
          <td colspan="20" style="text-align: center;">{{ $agenda->objetivo_desplazamiento }}</td>
        </tr>


      </table>

      <!-- ================= OBLIGACIONES DEL CONTRATO ================= -->
      <table class="datos-contrato">
        <tr>
          <td colspan="24" class="fecha-venc">OBLIGACIONES DEL CONTRATO</td>
        </tr>

        @if($agenda->obligaciones->count() > 0)
          @foreach ($agenda->obligaciones as $index => $obligacion)
            <tr>
              <td colspan="1" style="text-align: center; height: 18px;">{{ $index + 1 }}</td>
              <td colspan="23" style="font-size: 11px; line-height: 1.1; padding: 4px 8px;">{{ $obligacion->nombre }}</td>
            </tr>
          @endforeach
        @endif

      </table>

      <!-- ================= AGENDA ================= -->
      <table class="datos-contrato" style="table-layout: fixed; width: 100%;">
        <tr class="barra">
          <td colspan="24">AGENDA</td>
        </tr>

        <tr class="enca">
          <td colspan="24">ACTIVIDADES (Deberá contener información detallada de las tareas a realizar día a día)</td>
        </tr>

        @foreach ($agenda->actividades->sortBy('fecha') as $index => $actividad)
          @php
            $isFirst = $loop->first;
            $isLast = $loop->last;
            $fecha = \Carbon\Carbon::parse($actividad->fecha);
            $dayLabel = $isFirst ? 'Día Inicio' : ($isLast ? 'Día Fin' : 'Día ' . ($index + 1));
            // Calcular rutas desde la agenda si no están guardadas en la actividad
            $rutaPartesPdf = explode(' - ', $agenda->ruta);
            $mitadPdf = (int) (count($rutaPartesPdf) / 2);
            $rutaIdaPdf = $actividad->ruta_ida ?: implode(' - ', array_slice($rutaPartesPdf, 0, $mitadPdf));
            $rutaRegresoPdf = $actividad->ruta_regreso ?: implode(' - ', array_slice($rutaPartesPdf, $mitadPdf));
          @endphp

          <!-- Cabecera del día -->
          <tr class="resaltado">
            <td colspan="4" style="background-color: #000; color: #fff; text-align: center; vertical-align: middle;">
              {{ $dayLabel }}
            </td>
            <td colspan="2" style="text-align: center;">{{ $fecha->day }}</td>
            <td colspan="2" style="text-align: center;">{{ $fecha->format('m') }}</td>
            <td colspan="2" style="text-align: center;">{{ $fecha->year }}</td>
            <td colspan="14" style="background-color: #fff;"></td>
          </tr>

          {{-- Ruta de ida solo en el primer día o si existe dato --}}
          @if($isFirst)
            <tr>
              <td colspan="8" class="resaltado" style="text-align: left; background-color: #f8f9fa;">Desplazamiento ruta de
                ida:</td>
              <td colspan="16" style="text-align: left;">{{ $rutaIdaPdf }}</td>
            </tr>
            <tr>
              <td colspan="8" class="resaltado" style="text-align: left; background-color: #f8f9fa;">Medio de transporte:
                aéreo, terrestre, fluvial:</td>
              <td colspan="16" style="text-align: left;">
                {{ is_array($actividad->transporte_ida) ? implode(', ', array_map('ucfirst', $actividad->transporte_ida)) : ((is_array($actividad->medios_transporte ?? null)) ? implode(', ', array_map('ucfirst', $actividad->medios_transporte)) : ucfirst($actividad->medios_transporte)) }}
              </td>
            </tr>
          @endif

          <tr class="enca">
            <td colspan="24"
              style="font-size: 11px; text-align: center; background-color: #fff; font-weight: bold; height: 25px;">
              Actividades a ejecutar:
            </td>
          </tr>

          {{-- Bloque de 5 filas de actividades por día --}}
          @php
            $displayActivities = [];
            if (is_array($actividad->actividad)) {
              $displayActivities = $actividad->actividad;
            } else {
              $lines = array_filter(explode("\n", wordwrap($actividad->actividad ?? '', 100)));
              foreach ($lines as $l) {
                $displayActivities[] = ['hora' => 'AM/PM', 'actividad' => $l];
              }
            }
            // Asegurar 5 filas
            while (count($displayActivities) < 5) {
              $displayActivities[] = ['hora' => '', 'actividad' => ''];
            }
            $displayActivities = array_slice($displayActivities, 0, 5);
          @endphp

          @foreach($displayActivities as $lineIndex => $item)
            <tr>
              <td colspan="8" class="resaltado"
                style="font-size: 11px; background-color: #fff; text-align: left; padding-left: 5px; width: 33.33%; border: 1px solid black;">
                HORA: {{ $item['hora'] ?: ($item['actividad'] ? 'AM/PM' : '') }}
              </td>
              <td colspan="16"
                style="text-align: left; height: 35px; vertical-align: middle; line-height: 1.2; font-size: 12px; width: 66.67%; border: 1px solid black;">
                {{ $item['actividad'] }}
              </td>
            </tr>
          @endforeach

          {{-- Ruta de regreso solo en el último día --}}
          @if($isLast)
            @php
              $primeraActividad = $agenda->actividades->first();
            @endphp
            <tr>
              <td colspan="8" class="resaltado"
                style="text-align: left; background-color: #f8f9fa; height: 22px; font-size: 11px;">Desplazamiento ruta de
                regreso:</td>
              <td colspan="16" style="text-align: left; height: 22px; font-size: 11px;">{{ $rutaRegresoPdf }}</td>
            </tr>
            <tr>
              <td colspan="8" class="resaltado" style="text-align: left; background-color: #f8f9fa;">Medio de transporte:
                aéreo, terrestre, fluvial:</td>
              <td colspan="16" style="text-align: left;">
                @php
                  $transporteRegreso = $primeraActividad->transporte_regreso ?? $primeraActividad->medios_transporte ?? [];
                @endphp
                {{ is_array($transporteRegreso) ? implode(', ', array_map('ucfirst', $transporteRegreso)) : ucfirst($transporteRegreso) }}
              </td>
            </tr>
          @endif
        @endforeach

      </table>



      <!-- ================= OBSERVACIONES Y FIRMAS ================= -->
      <div class="no-break">
        <table class="datos-contrato" style="table-layout: fixed; width: 100%;">
          <!-- ================= OBSERVACIONES LIQUIDACIÓN ================= -->
          <tr class="barra">
            <td colspan="24">Observaciones:</td>
          </tr>
          <tr>
            <td colspan="18" style="text-align: left; padding-left: 10px; height: 28px; vertical-align: middle;">Se
              liquidan gastos de transporte entre terminales aéreas por valor de</td>
            <td colspan="6" class="resaltado" style="text-align: left; vertical-align: middle;">
              @php
                $valorAereo = $agenda->actividades->where('valor_aereo', '>', 0)->last()->valor_aereo ?? null;
              @endphp
              {{ $valorAereo ? '$ ' . number_format($valorAereo, 0, ',', '.') : 'N/A' }}
            </td>
          </tr>
          <tr>
            <td colspan="18" style="text-align: left; padding-left: 10px; height: 28px; vertical-align: middle;">Se
              liquidan gastos de transporte entre terminales terrestre por valor de</td>
            <td colspan="6" class="resaltado" style="text-align: left; vertical-align: middle;">
              @php
                $valorTerrestre = $agenda->actividades->where('valor_terrestre', '>', 0)->last()->valor_terrestre ?? null;
              @endphp
              {{ $valorTerrestre ? '$ ' . number_format($valorTerrestre, 0, ',', '.') : 'N/A' }}
            </td>
          </tr>
          <tr>
            <td colspan="18" style="text-align: left; padding-left: 10px; height: 28px; vertical-align: middle;">Se
              liquidan gastos de transporte intermunicipal por valor de</td>
            <td colspan="6" class="resaltado" style="text-align: left; vertical-align: middle;">
              @php
                $valorIntermunicipal = $agenda->actividades->where('valor_intermunicipal', '>', 0)->last()->valor_intermunicipal ?? null;
              @endphp
              {{ $valorIntermunicipal ? '$ ' . number_format($valorIntermunicipal, 0, ',', '.') : 'N/A' }}
            </td>
          </tr>

          <tr class="enca">
            <td colspan="24">FIRMAS</td>
          </tr>

          @php
            // 1. Funciones auxiliares para evitar repetición
            if (!function_exists('getBase64Signature')) {
              function getBase64Signature($path)
              {
                if (!$path)
                  return '';
                $fullPath = storage_path('app/public/' . $path);
                if (!file_exists($fullPath))
                  return '';

                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
              }
            }

            $estadoActual = $agenda->estado->nombre ?? '';

            // Firma del Contratista: Visible solo si ya fue enviada (no es BORRADOR)
            $showContratista = !in_array($estadoActual, ['BORRADOR', '']);
            $base64C = $showContratista ? getBase64Signature($agenda->user->firma ?? null) : '';

            // Firma del Supervisor: Visible solo si fue aprobada por Coordinación o roles posteriores
            $showSupervisor = in_array($estadoActual, ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA']);
            $base64S = $showSupervisor ? getBase64Signature($agenda->supervisor->firma ?? null) : '';

            // Firma del Ordenador: Visible solo si fue autorizada por el Subdirector o el proceso terminó
            $showOrdenador = in_array($estadoActual, ['APROBADA_ORDENADOR', 'APROBADA']);
            $base64O = $showOrdenador ? getBase64Signature($agenda->ordenador->firma ?? null) : '';
          @endphp

          <!-- FILA DE ETIQUETAS (NIVEL 1) -->
          <tr>
            <td colspan="8"
              style="height: 20px; text-align: left; font-weight: bold; font-size: 11px; border: 1px solid black; padding: 4px;">
              FIRMA ORDENADOR DE GASTO:</td>
            <td colspan="8"
              style="height: 20px; text-align: left; font-weight: bold; font-size: 11px; border: 1px solid black; padding: 4px;">
              FIRMA SUPERVISOR DEL CONTRATO :</td>
            <td colspan="8"
              style="height: 20px; text-align: left; font-weight: bold; font-size: 11px; border: 1px solid black; padding: 4px;">
              FIRMA DEL CONTRATISTA:</td>
          </tr>

          <!-- FILA DE FIRMAS (NIVEL 2) -->
          <tr>
            <td colspan="8" style="height: 60px; text-align: center; vertical-align: middle; border: 1px solid black;">
              @if($base64O) <img src="{{ $base64O }}" height="40"> @endif
            </td>
            <td colspan="8" style="height: 60px; text-align: center; vertical-align: middle; border: 1px solid black;">
              @if($base64S) <img src="{{ $base64S }}" height="40"> @endif
            </td>
            <td colspan="8" rowspan="2"
              style="height: 80px; text-align: center; vertical-align: middle; border: 1px solid black;">
              @if($base64C) <img src="{{ $base64C }}" height="50"> @endif
            </td>
          </tr>

          <!-- FILA ETIQUETA "NOMBRES Y APELLIDOS" (NIVEL 3 - Solo para primeros dos) -->
          <tr>
            <td colspan="8"
              style="height: 18px; text-align: left; font-weight: bold; font-size: 11px; border: 1px solid black; padding: 2px;">
              Nombres y Apellidos:</td>
            <td colspan="8"
              style="height: 18px; text-align: left; font-weight: bold; font-size: 11px; border: 1px solid black; padding: 2px;">
              Nombres y Apellidos:</td>
          </tr>

          <!-- FILA DE CARGOS Y NOMBRES (NIVEL 4) -->
          <tr>
            <td colspan="8"
              style="height: 35px; text-align: left; font-size: 11px; border: 1px solid black; padding: 2px; vertical-align: top;">
              Cargo: {{ $agenda->ordenador->cargo ?? 'SUBDIRECTOR ENCARGADO' }}<br>
              {{ $agenda->ordenador->nombre ?? 'DERLYS MARGOTH MADERA SOTO' }}
            </td>
            <td colspan="8"
              style="height: 35px; text-align: left; font-size: 11px; border: 1px solid black; padding: 2px; vertical-align: top;">
              Cargo: {{ $agenda->supervisor->cargo ?? 'COORDINADOR ACADEMICO' }}<br>
              {{ $agenda->supervisor->nombre ?? 'FREDDY CAMACHO GARCÍA' }}
            </td>
            <td colspan="8"
              style="height: 30px; text-align: left; border: 1px solid black; padding: 2px; vertical-align: bottom;">
              <table style="width: 100%; border: none; font-size: 8px;">
                <tr>
                  <td style="border: none; padding: 0; font-weight: bold; text-align: left;">Nombres y Apellidos:</td>
                  <td style="border: none; padding: 0; font-weight: bold; text-align: right;">{{ $agenda->user->name }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>


        </table>
      </div>

  </div>

  <button id="btnPdf">
    <span>Descargar Formato</span>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
      stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
      <polyline points="7 10 12 15 17 10"></polyline>
      <line x1="12" y1="15" x2="12" y2="3"></line>
    </svg>
  </button>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="{{ asset('js/scripts.js') }}"></script>
</body>

</html>