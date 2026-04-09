@extends('layouts.menuadmin')

@section('title', 'Editar Periodo — ' . $periodo->nombre)

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-pen-to-square"></i>
                Editar Periodo
            </h2>
            <p class="cabecera-subtitulo">
                {{ $anioLectivo->nombre }} › {{ $periodo->nombre }}
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — datos del periodo
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Editando: <strong>{{ $periodo->nombre }}</strong>
            &nbsp;·&nbsp;
            @if($periodo->abierto)
                <span style="color:#047857;">
                    <i class="fa-solid fa-lock-open"></i> Abierto
                </span>
            @else
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-lock"></i> Cerrado
                </span>
            @endif
        </div>

        <form method="POST"
              action="{{ route('admin.academico.anios.periodos.update', [$anioLectivo->id, $periodo->id]) }}"
              data-form="periodo">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Número (solo lectura) ── --}}
                <div class="campo">
                    <label>
                        <i class="fa-solid fa-hashtag"></i>
                        Periodo
                    </label>
                    <input type="text"
                           value="Periodo {{ $periodo->numero }}"
                           disabled>
                </div>

                {{-- Nombre ── --}}
                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-tag"></i>
                        Nombre
                    </label>
                    <input type="text"
                           id="nombre" name="nombre"
                           value="{{ old('nombre', $periodo->nombre) }}"
                           maxlength="100">
                    @error('nombre')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Fecha inicio ── --}}
                <div class="campo">
                    <label for="fecha_inicio">
                        <i class="fa-solid fa-calendar-day"></i>
                        Fecha Inicio <span>*</span>
                    </label>
                    <input type="date"
                           id="fecha_inicio" name="fecha_inicio"
                           value="{{ old('fecha_inicio', optional($periodo->fecha_inicio)->format('Y-m-d')) }}"
                           min="{{ $anioLectivo->fecha_inicio->format('Y-m-d') }}"
                           max="{{ $anioLectivo->fecha_fin->format('Y-m-d') }}"
                           required>
                    @error('fecha_inicio')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Fecha fin ── --}}
                <div class="campo">
                    <label for="fecha_fin">
                        <i class="fa-solid fa-calendar-check"></i>
                        Fecha Fin <span>*</span>
                    </label>
                    <input type="date"
                           id="fecha_fin" name="fecha_fin"
                           value="{{ old('fecha_fin', optional($periodo->fecha_fin)->format('Y-m-d')) }}"
                           min="{{ $anioLectivo->fecha_inicio->format('Y-m-d') }}"
                           max="{{ $anioLectivo->fecha_fin->format('Y-m-d') }}"
                           required>
                    @error('fecha_fin')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>

            @error('error_academico')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.academico.anios.periodos.index', $anioLectivo->id) }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Actualizar
                </button>
            </div>

        </form>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA ACCIÓN — Cerrar / Reabrir
         Cerrar → .btn-advertencia (ámbar)
         Reabrir → .btn-secundario (verde)
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-accion">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-lock"></i> Estado del periodo
        </h3>

        <p class="seccion-desc">
            @if($periodo->abierto)
                El periodo está <strong>abierto</strong> — los docentes pueden registrar notas.
                Al cerrarlo, las notas quedarán bloqueadas hasta que lo reabras.
            @else
                El periodo está <strong>cerrado</strong> — las notas están bloqueadas.
                Puedes reabrirlo si necesitas hacer correcciones.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($periodo->abierto)
                <form method="POST"
                      action="{{ route('admin.academico.anios.periodos.cerrar', [$anioLectivo->id, $periodo->id]) }}"
                      class="form-cerrar"
                      data-nombre="{{ $periodo->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-lock"></i> Cerrar periodo
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.academico.anios.periodos.reabrir', [$anioLectivo->id, $periodo->id]) }}"
                      class="form-reabrir"
                      data-nombre="{{ $periodo->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-lock-open"></i> Reabrir periodo
                    </button>
                </form>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PELIGRO — Eliminar
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-peligro">

        <h3 class="seccion-titulo-form peligro">
            <i class="fa-solid fa-triangle-exclamation"></i> Zona de peligro
        </h3>

        <p class="seccion-desc">
            Eliminar el periodo es una acción <strong>permanente</strong>.
            Solo es posible si no tiene notas registradas.
            @if($periodo->notas()->count() > 0)
                <br>
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Este periodo tiene <strong>{{ $periodo->notas()->count() }} nota(s)</strong>
                    y no puede eliminarse.
                </span>
            @endif
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.academico.anios.periodos.destroy', [$anioLectivo->id, $periodo->id]) }}"
                  class="form-eliminar"
                  data-nombre="{{ $periodo->nombre }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="btn btn-peligro"
                        {{ $periodo->notas()->count() > 0 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-trash"></i> Eliminar periodo
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/periodo.js') }}"></script>
@endpush
