@extends('layouts.menuadmin')

@section('title', 'Nueva Sede')

@push('styles')
    {{-- academico-form.css ya está en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-school"></i>
            Nueva Sede
        </h2>
    </div>

    {{-- ── Tarjeta del formulario ── --}}
    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.academico.sedes.store') }}"
              data-form="sede">
            @csrf

            <div class="grid-campos">

                {{-- Código · opcional · único · solo letras, números y guiones --}}
                <div class="campo">
                    <label for="codigo">
                        <i class="fa-solid fa-barcode"></i> Código
                    </label>
                    <input type="text"
                           id="codigo"
                           name="codigo"
                           value="{{ old('codigo') }}"
                           maxlength="20"
                           placeholder="Ej: S-001"
                           autocomplete="off">
                    @error('codigo')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                    <span class="nota-campo">Opcional. Solo letras, números, guiones y guion bajo.</span>
                </div>

                {{-- Nombre · obligatorio · único --}}
                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-school"></i>
                        Nombre <span aria-hidden="true">*</span>
                    </label>
                    <input type="text"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre') }}"
                           maxlength="100"
                           placeholder="Nombre oficial de la sede"
                           required
                           autocomplete="off">
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Dirección · opcional · ancho completo --}}
                <div class="campo campo-ancho">
                    <label for="direccion">
                        <i class="fa-solid fa-location-dot"></i> Dirección
                    </label>
                    <input type="text"
                           id="direccion"
                           name="direccion"
                           value="{{ old('direccion') }}"
                           maxlength="150"
                           placeholder="Dirección física de la sede">
                    @error('direccion')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Teléfono · opcional --}}
                <div class="campo">
                    <label for="telefono">
                        <i class="fa-solid fa-phone"></i> Teléfono
                    </label>
                    <input type="tel"
                           id="telefono"
                           name="telefono"
                           value="{{ old('telefono') }}"
                           maxlength="20"
                           placeholder="Ej: 3001234567">
                    @error('telefono')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Estado inicial · por defecto Activa --}}
                <div class="campo">
                    <label for="activa">
                        <i class="fa-solid fa-toggle-on"></i> Estado inicial
                    </label>
                    <select id="activa" name="activa">
                        <option value="1" {{ old('activa', '1') == '1' ? 'selected' : '' }}>
                            Activa
                        </option>
                        <option value="0" {{ old('activa') === '0' ? 'selected' : '' }}>
                            Inactiva
                        </option>
                    </select>
                    @error('activa')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- Error general del servidor --}}
            @error('error_sede')
                <div class="alerta-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.academico.sedes.index') }}"
                   class="btn btn-neutro">
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
    <script src="{{ asset('js/modulos/academico/estructura/sede.js') }}"></script>
@endpush
