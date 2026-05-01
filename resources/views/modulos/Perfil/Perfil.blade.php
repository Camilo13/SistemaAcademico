@extends($layout)

@section('title', 'Mi Perfil')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/perfil/perfil.css') }}">
@endpush

@section('content')

<section class="perfil">

    {{-- ══════════════════════════════════════════
         ENCABEZADO
    ══════════════════════════════════════════ --}}
    <header class="perfil-header">

        {{-- Avatar con ícono según rol --}}
        <div class="perfil-avatar rol-{{ $user->rol }}">
            @switch($user->rol)
                @case('administrador')
                    <i class="fa-solid fa-shield-halved"></i>
                    @break
                @case('docente')
                    <i class="fa-solid fa-chalkboard-user"></i>
                    @break
                @default
                    <i class="fa-solid fa-user-graduate"></i>
            @endswitch
        </div>

        <div>
            <h2 class="perfil-titulo">Mi Perfil</h2>
            <p class="perfil-descripcion">
                Consulta tu información personal y actualiza tus datos de contacto.
            </p>
        </div>

    </header>

    {{-- ══════════════════════════════════════════
         GRID DE TARJETAS
    ══════════════════════════════════════════ --}}
    <div class="perfil-grid">

        {{-- ── Tarjeta 1: Información personal (solo lectura) ── --}}
        <article class="card perfil-info">

            <h3 class="card-titulo">Información Personal</h3>

            {{-- Los datos de identidad no son editables desde el perfil --}}
            <div style="display:flex; flex-direction:column; gap:0.85rem;">

                <div class="campo-readonly">
                    <span class="campo-label">
                        <i class="fa-solid fa-user"></i> Nombre completo
                    </span>
                    <span class="campo-valor">
                        {{ $user->nombre }} {{ $user->apellidos }}
                    </span>
                </div>

                <div class="campo-readonly">
                    <span class="campo-label">
                        <i class="fa-solid fa-id-card"></i> Identificación
                    </span>
                    <span class="campo-valor">{{ $user->identificacion }}</span>
                </div>

                <div class="campo-readonly">
                    <span class="campo-label">
                        <i class="fa-solid fa-envelope"></i> Correo electrónico
                    </span>
                    <span class="campo-valor">{{ $user->correo }}</span>
                </div>

                <div class="campo-readonly">
                    <span class="campo-label">
                        <i class="fa-solid fa-tag"></i> Rol en el sistema
                    </span>
                    <span class="campo-valor" style="background:transparent; border:none; padding:0.3rem 0;">
                        <span class="badge-rol rol-{{ $user->rol }}">
                            @switch($user->rol)
                                @case('administrador')
                                    <i class="fa-solid fa-shield-halved"></i> Administrador
                                    @break
                                @case('docente')
                                    <i class="fa-solid fa-chalkboard-user"></i> Docente
                                    @break
                                @default
                                    <i class="fa-solid fa-user-graduate"></i> Estudiante
                            @endswitch
                        </span>
                    </span>
                </div>

            </div>

            <hr class="perfil-separador">

            <p class="perfil-nota-readonly">
                <i class="fa-solid fa-lock"></i>
                Estos datos solo pueden ser modificados por un administrador.
            </p>

        </article>

        {{-- ── Tarjeta 2: Información de contacto (editable) ── --}}
        <article class="card perfil-editar">

            <h3 class="card-titulo">Información de Contacto</h3>

            <form id="perfilForm"
                  method="POST"
                  action="{{ route('perfil.update') }}"
                  class="formulario">
                @csrf
                @method('PUT')

                {{-- Contacto --}}
                <div class="grupo-formulario">
                    <label for="contacto">
                        <i class="fa-solid fa-phone"></i> NÚMERO DE CONTACTO
                    </label>
                    <input type="tel"
                           id="contacto"
                           name="contacto"
                           value="{{ old('contacto', $user->contacto) }}"
                           placeholder="Ej: 3001234567"
                           maxlength="20"
                           disabled>
                    @error('contacto')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Ubicación --}}
                <div class="grupo-formulario">
                    <label for="ubicacion">
                        <i class="fa-solid fa-location-dot"></i> UBICACIÓN
                    </label>
                    <input type="text"
                           id="ubicacion"
                           name="ubicacion"
                           value="{{ old('ubicacion', $user->ubicacion) }}"
                           placeholder="Ciudad o dirección"
                           maxlength="150"
                           disabled>
                    @error('ubicacion')
                        <span class="error-campo">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Error general del servidor --}}
                @error('error_perfil')
                    <div class="alerta-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $message }}
                    </div>
                @enderror

                {{-- Botones de acción --}}
                <div class="perfil-acciones">

                    {{-- Editar: visible por defecto --}}
                    <button type="button"
                            id="btnEditar"
                            class="btn btn-secundario">
                        <i class="fa-solid fa-pen"></i> Editar
                    </button>

                    {{-- Guardar: oculto hasta que se presione Editar --}}
                    <button type="submit"
                            id="btnGuardar"
                            class="btn btn-primario oculto">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                    </button>

                    {{-- Cancelar: oculto hasta que se presione Editar --}}
                    <button type="button"
                            id="btnCancelar"
                            class="btn btn-neutro oculto">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </button>

                </div>

            </form>

        </article>

    </div>

</section>

@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/perfil/perfil.js') }}"></script>
@endpush
