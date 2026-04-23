@extends('layouts.menudocente')
@section('title', 'Resumen del grupo — ' . optional($grupo->grado)->nombre . ' ' . $grupo->nombre)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/notas/notas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/grupos/resumen.css') }}">
@endpush

@section('content')
<div class="contenedor-resumen-grupo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-chart-bar"></i>
                Resumen del Grupo
            </h2>
            <p class="cabecera-subtitulo">
                {{ optional($grupo->grado)->nombre }} — Grupo {{ $grupo->nombre }}
                &nbsp;·&nbsp;
                <i class="fa-solid fa-calendar-days"></i>
                {{ optional($grupo->anioLectivo)->nombre }}
            </p>
        </div>
        <a href="{{ route('docente.grupos.index') }}" class="btn btn-neutro btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Mis Grupos
        </a>
    </div>

    {{-- ── Panel de estadísticas ── --}}
    <div class="stats-panel">
        <div class="stat-card">
            <div class="stat-numero">{{ $stats['total'] }}</div>
            <div class="stat-label">
                <i class="fa-solid fa-users"></i> Total estudiantes
            </div>
        </div>
        <div class="stat-card stat-aprobado">
            <div class="stat-numero">{{ $stats['aprobados'] }}</div>
            <div class="stat-label">
                <i class="fa-solid fa-circle-check"></i> Aprobados
            </div>
        </div>
        <div class="stat-card stat-reprobado">
            <div class="stat-numero">{{ $stats['reprobados'] }}</div>
            <div class="stat-label">
                <i class="fa-solid fa-circle-xmark"></i> En riesgo
            </div>
        </div>
        <div class="stat-card stat-pendiente">
            <div class="stat-numero">{{ $stats['sin_calificar'] }}</div>
            <div class="stat-label">
                <i class="fa-solid fa-clock"></i> Sin calificar
            </div>
        </div>
    </div>

    {{-- ── Tabla de estudiantes ── --}}
    @if($estudiantes->isEmpty())
        <div class="sin-registros">
            <i class="fa-solid fa-inbox"></i>
            No hay estudiantes inscritos activamente en este grupo.
        </div>
    @else
        <div class="tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th class="col-centrado">Promedio</th>
                        <th class="col-centrado">Aprobadas</th>
                        <th class="col-centrado">Reprobadas</th>
                        <th class="col-centrado">Sin calificar</th>
                        <th>Estado anual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estudiantes as $est)
                        <tr class="{{ $est['reprobadas'] > 0 ? 'fila-riesgo' : '' }}">

                            <td data-label="Estudiante">
                                <div class="nombre-estudiante">
                                    {{ $est['nombre'] }}
                                </div>
                                <div class="id-estudiante">
                                    {{ $est['identificacion'] }}
                                </div>
                            </td>

                            <td data-label="Promedio" class="col-centrado">
                                @if(!is_null($est['promedio']))
                                    <span class="promedio-chip {{ $est['aprobado'] ? 'promedio-aprobado' : 'promedio-reprobado' }}">
                                        {{ number_format($est['promedio'], 2) }}
                                    </span>
                                @else
                                    <span class="promedio-chip promedio-sin">—</span>
                                @endif
                            </td>

                            <td data-label="Aprobadas" class="col-centrado">
                                <span class="conteo-chip conteo-aprobadas">
                                    {{ $est['aprobadas'] }}
                                </span>
                            </td>

                            <td data-label="Reprobadas" class="col-centrado">
                                @if($est['reprobadas'] > 0)
                                    <span class="conteo-chip conteo-reprobadas">
                                        {{ $est['reprobadas'] }}
                                    </span>
                                @else
                                    <span class="conteo-chip conteo-ok">0</span>
                                @endif
                            </td>

                            <td data-label="Sin calificar" class="col-centrado">
                                @if($est['sin_calificar'] > 0)
                                    <span class="conteo-chip conteo-pendiente">
                                        {{ $est['sin_calificar'] }}
                                    </span>
                                @else
                                    <span class="conteo-chip conteo-ok">0</span>
                                @endif
                            </td>

                            <td data-label="Estado">
                                @if(is_null($est['promedio']))
                                    <span class="estado estado-pendiente">
                                        <i class="fa-solid fa-clock"></i> Sin calificar
                                    </span>
                                @elseif($est['aprobado'])
                                    <span class="estado estado-activo">
                                        <i class="fa-solid fa-check"></i> Aprobado
                                    </span>
                                @else
                                    <span class="estado estado-inactivo">
                                        <i class="fa-solid fa-xmark"></i> En riesgo
                                    </span>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection