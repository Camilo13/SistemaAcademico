@extends('layouts.menuadmin')

@section('title', 'Nueva Imagen — Carrusel')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/portal/carrusel/carrusel.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-image"></i>
            Nueva Imagen del Carrusel
        </h2>
    </div>

    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.carrusel.store') }}"
              enctype="multipart/form-data"
              data-form="carrusel">
            @csrf

            <div class="grid-campos">

                {{-- Imagen ── --}}
                <div class="campo campo-ancho">
                    <label for="imagen">
                        <i class="fa-solid fa-image"></i>
                        Imagen <span>*</span>
                    </label>
                    <input type="file"
                           id="imagen" name="imagen"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           required>
                    <span class="nota-campo">
                        Formatos: JPG, JPEG, PNG, WEBP · Máx. 2 MB
                    </span>
                    @error('imagen')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Preview ── --}}
                <div class="campo campo-ancho">
                    <div class="carrusel-preview" id="previewImagen">
                        <i class="fa-regular fa-image"></i>
                        <span>Vista previa</span>
                    </div>
                </div>

                {{-- Orden ── --}}
                <div class="campo">
                    <label for="orden">
                        <i class="fa-solid fa-list-ol"></i>
                        Orden de aparición
                    </label>
                    <input type="number"
                           id="orden" name="orden"
                           value="{{ old('orden', 0) }}"
                           min="0">
                    <span class="nota-campo">
                        Las imágenes se muestran de menor a mayor orden.
                    </span>
                    @error('orden')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Activo ── patrón hidden + checkbox ── --}}
                <div class="campo">
                    <label>
                        <i class="fa-solid fa-toggle-on"></i>
                        Estado inicial
                    </label>
                    <div class="campo-check" style="margin-top:0.4rem;">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox"
                               id="activo" name="activo" value="1"
                               {{ old('activo', '1') === '1' ? 'checked' : '' }}>
                        <label for="activo" class="label-check">
                            <i class="fa-solid fa-circle-check"></i>
                            Imagen activa al crear
                        </label>
                    </div>
                    @error('activo')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            @error('error_carrusel')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.carrusel.index') }}" class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar imagen
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/portal/carrusel/carrusel.js') }}"></script>
@endpush
