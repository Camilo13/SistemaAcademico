@extends('layouts.menuadmin')

@section('title', 'Nuevo Grado')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-layer-group"></i>
            Nuevo Grado
        </h2>
    </div>

    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.academico.grados.store') }}"
              data-form="grado">
            @csrf

            <div class="grid-campos">

                {{-- Sede ── --}}
                <div class="campo">
                    <label for="sede_id">
                        <i class="fa-solid fa-school"></i>
                        Sede <span>*</span>
                    </label>
                    <select id="sede_id" name="sede_id" required>
                        <option value="">Seleccione una sede</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}"
                                {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                                {{ $sede->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('sede_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Nivel ── --}}
                <div class="campo">
                    <label for="nivel">
                        <i class="fa-solid fa-arrow-up-1-9"></i>
                        Nivel (1–11) <span>*</span>
                    </label>
                    <input type="number"
                           id="nivel" name="nivel"
                           value="{{ old('nivel') }}"
                           min="1" max="11"
                           placeholder="Ej: 6"
                           required>
                    <span class="nota-campo">Debe ser único por sede.</span>
                    @error('nivel')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Nombre ── --}}
                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-tag"></i>
                        Nombre <span>*</span>
                    </label>
                    <input type="text"
                           id="nombre" name="nombre"
                           value="{{ old('nombre') }}"
                           maxlength="100"
                           placeholder="Ej: Sexto"
                           required autocomplete="off">
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Tipo ── --}}
                <div class="campo">
                    <label for="tipo">
                        <i class="fa-solid fa-list"></i>
                        Tipo <span>*</span>
                    </label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Seleccione tipo</option>
                        @foreach(['Preescolar', 'Primaria', 'Secundaria', 'Media'] as $t)
                            <option value="{{ $t }}"
                                {{ old('tipo', 'Primaria') === $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Activo — hidden + checkbox ── --}}
                <div class="campo-check">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox"
                           id="activo" name="activo" value="1"
                           {{ old('activo', '1') !== '0' ? 'checked' : '' }}>
                    <label for="activo" class="label-check">
                        <i class="fa-solid fa-circle-check"></i>
                        Grado activo
                    </label>
                </div>

            </div>

            @error('error_grado')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.academico.grados.index') }}" class="btn btn-neutro">
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
    <script src="{{ asset('js/modulos/academico/estructura/grado.js') }}"></script>
@endpush
