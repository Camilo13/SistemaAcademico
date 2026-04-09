@extends('layouts.menuestudiante')
@section('title', 'Mis Faltas — ' . $periodo->nombre)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/estudiante/asistencia/periodo.css') }}">
@endpush

@section('content')
<div class="contenedor-asistencia-periodo">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-calendar-check"></i> Mis Faltas</h2>
            <p class="cabecera-subtitulo">
                {{ $periodo->nombre }}
                &nbsp;·&nbsp;
                {{ $anioActivo->nombre ?? '' }}
                <span class="estado {{ $periodo->estaAbierto() ? 'estado-abierto' : 'estado-cerrado' }}"
                      style="margin-left:0.4rem;">
                    {{ $periodo->estaAbierto() ? 'Abierto' : 'Cerrado' }}
                </span>
            </p>
        </div>
        <a href="{{ route('estudiante.asistencia.index') }}"
           class="btn btn-neutro btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Resumen anual
        </a>
    </div>

    {{-- Navegación entre periodos --}}
    <div class="nav-periodos">
        <a href="{{ route('estudiante.asistencia.index') }}" class="tab">Anual</a>
        @foreach($periodos as $p)
            <a href="{{ route('estudiante.asistencia.periodo', $p->id) }}"
               class="tab {{ $p->id === $periodo->id ? 'activo' : '' }}">
                {{ $p->nombre }}
            </a>
        @endforeach
    </div>

    {{-- Tabla de faltas del periodo --}}
    @if($inscripcion)
        @php
            $materiasActivas = $inscripcion->inscripcionMaterias->where('estado', 'activa');
        @endphp

        @if($materiasActivas->isNotEmpty())
            <div class="tabla-contenedor">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Docente</th>
                            <th class="text-center">Justificadas</th>
                            <th class="text-center">Injustificadas</th>
                            <th class="text-center">Total</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materiasActivas as $im)
                            @php
                                $reg   = $im->asistencias->first();
                                $total = $reg ? $reg->totalFaltas() : null;
                                $clase = is_null($total) ? 'faltas-sin'
                                    : ($total === 0 ? 'faltas-ok'
                                    : ($total <= 3 ? 'faltas-bajo' : 'faltas-alto'));
                            @endphp
                            <tr>
                                <td data-label="Materia">
                                    {{ $im->asignacion->materia->nombre ?? '—' }}
                                </td>
                                <td data-label="Docente">
                                    {{ $im->asignacion->docente->nombre_completo ?? '—' }}
                                </td>
                                <td data-label="Justificadas" class="text-center">
                                    <span class="faltas-chip {{ $reg ? ($reg->faltas_justificadas > 0 ? 'faltas-bajo' : 'faltas-ok') : 'faltas-sin' }}">
                                        {{ $reg ? $reg->faltas_justificadas : '—' }}
                                    </span>
                                </td>
                                <td data-label="Injustificadas" class="text-center">
                                    <span class="faltas-chip {{ $reg ? ($reg->faltas_injustificadas > 0 ? 'faltas-alto' : 'faltas-ok') : 'faltas-sin' }}">
                                        {{ $reg ? $reg->faltas_injustificadas : '—' }}
                                    </span>
                                </td>
                                <td data-label="Total" class="text-center">
                                    @php
                                        $ct = is_null($total) ? 'total-sin'
                                            : ($total === 0 ? 'total-ok'
                                            : ($total <= 3 ? 'total-medio' : 'total-alto'));
                                    @endphp
                                    <span class="total-chip {{ $ct }}">
                                        {{ is_null($total) ? '—' : $total }}
                                    </span>
                                </td>
                                <td data-label="Observación">
                                    {{ $reg?->observacion ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="sin-registros">No tienes materias activas en este periodo.</div>
        @endif

    @else
        <div class="sin-registros">
            No tienes una inscripción activa en el año lectivo vigente.
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/estudiante/asistencia.js') }}"></script>
@endpush
