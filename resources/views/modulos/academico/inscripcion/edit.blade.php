@extends('layouts.menuadmin')

@section('title', 'Editar Inscripción')

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar Inscripción
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — cambio de grupo
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Estudiante:
            <strong>
                {{ optional($inscripcion->estudiante)->nombre }}
                {{ optional($inscripcion->estudiante)->apellidos }}
            </strong>
            &nbsp;·&nbsp;
            Año: <strong>{{ optional($inscripcion->grupo->anioLectivo)->nombre ?? '—' }}</strong>
            &nbsp;·&nbsp;
            @php
                $estadoClase = match($inscripcion->estado) {
                    'activa'     => 'color:#047857',
                    'retirada'   => 'color:#991b1b',
                    'finalizada' => 'color:#374151',
                    default      => 'color:#6b7280',
                };
            @endphp
            <span style="{{ $estadoClase }};">
                <i class="fa-solid fa-circle-dot"></i>
                {{ ucfirst($inscripcion->estado) }}
            </span>
        </div>

        {{-- Form tag correctamente formado ── --}}
        <form method="POST"
              action="{{ route('admin.academico.inscripciones.update', $inscripcion->id) }}"
              data-form="inscripcion">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Estudiante (solo lectura) ── --}}
                <div class="campo campo-ancho">
                    <label>
                        <i class="fa-solid fa-user-graduate"></i>
                        Estudiante
                    </label>
                    <input type="text"
                           value="{{ optional($inscripcion->estudiante)->nombre }} {{ optional($inscripcion->estudiante)->apellidos }}"
                           disabled>
                    <span class="nota-campo">El estudiante no puede modificarse.</span>
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
                                {{ old('grupo_id', $inscripcion->grupo_id) == $grupo->id ? 'selected' : '' }}>
                                {{ optional($grupo->grado)->nombre }} {{ $grupo->nombre }}
                                — {{ optional($grupo->anioLectivo)->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <span class="nota-campo">
                        Solo puede cambiarse a otro grupo del mismo año lectivo.
                    </span>
                    @error('grupo_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Fecha (solo visual) ── --}}
                <div class="campo">
                    <label>
                        <i class="fa-solid fa-calendar-day"></i>
                        F. Inscripción
                    </label>
                    <input type="text"
                           value="{{ $inscripcion->fecha_inscripcion
                               ? $inscripcion->fecha_inscripcion->format('d/m/Y')
                               : '—' }}"
                           disabled>
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
                @if($inscripcion->estaActiva())
                    <button type="submit" class="btn btn-primario">
                        <i class="fa-solid fa-floppy-disk"></i> Actualizar
                    </button>
                @endif
            </div>

        </form>

        {{-- Accesos rápidos — Materias y Boletín ── --}}
        <div class="inscripcion-accesos">
            <a href="{{ route('admin.academico.inscripciones.materias.index', $inscripcion->id) }}"
               class="btn btn-secundario btn-sm">
                <i class="fa-solid fa-book-open"></i> Ver Materias
            </a>
            <a href="{{ route('admin.academico.boletin.show', $inscripcion->id) }}"
               class="btn btn-neutro btn-sm">
                <i class="fa-solid fa-file-lines"></i> Ver Boletín
            </a>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA ACCIÓN — Retirar / Finalizar
         Solo si la inscripción está activa
    ══════════════════════════════════════════ --}}
    @if($inscripcion->estaActiva())
    <div class="tarjeta-form tarjeta-accion">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-user-tag"></i> Cambiar estado
        </h3>

        <p class="seccion-desc">
            La inscripción está <strong>activa</strong>. Puedes retirar al estudiante
            o marcarla como finalizada al término del año.
        </p>

        <div class="acciones-secundarias">

            <form method="POST"
                  action="{{ route('admin.academico.inscripciones.retirar', $inscripcion->id) }}"
                  class="form-retirar"
                  data-nombre="{{ optional($inscripcion->estudiante)->nombre }} {{ optional($inscripcion->estudiante)->apellidos }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-advertencia">
                    <i class="fa-solid fa-user-minus"></i> Retirar estudiante
                </button>
            </form>

            <form method="POST"
                  action="{{ route('admin.academico.inscripciones.finalizar', $inscripcion->id) }}"
                  class="form-finalizar"
                  data-nombre="{{ optional($inscripcion->estudiante)->nombre }} {{ optional($inscripcion->estudiante)->apellidos }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-neutro">
                    <i class="fa-solid fa-flag-checkered"></i> Finalizar inscripción
                </button>
            </form>

        </div>

    </div>
    @endif

    {{-- ══════════════════════════════════════════
         TARJETA PELIGRO — Eliminar
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-peligro">

        <h3 class="seccion-titulo-form peligro">
            <i class="fa-solid fa-triangle-exclamation"></i> Zona de peligro
        </h3>

        @php
            $tieneMaterias = $inscripcion->inscripcionMaterias()->exists();
            $tieneNotas    = $inscripcion->notas()->exists();
            $puedeEliminar = !$tieneMaterias && !$tieneNotas;
        @endphp

        <p class="seccion-desc">
            Eliminar esta inscripción es una acción <strong>permanente</strong>.
            Solo es posible si no tiene materias ni notas registradas.
            @if(!$puedeEliminar)
                <br>
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Tiene información académica asociada y no puede eliminarse.
                </span>
            @endif
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.academico.inscripciones.destroy', $inscripcion->id) }}"
                  class="form-eliminar"
                  data-nombre="{{ optional($inscripcion->estudiante)->nombre }} {{ optional($inscripcion->estudiante)->apellidos }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="btn btn-peligro"
                        {{ !$puedeEliminar ? 'disabled' : '' }}>
                    <i class="fa-solid fa-trash"></i> Eliminar inscripción
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/inscripcion.js') }}"></script>
@endpush
