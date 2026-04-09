@extends('layouts.menuadmin')

@section('title', 'Agregar Materia')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-book-medical"></i>
                Agregar Materia
            </h2>
            <p class="cabecera-subtitulo">
                <strong>
                    {{ optional($inscripcion->estudiante)->nombre }}
                    {{ optional($inscripcion->estudiante)->apellidos }}
                </strong>
                &nbsp;·&nbsp;
                Grupo {{ optional($inscripcion->grupo)->nombre ?? '—' }}
                — {{ optional($inscripcion->grupo->anioLectivo)->nombre ?? '—' }}
            </p>
        </div>
    </div>

    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Solo se muestran las asignaciones <strong>activas</strong> del grupo.
            No puede repetirse la misma materia para este estudiante.
        </div>

        {{-- Form tag correctamente formado ── --}}
        <form method="POST"
              action="{{ route('admin.academico.inscripciones.materias.store', $inscripcion->id) }}"
              data-form="inscripcion-materia">
            @csrf

            {{-- inscripcion_id hidden ── --}}
            <input type="hidden" name="inscripcion_id" value="{{ $inscripcion->id }}">

            <div class="grid-campos">

                <div class="campo campo-ancho">
                    <label for="asignacion_id">
                        <i class="fa-solid fa-book-open"></i>
                        Materia / Docente <span>*</span>
                    </label>
                    <select id="asignacion_id" name="asignacion_id" required
                            {{ $asignaciones->isEmpty() ? 'disabled' : '' }}>
                        <option value="">Seleccione una materia</option>
                        @forelse($asignaciones as $asignacion)
                            <option value="{{ $asignacion->id }}"
                                {{ old('asignacion_id') == $asignacion->id ? 'selected' : '' }}>
                                {{ optional($asignacion->materia)->nombre ?? '—' }}
                                —
                                {{ optional($asignacion->docente)->nombre }}
                                {{ optional($asignacion->docente)->apellidos }}
                            </option>
                        @empty
                        @endforelse
                    </select>
                    @if($asignaciones->isEmpty())
                        <span class="nota-campo" style="color:#d97706;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            No hay asignaciones activas disponibles para este grupo.
                        </span>
                    @endif
                    @error('asignacion_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>

            @error('error_inscripcion_materia')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.academico.inscripciones.materias.index', $inscripcion->id) }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit"
                        class="btn btn-primario"
                        {{ $asignaciones->isEmpty() ? 'disabled' : '' }}>
                    <i class="fa-solid fa-floppy-disk"></i> Inscribir Materia
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/inscripcion_materia.js') }}"></script>
@endpush
