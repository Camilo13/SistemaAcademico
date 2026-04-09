@extends('layouts.menuadmin')

@section('title', 'Editar Evento — ' . $evento->titulo)

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-calendar-pen"></i>
            Editar Evento
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — datos del evento
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Editando: <strong>{{ $evento->titulo }}</strong>
            &nbsp;·&nbsp;
            {{ $evento->fecha_evento->format('d/m/Y H:i') }}
            &nbsp;·&nbsp;
            @if($evento->activo)
                <span style="color:#047857;">
                    <i class="fa-solid fa-circle-check"></i> Activo
                </span>
            @else
                <span style="color:#6b7280;">
                    <i class="fa-solid fa-circle-xmark"></i> Inactivo
                </span>
            @endif
        </div>

        <form method="POST"
              action="{{ route('admin.eventos.update', $evento->id) }}"
              id="formEvento"
              data-form="evento">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Título ── --}}
                <div class="campo campo-ancho">
                    <label for="titulo">
                        <i class="fa-solid fa-heading"></i>
                        Título <span>*</span>
                    </label>
                    <input type="text"
                           id="titulo" name="titulo"
                           value="{{ old('titulo', $evento->titulo) }}"
                           maxlength="255" required autocomplete="off">
                    @error('titulo')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Descripción ── --}}
                <div class="campo campo-ancho">
                    <label for="descripcion">
                        <i class="fa-solid fa-align-left"></i>
                        Descripción <span>*</span>
                    </label>
                    <textarea id="descripcion" name="descripcion"
                              rows="4" maxlength="2000" required>{{ old('descripcion', $evento->descripcion) }}</textarea>
                    @error('descripcion')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Lugar ── --}}
                <div class="campo">
                    <label for="lugar">
                        <i class="fa-solid fa-location-dot"></i>
                        Lugar <span>*</span>
                    </label>
                    <input type="text"
                           id="lugar" name="lugar"
                           value="{{ old('lugar', $evento->lugar) }}"
                           maxlength="255" required>
                    @error('lugar')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Fecha y hora ── --}}
                <div class="campo">
                    <label for="fecha_evento">
                        <i class="fa-solid fa-calendar-day"></i>
                        Fecha y hora <span>*</span>
                    </label>
                    <input type="datetime-local"
                           id="fecha_evento" name="fecha_evento"
                           value="{{ old('fecha_evento', $evento->fecha_evento->format('Y-m-d\TH:i')) }}"
                           required>
                    @error('fecha_evento')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>

            @error('error_evento')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.eventos.index') }}" class="btn btn-neutro">
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
            <i class="fa-solid fa-toggle-on"></i> Visibilidad del evento
        </h3>

        <p class="seccion-desc">
            @if($evento->activo)
                El evento está <strong>activo</strong> y se muestra en el portal público.
            @else
                El evento está <strong>inactivo</strong> y no se muestra en el portal.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($evento->activo)
                <form method="POST"
                      action="{{ route('admin.eventos.desactivar', $evento->id) }}"
                      class="form-desactivar"
                      data-nombre="{{ $evento->titulo }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-eye-slash"></i> Desactivar evento
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.eventos.activar', $evento->id) }}"
                      class="form-activar"
                      data-nombre="{{ $evento->titulo }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-eye"></i> Activar evento
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
            Eliminar este evento es una acción <strong>permanente</strong> e irreversible.
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.eventos.destroy', $evento->id) }}"
                  class="form-eliminar"
                  data-nombre="{{ $evento->titulo }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-peligro">
                    <i class="fa-solid fa-trash"></i> Eliminar evento
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/portal/eventos/eventos.js') }}"></script>
@endpush
