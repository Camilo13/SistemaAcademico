@extends('layouts.menuadmin')

@section('title', 'Nuevo Grupo')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-users"></i>
            Nuevo Grupo
        </h2>
    </div>

    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.academico.estructura.grupos.store') }}"
              data-form="grupo">
            @csrf

            <div class="grid-campos">

                {{-- Grado ── --}}
                <div class="campo">
                    <label for="grado_id">
                        <i class="fa-solid fa-layer-group"></i>
                        Grado <span>*</span>
                    </label>
                    <select id="grado_id" name="grado_id" required>
                        <option value="">Seleccione un grado</option>
                        @foreach($grados as $grado)
                            <option value="{{ $grado->id }}"
                                {{ old('grado_id') == $grado->id ? 'selected' : '' }}>
                                {{ $grado->nombre }}
                                @if($grado->sede) — {{ $grado->sede->nombre }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('grado_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Año Lectivo ── --}}
                <div class="campo">
                    <label for="anio_lectivo_id">
                        <i class="fa-solid fa-calendar-days"></i>
                        Año Lectivo <span>*</span>
                    </label>
                    <select id="anio_lectivo_id" name="anio_lectivo_id" required>
                        <option value="">Seleccione un año</option>
                        @foreach($anios as $anio)
                            <option value="{{ $anio->id }}"
                                {{ old('anio_lectivo_id') == $anio->id ? 'selected' : '' }}>
                                {{ $anio->nombre }}
                                @if($anio->activo) (Activo) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('anio_lectivo_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Nombre/Identificador ── --}}
                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-tag"></i>
                        Identificador <span>*</span>
                    </label>
                    <input type="text"
                           id="nombre" name="nombre"
                           value="{{ old('nombre') }}"
                           maxlength="10"
                           placeholder="Ej: A"
                           required autocomplete="off">
                    <span class="nota-campo">
                        Único por grado y año. Se convierte a mayúsculas.
                    </span>
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Cupo Máximo ── --}}
                <div class="campo">
                    <label for="cupo_maximo">
                        <i class="fa-solid fa-users"></i>
                        Cupo Máximo
                    </label>
                    <input type="number"
                           id="cupo_maximo" name="cupo_maximo"
                           value="{{ old('cupo_maximo') }}"
                           min="1"
                           placeholder="Vacío = sin límite">
                    @error('cupo_maximo')
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
                        Grupo activo
                    </label>
                </div>

            </div>

            @error('error_grupo')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.academico.estructura.grupos.index') }}"
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
    <script src="{{ asset('js/modulos/academico/estructura/grupo.js') }}"></script>
@endpush
