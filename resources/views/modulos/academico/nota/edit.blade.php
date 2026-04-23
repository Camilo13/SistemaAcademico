@extends('layouts.menuadmin')

@section('title', 'Editar Nota')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/nota/nota.css') }}">
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

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — editar nota
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        {{-- Ficha de contexto ── --}}
        <div class="nota-ficha">
            <div class="nota-ficha-item">
                <span><i class="fa-solid fa-user-graduate"></i> Estudiante</span>
                <strong>{{ optional($nota->inscripcionMateria->inscripcion->estudiante)->nombre_completo ?? '—' }}</strong>
            </div>
            <div class="nota-ficha-item">
                <span><i class="fa-solid fa-book"></i> Materia</span>
                <strong>{{ optional($nota->inscripcionMateria->asignacion->materia)->nombre ?? '—' }}</strong>
            </div>
            <div class="nota-ficha-item">
                <span><i class="fa-solid fa-calendar-days"></i> Periodo</span>
                <strong>{{ optional($nota->periodo)->nombre ?? '—' }}</strong>
            </div>
            <div class="nota-ficha-item">
                <span><i class="fa-solid fa-star"></i> Nota actual</span>
                @php $valActual = (float) $nota->nota; @endphp
                <span class="nota-valor {{ $valActual >= 3.0 ? 'nota-aprobada' : 'nota-reprobada' }}">
                    {{ number_format($valActual, 1) }}
                </span>
            </div>
        </div>

        {{-- Bloqueo si periodo cerrado ── --}}
        @if(optional($nota->periodo)->estaCerrado())
            <div class="alerta-advertencia">
                <i class="fa-solid fa-lock"></i>
                El periodo está <strong>cerrado</strong>. No es posible modificar esta nota.
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.academico.notas.update', $nota->id) }}"
              data-form="nota">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Periodo (solo lectura) ── --}}
                <div class="campo">
                    <label>
                        <i class="fa-solid fa-calendar-days"></i>
                        Periodo
                    </label>
                    <input type="text"
                           value="{{ optional($nota->periodo)->nombre ?? '—' }}"
                           disabled>
                    <span class="nota-campo">El periodo no se puede cambiar.</span>
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
                           {{ optional($nota->periodo)->estaCerrado() ? 'disabled' : '' }}
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
                              {{ optional($nota->periodo)->estaCerrado() ? 'disabled' : '' }}>{{ old('observacion', $nota->observacion) }}</textarea>
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
                <a href="{{ route('admin.academico.notas.index', $nota->inscripcion_materia_id) }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                @if(optional($nota->periodo)->estaAbierto())
                    <button type="submit" class="btn btn-primario">
                        <i class="fa-solid fa-floppy-disk"></i> Actualizar
                    </button>
                @endif
            </div>

        </form>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PELIGRO — Eliminar nota
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-peligro">

        <h3 class="seccion-titulo-form peligro">
            <i class="fa-solid fa-triangle-exclamation"></i> Zona de peligro
        </h3>

        @php
            $periodoAbierto = optional($nota->periodo)->estaAbierto();
        @endphp

        <p class="seccion-desc">
            Eliminar esta nota es una acción <strong>permanente</strong>.
            Solo es posible si el periodo está abierto.
            @if(!$periodoAbierto)
                <br>
                <span class="texto-peligro">
                    <i class="fa-solid fa-circle-xmark"></i>
                    El periodo está cerrado y no permite eliminación.
                </span>
            @endif
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.academico.notas.destroy', $nota->id) }}"
                  class="form-eliminar"
                  data-nombre="Nota {{ optional($nota->periodo)->nombre }} — {{ number_format($valActual, 1) }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="btn btn-peligro"
                        {{ !$periodoAbierto ? 'disabled' : '' }}>
                    <i class="fa-solid fa-trash"></i> Eliminar nota
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/nota.js') }}"></script>
@endpush