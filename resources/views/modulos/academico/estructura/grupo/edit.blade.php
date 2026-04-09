@extends('layouts.menuadmin')

@section('title', 'Editar Grupo — ' . $grupo->nombre)

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar Grupo
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — datos del grupo
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Editando: <strong>{{ $grupo->nombre }}</strong>
            &nbsp;·&nbsp;
            {{ optional($grupo->grado)->nombre ?? '—' }}
            &nbsp;·&nbsp;
            {{ optional($grupo->anioLectivo)->nombre ?? '—' }}
            &nbsp;·&nbsp;
            @if($grupo->activo)
                <span style="color:#047857;">
                    <i class="fa-solid fa-circle-check"></i> Activo
                </span>
            @else
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i> Inactivo
                </span>
            @endif
        </div>

        {{-- Form tag correctamente formado ── --}}
        <form method="POST"
              action="{{ route('admin.academico.estructura.grupos.update', $grupo->id) }}"
              data-form="grupo">
            @csrf @method('PUT')

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
                                {{ old('grado_id', $grupo->grado_id) == $grado->id ? 'selected' : '' }}>
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
                                {{ old('anio_lectivo_id', $grupo->anio_lectivo_id) == $anio->id ? 'selected' : '' }}>
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

                {{-- Nombre ── --}}
                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-tag"></i>
                        Identificador <span>*</span>
                    </label>
                    <input type="text"
                           id="nombre" name="nombre"
                           value="{{ old('nombre', $grupo->nombre) }}"
                           maxlength="10" required autocomplete="off">
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
                           value="{{ old('cupo_maximo', $grupo->cupo_maximo) }}"
                           min="1"
                           placeholder="Vacío = sin límite">
                    @if($grupo->inscripciones()->count() > 0)
                        <span class="nota-campo">
                            <i class="fa-solid fa-circle-info"></i>
                            Actualmente hay <strong>{{ $grupo->inscripciones()->count() }}</strong>
                            estudiante(s) inscritos. El cupo no puede ser menor.
                        </span>
                    @endif
                    @error('cupo_maximo')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
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
            <i class="fa-solid fa-toggle-on"></i> Estado del grupo
        </h3>

        <p class="seccion-desc">
            @if($grupo->activo)
                El grupo está <strong>activo</strong>. Acepta inscripciones
                y aparece en horarios y asignaciones.
            @else
                El grupo está <strong>inactivo</strong>. No acepta nuevas
                inscripciones ni aparece en opciones de asignación.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($grupo->activo)
                <form method="POST"
                      action="{{ route('admin.academico.estructura.grupos.desactivar', $grupo->id) }}"
                      class="form-desactivar"
                      data-nombre="{{ $grupo->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-eye-slash"></i> Desactivar grupo
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.academico.estructura.grupos.activar', $grupo->id) }}"
                      class="form-activar"
                      data-nombre="{{ $grupo->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-eye"></i> Activar grupo
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

        @php
            $totalInscritos   = $grupo->inscripciones()->count();
            $totalAsignaciones = $grupo->asignaciones()->count();
            $puedeEliminar    = $totalInscritos === 0 && $totalAsignaciones === 0;
        @endphp

        <p class="seccion-desc">
            Eliminar este grupo es una acción <strong>permanente</strong>.
            Solo es posible si no tiene inscripciones ni asignaciones.
            @if(!$puedeEliminar)
                <br>
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Tiene
                    @if($totalInscritos > 0)
                        <strong>{{ $totalInscritos }} inscripción(es)</strong>
                    @endif
                    @if($totalInscritos > 0 && $totalAsignaciones > 0) y @endif
                    @if($totalAsignaciones > 0)
                        <strong>{{ $totalAsignaciones }} asignación(es)</strong>
                    @endif
                    y no puede eliminarse.
                </span>
            @endif
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.academico.estructura.grupos.destroy', $grupo->id) }}"
                  class="form-eliminar"
                  data-nombre="{{ $grupo->nombre }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="btn btn-peligro"
                        {{ !$puedeEliminar ? 'disabled' : '' }}>
                    <i class="fa-solid fa-trash"></i> Eliminar grupo
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/estructura/grupo.js') }}"></script>
@endpush
