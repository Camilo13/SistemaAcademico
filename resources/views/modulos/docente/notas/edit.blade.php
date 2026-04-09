@extends('layouts.menudocente')

@section('title', 'Editar Nota')

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
                <i class="fa-solid fa-pen-to-square"></i>
                Editar Nota
            </h2>
            <p class="cabecera-subtitulo">
                <strong>{{ optional($nota->inscripcionMateria->asignacion->materia)->nombre ?? '—' }}</strong>
                &nbsp;·&nbsp;
                {{ optional($nota->inscripcionMateria->inscripcion->estudiante)->nombre_completo ?? '—' }}
                &nbsp;·&nbsp;
                {{ optional($nota->periodo)->nombre ?? '—' }}
            </p>
        </div>
    </div>

    {{-- ── Ficha de contexto ── --}}
    <div class="ficha-contexto">
        <div class="ficha-dato">
            <span><i class="fa-solid fa-user-graduate"></i> Estudiante</span>
            <strong>{{ optional($nota->inscripcionMateria->inscripcion->estudiante)->nombre_completo ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-book"></i> Materia</span>
            <strong>{{ optional($nota->inscripcionMateria->asignacion->materia)->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-layer-group"></i> Periodo</span>
            <strong>{{ optional($nota->periodo)->nombre ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-star"></i> Nota actual</span>
            <strong class="{{ $nota->nota >= 3.0 ? 'texto-aprobado' : 'texto-reprobado' }}">
                {{ number_format($nota->nota, 2) }}
            </strong>
        </div>
    </div>

    {{-- ── Formulario ── --}}
    <div class="tarjeta-form">

        @if(!optional($nota->periodo)->abierto)
            <div class="alerta-advertencia">
                <i class="fa-solid fa-lock"></i>
                El periodo está <strong>cerrado</strong>. No se pueden modificar las notas.
            </div>
        @endif

        <form method="POST"
              action="{{ route('docente.notas.update', $nota->id) }}"
              data-form="nota-docente">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Periodo (solo lectura) ── --}}
                <div class="campo">
                    <label>
                        <i class="fa-solid fa-layer-group"></i>
                        Periodo
                    </label>
                    <input type="text"
                           value="{{ optional($nota->periodo)->nombre ?? '—' }}"
                           disabled>
                    <span class="nota-campo">El periodo no se puede modificar.</span>
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
                           value="{{ old('nota', $nota->nota) }}"
                           required
                           {{ !optional($nota->periodo)->abierto ? 'disabled' : '' }}
                           autocomplete="off">
                    <span class="nota-campo">Rango válido: 0.00 a 5.00 · Aprobado ≥ 3.00</span>
                    @error('nota')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Preview de desempeño ── --}}
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
                              {{ !optional($nota->periodo)->abierto ? 'disabled' : '' }}>{{ old('observacion', $nota->observacion) }}</textarea>
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
                <a href="{{ route('docente.notas.estudiantes', $nota->inscripcionMateria->asignacion->id) }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                @if(optional($nota->periodo)->abierto)
                    <button type="submit" class="btn btn-primario">
                        <i class="fa-solid fa-floppy-disk"></i> Actualizar
                    </button>
                @endif
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/docente/notas.js') }}"></script>
@endpush
