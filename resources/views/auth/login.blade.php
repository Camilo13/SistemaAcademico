<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Akwe Uus Yat</title>

    {{-- Estilos propios del login --}}
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>

<div class="contenedor-login">
    <div class="caja-login">

        <div class="encabezado-login">
            <h2>Portal Académico</h2>
            <p>Inicia sesión para ingresar a la plataforma</p>
            <img src="{{ asset('img/logo.png') }}" alt="Logo institucional" class="logo">
        </div>

        {{-- Formulario de login --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="grupo-campo">
                <label for="identificacion">Usuario</label>
                <input
                    type="text"
                    id="identificacion"
                    name="identificacion"
                    value="{{ old('identificacion') }}"
                    placeholder="Ingrese su número de identificación"
                    required
                    autofocus>
            </div>

            <div class="grupo-campo">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ingrese su contraseña"
                    required>
            </div>

            <div class="botones-accion">
                <a href="{{ route('inicio') }}" class="boton-login">
                    Atrás
                </a>

                <button type="submit" class="boton-login">
                    Ingresar
                </button>
            </div>
        </form>

        <div class="pie-login">
            <a href="{{ route('solicitud.create') }}">
                Regístrate ahora
            </a>
        </div>

    </div>
</div>

{{-- =====================
| Scripts
|===================== --}}

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Contrato ÚNICO de mensajes para toda la aplicación --}}
<script>
    window.APP_ALERTS = {
        exito: @json(session('exito')),
        error: @json(session('error')),
        info: @json(session('info')),
        advertencia: @json(session('advertencia')),
        errores: @json($errors->all()),
    };
</script>

{{-- Notificaciones globales --}}
<script src="{{ asset('js/global/notificaciones.js') }}"></script>

{{-- JS específico del login (ligero, sin lógica de mensajes) --}}
<script src="{{ asset('js/auth/login.js') }}"></script>

</body>
</html>
