@extends('layouts.menuadmin')

@section('title', 'Editar Imagen — Carrusel')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/portal/carrusel/carrusel.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar Imagen del Carrusel
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — imagen y orden
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Imagen en posición <strong>#{{ $carrusel->orden }}</strong>
            &nbsp;·&nbsp;
            @if($carrusel->activo)
                <span style="color:#047857;">
                    <i class="fa-solid fa-circle-check"></i> Activa
                </span>
            @else
                <span style="color:#6b7280;">
                    <i class="fa-solid fa-circle-xmark"></i> Inactiva
                </span>
            @endif
        </div>

        <form method="POST"
              action="{{ route('admin.carrusel.update', $carrusel->id) }}"
              enctype="multipart/form-data"
              data-form="carrusel">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Preview de la imagen actual ── --}}
                <div class="campo campo-ancho">
                    <label>
                        <i class="fa-solid fa-image"></i>
                        Imagen actual
                    </label>
                    <div class="carrusel-preview carrusel-preview-actual" id="previewImagen">
                        <img src="{{ Storage::url($carrusel->imagen) }}"
                             alt="Imagen actual del carrusel">
                    </div>
                </div>

                {{-- Reemplazar imagen ── --}}
                <div class="campo campo-ancho">
                    <label for="imagen">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        Reemplazar imagen
                    </label>
                    <input type="file"
                           id="imagen" name="imagen"
                           accept="image/jpg,image/jpeg,image/png,image/webp">
                    <span class="nota-campo">
                        Opcional. Si seleccionas una nueva, la actual se eliminará.
                        Formatos: JPG, JPEG, PNG, WEBP · Máx. 2 MB.
                    </span>
                    @error('imagen')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                    <div class="nota-ayuda">
                        <i class="fa-solid fa-circle-info"></i>
                        Para que la imagen se vea bien en el carrusel, sube una foto tomada en modo horizontal.
                        Las fotos tomadas en modo vertical desde el celular pueden aparecer cortadas.
                        El tamaño ideal es de <strong>1200 × 600 píxeles</strong>.
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
                           value="{{ old('orden', $carrusel->orden) }}"
                           min="0">
                    @error('orden')
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
                    <i class="fa-solid fa-floppy-disk"></i> Actualizar
                </button>
            </div>

        </form>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA ACCIÓN — Activar / Desactivar
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-accion">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-toggle-on"></i> Visibilidad en el carrusel
        </h3>

        <p class="seccion-desc">
            @if($carrusel->activo)
                Esta imagen está <strong>activa</strong> y se muestra en el portal público.
            @else
                Esta imagen está <strong>inactiva</strong> y no se muestra en el portal.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($carrusel->activo)
                <form method="POST"
                      action="{{ route('admin.carrusel.desactivar', $carrusel->id) }}"
                      class="form-desactivar"
                      data-nombre="imagen #{{ $carrusel->orden }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-eye-slash"></i> Desactivar imagen
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.carrusel.activar', $carrusel->id) }}"
                      class="form-activar"
                      data-nombre="imagen #{{ $carrusel->orden }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-eye"></i> Activar imagen
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
            Eliminar esta imagen es una acción <strong>permanente</strong>.
            El archivo físico también se eliminará del servidor.
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.carrusel.destroy', $carrusel->id) }}"
                  class="form-eliminar"
                  data-nombre="imagen #{{ $carrusel->orden }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-peligro">
                    <i class="fa-solid fa-trash"></i> Eliminar imagen
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/portal/carrusel/carrusel.js') }}"></script>
@endpush
