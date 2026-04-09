@extends('layouts.menuadmin')

@section('title', 'Nuevo Recurso — ' . $materia->nombre)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/biblioteca/gestion/recurso/form.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-file-circle-plus"></i>
                Nuevo Recurso
            </h2>
            <p class="cabecera-subtitulo">
                Materia: <strong>{{ $materia->nombre }}</strong>
            </p>
        </div>
    </div>

    <div class="tarjeta-form">

        <form id="form-recurso"
              method="POST"
              action="{{ route('admin.biblioteca.materias.recursos.store', $materia->id_materia) }}"
              enctype="multipart/form-data"
              data-form="recurso">
            @csrf

            <div class="grid-campos">

                {{-- Título --}}
                <div class="campo campo-ancho">
                    <label for="titulo">
                        <i class="fa-solid fa-heading"></i>
                        Título <span>*</span>
                    </label>
                    <input type="text" id="titulo" name="titulo"
                           value="{{ old('titulo') }}"
                           maxlength="255" required
                           placeholder="Título del recurso"
                           autocomplete="off">
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
                              rows="3" maxlength="500"
                              placeholder="Descripción breve (opcional)">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tipo --}}
                <div class="campo">
                    <label for="tipo">
                        <i class="fa-solid fa-tag"></i>
                        Tipo de recurso <span>*</span>
                    </label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="archivo" @selected(old('tipo') === 'archivo')>📄 Documento / Presentación</option>
                        <option value="video"   @selected(old('tipo') === 'video')>🎥 Video</option>
                        <option value="audio"   @selected(old('tipo') === 'audio')>🎧 Audio</option>
                        <option value="imagen"  @selected(old('tipo') === 'imagen')>🖼️ Imagen</option>
                        <option value="enlace"  @selected(old('tipo') === 'enlace')>🔗 Enlace web</option>
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
                           value="{{ old('autor') }}"
                           maxlength="150"
                           placeholder="Nombre del autor o fuente">
                    @error('autor')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Método --}}
                <div class="campo campo-ancho oculto" id="campo-metodo">
                    <label>
                        <i class="fa-solid fa-upload"></i>
                        ¿Cómo se proporcionará? <span>*</span>
                    </label>
                    <div class="opciones-radio">
                        <label class="opcion-radio">
                            <input type="radio" name="metodo" value="archivo"
                                   @checked(old('metodo') === 'archivo')>
                            <i class="fa-solid fa-file-arrow-up"></i> Subir archivo
                        </label>
                        <label class="opcion-radio">
                            <input type="radio" name="metodo" value="url"
                                   @checked(old('metodo') === 'url')>
                            <i class="fa-solid fa-link"></i> Pegar enlace
                        </label>
                    </div>
                    @error('metodo')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- URL --}}
                <div class="campo campo-ancho oculto" id="campo-url">
                    <label for="url">
                        <i class="fa-solid fa-link"></i>
                        URL del recurso <span>*</span>
                    </label>
                    <input type="url" id="url" name="url"
                           value="{{ old('url') }}"
                           maxlength="500"
                           placeholder="https://...">
                    @error('url')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Archivo --}}
                <div class="campo campo-ancho oculto" id="campo-archivo">
                    <label for="archivo">
                        <i class="fa-solid fa-file-arrow-up"></i>
                        Archivo <span>*</span>
                    </label>
                    <input type="file" id="archivo" name="archivo">
                    <span class="nota-campo">
                        Máx. 50 MB · PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, MP4, MP3, JPG, PNG, GIF, WEBP.
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

            {{-- Cancelar → .btn-neutro · Guardar → .btn-primario ── --}}
            <div class="acciones-form">
                <a href="{{ route('admin.biblioteca.materias.recursos.index', $materia->id_materia) }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar recurso
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/biblioteca/gestion/recurso/recurso.js') }}"></script>
@endpush
