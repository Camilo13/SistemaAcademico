<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Estudiante') — I.E.A.</title>

    {{-- ── CSS globales ── --}}
    <link rel="stylesheet" href="{{ asset('css/layout/menuestudiante.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global/notificaciones.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/botones.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/formularios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/resumen.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/tarjetas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global/tipografia.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/tablas.css') }}">

    {{-- ── FontAwesome 6 ── --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">

    {{-- ── CSS específico de la vista activa ── --}}
    @stack('styles')
</head>
<body>

    {{-- ── Botón hamburguesa (móvil) ── --}}
    <button class="boton-menu" onclick="toggleMenu()" aria-label="Abrir menú">
        <i class="fas fa-bars"></i>
    </button>

    {{-- ════════════════════════════════════════════════
         BARRA LATERAL
    ════════════════════════════════════════════════ --}}
    <aside class="barra-lateral" data-menu>

        {{-- Logo / Nombre institución --}}
        <div class="barra-logo">
            I.E.A. Akwe Uus Yat
        </div>

        {{-- ── Navegación principal ── --}}
        <nav class="barra-nav">
            <ul class="lista-menu">

                {{-- ── 1. Perfil ── --}}
                <li>
                    <a href="{{ route('perfil') }}">
                        <i class="fas fa-user-circle icono"></i>
                        Perfil
                    </a>
                </li>

                {{-- ── 2. Mi Académico (desplegable) ── --}}
                <li class="menu-padre">
                    <button type="button" class="menu-toggle">
                        <span>
                            <i class="fas fa-graduation-cap icono"></i>
                            Mi Académico
                        </span>
                        <i class="fas fa-chevron-down flecha"></i>
                    </button>

                    <ul class="submenu">

                        {{-- ┄ Notas ┄ --}}
                        <li class="submenu-titulo">Notas</li>

                        <li>
                            <a href="{{ route('estudiante.notas.index') }}">
                                <i class="fas fa-chart-line icono"></i> Mis Notas
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('estudiante.boletin.index') }}">
                                <i class="fas fa-file-alt icono"></i> Mis Boletines
                            </a>
                        </li>

                        {{-- ┄ Inscripciones ┄ --}}
                        <li class="submenu-titulo">Inscripciones</li>

                        {{-- NOTA: rutas con prefijo academico --}}
                        <li>
                            <a href="{{ route('estudiante.academico.inscripciones') }}">
                                <i class="fas fa-book-open icono"></i> Mis Inscripciones
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('estudiante.academico.materias') }}">
                                <i class="fas fa-layer-group icono"></i> Mis Materias
                            </a>
                        </li>

                        {{-- ┄ Asistencia ┄ --}}
                        <li class="submenu-titulo">Asistencia</li>
                        <li>
                            <a href="{{ route('estudiante.asistencia.index') }}">
                                <i class="fas fa-calendar-check icono"></i> Mis Faltas
                            </a>
                        </li>

                    </ul>
                </li>

                {{-- ── 3. Mi Horario ── --}}
                <li>
                    <a href="{{ route('estudiante.horario.index') }}">
                        <i class="fas fa-calendar-week icono"></i>
                        Mi Horario
                    </a>
                </li>

                {{-- ── 4. Biblioteca Digital ── --}}
                <li>
                    <a href="{{ route('biblioteca.materias.index') }}">
                        <i class="fas fa-book icono"></i>
                        Biblioteca Digital
                    </a>
                </li>

            </ul>
        </nav>

        {{-- ── Cerrar sesión ── --}}
        <div class="barra-salida">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="boton-salir">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </button>
            </form>
        </div>

    </aside>

    {{-- ════════════════════════════════════════════════
         CONTENIDO PRINCIPAL
    ════════════════════════════════════════════════ --}}
    <main class="contenido">
        @yield('content')
    </main>

    {{-- ── JS globales ── --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/global/notificaciones.js') }}"></script>
    <script src="{{ asset('js/layout/menu.js') }}"></script>

    <script>
        window.APP_ALERTS = {
            exito:       @json(session('exito')),
            error:       @json(session('error')),
            advertencia: @json(session('advertencia')),
            info:        @json(session('info')),
            errores:     @json($errors->all())
        };
    </script>

    {{-- ── JS específico de la vista activa ── --}}
    @stack('scripts')

</body>
</html>