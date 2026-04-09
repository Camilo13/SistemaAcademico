@extends('layouts.menuadmin')

@section('title', 'Editar Recurso — ' . $recurso->titulo)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/biblioteca/gestion/recurso/form.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-pen-to-square"></i>
                Editar Recurso
            </h2>
            <p class="cabecera-subtitulo">
                Materia: <strong>{{ $materia->nombre }}</strong>
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Editando: <strong>{{ $recurso->titulo }}</strong>
            &nbsp;·&nbsp;
            @if($recurso->visible)
                <span style="color:#047857;">
                    <i class="fa-solid fa-eye"></i> Visible
                </span>
            @else
                <span style="color:#6b7280;">
                    <i class="fa-solid fa-eye-slash"></i> Oculto
                </span>
            @endif
        </div>

        <form method="POST"
              action="{{ route('admin.biblioteca.materias.recursos.update', [$materia->id_materia, $recurso->id_recurso]) }}"
              enctype="multipart/form-data"
              data-form="recurso">
            @csrf @method('PUT')

            <div class="grid-campos">

                {{-- Título --}}
                <div class="campo campo-ancho">
                    <label for="titulo">
                        <i class="fa-solid fa-heading"></i>
                        Título <span>*</span>
                    </label>
                    <input type="text" id="titulo" name="titulo"
                           value="{{ old('titulo', $recurso->titulo) }}"
                           maxlength="255" required autocomplete="off">
                    @error('titulo')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="campo campo-ancho">
                    <label for="descripcion">
                        <i class="fa-solid fa-align-left"></i>
                        Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion"
                              rows="3" maxlength="500">{{ old('descripcion', $recurso->descripcion) }}</textarea>
                    @error('descripcion')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tipo --}}
                <div class="campo">
                    <label for="tipo">
                        <i class="fa-solid fa-tag"></i>
                        Tipo <span>*</span>
                    </label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Seleccione</option>
                        <option value="archivo" @selected(old('tipo', $recurso->tipo) === 'archivo')>📄 Documento</option>
                        <option value="video"   @selected(old('tipo', $recurso->tipo) === 'video')>🎥 Video</option>
                        <option value="audio"   @selected(old('tipo', $recurso->tipo) === 'audio')>🎧 Audio</option>
                        <option value="imagen"  @selected(old('tipo', $recurso->tipo) === 'imagen')>🖼️ Imagen</option>
                        <option value="enlace"  @selected(old('tipo', $recurso->tipo) === 'enlace')>🔗 Enlace</option>
                    </select>
                    @error('tipo')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Autor --}}
                <div class="campo">
                    <label for="autor">
                        <i class="fa-solid fa-user-pen"></i>
                        Autor / Fuente
                    </label>
                    <input type="text" id="autor" name="autor"
                           value="{{ old('autor', $recurso->autor) }}"
                           maxlength="150">
                    @error('autor')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Método --}}
                <div class="campo campo-ancho oculto" id="campo-metodo">
                    <label>
                        <i class="fa-solid fa-upload"></i>
                        ¿Cómo se proporcionará?
                    </label>
                    <div class="opciones-radio">
                        <label class="opcion-radio">
                            <input type="radio" name="metodo" value="archivo"
                                   @checked(old('metodo', $recurso->origen) === 'archivo')>
                            <i class="fa-solid fa-file-arrow-up"></i> Subir archivo
                        </label>
                        <label class="opcion-radio">
                            <input type="radio" name="metodo" value="url"
                                   @checked(old('metodo', $recurso->origen) === 'url')>
                            <i class="fa-solid fa-link"></i> Enlace externo
                        </label>
                    </div>
                    @error('metodo')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- URL --}}
                <div class="campo campo-ancho oculto" id="campo-url">
                    <label for="url">
                        <i class="fa-solid fa-link"></i> URL
                    </label>
                    <input type="url" id="url" name="url"
                           value="{{ old('url', $recurso->origen === 'url' ? $recurso->url : '') }}"
                           maxlength="500" placeholder="https://...">
                    @error('url')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Archivo --}}
                <div class="campo campo-ancho oculto" id="campo-archivo">
                    <label for="archivo">
                        <i class="fa-solid fa-file-arrow-up"></i> Reemplazar archivo
                    </label>
                    <input type="file" id="archivo" name="archivo">
                    <span class="nota-campo">
                        Deja vacío para conservar el archivo actual. Si subes uno nuevo, el anterior se eliminará.
                    </span>
                    @error('archivo')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>

            @error('error_recurso')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            {{-- Cancelar → .btn-neutro · Actualizar → .btn-primario ── --}}
            <div class="acciones-form">
                <a href="{{ route('admin.biblioteca.materias.recursos.index', $materia->id_materia) }}"
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
         TARJETA ACCIÓN — Visibilidad
         Ocultar → .btn-advertencia (ámbar)
         Publicar → .btn-secundario (verde)
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-accion">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-eye"></i> Visibilidad del recurso
        </h3>

        <p class="seccion-desc">
            @if($recurso->visible)
                El recurso está <strong>visible</strong> para docentes y estudiantes.
            @else
                El recurso está <strong>oculto</strong>. No será visible hasta que lo actives.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($recurso->visible)
                <form method="POST"
                      action="{{ route('admin.biblioteca.materias.recursos.desactivar', [$materia->id_materia, $recurso->id_recurso]) }}"
                      class="form-desactivar"
                      data-nombre="{{ $recurso->titulo }}">
                    @csrf @method('PATCH')
                    {{-- Ocultar → .btn-advertencia (ámbar) ── --}}
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-eye-slash"></i> Ocultar recurso
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.biblioteca.materias.recursos.activar', [$materia->id_materia, $recurso->id_recurso]) }}"
                      class="form-activar"
                      data-nombre="{{ $recurso->titulo }}">
                    @csrf @method('PATCH')
                    {{-- Publicar → .btn-secundario (verde claro) ── --}}
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-eye"></i> Publicar recurso
                    </button>
                </form>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PELIGRO — Eliminar
         Eliminar → .btn-peligro (rojo)
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-peligro">

        <h3 class="seccion-titulo-form peligro">
            <i class="fa-solid fa-triangle-exclamation"></i> Zona de peligro
        </h3>

        <p class="seccion-desc">
            Eliminar este recurso es una acción <strong>permanente</strong>.
            @if($recurso->origen === 'archivo')
                El archivo físico también será eliminado del servidor.
            @endif
        </p>

        <div class="acciones-secundarias">
            <form method="POST"
                  action="{{ route('admin.biblioteca.materias.recursos.destroy', [$materia->id_materia, $recurso->id_recurso]) }}"
                  class="form-eliminar"
                  data-nombre="{{ $recurso->titulo }}">
                @csrf @method('DELETE')
                {{-- Eliminar → .btn-peligro (rojo) ── --}}
                <button type="submit" class="btn btn-peligro">
                    <i class="fa-solid fa-trash"></i> Eliminar recurso
                </button>
            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/biblioteca/gestion/recurso/recurso.js') }}"></script>
@endpush
