@extends('layouts.menudocente')
@section('title', 'Registrar Faltas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/asistencia/create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/formularios.css') }}">
@endpush

@section('content')
<div class="contenedor-crear-asistencia">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-calendar-plus"></i> Registrar Faltas</h2>
            <p class="cabecera-subtitulo">
                Ingresa las faltas del periodo seleccionado para este estudiante
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

    {{-- Ficha del estudiante --}}
    @php $estudiante = $inscripcionMateria->inscripcion->estudiante; @endphp
    <div class="ficha-contexto">
        <div class="ficha-dato">
            <span><i class="fa-solid fa-user"></i> Estudiante</span>
            <strong>{{ $estudiante->nombre_completo ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-id-card"></i> Identificación</span>
            <strong>{{ $estudiante->identificacion ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-book"></i> Materia</span>
            <strong>{{ $asignacion->materia->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-users"></i> Grupo</span>
            <strong>{{ $asignacion->grupo->grado->nombre ?? '—' }} – {{ $asignacion->grupo->nombre ?? '—' }}</strong>
        </div>
    </div>

    {{-- Formulario --}}
    <form method="POST"
          action="{{ route('docente.asistencia.store', [$asignacion->id, $inscripcionMateria->id]) }}">
        @csrf

        <div class="formulario-asistencia">

            {{-- Periodo --}}
            <div class="campo">
                <label for="periodo_id">
                    <i class="fa-solid fa-calendar-days"></i>
                    Periodo *
                </label>
                <select name="periodo_id" id="periodo_id" required>
                    <option value="">— Selecciona un periodo —</option>
                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo->id }}"
                            {{ old('periodo_id') == $periodo->id ? 'selected' : '' }}
                            {{ $periodosConRegistro->contains($periodo->id) ? 'disabled' : '' }}>
                            {{ $periodo->nombre }}
                            @if($periodo->estaCerrado()) (Cerrado) @endif
                            @if($periodosConRegistro->contains($periodo->id)) (Ya registrado) @endif
                        </option>
                    @endforeach
                </select>
                @error('periodo_id')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

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
                           value="{{ old('faltas_justificadas', 0) }}"
                           required>
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
                           value="{{ old('faltas_injustificadas', 0) }}"
                           required>
                    @error('faltas_injustificadas')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Total en tiempo real --}}
            <div class="resumen-faltas">
                <i class="fa-solid fa-sigma"></i>
                Total de faltas:
                <span id="total-faltas-preview">0</span>
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
                          placeholder="Notas adicionales sobre la asistencia…">{{ old('observacion') }}</textarea>
                @error('observacion')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            {{-- Acciones --}}
            <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>
                <a href="{{ route('docente.asistencia.estudiantes', $asignacion->id) }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/docente/asistencia.js') }}"></script>
@endpush
