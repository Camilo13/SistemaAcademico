<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletín — {{ $boletin['estudiante']['nombre'] ?? '' }}</title>

    {{-- CSS base (variables + tipografía + botones) --}}
    <link rel="stylesheet" href="{{ asset('css/global/tipografia.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/botones.css') }}">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">

    {{-- CSS específico del boletín --}}
    <link rel="stylesheet" href="{{ asset('css/modulos/estudiante/boletin/pdf.css') }}">
</head>
<body>

<div class="contenedor-pdf">

    {{-- Barra de acciones — solo imprimir (se oculta al imprimir) --}}
    <div class="barra-pdf">
        <button class="btn btn-primario" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Imprimir / Guardar PDF
        </button>
    </div>

    {{-- Hoja del boletín --}}
    <div class="hoja-boletin">

        {{-- Encabezado institucional --}}
        <div class="encabezado-inst">
            <div class="encabezado-inst-texto">
                <h1>I.E. Akwe Uus Yat</h1>
                <p>Sistema de Gestión Académica</p>
            </div>
            <div style="text-align:right;">
                <div class="encabezado-titulo">BOLETÍN DE CALIFICACIONES</div>
                <p style="font-size:var(--texto-xs);color:var(--color-texto-secundario);">
                    Año Lectivo: {{ $boletin['anio_lectivo'] ?? '—' }}
                </p>
            </div>
        </div>

        {{-- Datos del estudiante --}}
        <div class="datos-estudiante">
            <div class="dato-fila">
                <span>Estudiante</span>
                <strong>{{ $boletin['estudiante']['nombre'] ?? '—' }}</strong>
            </div>
            <div class="dato-fila">
                <span>Grupo</span>
                <strong>{{ $boletin['grupo'] ?? '—' }}</strong>
            </div>
            <div class="dato-fila">
                <span>Fecha generación</span>
                <strong>{{ \Carbon\Carbon::parse($boletin['fecha_generacion'])->format('d/m/Y') }}</strong>
            </div>
        </div>

        {{-- Tabla de materias --}}
        <table class="tabla-boletin">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Docente</th>
                    <th>Promedio</th>
                    <th>Desempeño</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boletin['materias'] as $materia)
                    @php
                        $cls = match($materia['estado_academico']) {
                            'Desempeño Superior' => 'des-superior',
                            'Desempeño Alto'     => 'des-alto',
                            'Desempeño Básico'   => 'des-basico',
                            'Desempeño Bajo'     => 'des-bajo',
                            default              => '',
                        };
                    @endphp
                    <tr class="{{ $materia['aprobada'] ? '' : 'fila-reprobada' }}">
                        <td><strong>{{ $materia['materia_nombre'] }}</strong></td>
                        <td>{{ $materia['docente_nombre'] }}</td>
                        <td>
                            @if(!is_null($materia['promedio']))
                                <span class="nota-chip {{ $materia['aprobada'] ? 'aprobada' : 'reprobada' }}">
                                    {{ number_format($materia['promedio'], 2) }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="badge-desempeno {{ $cls }}">
                                {{ $materia['estado_academico'] }}
                            </span>
                        </td>
                        <td>
                            @if(!is_null($materia['promedio']))
                                <span class="nota-chip {{ $materia['aprobada'] ? 'aprobada' : 'reprobada' }}">
                                    {{ $materia['aprobada'] ? 'Aprobada' : 'Reprobada' }}
                                </span>
                            @else
                                <span style="color:var(--color-texto-tenue);font-style:italic;">Sin calificar</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--color-texto-tenue);padding:1.5rem;">
                            No hay materias activas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Resultado final --}}
        <div class="resultado-final">
            <span class="resultado-texto">
                Promedio general:
                <strong>
                    {{ !is_null($boletin['promedio_general'])
                        ? number_format($boletin['promedio_general'], 2)
                        : '—' }}
                </strong>
            </span>
            <span class="resultado-estado {{ $boletin['aprobado_anio'] ? 'estado-aprobado' : 'estado-reprobado' }}">
                {{ $boletin['aprobado_anio'] ? 'APROBADO' : 'REPROBADO' }}
            </span>
        </div>

        {{-- Escala de desempeño --}}
        <table class="tabla-boletin tabla-escala">
            <thead>
                <tr><th colspan="2">Escala de Desempeño</th></tr>
            </thead>
            <tbody>
                <tr><td>4.5 – 5.0</td><td>Desempeño Superior</td></tr>
                <tr><td>4.0 – 4.4</td><td>Desempeño Alto</td></tr>
                <tr><td>3.0 – 3.9</td><td>Desempeño Básico</td></tr>
                <tr><td>0.0 – 2.9</td><td>Desempeño Bajo</td></tr>
            </tbody>
        </table>

        {{-- Firmas --}}
        <div class="firma-seccion">
            <div class="firma-item">
                <div class="firma-linea"></div>
                <div class="firma-nombre">Rector(a)</div>
            </div>
            <div class="firma-item">
                <div class="firma-linea"></div>
                <div class="firma-nombre">Director(a) de Grupo</div>
            </div>
            <div class="firma-item">
                <div class="firma-linea"></div>
                <div class="firma-nombre">Padre / Acudiente</div>
            </div>
        </div>

    </div>{{-- fin .hoja-boletin --}}

</div>

</body>
</html>