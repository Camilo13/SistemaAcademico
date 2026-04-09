@extends('layouts.menuadmin')

@section('title', 'Editar Materia — ' . $materia->nombre)

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar Materia
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — Datos de la materia
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Editando: <strong>{{ $materia->nombre }}</strong>
            &nbsp;·&nbsp;
            @if($materia->visible)
                <span style="color:#047857;">
                    <i class="fa-solid fa-eye"></i> Visible
                </span>
            @else
                <span style="color:#6b7280;">
                    <i class="fa-solid fa-eye-slash"></i> Oculta
                </span>
            @endif
        </div>

        <form method="POST"
              action="{{ route('admin.biblioteca.materias.update', $materia->id_materia) }}"
              data-form="materia">
            @csrf
            @method('PUT')

            <div class="grid-campos">

                <div class="campo campo-ancho">
                    <label for="nombre">
                        <i class="fa-solid fa-book"></i>
                        Nombre <span>*</span>
                    </label>
                    <input type="text"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre', $materia->nombre) }}"
                           maxlength="150"
                           required
                           autocomplete="off">
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="campo campo-ancho">
                    <label for="descripcion">
                        <i class="fa-solid fa-align-left"></i>
                        Descripción
                    </label>
                    <textarea id="descripcion"
                              name="descripcion"
                              rows="4"
                              maxlength="500">{{ old('descripcion', $materia->descripcion) }}</textarea>
                    @error('descripcion')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            @error('error_materia')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.biblioteca.materias.index') }}"
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
         TARJETA ACCIÓN — Visibilidad
         Borde ámbar
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-accion">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-eye"></i> Visibilidad de la materia
        </h3>

        <p class="seccion-desc">
            @if($materia->visible)
                La materia está <strong>visible</strong> para docentes y estudiantes.
                Puedes ocultarla si aún no está lista para publicarse.
            @else
                La materia está <strong>oculta</strong>.
                Nadie más puede verla hasta que la actives.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($materia->visible)
                <form method="POST"
                      action="{{ route('admin.biblioteca.materias.desactivar', $materia->id_materia) }}"
                      class="form-desactivar"
                      data-nombre="{{ $materia->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-eye-slash"></i> Ocultar materia
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.biblioteca.materias.activar', $materia->id_materia) }}"
                      class="form-activar"
                      data-nombre="{{ $materia->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-eye"></i> Publicar materia
                    </button>
                </form>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PELIGRO — Eliminar
         Borde rojo — solo si no tiene recursos
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-peligro">

        <h3 class="seccion-titulo-form peligro">
            <i class="fa-solid fa-triangle-exclamation"></i> Zona de peligro
        </h3>

        <p class="seccion-desc">
            Eliminar esta materia es una acción <strong>permanente</strong>.
            Solo es posible si no tiene recursos asociados.
            @if($materia->recursos()->count() > 0)
                <br><span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Esta materia tiene <strong>{{ $materia->recursos()->count() }} recurso(s)</strong>
                    y no puede eliminarse hasta que los elimines primero.
                </span>
            @endif
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.biblioteca.materias.destroy', $materia->id_materia) }}"
                  class="form-eliminar"
                  data-nombre="{{ $materia->nombre }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="btn btn-peligro"
                        {{ $materia->recursos()->count() > 0 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-trash"></i> Eliminar materia
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/biblioteca/gestion/materia/materia.js') }}"></script>
@endpush
