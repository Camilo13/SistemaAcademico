@extends('layouts.menuadmin')

@section('title', 'Editar Asignación')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar Asignación
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            <strong>{{ optional($asignacion->docente)->nombre }} {{ optional($asignacion->docente)->apellidos }}</strong>
            &rarr; <strong>{{ optional($asignacion->materia)->nombre ?? '—' }}</strong>
            &rarr; {{ optional($asignacion->grupo->grado)->nombre ?? '—' }}
            {{ optional($asignacion->grupo)->nombre ?? '—' }}
            &nbsp;·&nbsp;
            @if($asignacion->activa)
                <span style="color:#047857;">
                    <i class="fa-solid fa-circle-check"></i> Activa
                </span>
            @else
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i> Inactiva
                </span>
            @endif
        </div>

        {{-- Form correctamente formado ── --}}
        <form method="POST"
              action="{{ route('admin.academico.asignaciones.update', $asignacion->id) }}"
              data-form="asignacion">
            @csrf @method('PUT')

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
                                {{ old('docente_id', $asignacion->docente_id) == $docente->id ? 'selected' : '' }}>
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
                                {{ old('materia_id', $asignacion->materia_id) == $materia->id ? 'selected' : '' }}>
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
                                {{ old('grupo_id', $asignacion->grupo_id) == $grupo->id ? 'selected' : '' }}>
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
            <i class="fa-solid fa-toggle-on"></i> Estado de la asignación
        </h3>

        <p class="seccion-desc">
            @if($asignacion->activa)
                La asignación está <strong>activa</strong>. El docente tiene
                acceso a registrar notas para este grupo y materia.
            @else
                La asignación está <strong>inactiva</strong>. El docente
                no tiene acceso a esta materia/grupo hasta que la actives.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($asignacion->activa)
                <form method="POST"
                      action="{{ route('admin.academico.asignaciones.desactivar', $asignacion->id) }}"
                      class="form-desactivar"
                      data-nombre="la asignación">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-eye-slash"></i> Desactivar asignación
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.academico.asignaciones.activar', $asignacion->id) }}"
                      class="form-activar"
                      data-nombre="la asignación">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-eye"></i> Activar asignación
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

        @php $totalNotas = $asignacion->notas()->count(); @endphp

        <p class="seccion-desc">
            Eliminar esta asignación es una acción <strong>permanente</strong>.
            Solo es posible si no tiene notas registradas.
            @if($totalNotas > 0)
                <br>
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Tiene <strong>{{ $totalNotas }} nota(s)</strong>
                    registrada(s) y no puede eliminarse.
                </span>
            @endif
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.academico.asignaciones.destroy', $asignacion->id) }}"
                  class="form-eliminar"
                  data-nombre="la asignación">
                @csrf @method('DELETE')
                <button type="submit"
                        class="btn btn-peligro"
                        {{ $totalNotas > 0 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-trash"></i> Eliminar asignación
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/asignacion.js') }}"></script>
@endpush
