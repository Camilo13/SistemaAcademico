@extends('layouts.menuadmin')

@section('title', 'Nueva Asignación')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-chalkboard-user"></i>
            Nueva Asignación
        </h2>
    </div>

    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            No puede repetirse la misma combinación
            <strong>docente + materia + grupo</strong>.
            La materia debe pertenecer al mismo grado que el grupo.
        </div>

        <form method="POST"
              action="{{ route('admin.academico.asignaciones.store') }}"
              data-form="asignacion">
            @csrf

            <div class="grid-campos">

                {{-- Docente ── --}}
                <div class="campo campo-ancho">
                    <label for="docente_id">
                        <i class="fa-solid fa-user-tie"></i>
                        Docente <span>*</span>
                    </label>
                    <select id="docente_id" name="docente_id" required>
                        <option value="">Seleccione un docente</option>
                        @foreach($docentes as $docente)
                            <option value="{{ $docente->id }}"
                                {{ old('docente_id') == $docente->id ? 'selected' : '' }}>
                                {{ $docente->nombre }} {{ $docente->apellidos }}
                            </option>
                        @endforeach
                    </select>
                    @error('docente_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Materia ── --}}
                <div class="campo">
                    <label for="materia_id">
                        <i class="fa-solid fa-book-open"></i>
                        Materia <span>*</span>
                    </label>
                    <select id="materia_id" name="materia_id" required>
                        <option value="">Seleccione una materia</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ old('materia_id') == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                                @if($materia->grado) — {{ $materia->grado->nombre }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('materia_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Grupo ── --}}
                <div class="campo">
                    <label for="grupo_id">
                        <i class="fa-solid fa-users"></i>
                        Grupo <span>*</span>
                    </label>
                    <select id="grupo_id" name="grupo_id" required>
                        <option value="">Seleccione un grupo</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id }}"
                                {{ old('grupo_id') == $grupo->id ? 'selected' : '' }}>
                                {{ optional($grupo->grado)->nombre }} {{ $grupo->nombre }}
                                — {{ optional($grupo->anioLectivo)->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('grupo_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>

            @error('error_asignacion')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.academico.asignaciones.index') }}"
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
    <script src="{{ asset('js/modulos/academico/asignacion.js') }}"></script>
@endpush
