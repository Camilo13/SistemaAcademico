@extends('layouts.menuadmin')

@section('title', 'Nueva Nota')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/nota/nota.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-star-half-stroke"></i>
                Nueva Nota
            </h2>
            <p class="cabecera-subtitulo">
                <strong>{{ optional($inscripcionMateria->asignacion->materia)->nombre ?? '—' }}</strong>
                &nbsp;·&nbsp;
                {{ optional($inscripcionMateria->inscripcion->estudiante)->nombre_completo ?? '—' }}
            </p>
        </div>
    </div>

    <div class="tarjeta-form">

        {{-- Ficha de contexto ── --}}
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
        </div>

        <form method="POST"
              action="{{ route('admin.academico.notas.store', $inscripcionMateria->id) }}"
              data-form="nota">
            @csrf
            <input type="hidden" name="inscripcion_materia_id" value="{{ $inscripcionMateria->id }}">

            <div class="grid-campos">

                {{-- Periodo ── --}}
                <div class="campo">
                    <label for="periodo_id">
                        <i class="fa-solid fa-calendar-days"></i>
                        Periodo <span>*</span>
                    </label>
                    <select id="periodo_id" name="periodo_id" required>
                        <option value="">Seleccione un periodo</option>
                        @foreach($periodos as $periodo)
                            <option value="{{ $periodo->id }}"
                                {{ old('periodo_id') == $periodo->id ? 'selected' : '' }}
                                {{ $periodo->estaCerrado() ? 'disabled' : '' }}>
                                {{ $periodo->nombre }}
                                @if($periodo->estaCerrado())
                                    (Cerrado)
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
                           placeholder="0.0 — 5.0"
                           required
                           autocomplete="off">
                    <span class="nota-campo">Rango: 0.0 a 5.0 · Aprobado ≥ 3.0</span>
                    @error('nota')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Observación ── --}}
                <div class="campo campo-ancho">
                    <label for="observacion">
                        <i class="fa-solid fa-comment-dots"></i>
                        Observación
                    </label>
                    <textarea id="observacion" name="observacion"
                              rows="3" maxlength="1000"
                              placeholder="Observaciones académicas o de comportamiento…">{{ old('observacion') }}</textarea>
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
                <a href="{{ route('admin.academico.notas.index', $inscripcionMateria->id) }}"
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
    <script src="{{ asset('js/modulos/academico/nota.js') }}"></script>
@endpush