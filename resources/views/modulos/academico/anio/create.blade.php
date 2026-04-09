@extends('layouts.menuadmin')

@section('title', 'Nuevo Año Lectivo')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-calendar-plus"></i>
            Nuevo Año Lectivo
        </h2>
    </div>

    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.academico.anios.store') }}"
              data-form="anio">
            @csrf

            <div class="grid-campos">

                {{-- Nombre ── --}}
                <div class="campo campo-ancho">
                    <label for="nombre">
                        <i class="fa-solid fa-calendar"></i>
                        Nombre <span>*</span>
                    </label>
                    <input type="text"
                           id="nombre" name="nombre"
                           value="{{ old('nombre') }}"
                           maxlength="20"
                           placeholder="Ej: 2025"
                           required autocomplete="off">
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                    <span class="nota-campo">Máximo 20 caracteres. Debe ser único.</span>
                </div>

                {{-- Fecha inicio ── --}}
                <div class="campo">
                    <label for="fecha_inicio">
                        <i class="fa-solid fa-calendar-day"></i>
                        Fecha de Inicio <span>*</span>
                    </label>
                    <input type="date"
                           id="fecha_inicio" name="fecha_inicio"
                           value="{{ old('fecha_inicio') }}"
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
                           value="{{ old('fecha_fin') }}"
                           required>
                    @error('fecha_fin')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Activo — hidden + checkbox ── --}}
                <div class="campo-check">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox"
                           id="activo" name="activo" value="1"
                           {{ old('activo') ? 'checked' : '' }}>
                    <label for="activo" class="label-check">
                        <i class="fa-solid fa-power-off"></i>
                        Marcar como año activo
                    </label>
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
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/anio.js') }}"></script>
@endpush
