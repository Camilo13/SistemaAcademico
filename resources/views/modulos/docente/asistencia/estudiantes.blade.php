@extends('layouts.menudocente')
@section('title', 'Asistencia — ' . ($asignacion->materia->nombre ?? 'Grupo'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/asistencia/estudiantes.css') }}">
@endpush

@section('content')
<div class="contenedor-asistencia-est">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2>
                <i class="fa-solid fa-calendar-check"></i>
                Asistencia — {{ $asignacion->materia->nombre ?? '—' }}
            </h2>
            <p class="cabecera-subtitulo">
                {{ $asignacion->grupo->grado->nombre ?? '—' }}
                — Grupo {{ $asignacion->grupo->nombre ?? '—' }}
                &nbsp;·&nbsp;
                {{ $asignacion->grupo->anioLectivo->nombre ?? '—' }}
            </p>
        </div>
        <a href="{{ route('docente.asistencia.index') }}"
           class="btn btn-neutro btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    {{-- Ficha de la asignación --}}
    <div class="ficha-asignacion">
        <div class="ficha-dato">
            <span><i class="fa-solid fa-book"></i> Materia</span>
            <strong>{{ $asignacion->materia->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-users"></i> Grupo</span>
            <strong>{{ $asignacion->grupo->grado->nombre ?? '—' }} – {{ $asignacion->grupo->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-calendar-days"></i> Año lectivo</span>
            <strong>{{ $asignacion->grupo->anioLectivo->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-user-graduate"></i> Estudiantes</span>
            <strong>{{ $inscripcionMaterias->count() }}</strong>
        </div>
    </div>

    {{-- Tabla principal --}}
    @if($inscripcionMaterias->isNotEmpty())

        <div class="tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th class="col-estudiante">Estudiante</th>
                        @foreach($periodos as $periodo)
                            <th>
                                {{ $periodo->nombre }}
                                @if($periodo->estaCerrado())
                                    <span class="badge-cerrado">Cerrado</span>
                                @endif
                            </th>
                        @endforeach
                        <th>Total</th>
                        <th class="col-acciones">Registrar falta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscripcionMaterias as $im)
                        @php
                            $estudiante   = $im->inscripcion->estudiante;
                            $asistencias  = $im->asistencias->keyBy('periodo_id');
                            $totalAnual   = $im->asistencias->sum(fn($a) => $a->faltas_justificadas + $a->faltas_injustificadas);
                        @endphp
                        <tr>
                            {{-- Nombre --}}
                            <td data-label="Estudiante">
                                <div class="nombre-estudiante">
                                    {{ $estudiante->nombre_completo ?? '—' }}
                                </div>
                                <div class="id-estudiante">
                                    {{ $estudiante->identificacion ?? '' }}
                                </div>
                            </td>

                            {{-- Faltas por periodo --}}
                            @foreach($periodos as $periodo)
                                @php
                                    $reg   = $asistencias->get($periodo->id);
                                    $total = $reg ? $reg->totalFaltas() : null;
                                    $clase = is_null($total) ? 'faltas-sin'
                                        : ($total === 0 ? 'faltas-ok'
                                        : ($total <= 3 ? 'faltas-bajo' : 'faltas-alto'));
                                @endphp
                                <td data-label="{{ $periodo->nombre }}" class="text-center">
                                    @if($reg)
                                        <span class="faltas-chip {{ $clase }}"
                                              title="Justificadas: {{ $reg->faltas_justificadas }} | Injustificadas: {{ $reg->faltas_injustificadas }}">
                                            {{ $total }}
                                        </span>
                                    @else
                                        <span class="faltas-chip faltas-sin">—</span>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Total anual --}}
                            <td data-label="Total" class="text-center">
                                @php
                                    $claseTotal = $totalAnual === 0 ? 'total-ok'
                                        : ($totalAnual <= 5 ? 'total-medio' : 'total-alto');
                                    if ($im->asistencias->isEmpty()) $claseTotal = 'total-sin';
                                @endphp
                                <span class="total-chip {{ $claseTotal }}">
                                    {{ $im->asistencias->isEmpty() ? '—' : $totalAnual }}
                                </span>
                            </td>

                            {{-- Acciones --}}
                            <td class="col-acciones" data-label="Acciones">
                                <div class="acciones">
                                    {{-- Registrar falta en nuevo periodo --}}
                                    <a href="{{ route('docente.asistencia.create', [$asignacion->id, $im->id]) }}"
                                       class="btn-icono activar"
                                       title="Registrar faltas">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>

                                    {{-- Editar cada registro existente --}}
                                    @foreach($asistencias as $reg)
                                        <a href="{{ route('docente.asistencia.edit', $reg->id) }}"
                                           class="btn-icono editar"
                                           title="Editar {{ $reg->periodo->nombre ?? 'período' }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endforeach

                                    {{-- Eliminar cada registro --}}
                                    @foreach($asistencias as $reg)
                                        @if($reg->periodo && $reg->periodo->estaAbierto())
                                            <form method="POST"
                                                  action="{{ route('docente.asistencia.destroy', $reg->id) }}"
                                                  class="form-eliminar">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn-icono eliminar"
                                                        title="Eliminar {{ $reg->periodo->nombre ?? 'período' }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @else
        <div class="sin-registros">
            No hay estudiantes activos inscritos en esta materia.
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/docente/asistencia.js') }}"></script>
@endpush