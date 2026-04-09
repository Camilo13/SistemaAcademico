@extends('layouts.menuadmin')

@section('title', 'Carrusel de Inicio')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/portal/carrusel/carrusel.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-images"></i>
                Carrusel de Inicio
            </h2>
            <p class="cabecera-subtitulo">
                Imágenes que se muestran en el hero del portal público.
            </p>
        </div>
        <a href="{{ route('admin.carrusel.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nueva imagen
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_carrusel')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Grid de imágenes ── --}}
    @if($imagenes->isEmpty())

        <div class="tarjeta-form carrusel-vacio">
            <i class="fa-regular fa-image fa-2x"></i>
            <p>No hay imágenes registradas en el carrusel.</p>
            <a href="{{ route('admin.carrusel.create') }}" class="btn btn-primario btn-sm">
                <i class="fa-solid fa-plus"></i> Agregar primera imagen
            </a>
        </div>

    @else

        <div class="carrusel-grid">

            @foreach($imagenes as $imagen)

                <div class="carrusel-item">

                    {{-- Imagen ── --}}
                    <div class="carrusel-imagen">
                        <img src="{{ Storage::url($imagen->imagen) }}"
                             alt="Imagen carrusel orden {{ $imagen->orden }}">
                    </div>

                    {{-- Info ── --}}
                    <div class="carrusel-info">
                        <span class="carrusel-orden">
                            <i class="fa-solid fa-list-ol"></i>
                            Orden: {{ $imagen->orden }}
                        </span>
                        <span class="estado {{ $imagen->activo ? 'estado-activo' : 'estado-inactivo' }}">
                            <i class="fa-solid {{ $imagen->activo ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                            {{ $imagen->activo ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>

                    {{-- Acciones ── solo editar (btn-icono) + eliminar ── --}}
                    <div class="carrusel-acciones">

                        <a href="{{ route('admin.carrusel.edit', $imagen->id) }}"
                           class="btn-icono editar"
                           title="Editar imagen">
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <form method="POST"
                              action="{{ route('admin.carrusel.destroy', $imagen->id) }}"
                              class="form-eliminar"
                              data-nombre="imagen #{{ $imagen->orden }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn-icono eliminar"
                                    title="Eliminar imagen">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>

                    </div>

                </div>

            @endforeach

        </div>

    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/portal/carrusel/carrusel.js') }}"></script>
@endpush
