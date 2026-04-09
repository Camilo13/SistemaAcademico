@extends('layouts.menudocente')
@section('title', 'Editar Faltas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/asistencia/edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/formularios.css') }}">
@endpush

@section('content')
<div class="contenedor-editar-asistencia">

    {{-- Cabecera --}}
    @php
        $asignacion  = $asistencia->inscripcionMateria->asignacion;
        $estudiante  = $asistencia->inscripcionMateria->inscripcion->estudiante;
        $periodoAbierto = $asistencia->periodo && $asistencia->periodo->estaAbierto();
    @endphp
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-pen-to-square"></i> Editar Faltas</h2>
            <p class="cabecera-subtitulo">
                {{ $asistencia->periodo->nombre ?? '—' }}
                &nbsp;·&nbsp;
                {{ $asignacion->materia->nombre ?? '—' }}
            </p>
        </div>
        <a href="{{ route('docente.asistencia.estudiantes', $asignacion->id) }}"
           class="btn btn-neutro btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    {{-- Error académico --}}
    @if($errors->has('error_academico'))
        <div class="alerta-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ $errors->first('error_academico') }}
        </div>
    @endif

    {{-- Aviso periodo cerrado --}}
    @if(!$periodoAbierto)
        <div class="aviso-cerrado">
            <i class="fa-solid fa-lock"></i>
            Este periodo está <strong>cerrado</strong>. No se pueden modificar los registros de asistencia.
        </div>
    @endif

    {{-- Ficha de contexto --}}
    <div class="ficha-contexto">
        <div class="ficha-dato">
            <span><i class="fa-solid fa-user"></i> Estudiante</span>
            <strong>{{ $estudiante->nombre_completo ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-book"></i> Materia</span>
            <strong>{{ $asignacion->materia->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-calendar-days"></i> Periodo</span>
            <strong>{{ $asistencia->periodo->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-users"></i> Grupo</span>
            <strong>{{ $asignacion->grupo->nombre ?? '—' }}</strong>
        </div>
    </div>

    {{-- Registro actual --}}
    <div class="info-registro">
        <span class="info-registro-label">Registro actual:</span>
        <div class="faltas-actuales">
            <div class="falta-badge">
                <small>Justificadas</small>
                <strong>{{ $asistencia->faltas_justificadas }}</strong>
            </div>
            <div class="falta-badge">
                <small>Injustificadas</small>
                <strong>{{ $asistencia->faltas_injustificadas }}</strong>
            </div>
            <div class="falta-badge">
                <small>Total</small>
                <strong>{{ $asistencia->totalFaltas() }}</strong>
            </div>
        </div>
    </div>

    {{-- Formulario --}}
    <form method="POST" action="{{ route('docente.asistencia.update', $asistencia->id) }}">
        @csrf
        @method('PUT')

        <div class="formulario-asistencia">

            {{-- Faltas --}}
            <div class="campos-faltas">
                <div class="campo">
                    <label for="faltas_justificadas">
                        <i class="fa-solid fa-circle-check"></i>
                        Faltas justificadas *
                    </label>
                    <input type="number"
                           id="faltas_justificadas"
                           name="faltas_justificadas"
                           min="0" max="999"
                           value="{{ old('faltas_justificadas', $asistencia->faltas_justificadas) }}"
                           required
                           {{ !$periodoAbierto ? 'disabled' : '' }}>
                    @error('faltas_justificadas')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                <div class="campo">
                    <label for="faltas_injustificadas">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Faltas injustificadas *
                    </label>
                    <input type="number"
                           id="faltas_injustificadas"
                           name="faltas_injustificadas"
                           min="0" max="999"
                           value="{{ old('faltas_injustificadas', $asistencia->faltas_injustificadas) }}"
                           required
                           {{ !$periodoAbierto ? 'disabled' : '' }}>
                    @error('faltas_injustificadas')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Total en tiempo real --}}
            <div class="resumen-faltas">
                <i class="fa-solid fa-sigma"></i>
                Total de faltas:
                <span id="total-faltas-preview">{{ $asistencia->totalFaltas() }}</span>
            </div>

            {{-- Observación --}}
            <div class="campo">
                <label for="observacion">
                    <i class="fa-solid fa-comment-dots"></i>
                    Observación
                    <small class="texto-tenue">(opcional)</small>
                </label>
                <textarea name="observacion"
                          id="observacion"
                          rows="3"
                          maxlength="1000"
                          {{ !$periodoAbierto ? 'disabled' : '' }}>{{ old('observacion', $asistencia->observacion) }}</textarea>
                @error('observacion')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            {{-- Acciones --}}
            @if($periodoAbierto)
                <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primario">
                        <i class="fa-solid fa-floppy-disk"></i> Actualizar
                    </button>
                    <a href="{{ route('docente.asistencia.estudiantes', $asignacion->id) }}"
                       class="btn btn-neutro">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </a>
                </div>
            @else
                <a href="{{ route('docente.asistencia.estudiantes', $asignacion->id) }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-arrow-left"></i> Volver al listado
                </a>
            @endif

        </div>
    </form>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/docente/asistencia.js') }}"></script>
@endpush
