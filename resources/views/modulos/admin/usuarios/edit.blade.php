@extends('layouts.menuadmin')

@section('title', 'Editar — ' . $usuario->nombre_completo)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/admin/usuarios/edit.css') }}">
@endpush

@section('content')
<div class="contenedor-modulo">

    {{-- ══════════════════════════════════════════
         CABECERA — Sin botón "Volver"
    ══════════════════════════════════════════ --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2><i class="fa-solid fa-user-pen"></i> Editar Usuario</h2>
            <p class="cabecera-subtitulo">{{ $usuario->nombre_completo }}</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TARJETA PRINCIPAL — Datos personales
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <div class="info-registro">
            <i class="fa-solid fa-circle-info"></i>
            ID: <strong>{{ $usuario->identificacion }}</strong>
            &nbsp;·&nbsp; Rol: <strong>{{ ucfirst($usuario->rol) }}</strong>
            &nbsp;·&nbsp; Estado: <strong>{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</strong>
            &nbsp;·&nbsp; Creado: <strong>{{ $usuario->created_at->format('d/m/Y') }}</strong>
        </div>

        <form method="POST"
              action="{{ route('admin.usuarios.update', $usuario) }}"
              data-form="usuario">
            @csrf @method('PUT')

            <div class="grid-campos">

                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-user"></i> Nombre <span>*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre', $usuario->nombre) }}"
                           required maxlength="255">
                    @error('nombre') <span class="error-campo"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="apellidos">
                        <i class="fa-solid fa-user"></i> Apellidos <span>*</span>
                    </label>
                    <input type="text" id="apellidos" name="apellidos"
                           value="{{ old('apellidos', $usuario->apellidos) }}"
                           required maxlength="255">
                    @error('apellidos') <span class="error-campo"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="identificacion">
                        <i class="fa-solid fa-id-card"></i> Identificación <span>*</span>
                    </label>
                    <input type="text" id="identificacion" name="identificacion"
                           value="{{ old('identificacion', $usuario->identificacion) }}"
                           required maxlength="30" inputmode="numeric">
                    @error('identificacion') <span class="error-campo"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="correo">
                        <i class="fa-solid fa-envelope"></i> Correo electrónico <span>*</span>
                    </label>
                    <input type="email" id="correo" name="correo"
                           value="{{ old('correo', $usuario->correo) }}"
                           required maxlength="255">
                    @error('correo') <span class="error-campo"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="ubicacion">
                        <i class="fa-solid fa-location-dot"></i> Ubicación
                    </label>
                    <input type="text" id="ubicacion" name="ubicacion"
                           value="{{ old('ubicacion', $usuario->ubicacion) }}"
                           maxlength="150">
                    @error('ubicacion') <span class="error-campo">{{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="contacto">
                        <i class="fa-solid fa-phone"></i> Contacto
                    </label>
                    <input type="text" id="contacto" name="contacto"
                           value="{{ old('contacto', $usuario->contacto) }}"
                           maxlength="20" inputmode="numeric">
                    @error('contacto') <span class="error-campo">{{ $message }}</span> @enderror
                </div>

                <div class="campo">
                    <label for="rol">
                        <i class="fa-solid fa-tag"></i> Rol <span>*</span>
                    </label>
                    <select id="rol" name="rol" required>
                        <option value="administrador" {{ old('rol', $usuario->rol) === 'administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="docente"       {{ old('rol', $usuario->rol) === 'docente'       ? 'selected' : '' }}>Docente</option>
                        <option value="estudiante"    {{ old('rol', $usuario->rol) === 'estudiante'    ? 'selected' : '' }}>Estudiante</option>
                    </select>
                    @error('rol') <span class="error-campo">{{ $message }}</span> @enderror
                </div>

                <div class="campo-check">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" id="activo" name="activo" value="1"
                           {{ old('activo', $usuario->activo) ? 'checked' : '' }}>
                    <label for="activo" class="label-check">
                        Usuario activo
                    </label>
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
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
            </div>

        </form>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA — Cambiar contraseña
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-lock"></i> Cambiar contraseña
        </h3>

        <p class="seccion-desc">
            Deja los campos vacíos si no deseas cambiar la contraseña.
            La nueva contraseña debe tener al menos 8 caracteres.
        </p>

        <form method="POST"
              action="{{ route('admin.usuarios.password', $usuario) }}"
              id="form-password">
            @csrf @method('PATCH')

            <div class="grid-campos">

                <div class="campo">
                    <label for="password">
                        <i class="fa-solid fa-lock"></i> Nueva contraseña <span>*</span>
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
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           required minlength="8"
                           placeholder="Repite la nueva contraseña"
                           autocomplete="new-password">
                </div>

                {{-- Barra de fortaleza (controlada por JS) --}}
                <div class="campo-ancho">
                    <div class="fortaleza-password">
                        <div class="fortaleza-barra" id="fortaleza-barra"></div>
                    </div>
                    <p class="fortaleza-label" id="fortaleza-label"></p>
                </div>

            </div>

            <div class="acciones-form">
                <button type="submit" class="btn btn-secundario">
                    <i class="fa-solid fa-key"></i> Actualizar contraseña
                </button>
            </div>

        </form>

    </div>

    {{-- ══════════════════════════════════════════
         TARJETA ACCIÓN — Activar / Desactivar
         Borde ámbar izquierdo
    ══════════════════════════════════════════ --}}
    <div class="tarjeta-form tarjeta-accion">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-toggle-on"></i> Estado del usuario
        </h3>

        <p class="seccion-desc">
            @if($usuario->activo)
                El usuario está <strong>activo</strong> y puede iniciar sesión en el sistema.
            @else
                El usuario está <strong>inactivo</strong> y no puede iniciar sesión.
            @endif
        </p>

        <div class="acciones-secundarias">
            @if($usuario->activo)
                <form method="POST"
                      action="{{ route('admin.usuarios.desactivar', $usuario) }}"
                      class="form-desactivar"
                      data-nombre="{{ $usuario->nombre_completo }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-advertencia">
                        <i class="fa-solid fa-toggle-off"></i> Desactivar usuario
                    </button>
                </form>
            @else
                <form method="POST"
                      action="{{ route('admin.usuarios.activar', $usuario) }}"
                      class="form-activar"
                      data-nombre="{{ $usuario->nombre_completo }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secundario">
                        <i class="fa-solid fa-toggle-on"></i> Activar usuario
                    </button>
                </form>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         SECCIÓN INFORMATIVA — Relaciones
         Solo lectura. Ayuda al admin a saber si
         el usuario puede eliminarse o no.
    ══════════════════════════════════════════ --}}
    @if($usuario->inscripciones->isNotEmpty() || $usuario->asignaciones->isNotEmpty())
    <div class="tarjeta-form">

        <h3 class="seccion-titulo-form">
            <i class="fa-solid fa-link"></i> Vínculos académicos
        </h3>
        <p class="seccion-desc">
            Este usuario tiene registros académicos asociados.
            No puede eliminarse mientras tenga estos vínculos activos.
        </p>

        {{-- Inscripciones --}}
        @if($usuario->inscripciones->isNotEmpty())
            <h4 class="relaciones-subtitulo">
                <i class="fa-solid fa-clipboard-list"></i>
                Inscripciones ({{ $usuario->inscripciones->count() }})
            </h4>
            <div class="tabla-contenedor">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Grupo</th>
                            <th>Grado</th>
                            <th>Año Lectivo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuario->inscripciones as $ins)
                            <tr>
                                <td>{{ $ins->grupo->nombre ?? '—' }}</td>
                                <td>{{ $ins->grupo->grado->nombre ?? '—' }}</td>
                                <td>{{ $ins->grupo->anioLectivo->nombre ?? '—' }}</td>
                                <td>
                                    <span class="estado estado-{{ strtolower($ins->estado ?? 'activo') }}">
                                        {{ ucfirst($ins->estado ?? 'Activo') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Asignaciones --}}
        @if($usuario->asignaciones->isNotEmpty())
            <h4 class="relaciones-subtitulo" style="margin-top:1.2rem;">
                <i class="fa-solid fa-chalkboard-user"></i>
                Asignaciones docentes ({{ $usuario->asignaciones->count() }})
            </h4>
            <div class="tabla-contenedor">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Grupo</th>
                            <th>Año Lectivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuario->asignaciones as $asig)
                            <tr>
                                <td>{{ $asig->materia->nombre ?? '—' }}</td>
                                <td>{{ $asig->grupo->nombre ?? '—' }}</td>
                                <td>{{ $asig->grupo->anioLectivo->nombre ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/admin/usuarios.js') }}"></script>
@endpush
