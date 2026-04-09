@extends('layouts.menuadmin')

@section('title', 'Nuevo Evento')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-calendar-plus"></i>
            Nuevo Evento
        </h2>
    </div>

    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.eventos.store') }}"
              id="formEvento"
              data-form="evento">
            @csrf

            <div class="grid-campos">

                {{-- Título ── --}}
                <div class="campo campo-ancho">
                    <label for="titulo">
                        Título <span>*</span>
                    </label>
                    <input type="text"
                           id="titulo" name="titulo"
                           value="{{ old('titulo') }}"
                           maxlength="255" required
                           placeholder="Nombre del evento"
                           autocomplete="off">
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
                              rows="4" maxlength="2000" required
                              placeholder="Descripción del evento">{{ old('descripcion') }}</textarea>
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
                           value="{{ old('lugar') }}"
                           maxlength="255" required
                           placeholder="Lugar del evento">
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
                           value="{{ old('fecha_evento') }}"
                           required>
                    @error('fecha_evento')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Estado inicial — hidden + checkbox ── --}}
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
                            Publicar evento al crear
                        </label>
                    </div>
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
                    <i class="fa-solid fa-floppy-disk"></i> Guardar evento
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/portal/eventos/eventos.js') }}"></script>
@endpush
