@extends('layouts.menuadmin')

@section('title', 'Nueva Materia — Biblioteca')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-circle-plus"></i>
            Nueva Materia
        </h2>
    </div>

    <div class="tarjeta-form">

        <form method="POST"
              action="{{ route('admin.biblioteca.materias.store') }}"
              data-form="materia">
            @csrf

            <div class="grid-campos">

                <div class="campo campo-ancho">
                    <label for="nombre">
                        <i class="fa-solid fa-book"></i>
                        Nombre <span>*</span>
                    </label>
                    <input type="text"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre') }}"
                           maxlength="150"
                           placeholder="Ej: Matemáticas"
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
                              maxlength="500"
                              placeholder="Descripción breve de la materia (opcional)">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                    <span class="nota-campo">Máximo 500 caracteres.</span>
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
                    <i class="fa-solid fa-floppy-disk"></i> Guardar materia
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/biblioteca/gestion/materia/materia.js') }}"></script>
@endpush
