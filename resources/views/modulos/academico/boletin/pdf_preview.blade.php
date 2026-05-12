<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletín — {{ $boletin['estudiante']['nombre'] ?? '' }}</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/global/tipografia.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/botones.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/boletin/pdf.css') }}">
</head>
<body>

{{-- ── Barra de acciones (se oculta al imprimir) ── --}}
<div class="barra-pdf">
    <button id="btn-imprimir" class="btn btn-primario">
        <i class="fa-solid fa-print"></i> Imprimir / Guardar PDF
    </button>
    <a href="{{ route('admin.academico.boletin.show', $inscripcion->id) }}"
       class="btn btn-neutro">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
</div>

{{-- ── Hoja del boletín ── --}}
<div class="hoja-boletin">

    {{-- ENCABEZADO INSTITUCIONAL --}}
    <div class="enc-institucional">
        <div class="enc-logo">
            <div class="enc-escudo">
                <i class="fa-solid fa-school"></i>
            </div>
        </div>
        <div class="enc-texto">
            <h1>{{ $boletin['institucion']['nombre'] }}</h1>
            <p>{{ $boletin['institucion']['resolucion'] }}</p>
            <p>{{ $boletin['sede'] }} — {{ $boletin['institucion']['municipio'] }}, {{ $boletin['institucion']['departamento'] }}</p>
        </div>
        <div class="enc-meta">
            <table class="tabla-meta">
                <tr>
                    <td>FECHA</td>
                    <td><strong>{{ $boletin['fecha_generacion'] }}</strong></td>
                </tr>
                <tr>
                    <td>AÑO</td>
                    <td><strong>{{ $boletin['anio_lectivo'] }}</strong></td>
                </tr>
                <tr>
                    <td>GRADO</td>
                    <td><strong>{{ $boletin['grado'] }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- TÍTULO DEL INFORME --}}
    <div class="inf-titulo">
        INFORME ACADÉMICO — AÑO LECTIVO {{ $boletin['anio_lectivo'] }}
    </div>

    {{-- DATOS DEL ESTUDIANTE --}}
    <div class="datos-estudiante">
        <div class="dato-item">
            <span>ESTUDIANTE</span>
            <strong>{{ $boletin['estudiante']['nombre'] }}</strong>
        </div>
        <div class="dato-item">
            <span>GRADO</span>
            <strong>{{ $boletin['grado'] }}</strong>
        </div>
        <div class="dato-item">
            <span>PUESTO</span>
            <strong>{{ $boletin['puesto'] ?? '—' }}</strong>
        </div>
        <div class="dato-item">
            <span>PROMEDIO GENERAL</span>
            <strong>
                {{ !is_null($boletin['promedio_general'])
                    ? number_format($boletin['promedio_general'], 2)
                    : '—' }}
            </strong>
        </div>
    </div>

    {{-- TABLA DE MATERIAS --}}
    <table class="tabla-boletin">
        <thead>
            <tr>
                <th class="col-materia">ÁREA / MATERIA</th>
                <th class="col-hi">H.I</th>
                <th class="col-desc">MADURACIÓN DEL FRUTO</th>
                @foreach($boletin['periodos'] as $periodo)
                    <th class="col-periodo">P{{ $periodo->numero }}</th>
                @endforeach
                @for($i = $boletin['periodos']->count() + 1; $i <= 3; $i++)
                    <th class="col-periodo">P{{ $i }}</th>
                @endfor
                <th class="col-definitiva">DEF.</th>
                <th class="col-desempeno">DESEMPEÑO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($boletin['materias_normales'] as $materia)
                <tr class="{{ !$materia['aprobada'] && !is_null($materia['promedio']) ? 'fila-baja' : '' }}">
                    <td class="col-materia">
                        <strong>{{ $materia['materia_nombre'] }}</strong>
                    </td>
                    <td class="col-hi col-centrado">
                        {{ $materia['intensidad_horaria'] ?? '—' }}
                    </td>
                    <td class="col-desc">
                        {{ $materia['descripcion'] ?? '' }}
                    </td>
                    @foreach($boletin['periodos'] as $periodo)
                        <td class="col-periodo col-centrado">
                            @php $n = $materia['notas_por_periodo'][$periodo->numero] ?? null; @endphp
                            {{ !is_null($n) ? number_format($n, 1) : '—' }}
                        </td>
                    @endforeach
                    @for($i = $boletin['periodos']->count() + 1; $i <= 3; $i++)
                        <td class="col-periodo col-centrado">—</td>
                    @endfor
                    <td class="col-definitiva col-centrado">
                        {{ !is_null($materia['promedio']) ? number_format($materia['promedio'], 2) : '—' }}
                    </td>
                    <td class="col-desempeno col-centrado des-{{ strtolower($materia['desempeno']) }}">
                        {{ $materia['desempeno'] }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="sin-registros">Sin materias registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PROMEDIOS Y RESULTADO --}}
    <div class="resultado-fila">
        <div class="resultado-item">
            <span>PROMEDIO GENERAL:</span>
            <strong>
                {{ !is_null($boletin['promedio_general'])
                    ? number_format($boletin['promedio_general'], 2)
                    : '—' }}
            </strong>
        </div>
        <div class="resultado-item">
            <span>PUESTO:</span>
            <strong>{{ $boletin['puesto'] ?? '—' }}</strong>
        </div>
        <div class="resultado-estado {{ $boletin['aprobado_anio'] ? 'aprobado' : 'reprobado' }}">
            {{ $boletin['aprobado_anio'] ? 'PROMOVIDO' : 'NO PROMOVIDO' }}
        </div>
    </div>

    {{-- ESCALA DE DESEMPEÑO --}}
    <div class="escala-fila">
        <span><strong>Escala:</strong></span>
        <span>4.5–5.0 → <strong>Superior</strong></span>
        <span>4.0–4.4 → <strong>Alto</strong></span>
        <span>3.0–3.9 → <strong>Básico</strong></span>
        <span>0.0–2.9 → <strong>Bajo</strong></span>
    </div>

    {{-- OBSERVACIONES --}}
    @if($boletin['materias_observacion']->isNotEmpty())
        <div class="observaciones-seccion">
            <div class="observaciones-titulo">OBSERVACIONES</div>
            @foreach($boletin['materias_observacion'] as $obs)
                <div class="observacion-item">
                    <span class="obs-label">{{ $obs['materia_nombre'] }}:</span>
                    <span class="obs-texto">{{ $obs['descripcion'] ?? '—' }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- FIRMAS --}}
    <div class="firmas-seccion">
        <div class="firma-item">
            @if($boletin['firma_rector'])
                <img src="{{ $boletin['firma_rector'] }}" alt="Firma rector">
            @else
                <div class="firma-linea"></div>
            @endif
            <div class="firma-cargo">Rector(a)</div>
        </div>
        <div class="firma-item">
            @if($boletin['firma_director'])
                <img src="{{ $boletin['firma_director'] }}" alt="Firma director">
            @else
                <div class="firma-linea"></div>
            @endif
            <div class="firma-cargo">{{ $boletin['director_nombre'] }}</div>
            <div class="firma-subcargo">Director(a) de Grado</div>
        </div>
        <div class="firma-item">
            <div class="firma-linea"></div>
            <div class="firma-cargo">Padre / Acudiente</div>
        </div>
    </div>

</div>

<script src="{{ asset('js/modulos/academico/boletin/pdf.js') }}"></script>
</body>
</html>
