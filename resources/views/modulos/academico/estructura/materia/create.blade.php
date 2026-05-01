@extends('layouts.menuadmin')

@section('title', 'Nueva Materia')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-book-open"></i>
            Nueva Materia
        </h2>
    </div>

    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.academico.estructura.materias.store') }}"
              data-form="materia">
            @csrf

            <div class="grid-campos">

                {{-- Grado ── --}}
                <div class="campo campo-ancho">
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

                {{-- Código (opcional) ── --}}
                <div class="campo">
                    <label for="codigo">
                        <i class="fa-solid fa-barcode"></i>
                        Código
                    </label>
                    <input type="text"
                           id="codigo" name="codigo"
                           value="{{ old('codigo') }}"
                           maxlength="20"
                           placeholder="Ej: MAT-01"
                           autocomplete="off">
                    <span class="nota-campo">Opcional. Debe ser único por grado.</span>
                    @error('codigo')
                        <span class="error-campo">{{ $message }}</span>
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
                           placeholder="Ej: Matemáticas"
                           required autocomplete="off">
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Intensidad horaria ── --}}
                <div class="campo">
                    <label for="intensidad_horaria">
                        <i class="fa-solid fa-clock"></i>
                        Horas / semana
                    </label>
                    <input type="number"
                           id="intensidad_horaria" name="intensidad_horaria"
                           value="{{ old('intensidad_horaria') }}"
                           min="1" max="40"
                           placeholder="Ej: 4">
                    <span class="nota-campo">Máximo 40 horas semanales.</span>
                    @error('intensidad_horaria')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Activa — hidden + checkbox ── --}}
                <div class="campo-check">
                    <input type="hidden" name="activa" value="0">
                    <input type="checkbox"
                           id="activa" name="activa" value="1"
                           {{ old('activa', '1') !== '0' ? 'checked' : '' }}>
                    <label for="activa" class="label-check">
                        Materia activa
                    </label>
                </div>

            </div>

            @error('error_materia')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.academico.estructura.materias.index') }}"
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
    <script src="{{ asset('js/modulos/academico/estructura/materia.js') }}"></script>
@endpush
