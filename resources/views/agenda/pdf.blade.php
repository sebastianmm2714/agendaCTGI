<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Formato Agenda Desplazamiento</title>
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
</head>

<body>
  @php
    $clasificacion = $agenda->clasificacion_informacion ?? 'publica';
  @endphp
  <div class="hoja">

    <!-- ================= ENCABEZADO ================= -->
    <table class="encabezado">
      <!-- Fila 1 -->
      <tr>
        <td class="logo" rowspan="2" colspan="4">
          @php
            $logoPath = public_path('images/sena/logoSena.png');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
              $logoData = file_get_contents($logoPath);
              $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
            }
          @endphp
          @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="SENA">
          @else
            <img src="{{ asset('images/sena/logoSena.png') }}" alt="SENA">
          @endif
        </td>
        <td class="info" colspan="20">Versión: 04</td>
      </tr>

      <!-- Fila 2 -->
      <tr>
        <td class="info" colspan="20">
          Código:<br>
          GTH-F-090
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
                        {{ $clasificacion == 'publica' ? 'X' : '' }}
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
                        {{ $clasificacion == 'clasificada' ? 'X' : '' }}
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
                          {{ $clasificacion == 'reservada' ? 'X' : '' }}
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td colspan="24"></td>
      </tr>

      <!-- ================= DATOS CONTRATO ================= -->
      <table class="datos-contrato">
        <!-- Fila Título -->
        <tr class="enca">
          <td colspan="24">DATOS DEL CONTRATISTA QUE SE DESPLAZA</td>
        </tr>

        <!-- Fila Fecha Elaboración -->
        <tr class="fecha-venc">
          <td colspan="12">FECHA DE ELABORACIÓN DE AGENDA</td>
          <td colspan="12">{{ $agenda->fecha_elaboracion ?? '' }}</td>
        </tr>

        <!-- Fila Nombres -->
        <tr class="resaltado">
          <td colspan="12">NOMBRES Y APELLIDOS</td>
          <td colspan="12">IDENTIFICACIÓN</td>
        </tr>
        <!-- Fila 1 -->
        <tr>
          <td colspan="12" class="resaltado nombre">
            {{ $agenda->nombre_completo ?? '' }}
          </td>
          <td colspan="2" class="resaltado">Tipo</td>
          <td colspan="2" class="resaltado">{{ $agenda->tipo_documento ?? '' }}</td>
          <td colspan="2" class="resaltado">No.</td>
          <td colspan="6" class="resaltado">{{ $agenda->numero_documento ?? '' }}</td>
        </tr>

        <!-- Fila 2 -->
        <tr class="fila-contrato">
          <td colspan="3" class="resaltado contrato">CONTRATO</td>
          <td colspan="2" class="resaltado">No.</td>
          <td colspan="4" class="resaltado">{{ $agenda->numero_contrato ?? '' }}</td>
          <td colspan="2" class="resaltado">AÑO</td>
          <td colspan="2" class="resaltado">{{ $agenda->anio_contrato ?? '' }}</td>

          <td colspan="4" class="fecha-venc" style="font-size: 8px; line-height: 1;">
            FECHA<br>
            VENCIMIENTO<br>
            DEL CONTRATO
          </td>
          <td colspan="2" class="resaltado">{{ date('d', strtotime($agenda->fecha_vencimiento)) }}</td>
          <td colspan="2" class="resaltado">{{ date('m', strtotime($agenda->fecha_vencimiento)) }}</td>
          <td colspan="3" class="resaltado">{{ date('Y', strtotime($agenda->fecha_vencimiento)) }}</td>
        </tr>


        <tr>
          <td colspan="4" class="fecha-venc">OBJETO CONTRACTUAL:</td>
          <td colspan="20">{{ $agenda->objetivo_contractual ?? '' }}</td>
        </tr>

        <tr>
          <td colspan="4" class="fecha-venc">DIRECCIÓN GENERAL/<br>REGIONAL</td>
          <td colspan="8" class="fecha-venc">{{ $agenda->direccion_general ?? '' }}</td>
          <td colspan="4">DEPENDENCIA/<br>CENTRO</td>
          <td colspan="8">{{ $agenda->dependencia_centro ?? '' }}</td>
        </tr>

        <tr>
          <td colspan="4" class="fecha-venc">NOMBRE DEL ORDENADOR DEL GASTO (de la Movilización)</td>
          <td colspan="8" class="fecha-venc">DERLYS MARGOTH MADERA SOTO</td>
          <td colspan="4" class="fecha-venc">CARGO</td>
          <td colspan="8" class="fecha-venc">SUBDIRECTOR ENCARGADA</td>
        </tr>

        <tr>
          <td colspan="4" class="fecha-venc">NOMBRE DEL SUPERVISOR(A) DEL CONTRATO</td>
          <td colspan="8" class="fecha-venc">FREDDY CAMACHO GARCÍA</td>
          <td colspan="4" class="fecha-venc">CARGO</td>
          <td colspan="8" class="fecha-venc">COORDINADOR ACADEMICO</td>
        </tr>

        <tr class="barra">
          <td colspan="24">INFORMACIÓN DEL DESPLAZAMIENTO</td>
        </tr>

        <tr>
          <td colspan="4" class="fecha-venc">RUTA</td>
          <td colspan="20" class="fecha-venc">{{ $agenda->ruta }}</td>
        </tr>

        <tr>
          <td colspan="4">DIRECCIÓN GENERAL/<br>REGIONAL</td>
          <td colspan="8">{{ $agenda->direccion_general }}</td>
          <td colspan="4">DEPENDENCIA/<br>CENTRO</td>
          <td colspan="8">{{ $agenda->dependencia_centro }}</td>
        </tr>

        <tr>
          <td colspan="4">CIUDAD/DEPARTAMENTO O <br> MUNICIPIO/DEPARTAMENTO <br> O CIUDAD/PAIS</td>
          <td colspan="5">{{ $agenda->ciudad_destino }}</td>
          <td colspan="3">ENTIDAD O <br> EMPRESA: </td>
          <td colspan="4">{{ $agenda->entidad_empresa }}</td>
          <td colspan="2">CONTACTO</td>
          <td colspan="6">{{ $agenda->contacto }}</td>
        </tr>


        @php
          use Carbon\Carbon;
          $inicio = Carbon::parse($agenda->fecha_inicio_desplazamiento);
          $fin = Carbon::parse($agenda->fecha_fin_desplazamiento);
        @endphp


        <tr>
          <td colspan="4">FECHA INICIO DEL <br> DESPLAZAMIENTO</td>
          <td colspan="2" class="resaltado">{{ $inicio->day }}</td>
          <td colspan="1" class="resaltado">{{ $inicio->month }}</td>
          <td colspan="2" class="resaltado">{{ $inicio->year }}</td>

          <td colspan="3">FECHA FIN <br> DESPLAZAMIENTO</td>
          <td colspan="4" class="resaltado">{{ $fin->day }}</td>
          <td colspan="2" class="resaltado">{{ $fin->month }}</td>
          <td colspan="6" class="resaltado">{{ $fin->year }}</td>
        </tr>

        <tr>
          <td colspan="4" class="fecha-venc">OBJETIVO DEL DESPLAZAMIENTO</td>
          <td colspan="20">{{ $agenda->objetivo_desplazamiento }}</td>
        </tr>

        <tr>
          <td colspan="24" class="fecha-venc">OBLIGACIONES DEL CONTRATO</td>
        </tr>

        @if(!empty($agenda->obligaciones_contrato))
          @foreach ($agenda->obligaciones_contrato as $index => $obligacion)
            <tr>
              <td colspan="1">{{ $index + 1 }}</td>
              <td colspan="23">{{ $obligacion }}</td>
            </tr>
          @endforeach
        @endif

        <!-- <tr>
        <td colspan="1">1</td>
        <td colspan="23">Cumplir a cabalidad el objeto del contrato en los programas y niveles de formación en el Centro
          Textil y de Gestión Industrial con seriedad, responsabilidad, profesionalismo, eficiencia, oportunidad y
          calidad de conformidad con la necesidad del servicio.</td>
      </tr>

      <tr>
        <td colspan="1">2</td>
        <td colspan="23">Diseñar, programar y ejecutar las estrategias de enseñanza - aprendizaje – evaluación
          correspondiente al programa y nivel de formación profesional bajo el enfoque metodológico adoptado por el SENA
          y según orientaciones de la Coordinación Académica.</td>
      </tr>

      <tr>
        <td colspan="1">3</td>
        <td colspan="23">Cumplir a cabalidad el objeto del contrato en los programas y niveles de formación en el Centro
          Textil y de Gestión Industrial con seriedad, responsabilidad, profesionalismo, eficiencia, oportunidad y
          calidad de conformidad con la necesidad del servicio.</td>
      </tr> -->

        <tr class="barra">
          <td colspan="24">AGENDA</td>
        </tr>

        <tr class="enca">
          <td colspan="24">ACTIVIDADES ( (Deberá contener información detallada de las tareas a realizar día a día)</td>
        </tr>

        @foreach ($agenda->actividades as $index => $actividad)
          @php
            $isFirst = $loop->first;
            $isLast = $loop->last;
            $fecha = \Carbon\Carbon::parse($actividad->fecha_reporte);
            $dayLabel = $isFirst ? 'Día Inicio' : ($isLast ? 'Día Fin' : 'Día ' . ($index + 1));
          @endphp

          <!-- Cabecera del día -->
          <tr class="resaltado">
            <td colspan="4" style="background-color: #000; color: #fff; text-align: center; vertical-align: middle;">
              {{ $dayLabel }}
            </td>
            <td colspan="2" style="text-align: center;">{{ $fecha->day }}</td>
            <td colspan="2" style="text-align: center;">{{ $fecha->format('m') }}</td>
            <td colspan="2" style="text-align: center;">{{ $fecha->year }}</td>
            <td colspan="14" class="amarillo"></td>
          </tr>

          {{-- Ruta de ida solo en el primer día o si existe dato --}}
          @if($isFirst)
            <tr>
              <td colspan="8" class="resaltado" style="text-align: left; background-color: #f8f9fa;">Desplazamiento ruta de
                ida:</td>
              <td colspan="16" style="text-align: left;">{{ $actividad->ruta_ida }}</td>
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
            <td colspan="24" style="font-size: 9px; text-align: center; background-color: #fff;">Actividades a ejecutar:
            </td>
          </tr>

          {{-- Bloque de 5 filas de actividades por día --}}
          @php
            $displayActivities = [];
            if (is_array($actividad->actividades_ejecutar)) {
              $displayActivities = $actividad->actividades_ejecutar;
            } else {
              $lines = array_filter(explode("\n", wordwrap($actividad->actividades_ejecutar ?? '', 100)));
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
              @if($lineIndex == 0)
                <td colspan="2" rowspan="5" class="resaltado" style="vertical-align: middle; background-color: #fff;">
                  {{ $fecha->day }}
                </td>
              @endif
              <td colspan="5" class="resaltado"
                style="font-size: 8px; background-color: #fff; text-align: left; padding-left: 5px;">
                HORA: {{ $item['hora'] ?: ($item['actividad'] ? 'AM/PM' : '') }}
              </td>
              <td colspan="17" style="text-align: left; height: 20px; vertical-align: middle;">{{ $item['actividad'] }}</td>
            </tr>
          @endforeach

          {{-- Ruta de regreso solo en el último día --}}
          @if($isLast)
            <tr>
              <td colspan="8" class="resaltado" style="text-align: left; background-color: #f8f9fa;">Desplazamiento ruta de
                regreso:</td>
              <td colspan="16" style="text-align: left;">{{ $actividad->ruta_regreso }}</td>
            </tr>
            <tr>
              <td colspan="8" class="resaltado" style="text-align: left; background-color: #f8f9fa;">Medio de transporte:
                aéreo, terrestre, fluvial:</td>
              <td colspan="16" style="text-align: left;">
                {{ is_array($actividad->transporte_regreso) ? implode(', ', array_map('ucfirst', $actividad->transporte_regreso)) : ((is_array($actividad->medios_transporte ?? null)) ? implode(', ', array_map('ucfirst', $actividad->medios_transporte)) : ucfirst($actividad->medios_transporte)) }}
              </td>
            </tr>
          @endif
        @endforeach

        <!-- ================= OBSERVACIONES LIQUIDACIÓN ================= -->
        <tr class="barra">
          <td colspan="24">Observaciones:</td>
        </tr>
        <tr>
          <td colspan="18" style="text-align: left; padding-left: 10px; height: 22px; vertical-align: middle;">Se
            liquidan gastos de transporte entre terminales aéreas por valor de</td>
          <td colspan="6" class="resaltado" style="text-align: left; vertical-align: middle;">
            $ {{ $agenda->actividades->where('valor_aereo', '!=', null)->last()->valor_aereo ?? 'N/A' }}
          </td>
        </tr>
        <tr>
          <td colspan="18" style="text-align: left; padding-left: 10px; height: 22px; vertical-align: middle;">Se
            liquidan gastos de transporte entre terminales terrestre por valor de</td>
          <td colspan="6" class="resaltado" style="text-align: left; vertical-align: middle;">
            $ {{ $agenda->actividades->where('valor_terrestre', '!=', null)->last()->valor_terrestre ?? 'N/A' }}
          </td>
        </tr>
        <tr>
          <td colspan="18" style="text-align: left; padding-left: 10px; height: 22px; vertical-align: middle;">Se
            liquidan gastos de transporte intermunicipal por valor de</td>
          <td colspan="6" class="resaltado" style="text-align: left; vertical-align: middle;">
            $
            {{ $agenda->actividades->where('valor_intermunicipal', '!=', null)->last()->valor_intermunicipal ?? 'N/A' }}
          </td>
        </tr>

        <tr class="enca">
          <td colspan="24">FIRMAS</td>
        </tr>

        <tr style="height: 80px;">
          <td colspan="8" class="fecha-venc">FIRMA ORDENADOR DE GASTO:
            @if(!empty($agenda->firma_ordenador))
              @php
                $path = storage_path('app/public/' . $agenda->firma_ordenador);
                $base64 = '';
                if (file_exists($path)) {
                  $type = pathinfo($path, PATHINFO_EXTENSION);
                  $data = file_get_contents($path);
                  $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
              @endphp
              @if($base64)
                <img src="{{ $base64 }}" height="70">
              @endif
            @endif
          </td>
          <td colspan="8" class="fecha-venc">FIRMA SUPERVISOR DEL CONTRATO :
            @if(!empty($agenda->firma_supervisor))
              @php
                $path = storage_path('app/public/' . $agenda->firma_supervisor);
                $base64 = '';
                if (file_exists($path)) {
                  $type = pathinfo($path, PATHINFO_EXTENSION);
                  $data = file_get_contents($path);
                  $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
              @endphp
              @if($base64)
                <img src="{{ $base64 }}" height="70">
              @endif
            @endif
          </td>
          <td colspan="8" rowspan="2" class="fecha-venc">FIRMA DEL CONTRATISTA:
            @if(!empty($agenda->firma_contratista))
              @php
                $path = storage_path('app/public/' . $agenda->firma_contratista);
                $base64 = '';
                if (file_exists($path)) {
                  $type = pathinfo($path, PATHINFO_EXTENSION);
                  $data = file_get_contents($path);
                  $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
              @endphp
              @if($base64)
                <img src="{{ $base64 }}" style="width:150px; height:auto;">
              @endif
            @endif
          </td>
        </tr>

        <tr style="height: 60px;">
          <td colspan="8" class="fecha-venc">Nombres y Apellidos:</td>
          <td colspan="8" class="fecha-venc">Nombres y Apellidos:</td>
        </tr>

        <tr style="height: 60px;">
          <td colspan="8" class="fecha-venc">
            Cargo: SUBDIRECTOR ENCARGADO<br>
            DERLYS MARGOTH MADERA SOTO
          </td>
          <td colspan="8" class="fecha-venc" style="height: 80px; text-align:center;">
            Cargo: COORDINADOR ACADEMICO<br>
            FREDDY CAMACHO GARCÍA
          </td>
          <td colspan="8" class="fecha-venc">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <span>Nombres y Apellidos:</span>
              <span style="font-weight: bold;">{{ $agenda->nombre_completo }}</span>
            </div>
          </td>
        </tr>

        <tr>
          <td colspan="24" style="text-align: right; font-size: 10px;">GTH-F-090 V.04</td>
        </tr>


      </table>


  </div>

  <button id="btnPdf">Generar PDF</button>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="{{ asset('js/scripts.js') }}"></script>
</body>

</html>