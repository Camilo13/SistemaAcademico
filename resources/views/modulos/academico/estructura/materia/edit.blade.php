@extends('layouts.menuadmin')

@section('title', 'Editar Materia — ' . $materia->nombre)

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar Materia
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — datos de la materia
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Editando: <strong>{{ $materia->nombre }}</strong>
            &nbsp;·&nbsp;
            {{ optional($materia->grado)->nombre ?? '—' }}
            @if(optional($materia->grado)->sede)
                — {{ $materia->grado->sede->nombre }}
            @endif
            &nbsp;·&nbsp;
            @if($materia->activa)
                <span style="color:#047857;">
                    <i class="fa-solid fa-circle-check"></i> Activa
                </span>
            @else
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i> Inactiva
                </span>
            @endif
        </div>

        {{-- Form correctamente formado (bug corregido del original) ── --}}
        <form method="POST"
              action="{{ route('admin.academico.estructura.materias.update', $materia->id) }}"
              data-form="materia">
            @csrf @method('PUT')

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
                                {{ old('grado_id', $materia->grado_id) == $grado->id ? 'selected' : '' }}>
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

                {{-- Código ── --}}
                <div class="campo">
                    <label for="codigo">
                        <i class="fa-solid fa-barcode"></i>
                        Código
                    </label>
                    <input type="text"
                           id="codigo" name="codigo"
                           value="{{ old('codigo', $materia->codigo) }}"
                           maxlength="20" autocomplete="off">
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
                           value="{{ old('nombre', $materia->nombre) }}"
                           maxlength="100" required autocomplete="off">
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
                           value="{{ old('intensidad_horaria', $materia->intensidad_horaria) }}"
                           min="1" max="40">
                    @error('intensidad_horaria')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
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
            <i class="fa-solid fa-toggle-on"></i> Estado de la materia
        </h3>

        <p class="seccion-desc">
            @if($materia->activa)
                La materia está <strong>activa</strong>. Aparece disponible
                para asignaciones y horarios.
            @else
                La materia está <strong>inactiva</strong>. No aparece
                en las opciones de asignación ni horario.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($materia->activa)
                <form method="POST"
                      action="{{ route('admin.academico.estructura.materias.desactivar', $materia->id) }}"
                      class="form-desactivar"
                      data-nombre="{{ $materia->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-eye-slash"></i> Desactivar materia
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.academico.estructura.materias.activar', $materia->id) }}"
                      class="form-activar"
                      data-nombre="{{ $materia->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-eye"></i> Activar materia
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

        <p class="seccion-desc">
            Eliminar esta materia es una acción <strong>permanente</strong>.
            Solo es posible si no tiene asignaciones registradas.
            @if($materia->asignaciones()->count() > 0)
                <br>
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Tiene <strong>{{ $materia->asignaciones()->count() }} asignación(es)</strong>
                    y no puede eliminarse.
                </span>
            @endif
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.academico.estructura.materias.destroy', $materia->id) }}"
                  class="form-eliminar"
                  data-nombre="{{ $materia->nombre }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="btn btn-peligro"
                        {{ $materia->asignaciones()->count() > 0 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-trash"></i> Eliminar materia
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/estructura/materia.js') }}"></script>
@endpush
