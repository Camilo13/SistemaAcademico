@extends('layouts.menuadmin')

@section('title', 'Nueva Inscripción')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-clipboard-list"></i>
            Nueva Inscripción
        </h2>
    </div>

    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Un estudiante solo puede inscribirse <strong>una vez</strong> en el mismo grupo.
            El estado inicial es <strong>activa</strong> automáticamente.
        </div>

        <form method="POST"
              action="{{ route('admin.academico.inscripciones.store') }}"
              data-form="inscripcion">
            @csrf

            <div class="grid-campos">

                {{-- Estudiante ── --}}
                <div class="campo campo-ancho">
                    <label for="estudiante_id">
                        <i class="fa-solid fa-user-graduate"></i>
                        Estudiante <span>*</span>
                    </label>
                    <select id="estudiante_id" name="estudiante_id" required>
                        <option value="">Seleccione un estudiante</option>
                        @foreach($estudiantes as $est)
                            <option value="{{ $est->id }}"
                                {{ old('estudiante_id') == $est->id ? 'selected' : '' }}>
                                {{ $est->nombre }} {{ $est->apellidos }}
                            </option>
                        @endforeach
                    </select>
                    @error('estudiante_id')
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
                                @if($grupo->cupo_maximo)
                                    ({{ $grupo->cupoDisponible() }} disponibles)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('grupo_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Fecha inscripción ── --}}
                <div class="campo">
                    <label for="fecha_inscripcion">
                        <i class="fa-solid fa-calendar-day"></i>
                        Fecha de Inscripción
                    </label>
                    <input type="date"
                           id="fecha_inscripcion" name="fecha_inscripcion"
                           value="{{ old('fecha_inscripcion', now()->format('Y-m-d')) }}">
                    <span class="nota-campo">Opcional. Por defecto es hoy.</span>
                    @error('fecha_inscripcion')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            @error('error_inscripcion')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.academico.inscripciones.index') }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Inscribir
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/inscripcion.js') }}"></script>
@endpush
