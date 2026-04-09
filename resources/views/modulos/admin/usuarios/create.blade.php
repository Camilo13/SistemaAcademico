@extends('layouts.menuadmin')

@section('title', 'Nuevo Usuario')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/admin/usuarios/create.css') }}">
@endpush

@section('content')
<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <h2><i class="fa-solid fa-user-plus"></i> Nuevo Usuario</h2>
    </div>

    <div class="tarjeta-form">
        <form method="POST"
              action="{{ route('admin.usuarios.store') }}"
              data-form="usuario">
            @csrf

            <div class="grid-campos">

                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-user"></i> Nombre <span>*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre') }}" required maxlength="255"
                           placeholder="Ej: María">
                    @error('nombre') <span class="error-campo"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="apellidos">
                        <i class="fa-solid fa-user"></i> Apellidos <span>*</span>
                    </label>
                    <input type="text" id="apellidos" name="apellidos"
                           value="{{ old('apellidos') }}" required maxlength="255"
                           placeholder="Ej: Rodríguez Gómez">
                    @error('apellidos') <span class="error-campo"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="identificacion">
                        <i class="fa-solid fa-id-card"></i> Identificación <span>*</span>
                    </label>
                    <input type="text" id="identificacion" name="identificacion"
                           value="{{ old('identificacion') }}" required maxlength="30"
                           placeholder="Solo números" inputmode="numeric">
                    @error('identificacion') <span class="error-campo"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="correo">
                        <i class="fa-solid fa-envelope"></i> Correo electrónico <span>*</span>
                    </label>
                    <input type="email" id="correo" name="correo"
                           value="{{ old('correo') }}" required maxlength="255"
                           placeholder="correo@ejemplo.com">
                    @error('correo') <span class="error-campo"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="ubicacion">
                        <i class="fa-solid fa-location-dot"></i> Ubicación
                    </label>
                    <input type="text" id="ubicacion" name="ubicacion"
                           value="{{ old('ubicacion') }}" maxlength="150"
                           placeholder="Ciudad o dirección">
                    @error('ubicacion') <span class="error-campo">{{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="contacto">
                        <i class="fa-solid fa-phone"></i> Contacto
                    </label>
                    <input type="text" id="contacto" name="contacto"
                           value="{{ old('contacto') }}" maxlength="20"
                           placeholder="Solo números" inputmode="numeric">
                    @error('contacto') <span class="error-campo">{{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="rol">
                        <i class="fa-solid fa-tag"></i> Rol <span>*</span>
                    </label>
                    <select id="rol" name="rol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="administrador" {{ old('rol') === 'administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="docente"       {{ old('rol') === 'docente'       ? 'selected' : '' }}>Docente</option>
                        <option value="estudiante"    {{ old('rol') === 'estudiante'    ? 'selected' : '' }}>Estudiante</option>
                    </select>
                    @error('rol') <span class="error-campo">{{ $message }}</span> @enderror
                </div>

                <div class="campo-check">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" id="activo" name="activo" value="1"
                           {{ old('activo', '1') === '1' ? 'checked' : '' }}>
                    <label for="activo" class="label-check">
                        <i class="fa-solid fa-circle-check"></i> Crear como usuario activo
                    </label>
                </div>

                {{-- Separador de sección ── contraseña ── --}}
                <div class="separador-seccion">
                    <i class="fa-solid fa-lock"></i> Contraseña de acceso
                </div>

                <div class="campo">
                    <label for="password">
                        <i class="fa-solid fa-lock"></i> Contraseña <span>*</span>
                    </label>
                    <input type="password" id="password" name="password"
                           required minlength="8"
                           placeholder="Mínimo 8 caracteres"
                           autocomplete="new-password">
                    @error('password') <span class="error-campo"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="password_confirmation">
                        <i class="fa-solid fa-lock"></i> Confirmar contraseña <span>*</span>
                    </label>
                    <input type="password" id="password_confirmation"
                           name="password_confirmation"
                           required minlength="8"
                           placeholder="Repite la contraseña"
                           autocomplete="new-password">
                </div>

                {{-- Barra de fortaleza --}}
                <div class="campo-ancho">
                    <div class="fortaleza-password">
                        <div class="fortaleza-barra" id="fortaleza-barra"></div>
                    </div>
                    <p class="fortaleza-label" id="fortaleza-label"></p>
                </div>

            </div>

            @error('error_usuario')
                <div class="alerta-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
            @enderror

            <div class="acciones-form">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Crear usuario
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/admin/usuarios.js') }}"></script>
@endpush
