@extends('layouts.menudocente')

@section('title', 'Registrar Nota')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/componentes/academico-form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/notas/notas.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-star-half-stroke"></i>
                Registrar Nota
            </h2>
            <p class="cabecera-subtitulo">
                <strong>{{ optional($asignacion->materia)->nombre ?? '—' }}</strong>
                &nbsp;·&nbsp;
                {{ optional($inscripcionMateria->inscripcion->estudiante)->nombre_completo ?? '—' }}
            </p>
        </div>
    </div>

    {{-- ── Ficha de contexto ── --}}
    <div class="ficha-contexto">
        <div class="ficha-dato">
            <span><i class="fa-solid fa-user-graduate"></i> Estudiante</span>
            <strong>{{ optional($inscripcionMateria->inscripcion->estudiante)->nombre_completo ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-id-card"></i> Identificación</span>
            <strong>{{ optional($inscripcionMateria->inscripcion->estudiante)->identificacion ?? '—' }}</strong>
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
    </div>

    {{-- ── Formulario ── --}}
    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('docente.notas.store', [$asignacion->id, $inscripcionMateria->id]) }}"
              data-form="nota-docente">
            @csrf

            <div class="grid-campos">

                {{-- Periodo ── --}}
                <div class="campo">
                    <label for="periodo_id">
                        <i class="fa-solid fa-layer-group"></i>
                        Periodo <span>*</span>
                    </label>
                    <select id="periodo_id" name="periodo_id" required>
                        <option value="">Seleccione un periodo</option>
                        @foreach($periodos as $periodo)
                            <option value="{{ $periodo->id }}"
                                {{ old('periodo_id', request('periodo')) == $periodo->id ? 'selected' : '' }}
                                {{ (!$periodo->abierto || $notasExistentes->contains($periodo->id)) ? 'disabled' : '' }}>
                                {{ $periodo->nombre }}
                                @if(!$periodo->abierto)
                                    (Cerrado)
                                @elseif($notasExistentes->contains($periodo->id))
                                    (Ya registrada)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('periodo_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Nota ── --}}
                <div class="campo">
                    <label for="nota">
                        <i class="fa-solid fa-star"></i>
                        Nota <span>*</span>
                    </label>
                    <input type="number"
                           id="nota" name="nota"
                           min="0" max="5" step="0.01"
                           value="{{ old('nota') }}"
                           placeholder="0.00 – 5.00"
                           required autocomplete="off">
                    <span class="nota-campo">
                        Rango válido: 0.00 a 5.00 · Aprobado ≥ 3.00
                    </span>
                    @error('nota')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Preview de desempeño — llenado por JS ── --}}
                <div class="campo campo-ancho">
                    <div id="preview-nota" class="preview-nota"></div>
                </div>

                {{-- Observación ── --}}
                <div class="campo campo-ancho">
                    <label for="observacion">
                        <i class="fa-solid fa-comment-dots"></i>
                        Observación
                    </label>
                    <textarea id="observacion" name="observacion"
                              rows="3" maxlength="1000"
                              placeholder="Observaciones académicas opcionales…">{{ old('observacion') }}</textarea>
                    @error('observacion')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            @error('error_academico')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('docente.notas.estudiantes', $asignacion->id) }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Nota
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/docente/notas.js') }}"></script>
@endpush
