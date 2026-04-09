@extends('layouts.menudocente')

@section('title', 'Notas — ' . optional($asignacion->materia)->nombre)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/componentes/academico-index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/notas/notas.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-users-between-lines"></i>
                Notas del Grupo
            </h2>
            <p class="cabecera-subtitulo">
                <strong>{{ optional($asignacion->materia)->nombre ?? '—' }}</strong>
                &nbsp;·&nbsp;
                {{ optional($asignacion->grupo->grado)->nombre ?? '—' }}
                — Grupo {{ optional($asignacion->grupo)->nombre ?? '—' }}
            </p>
        </div>
        <a href="{{ route('docente.notas.index') }}" class="btn btn-neutro btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Mis Notas
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_academico')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Ficha de la asignación ── --}}
    <div class="ficha-asignacion">
        <div class="ficha-dato">
            <span><i class="fa-solid fa-calendar-days"></i> Año Lectivo</span>
            <strong>{{ optional($asignacion->grupo->anioLectivo)->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-book"></i> Materia</span>
            <strong>{{ optional($asignacion->materia)->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-users"></i> Grupo</span>
            <strong>
                {{ optional($asignacion->grupo->grado)->nombre ?? '—' }}
                — {{ optional($asignacion->grupo)->nombre ?? '—' }}
            </strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-user-graduate"></i> Estudiantes</span>
            <strong>{{ $inscripcionMaterias->count() }}</strong>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         Barra bulk — modo 'usuarios'
           1 seleccionado  → Registrar/Editar + Eliminar notas
           2+ seleccionados → solo Eliminar notas
    ══════════════════════════════════════════ --}}
    <div class="barra-bulk"
         data-bulk-modo="usuarios"
         data-entidad="estudiante(s)"
         data-url-editar="{{ route('docente.notas.create', [$asignacion->id, ':id']) }}"
         data-url-destroy="{{ url('docente/notas/' . $asignacion->id) }}/:id/borrar-notas">

        <div class="bulk-info">
            <i class="fa-solid fa-check-square"></i>
            <span class="bulk-contador">0</span>
            seleccionado(s)
        </div>

        <div class="bulk-acciones">
            <a href="#" class="btn-bulk btn-bulk-editar">
                <i class="fa-solid fa-pen"></i> Registrar nota
            </a>
            <button type="button" class="btn-bulk btn-bulk-eliminar">
                <i class="fa-solid fa-trash"></i> Eliminar notas
            </button>
            <div class="bulk-separador"></div>
            <button type="button" class="btn-bulk btn-bulk-limpiar">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </button>
        </div>

    </div>

    {{-- ── Tabla — sin columna Acciones ── --}}
    @if($inscripcionMaterias->isNotEmpty())

        <div class="tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th class="col-check">
                            <input type="checkbox" class="checkbox-todos" title="Seleccionar todos">
                        </th>
                        <th>Estudiante</th>
                        @foreach($periodos as $periodo)
                            <th>
                                {{ $periodo->nombre }}
                                @if(!$periodo->abierto)
                                    <i class="fa-solid fa-lock periodo-lock"
                                       title="Periodo cerrado"></i>
                                @endif
                            </th>
                        @endforeach
                        <th>Promedio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inscripcionMaterias as $im)
                        @php
                            $notasPorPeriodo = $im->notas->keyBy('periodo_id');
                            $notas           = $im->notas->pluck('nota');
                            $promedio        = $notas->isNotEmpty()
                                ? round($notas->average(), 2) : null;
                            $aprobada        = !is_null($promedio) && $promedio >= 3.0;
                        @endphp
                        <tr data-id="{{ $im->id }}" class="fila-seleccionable">

                            <td class="col-check">
                                <input type="checkbox"
                                       class="checkbox-tabla"
                                       data-id="{{ $im->id }}"
                                       title="Seleccionar {{ optional($im->inscripcion->estudiante)->nombre_completo }}">
                            </td>

                            <td data-label="Estudiante">
                                <div class="nombre-estudiante">
                                    {{ optional($im->inscripcion->estudiante)->nombre_completo ?? '—' }}
                                </div>
                                <div class="id-estudiante">
                                    {{ optional($im->inscripcion->estudiante)->identificacion ?? '' }}
                                </div>
                            </td>

                            @foreach($periodos as $periodo)
                                @php $nota = $notasPorPeriodo[$periodo->id] ?? null; @endphp
                                <td data-label="{{ $periodo->nombre }}" class="celda-nota">
                                    @if($nota)
                                        <a href="{{ route('docente.notas.edit', $nota->id) }}"
                                           class="nota-chip {{ $nota->nota >= 3.0 ? 'nota-aprobada' : 'nota-reprobada' }}"
                                           title="Editar nota — {{ $periodo->nombre }}">
                                            {{ number_format($nota->nota, 2) }}
                                        </a>
                                    @elseif($periodo->abierto)
                                        <a href="{{ route('docente.notas.create', [$asignacion->id, $im->id]) }}?periodo={{ $periodo->id }}"
                                           class="nota-sin nota-agregar"
                                           title="Registrar nota — {{ $periodo->nombre }}">
                                            <i class="fa-solid fa-plus"></i>
                                        </a>
                                    @else
                                        <span class="nota-sin">—</span>
                                    @endif
                                </td>
                            @endforeach

                            <td data-label="Promedio">
                                @if(!is_null($promedio))
                                    <span class="promedio-chip {{ $aprobada ? 'promedio-aprobado' : 'promedio-reprobado' }}">
                                        {{ number_format($promedio, 2) }}
                                    </span>
                                @else
                                    <span class="promedio-chip promedio-sin">—</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr class="fila-vacia">
                            <td colspan="{{ $periodos->count() + 3 }}">
                                <i class="fa-solid fa-circle-info"></i>
                                No hay estudiantes inscritos en esta asignación.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @else
        <div class="sin-registros">
            <i class="fa-solid fa-circle-info"></i>
            No hay estudiantes inscritos en esta asignación.
        </div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/docente/notas.js') }}"></script>
@endpush
