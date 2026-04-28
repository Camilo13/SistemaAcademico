<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Akwe Uus Yat</title>
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>

<div class="login-contenedor">
    <div class="login-caja">

        <div class="login-cabecera">
            <h2 class="login-titulo">Portal Académico</h2>
            <p class="login-subtitulo">Inicia sesión para ingresar a la plataforma</p>
            <img src="{{ asset('img/logo.png') }}" alt="Logo institucional" class="login-logo">
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="login-campo">
                <label for="identificacion">Usuario</label>
                <input type="text"
                       id="identificacion"
                       name="identificacion"
                       value="{{ old('identificacion') }}"
                       placeholder="Ingrese su número de identificación"
                       required
                       autofocus>
            </div>

            <div class="login-campo">
                <label for="password">Contraseña</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Ingrese su contraseña"
                       required>
            </div>

            <div class="login-botones">
                <button type="submit" class="login-btn login-btn--primario">
                    Ingresar
                </button>
                <a href="{{ route('inicio') }}" class="login-btn login-btn--neutro">
                    Atrás
                </a>
            </div>

        </form>

        <div class="login-pie">
            <a href="{{ route('solicitud.create') }}">Regístrate ahora</a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.APP_ALERTS = {
        exito       : @json(session('exito')),
        error       : @json(session('error')),
        info        : @json(session('info')),
        advertencia : @json(session('advertencia')),
        errores     : @json($errors->all()),
    };
</script>
<script src="{{ asset('js/global/notificaciones.js') }}"></script>
<script src="{{ asset('js/auth/login.js') }}"></script>

</body>
</html>
