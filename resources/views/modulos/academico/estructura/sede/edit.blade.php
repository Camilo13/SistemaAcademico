@extends('layouts.menuadmin')

@section('title', 'Editar Sede — ' . $sede->nombre)

@push('styles')
    {{-- academico-form.css ya está en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ══════════════════════════════════════════
         CABECERA 
    ══════════════════════════════════════════ --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar — {{ $sede->nombre }}
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — Datos de la sede
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        {{-- Banner informativo del registro --}}
        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Editando: <strong>{{ $sede->nombre }}</strong>
            &nbsp;·&nbsp;
            @if($sede->activa)
                <span style="color:#047857;">
                    <i class="fa-solid fa-circle-check"></i> Activa
                </span>
            @else
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i> Inactiva
                </span>
            @endif
        </div>

        <form method="POST"
              action="{{ route('admin.academico.sedes.update', $sede->id) }}"
              data-form="sede">
            @csrf
            @method('PUT')

            <div class="grid-campos">

                {{-- Código --}}
                <div class="campo">
                    <label for="codigo">
                        <i class="fa-solid fa-barcode"></i> Código
                    </label>
                    <input type="text"
                           id="codigo"
                           name="codigo"
                           value="{{ old('codigo', $sede->codigo) }}"
                           maxlength="20"
                           autocomplete="off">
                    @error('codigo')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                    <span class="nota-campo">Opcional. Solo letras, números, guiones y guion bajo.</span>
                </div>

                {{-- Nombre --}}
                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-school"></i>
                        Nombre <span aria-hidden="true">*</span>
                    </label>
                    <input type="text"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre', $sede->nombre) }}"
                           maxlength="100"
                           required
                           autocomplete="off">
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Dirección --}}
                <div class="campo campo-ancho">
                    <label for="direccion">
                        <i class="fa-solid fa-location-dot"></i> Dirección
                    </label>
                    <input type="text"
                           id="direccion"
                           name="direccion"
                           value="{{ old('direccion', $sede->direccion) }}"
                           maxlength="150">
                    @error('direccion')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div class="campo">
                    <label for="telefono">
                        <i class="fa-solid fa-phone"></i> Teléfono
                    </label>
                    <input type="tel"
                           id="telefono"
                           name="telefono"
                           value="{{ old('telefono', $sede->telefono) }}"
                           maxlength="20">
                    @error('telefono')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
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
                    <i class="fa-solid fa-floppy-disk"></i> Actualizar
                </button>
            </div>

        </form>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA ACCIÓN — Activar / Desactivar
         Borde ámbar. Sin zona de peligro aquí —
         la eliminación se hace desde el index
         usando la barra de selección bulk.
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-accion">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-toggle-on"></i> Estado de la sede
        </h3>

        <p class="seccion-desc">
            @if($sede->activa)
                La sede está <strong>activa</strong>. Puedes desactivarla si ya no
                está en operación. Los grados y grupos asociados seguirán activos,
                pero la sede no estará disponible para nuevos registros.
            @else
                La sede está <strong>inactiva</strong>. Puedes reactivarla cuando
                vuelva a estar en operación.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($sede->activa)
                <form method="POST"
                      action="{{ route('admin.academico.sedes.desactivar', $sede->id) }}"
                      class="form-desactivar"
                      data-nombre="{{ $sede->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-toggle-off"></i> Desactivar sede
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.academico.sedes.activar', $sede->id) }}"
                      class="form-activar"
                      data-nombre="{{ $sede->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-toggle-on"></i> Activar sede
                    </button>
                </form>
            @endif
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/estructura/sede.js') }}"></script>
@endpush
