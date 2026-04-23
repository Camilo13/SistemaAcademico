<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Registro — Akwe Uus Yat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── Tipografía primero: define todas las variables CSS del sistema ── --}}
    <link rel="stylesheet" href="{{ asset('css/global/tipografia.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global/notificaciones.css') }}">

    {{-- ── Componentes reutilizables ── --}}
    <link rel="stylesheet" href="{{ asset('css/componentes/tarjetas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/formularios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/botones.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/resumen.css') }}">

    {{-- ── Estilos del módulo ── --}}
    <link rel="stylesheet" href="{{ asset('css/modulos/solicitudes/solicitud.css') }}">
</head>
<body>

<section class="registro">

    <div class="registro-contenedor card">

        {{-- ── Encabezado ── --}}
        <header class="registro-encabezado">
            <h2 class="registro-titulo">Solicitud de Registro</h2>
            <p class="registro-mensaje">
                Completa el formulario para solicitar tu acceso a la plataforma educativa.
            </p>
        </header>

        {{-- ── Barra de progreso ── --}}
        <div class="progreso">
            <div class="progreso-barra">
                <div class="progreso-relleno"></div>
            </div>
            <p class="progreso-texto">Paso 1 de 6</p>
        </div>

        {{-- ── Formulario multipaso ── --}}
        <form id="formSolicitud"
              method="POST"
              action="{{ route('solicitud.store') }}"
              class="formulario">
            @csrf

            {{-- PASO 1: Datos personales --}}
            <div class="registro-paso paso-activo" data-paso="1">
                <h3 class="card-titulo">Datos personales</h3>

                <div class="grupo-formulario">
                    <label for="nombre">Nombres</label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre') }}" required>
                </div>

                <div class="grupo-formulario">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos"
                           value="{{ old('apellidos') }}" required>
                </div>
            </div>

            {{-- PASO 2: Identificación --}}
            <div class="registro-paso paso-oculto" data-paso="2">
                <h3 class="card-titulo">Identificación</h3>

                <div class="grupo-formulario">
                    <label for="identificacion">Número de documento</label>
                    <input type="text" id="identificacion" name="identificacion"
                           value="{{ old('identificacion') }}" required>
                </div>

                <p class="registro-submensaje">
                    <i class="fa-solid fa-circle-info"></i>
                    Este número será utilizado para iniciar sesión en la plataforma.
                </p>
            </div>

            {{-- PASO 3: Ocupación --}}
            <div class="registro-paso paso-oculto" data-paso="3">
                <h3 class="card-titulo">Ocupación</h3>

                <div class="grupo-formulario">
                    <label for="rol">Ocupación</label>
                    <select id="rol" name="rol" required>
                        <option value="">Selecciona una opción</option>
                        <option value="docente"    {{ old('rol') === 'docente'    ? 'selected' : '' }}>Docente</option>
                        <option value="estudiante" {{ old('rol') === 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                    </select>
                </div>
            </div>

            {{-- PASO 4: Información de contacto --}}
            <div class="registro-paso paso-oculto" data-paso="4">
                <h3 class="card-titulo">Información de contacto</h3>

                <div class="grupo-formulario">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo"
                           value="{{ old('correo') }}" required>
                </div>

                <div class="grupo-formulario">
                    <label for="ubicacion">Ubicación</label>
                    <input type="text" id="ubicacion" name="ubicacion"
                           value="{{ old('ubicacion') }}" required>
                </div>

                <div class="grupo-formulario">
                    <label for="contacto">Número de contacto</label>
                    <input type="text" id="contacto" name="contacto"
                           value="{{ old('contacto') }}" required>
                </div>
            </div>

            {{-- PASO 5: Seguridad --}}
            <div class="registro-paso paso-oculto" data-paso="5">
                <h3 class="card-titulo">Seguridad</h3>

                <p class="registro-submensaje">
                    <i class="fa-solid fa-shield-halved"></i>
                    Esta contraseña se usará junto con tu identificación para ingresar.
                </p>

                <div class="grupo-formulario grupo-password">
                    <label for="password">Contraseña</label>
                    <div class="campo-password">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="btn-ver" data-target="password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="grupo-formulario grupo-password">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <div class="campo-password">
                        <input type="password" id="password_confirmation"
                               name="password_confirmation" required>
                        <button type="button" class="btn-ver" data-target="password_confirmation">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- PASO 6: Resumen --}}
            <div class="registro-paso paso-oculto" data-paso="6">
                <h3 class="card-titulo">Resumen de la solicitud</h3>

                <p class="registro-submensaje">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Verifica cuidadosamente la información antes de enviarla.
                </p>

                <div class="resumen" id="resumenSolicitud"></div>
            </div>

            {{-- ── Acciones ── --}}
            <div class="registro-acciones">
                <a href="{{ route('login') }}"
                   class="btn btn-neutro"
                   id="btnCancelar">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>

                <button type="button" class="btn btn-neutro oculto" id="btnAnterior">
                    <i class="fa-solid fa-arrow-left"></i> Anterior
                </button>

                <button type="button" class="btn btn-secundario" id="btnSiguiente">
                    Siguiente <i class="fa-solid fa-arrow-right"></i>
                </button>

                <button type="submit" class="btn btn-primario oculto" id="btnEnviar">
                    <i class="fa-solid fa-paper-plane"></i> Enviar solicitud
                </button>
            </div>

        </form>

    </div>
</section>

{{-- FontAwesome 6 --}}
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      crossorigin="anonymous" referrerpolicy="no-referrer">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.APP_ALERTS = {
        exito       : @json(session('exito')),
        error       : @json(session('error')),
        advertencia : @json(session('advertencia')),
        info        : @json(session('info')),
        errores     : @json($errors->all())
    };
</script>

<script src="{{ asset('js/global/notificaciones.js') }}"></script>
<script src="{{ asset('js/modulos/solicitudes/solicitud.js') }}"></script>

</body>
</html>
