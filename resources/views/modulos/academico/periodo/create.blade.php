@extends('layouts.menuadmin')

@section('title', 'Nuevo Periodo — ' . $anioLectivo->nombre)

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-calendar-plus"></i>
                Nuevo Periodo
            </h2>
            <p class="cabecera-subtitulo">
                Año lectivo: <strong>{{ $anioLectivo->nombre }}</strong>
            </p>
        </div>
    </div>

    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.academico.anios.periodos.store', $anioLectivo->id) }}"
              data-form="periodo">
            @csrf

            <div class="grid-campos">

                {{-- Número ── --}}
                <div class="campo">
                    <label for="numero">
                        <i class="fa-solid fa-hashtag"></i>
                        Número <span>*</span>
                    </label>
                    <select id="numero" name="numero" required>
                        <option value="">Seleccione...</option>
                        @for ($i = 1; $i <= 3; $i++)
                            <option value="{{ $i }}"
                                {{ old('numero') == $i ? 'selected' : '' }}>
                                Periodo {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('numero')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Nombre (opcional — el modelo lo genera si está vacío) ── --}}
                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-tag"></i>
                        Nombre
                    </label>
                    <input type="text"
                           id="nombre" name="nombre"
                           value="{{ old('nombre') }}"
                           maxlength="100"
                           placeholder="Ej: Primer Periodo (opcional)">
                    <span class="nota-campo">
                        Si lo dejas vacío se usará "Periodo N" automáticamente.
                    </span>
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
                           value="{{ old('fecha_inicio') }}"
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
                           value="{{ old('fecha_fin') }}"
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
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Periodo
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/periodo.js') }}"></script>
@endpush
