@extends('layouts.menuadmin')

@section('title', 'Boletín — ' . ($boletin['estudiante']['nombre'] ?? ''))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/boletin/boletin.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-file-lines"></i>
                Boletín Académico
            </h2>
            <p class="cabecera-subtitulo">
                Generado el
                {{ \Carbon\Carbon::parse($boletin['fecha_generacion'])->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="cabecera-acciones">
            <a href="{{ route('admin.academico.inscripciones.edit', $inscripcion->id) }}"
               class="btn btn-neutro btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Inscripción
            </a>
            <a href="{{ route('admin.academico.boletin.pdf', $inscripcion->id) }}"
               class="btn btn-secundario"
               target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF
            </a>
        </div>
    </div>

    {{-- ── Ficha del estudiante ── --}}
    <div class="boletin-ficha">
        <div class="boletin-ficha-item">
            <span><i class="fa-solid fa-user-graduate"></i> Estudiante</span>
            <strong>{{ $boletin['estudiante']['nombre'] ?? '—' }}</strong>
        </div>
        <div class="boletin-ficha-item">
            <span><i class="fa-solid fa-users"></i> Grupo</span>
            <strong>{{ $boletin['grupo'] ?? '—' }}</strong>
        </div>
        <div class="boletin-ficha-item">
            <span><i class="fa-solid fa-calendar-days"></i> Año Lectivo</span>
            <strong>{{ $boletin['anio_lectivo'] ?? '—' }}</strong>
        </div>
    </div>

    {{-- ── Resumen general ── --}}
    <div class="boletin-resumen">

        <div class="boletin-res-card {{ $boletin['aprobado_anio'] ? 'aprobado' : 'reprobado' }}">
            <i class="fa-solid {{ $boletin['aprobado_anio'] ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
            <div>
                <span>Resultado anual</span>
                <strong>{{ $boletin['aprobado_anio'] ? 'APROBADO' : 'REPROBADO' }}</strong>
            </div>
        </div>

        <div class="boletin-res-card">
            <i class="fa-solid fa-calculator"></i>
            <div>
                <span>Promedio general</span>
                <strong>
                    {{ !is_null($boletin['promedio_general'])
                        ? number_format($boletin['promedio_general'], 2)
                        : '—' }}
                </strong>
            </div>
        </div>

        <div class="boletin-res-card">
            <i class="fa-solid fa-book-open"></i>
            <div>
                <span>Total materias</span>
                <strong>{{ $boletin['total_materias'] }}</strong>
            </div>
        </div>

        <div class="boletin-res-card aprobadas">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <span>Aprobadas</span>
                <strong>{{ $boletin['materias_aprobadas'] }}</strong>
            </div>
        </div>

        <div class="boletin-res-card reprobadas">
            <i class="fa-solid fa-circle-xmark"></i>
            <div>
                <span>Reprobadas</span>
                <strong>{{ $boletin['materias_reprobadas'] }}</strong>
            </div>
        </div>

    </div>

    {{-- ── Tabla de materias ── --}}
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Docente</th>
                    <th class="col-centrado">Notas</th>
                    <th>Promedio</th>
                    <th>Desempeño</th>
                    <th>Resultado</th>
                    <th class="col-acciones">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boletin['materias'] as $materia)
                    <tr class="{{ $materia['aprobada'] ? '' : 'fila-reprobada' }}">

                        <td data-label="Materia">
                            <strong>{{ $materia['materia_nombre'] }}</strong>
                        </td>

                        <td data-label="Docente">
                            {{ $materia['docente_nombre'] }}
                        </td>

                        <td data-label="Notas" class="col-centrado">
                            {{ $materia['total_notas'] }}
                        </td>

                        <td data-label="Promedio">
                            @if(!is_null($materia['promedio']))
                                <span class="nota-chip {{ $materia['aprobada'] ? 'aprobada' : 'reprobada' }}">
                                    {{ number_format($materia['promedio'], 2) }}
                                </span>
                            @else
                                <span class="badge-sin-calificar">Sin notas</span>
                            @endif
                        </td>

                        <td data-label="Desempeño">
                            @php
                                $cls = match($materia['estado_academico']) {
                                    'Desempeño Superior' => 'des-superior',
                                    'Desempeño Alto'     => 'des-alto',
                                    'Desempeño Básico'   => 'des-basico',
                                    'Desempeño Bajo'     => 'des-bajo',
                                    default              => 'des-sin',
                                };
                            @endphp
                            <span class="badge-desempeno {{ $cls }}">
                                {{ $materia['estado_academico'] }}
                            </span>
                        </td>

                        <td data-label="Resultado">
                            @if(!is_null($materia['promedio']))
                                @if($materia['aprobada'])
                                    <span class="estado estado-activo">
                                        <i class="fa-solid fa-check"></i> Aprobada
                                    </span>
                                @else
                                    <span class="estado estado-inactivo">
                                        <i class="fa-solid fa-xmark"></i> Reprobada
                                    </span>
                                @endif
                            @else
                                <span class="estado estado-pendiente">Sin calificar</span>
                            @endif
                        </td>

                        <td class="col-acciones">
                            <div class="acciones">
                                <a href="{{ route('admin.academico.notas.index', $materia['inscripcion_materia_id']) }}"
                                   class="btn-icono ver"
                                   title="Ver notas de la materia">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="7">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay materias activas en esta inscripción.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/boletin.js') }}"></script>
@endpush
