@extends('layouts.menuadmin')

@section('title', 'Editar Grado — ' . $grado->nombre)

@push('styles')
    {{-- academico-form.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar Grado
        </h2>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — datos del grado
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Editando: <strong>{{ $grado->nombre }}</strong>
            &nbsp;·&nbsp;
            Sede: <strong>{{ optional($grado->sede)->nombre ?? '—' }}</strong>
            &nbsp;·&nbsp;
            @if($grado->activo)
                <span style="color:#047857;">
                    <i class="fa-solid fa-circle-check"></i> Activo
                </span>
            @else
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i> Inactivo
                </span>
            @endif
        </div>

        {{-- Form correctamente formado (era un bug en el original) ── --}}
        <form method="POST"
              action="{{ route('admin.academico.grados.update', $grado->id) }}"
              data-form="grado">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Sede ── --}}
                <div class="campo">
                    <label for="sede_id">
                        <i class="fa-solid fa-school"></i>
                        Sede <span>*</span>
                    </label>
                    <select id="sede_id" name="sede_id" required>
                        <option value="">Seleccione una sede</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}"
                                {{ old('sede_id', $grado->sede_id) == $sede->id ? 'selected' : '' }}>
                                {{ $sede->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('sede_id')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Nivel ── --}}
                <div class="campo">
                    <label for="nivel">
                        <i class="fa-solid fa-arrow-up-1-9"></i>
                        Nivel (1–11) <span>*</span>
                    </label>
                    <input type="number"
                           id="nivel" name="nivel"
                           value="{{ old('nivel', $grado->nivel) }}"
                           min="1" max="11" required>
                    @error('nivel')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
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
                           value="{{ old('nombre', $grado->nombre) }}"
                           maxlength="100" required autocomplete="off">
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Tipo ── --}}
                <div class="campo">
                    <label for="tipo">
                        <i class="fa-solid fa-list"></i>
                        Tipo <span>*</span>
                    </label>
                    <select id="tipo" name="tipo" required>
                        @foreach(['Preescolar', 'Primaria', 'Secundaria', 'Media'] as $t)
                            <option value="{{ $t }}"
                                {{ old('tipo', $grado->tipo) === $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            @error('error_grado')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.academico.grados.index') }}" class="btn btn-neutro">
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
            <i class="fa-solid fa-toggle-on"></i> Estado del grado
        </h3>

        <p class="seccion-desc">
            @if($grado->activo)
                El grado está <strong>activo</strong>. Aparece disponible
                para asignaciones, grupos e inscripciones.
            @else
                El grado está <strong>inactivo</strong>. No aparece en
                las opciones de asignación ni inscripción.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($grado->activo)
                <form method="POST"
                      action="{{ route('admin.academico.grados.desactivar', $grado->id) }}"
                      class="form-desactivar"
                      data-nombre="{{ $grado->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-eye-slash"></i> Desactivar grado
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.academico.grados.activar', $grado->id) }}"
                      class="form-activar"
                      data-nombre="{{ $grado->nombre }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-eye"></i> Activar grado
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
            Eliminar este grado es una acción <strong>permanente</strong>.
            Solo es posible si no tiene grupos ni materias asociadas.
            @if($grado->grupos()->count() > 0 || $grado->materias()->count() > 0)
                <br>
                <span style="color:#991b1b;">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Tiene
                    @if($grado->grupos()->count() > 0)
                        <strong>{{ $grado->grupos()->count() }} grupo(s)</strong>
                    @endif
                    @if($grado->materias()->count() > 0)
                        @if($grado->grupos()->count() > 0) y @endif
                        <strong>{{ $grado->materias()->count() }} materia(s)</strong>
                    @endif
                    asociada(s) y no puede eliminarse.
                </span>
            @endif
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.academico.grados.destroy', $grado->id) }}"
                  class="form-eliminar"
                  data-nombre="{{ $grado->nombre }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="btn btn-peligro"
                        {{ ($grado->grupos()->count() > 0 || $grado->materias()->count() > 0) ? 'disabled' : '' }}>
                    <i class="fa-solid fa-trash"></i> Eliminar grado
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/estructura/grado.js') }}"></script>
@endpush
