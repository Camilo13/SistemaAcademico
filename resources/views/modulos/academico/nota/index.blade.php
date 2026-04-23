@extends('layouts.menuadmin')

@section('title', 'Notas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/nota/nota.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-star-half-stroke"></i>
                Notas
            </h2>
            <p class="cabecera-subtitulo">
                <strong>{{ optional($inscripcionMateria->asignacion->materia)->nombre ?? '—' }}</strong>
                &nbsp;·&nbsp;
                {{ $inscripcionMateria->asignacion->docente->nombre_completo ?? '—' }}
                &nbsp;·&nbsp;
                {{ optional($inscripcionMateria->inscripcion->estudiante)->nombre_completo ?? '—' }}
            </p>
        </div>
        <div class="nota-cabecera-acciones">
            <a href="{{ route('admin.academico.inscripciones.materias.index', $inscripcionMateria->inscripcion_id) }}"
               class="btn btn-neutro btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Materias
            </a>
            @if($inscripcionMateria->estaActiva())
                <a href="{{ route('admin.academico.notas.create', $inscripcionMateria->id) }}"
                   class="btn btn-primario">
                    <i class="fa-solid fa-plus"></i> Nueva Nota
                </a>
            @endif
        </div>
    </div>

    {{-- ── Error general ── --}}
    @error('error_academico')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Ficha de contexto ── --}}
    <div class="nota-ficha">
        <div class="nota-ficha-item">
            <span><i class="fa-solid fa-user-graduate"></i> Estudiante</span>
            <strong>{{ optional($inscripcionMateria->inscripcion->estudiante)->nombre_completo ?? '—' }}</strong>
        </div>
        <div class="nota-ficha-item">
            <span><i class="fa-solid fa-book"></i> Materia</span>
            <strong>{{ optional($inscripcionMateria->asignacion->materia)->nombre ?? '—' }}</strong>
        </div>
        <div class="nota-ficha-item">
            <span><i class="fa-solid fa-users"></i> Grupo</span>
            <strong>{{ optional($inscripcionMateria->inscripcion->grupo)->nombre ?? '—' }}</strong>
        </div>
        <div class="nota-ficha-item">
            <span><i class="fa-solid fa-circle-check"></i> Estado materia</span>
            @if($inscripcionMateria->estaActiva())
                <span class="estado estado-activo">
                    <i class="fa-solid fa-circle-check"></i> Activa
                </span>
            @else
                <span class="estado estado-inactivo">
                    <i class="fa-solid fa-circle-xmark"></i> Retirada
                </span>
            @endif
        </div>
    </div>

    {{-- ── Tabla de notas ── --}}
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Periodo</th>
                    <th>Nota</th>
                    <th>Desempeño</th>
                    <th>Observación</th>
                    <th class="col-acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notas as $nota)
                    @php
                        $val = (float) $nota->nota;
                        [$desempenoLabel, $desempenoClase] = match(true) {
                            $val >= 4.6 => ['Superior',   'desempeno-superior'],
                            $val >= 4.0 => ['Alto',        'desempeno-alto'],
                            $val >= 3.0 => ['Básico',      'desempeno-basico'],
                            default     => ['Bajo',        'desempeno-bajo'],
                        };
                    @endphp
                    <tr>
                        <td data-label="Periodo">
                            {{ optional($nota->periodo)->nombre ?? '—' }}
                            @if(optional($nota->periodo)->estaCerrado())
                                <span class="nota-periodo-cerrado">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                            @endif
                        </td>

                        <td data-label="Nota" class="celda-nota">
                            <span class="nota-valor {{ $val >= 3.0 ? 'nota-aprobada' : 'nota-reprobada' }}">
                                {{ number_format($val, 1) }}
                            </span>
                        </td>

                        <td data-label="Desempeño">
                            <span class="desempeno-badge {{ $desempenoClase }}">
                                {{ $desempenoLabel }}
                            </span>
                        </td>

                        <td data-label="Observación" class="celda-obs">
                            {{ $nota->observacion ?: '—' }}
                        </td>

                        <td class="col-acciones">
                            <div class="acciones">
                                <a href="{{ route('admin.academico.notas.edit', $nota->id) }}"
                                   class="btn-icono editar"
                                   title="Editar nota">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="5">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay notas registradas para esta materia.
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
    <script src="{{ asset('js/modulos/academico/nota.js') }}"></script>
@endpush
