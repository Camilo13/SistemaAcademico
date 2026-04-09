@extends('layouts.menuadmin')

@section('title', 'Editar Solicitud')

@push('styles')
    {{-- academico-form.css ya está en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera — sin botón Volver ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-pen-to-square"></i>
            Editar Solicitud
        </h2>
    </div>

    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            Solo se pueden modificar solicitudes en estado
            <strong>pendiente</strong>. Los cambios se guardarán antes de aprobarla.
        </div>

        <form method="POST"
              action="{{ route('admin.solicitudes.update', $solicitud) }}"
              data-form="solicitud">
            @csrf
            @method('PUT')

            <div class="grid-campos">

                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-user"></i> Nombre <span>*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre', $solicitud->nombre) }}"
                           required maxlength="255">
                    @error('nombre')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="campo">
                    <label for="apellidos">
                        <i class="fa-solid fa-user"></i> Apellidos <span>*</span>
                    </label>
                    <input type="text" id="apellidos" name="apellidos"
                           value="{{ old('apellidos', $solicitud->apellidos) }}"
                           required maxlength="255">
                    @error('apellidos')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="campo">
                    <label for="identificacion">
                        <i class="fa-solid fa-id-card"></i> Identificación <span>*</span>
                    </label>
                    <input type="text" id="identificacion" name="identificacion"
                           value="{{ old('identificacion', $solicitud->identificacion) }}"
                           required maxlength="30" inputmode="numeric">
                    @error('identificacion')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="campo">
                    <label for="rol">
                        <i class="fa-solid fa-tag"></i> Rol solicitado <span>*</span>
                    </label>
                    <select id="rol" name="rol" required>
                        <option value="docente"
                            @selected(old('rol', $solicitud->rol) === 'docente')>
                            Docente
                        </option>
                        <option value="estudiante"
                            @selected(old('rol', $solicitud->rol) === 'estudiante')>
                            Estudiante
                        </option>
                    </select>
                    @error('rol')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                <div class="campo">
                    <label for="correo">
                        <i class="fa-solid fa-envelope"></i> Correo electrónico <span>*</span>
                    </label>
                    <input type="email" id="correo" name="correo"
                           value="{{ old('correo', $solicitud->correo) }}"
                           required maxlength="255">
                    @error('correo')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="campo">
                    <label for="contacto">
                        <i class="fa-solid fa-phone"></i> Contacto <span>*</span>
                    </label>
                    <input type="text" id="contacto" name="contacto"
                           value="{{ old('contacto', $solicitud->contacto) }}"
                           required maxlength="20" inputmode="numeric">
                    @error('contacto')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                <div class="campo campo-ancho">
                    <label for="ubicacion">
                        <i class="fa-solid fa-location-dot"></i> Ubicación <span>*</span>
                    </label>
                    <input type="text" id="ubicacion" name="ubicacion"
                           value="{{ old('ubicacion', $solicitud->ubicacion) }}"
                           required maxlength="150">
                    @error('ubicacion')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- Error general del servidor --}}
            @error('error_solicitud')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            {{-- Cancelar → index (no al show que ya no existe) --}}
            <div class="acciones-form">
                <a href="{{ route('admin.solicitudes.index') }}"
                   class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/solicitudes/gestion.js') }}"></script>
@endpush
