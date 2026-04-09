@extends('layouts.menuadmin')

@section('title', 'Editar — ' . $anio->nombre)

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar — {{ $anio->nombre }}
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — datos del año
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Editando: <strong>{{ $anio->nombre }}</strong>
            &nbsp;·&nbsp;
            @if($anio->activo)
                <span style="color:#047857;">
                    <i class="fa-solid fa-circle-check"></i> Año activo
                </span>
            @else
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i> Año inactivo
                </span>
            @endif
        </div>

        <form method="POST"
              action="{{ route('admin.academico.anios.update', $anio->id) }}"
              data-form="anio">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Nombre ── --}}
                <div class="campo campo-ancho">
                    <label for="nombre">
                        <i class="fa-solid fa-calendar"></i>
                        Nombre <span>*</span>
                    </label>
                    <input type="text"
                           id="nombre" name="nombre"
                           value="{{ old('nombre', $anio->nombre) }}"
                           maxlength="20" required autocomplete="off">
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Fecha inicio ── --}}
                <div class="campo">
                    <label for="fecha_inicio">
                        <i class="fa-solid fa-calendar-day"></i>
                        Fecha de Inicio <span>*</span>
                    </label>
                    <input type="date"
                           id="fecha_inicio" name="fecha_inicio"
                           value="{{ old('fecha_inicio', optional($anio->fecha_inicio)->format('Y-m-d')) }}"
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
                        Fecha de Fin <span>*</span>
                    </label>
                    <input type="date"
                           id="fecha_fin" name="fecha_fin"
                           value="{{ old('fecha_fin', optional($anio->fecha_fin)->format('Y-m-d')) }}"
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
                <a href="{{ route('admin.academico.anios.index') }}" class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Actualizar
                </button>
            </div>

        </form>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA ACCIÓN — Activar año lectivo
         Solo activar. Para desactivar se activa otro.
         El modelo garantiza unicidad via boot().
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-accion">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-power-off"></i> Estado del año lectivo
        </h3>

        <p class="seccion-desc">
            @if($anio->activo)
                Este año lectivo está <strong>activo</strong>. Es el año en curso para
                inscripciones, notas y asistencias. Para desactivarlo, activa otro año lectivo.
            @else
                Este año lectivo está <strong>inactivo</strong>. Actívalo para que sea
                el año en curso del sistema. El año activo actual se desactivará automáticamente.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($anio->activo)
                <span class="estado estado-activo">
                    <i class="fa-solid fa-circle-check"></i> Activo actualmente
                </span>
            @else
                <form method="POST"
                      action="{{ route('admin.academico.anios.activar', $anio->id) }}"
                      class="form-activar"
                      data-nombre="{{ $anio->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-power-off"></i> Activar año lectivo
                    </button>
                </form>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         SIN tarjeta-peligro — eliminar se hace
         desde el index seleccionando la fila.
    ══════════════════════════════════════════ --}}

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/anios.js') }}"></script>
@endpush
