@extends('layouts.menuadmin')

@section('title', 'Configuración del Sistema')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/admin/configuracion/configuracion.css') }}">
@endpush

@section('content')

@use('Illuminate\Support\Facades\Storage')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-sliders"></i>
            Configuración del Sistema
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA 1 — Datos institucionales
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.configuracion.update') }}"
              data-form="configuracion">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Nombre institución ── --}}
                <div class="campo campo-ancho">
                    <label for="nombre_institucion">
                        <i class="fa-solid fa-school"></i>
                        Nombre de la institución
                    </label>
                    <input type="text"
                           id="nombre_institucion"
                           name="nombre_institucion"
                           value="{{ old('nombre_institucion', $config['nombre_institucion'] ?? '') }}"
                           maxlength="255"
                           placeholder="Ej: Institución Educativa Agroambiental Akwe Uus Yat"
                           autocomplete="off">
                    @error('nombre_institucion')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- NIT ── --}}
                <div class="campo">
                    <label for="nit_institucion">
                        <i class="fa-solid fa-id-badge"></i>
                        NIT / Código DANE
                    </label>
                    <input type="text"
                           id="nit_institucion"
                           name="nit_institucion"
                           value="{{ old('nit_institucion', $config['nit_institucion'] ?? '') }}"
                           maxlength="50"
                           placeholder="Ej: 2193551131"
                           autocomplete="off">
                    @error('nit_institucion')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Resolución ── --}}
                <div class="campo">
                    <label for="resolucion">
                        <i class="fa-solid fa-file-contract"></i>
                        Resolución
                    </label>
                    <input type="text"
                           id="resolucion"
                           name="resolucion"
                           value="{{ old('resolucion', $config['resolucion'] ?? '') }}"
                           maxlength="255"
                           placeholder="Ej: Resolución 3239 de 14 de Mayo de 2012"
                           autocomplete="off">
                    @error('resolucion')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Municipio ── --}}
                <div class="campo">
                    <label for="municipio">
                        <i class="fa-solid fa-map-pin"></i>
                        Municipio
                    </label>
                    <input type="text"
                           id="municipio"
                           name="municipio"
                           value="{{ old('municipio', $config['municipio'] ?? '') }}"
                           maxlength="100"
                           placeholder="Ej: Inzá"
                           autocomplete="off">
                    @error('municipio')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Departamento ── --}}
                <div class="campo">
                    <label for="departamento">
                        <i class="fa-solid fa-map"></i>
                        Departamento
                    </label>
                    <input type="text"
                           id="departamento"
                           name="departamento"
                           value="{{ old('departamento', $config['departamento'] ?? '') }}"
                           maxlength="100"
                           placeholder="Ej: Cauca"
                           autocomplete="off">
                    @error('departamento')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            @error('error_config')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar datos
                </button>
            </div>

        </form>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA 2 — Firma del rector
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-signature"></i> Firma del Rector
        </h3>

        <p class="seccion-desc">
            Esta firma aparecerá automáticamente en todos los boletines académicos generados por el sistema.
            Se recomienda imagen PNG con fondo transparente o blanco.
        </p>

        {{-- Preview firma actual ── --}}
        <div class="config-firma-preview">
            @php $firmaRector = $config['firma_rector'] ?? null; @endphp
            @if($firmaRector)
                <img src="{{ Storage::url($firmaRector) }}"
                     alt="Firma del rector"
                     class="config-firma-img">
            @else
                <span class="config-firma-vacia">
                    <i class="fa-solid fa-image-slash"></i>
                    Sin firma registrada.
                </span>
            @endif
        </div>

        <form method="POST"
              action="{{ route('admin.configuracion.firma') }}"
              enctype="multipart/form-data">
            @csrf

            <div class="grid-campos">
                <div class="campo campo-ancho">
                    <label for="firma_rector">
                        <i class="fa-solid fa-upload"></i>
                        Subir nueva firma
                    </label>
                    <input type="file"
                           id="firma_rector"
                           name="firma_rector"
                           accept=".png,.jpg,.jpeg">
                    <span class="nota-campo">PNG o JPG — máximo 2 MB.</span>
                    @error('firma_rector')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="acciones-form">
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar firma
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
@endpush
