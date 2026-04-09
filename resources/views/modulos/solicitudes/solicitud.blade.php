<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- =====================================================
        ESTILOS REUTILIZABLES
    ====================================================== --}}
    <link rel="stylesheet" href="{{ asset('css/componentes/tarjetas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/formularios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/botones.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/resumen.css') }}">

    {{-- =====================================================
        ESTILOS DEL MÓDULO
    ====================================================== --}}
    <link rel="stylesheet" href="{{ asset('css/modulos/solicitudes/solicitud.css') }}">
</head>
<body>

<section class="registro">

    <div class="registro-contenedor card">

        {{-- =====================================================
            ENCABEZADO
        ====================================================== --}}
        <header class="registro-encabezado">
            <h2 class="registro-titulo">Solicitud de Registro</h2>
            <p class="registro-mensaje">
                Completa el formulario para solicitar tu acceso a la plataforma educativa.
            </p>
        </header>

        {{-- =====================================================
            BARRA DE PROGRESO
        ====================================================== --}}
        <div class="progreso">
            <div class="progreso-barra">
                <div class="progreso-relleno"></div>
            </div>
            <p class="progreso-texto">Paso 1 de 6</p>
        </div>

        {{-- =====================================================
            FORMULARIO MULTIPASO
        ====================================================== --}}
        <form
            id="formSolicitud"
            method="POST"
            action="{{ route('solicitud.store') }}"
            class="formulario"
        >
            @csrf

            {{-- =====================================================
                PASO 1: DATOS PERSONALES
            ====================================================== --}}
            <div class="registro-paso paso-activo" data-paso="1">
                <h3 class="card-titulo">Datos personales</h3>

                <div class="grupo-formulario">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="grupo-formulario">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" required>
                </div>
            </div>

            {{-- =====================================================
                PASO 2: IDENTIFICACIÓN
            ====================================================== --}}
            <div class="registro-paso paso-oculto" data-paso="2">
                <h3 class="card-titulo">Identificación</h3>

                <div class="grupo-formulario">
                    <label for="identificacion">Número de documento</label>
                    <input type="text" id="identificacion" name="identificacion" required>
                </div>

                <p class="registro-submensaje">
                    Este número será utilizado para iniciar sesión en la plataforma.
                </p>
            </div>

            {{-- =====================================================
                PASO 3: OCUPACIÓN
            ====================================================== --}}
            <div class="registro-paso paso-oculto" data-paso="3">
                <h3 class="card-titulo">Ocupación</h3>

                <div class="grupo-formulario">
                    <label for="rol">Ocupación</label>
                    <select id="rol" name="rol" required>
                        <option value="">Selecciona una opción</option>
                        <option value="docente">Docente</option>
                        <option value="estudiante">Estudiante</option>
                    </select>
                </div>
            </div>

            {{-- =====================================================
                PASO 4: INFORMACIÓN DE CONTACTO
            ====================================================== --}}
            <div class="registro-paso paso-oculto" data-paso="4">
                <h3 class="card-titulo">Información de contacto</h3>

                <div class="grupo-formulario">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div class="grupo-formulario">
                    <label for="ubicacion">Ubicación</label>
                    <input type="text" id="ubicacion" name="ubicacion" required>
                </div>

                <div class="grupo-formulario">
                    <label for="contacto">Contacto</label>
                    <input type="text" id="contacto" name="contacto" required>
                </div>
            </div>

            {{-- =====================================================
                PASO 5: SEGURIDAD
            ====================================================== --}}
            <div class="registro-paso paso-oculto" data-paso="5">
                <h3 class="card-titulo">Seguridad</h3>

                <p class="registro-submensaje">
                    Esta contraseña será utilizada junto con tu identificación
                    para ingresar al sistema.
                </p>

                <div class="grupo-formulario grupo-password">
                    <label for="password">Contraseña</label>
                    <div class="campo-password">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="btn-ver" data-target="password">
                            Ver
                        </button>
                    </div>
                </div>

                <div class="grupo-formulario grupo-password">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <div class="campo-password">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                        >
                        <button type="button" class="btn-ver" data-target="password_confirmation">
                            Ver
                        </button>
                    </div>
                </div>
            </div>

            {{-- =====================================================
                PASO 6: RESUMEN FINAL
            ====================================================== --}}
            <div class="registro-paso paso-oculto" data-paso="6">
                <h3 class="card-titulo">Resumen de la solicitud</h3>

                <p class="registro-submensaje">
                    Verifica cuidadosamente la información antes de enviarla.
                </p>

                <div class="resumen" id="resumenSolicitud"></div>
            </div>

            {{-- =====================================================
                ACCIONES
            ====================================================== --}}
            <div class="registro-acciones">

                <a href="{{ route('login') }}" class="btn btn-neutro" id="btnCancelar">
                    Cancelar
                </a>

                <button type="button" class="btn btn-neutro" id="btnAnterior">
                    Anterior
                </button>

                <button type="button" class="btn btn-secundario" id="btnSiguiente">
                    Siguiente
                </button>

                <button
                    type="submit"
                    class="btn btn-primario oculto"
                    id="btnEnviar"
                >
                    Enviar solicitud
                </button>
            </div>

        </form>

    </div>
</section>

{{-- =====================================================
    SCRIPTS
====================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Contrato ÚNICO de mensajes (global) --}}
<script>
    window.APP_ALERTS = {
        exito: @json(session('exito')),
        error: @json(session('error')),
        advertencia: @json(session('advertencia')),
        info: @json(session('info')),
        errores: @json($errors->all())
    };
</script>

<script src="{{ asset('js/global/notificaciones.js') }}"></script>
<script src="{{ asset('js/modulos/solicitud/solicitud.js') }}"></script>

</body>
</html>
