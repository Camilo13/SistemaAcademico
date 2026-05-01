<!DOCTYPE html>
<html lang="es">
<script>!function(){var t=localStorage.getItem('acc_tema'),f=localStorage.getItem('acc_fuente');if(t==='oscuro')document.documentElement.classList.add('modo-oscuro');if(f&&f!=='normal')document.documentElement.classList.add('fuente-'+f);}();</script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') — I.E.A. Akwe Uus Yat</title>
    
    <link rel="stylesheet" href="{{ asset('css/global/tipografia.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global/notificaciones.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/menuadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/botones.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/formularios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/resumen.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/tarjetas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/tablas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/academico-form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/componentes/academico-index.css') }}">

    {{-- ── FontAwesome 6 ── --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="stylesheet" href="{{ asset('css/accesibilidad.css') }}">

    {{-- ── CSS específico de cada vista ── --}}
    @stack('styles')
</head>
<body>

    {{-- ── Botón hamburguesa (solo en móvil) ── --}}
    <button class="boton-menu" onclick="toggleMenu()" aria-label="Abrir menú">
        <i class="fas fa-bars"></i>
    </button>

    {{-- ══════════════════════════════════════════════════════
         BARRA LATERAL
    ══════════════════════════════════════════════════════ --}}
    <aside class="barra-lateral" data-menu>

        {{-- Logo institución --}}
        <div class="barra-logo">
            I.E.A. Akwe Uus Yat
        </div>

        {{-- ── Navegación ── --}}
        <nav class="barra-nav">
            <ul class="lista-menu">

                {{-- ── Inicio ── --}}
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'activo' : '' }}">
                        <i class="fas fa-house icono"></i>
                        Inicio
                    </a>
                </li>

                {{-- ── Perfil ── --}}
                <li>
                    <a href="{{ route('perfil') }}"
                       class="{{ request()->routeIs('perfil') ? 'activo' : '' }}">
                        <i class="fas fa-user-circle icono"></i>
                        Mi Perfil
                    </a>
                </li>

                {{-- ══════════════════════════════════════════
                     GESTIÓN ACADÉMICA (desplegable)
                ══════════════════════════════════════════ --}}
                <li class="menu-padre">
                    <button type="button" class="menu-toggle">
                        <span>
                            <i class="fas fa-graduation-cap icono"></i>
                            Gestión Académica
                        </span>
                        <i class="fas fa-chevron-down flecha"></i>
                    </button>

                    <ul class="submenu">

                        {{-- ┄ Estructura ┄ --}}
                        <li class="submenu-titulo">Estructura</li>

                        <li>
                            <a href="{{ route('admin.academico.sedes.index') }}"
                               class="{{ request()->routeIs('admin.academico.sedes.*') ? 'activo' : '' }}">
                                <i class="fas fa-school icono"></i> Sedes
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.academico.grados.index') }}"
                               class="{{ request()->routeIs('admin.academico.grados.*') ? 'activo' : '' }}">
                                <i class="fas fa-layer-group icono"></i> Grados
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.academico.estructura.materias.index') }}"
                               class="{{ request()->routeIs('admin.academico.estructura.materias.*') ? 'activo' : '' }}">
                                <i class="fas fa-book-open icono"></i> Materias
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.academico.estructura.grupos.index') }}"
                               class="{{ request()->routeIs('admin.academico.estructura.grupos.*') ? 'activo' : '' }}">
                                <i class="fas fa-users icono"></i> Grupos
                            </a>
                        </li>

                        {{-- ┄ Calendario ┄ --}}
                        <li class="submenu-titulo">Calendario</li>

                        <li>
                            <a href="{{ route('admin.academico.anios.index') }}"
                               class="{{ request()->routeIs('admin.academico.anios.*') ? 'activo' : '' }}">
                                <i class="fas fa-calendar-check icono"></i> Años Lectivos
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.academico.horarios.index') }}"
                               class="{{ request()->routeIs('admin.academico.horarios.*') ? 'activo' : '' }}">
                                <i class="fas fa-clock icono"></i> Horarios
                            </a>
                        </li>

                        {{-- ┄ Operativa ┄ --}}
                        <li class="submenu-titulo">Operativa</li>

                        <li>
                            <a href="{{ route('admin.academico.asignaciones.index') }}"
                               class="{{ request()->routeIs('admin.academico.asignaciones.*') ? 'activo' : '' }}">
                                <i class="fas fa-chalkboard-teacher icono"></i> Asignaciones
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.academico.inscripciones.index') }}"
                               class="{{ request()->routeIs('admin.academico.inscripciones.*') ? 'activo' : '' }}">
                                <i class="fas fa-clipboard-list icono"></i> Inscripciones
                            </a>
                        </li>

                    </ul>
                </li>

                {{-- ── Gestión de Usuarios ── --}}
                <li>
                    <a href="{{ route('admin.usuarios.index') }}"
                       class="{{ request()->routeIs('admin.usuarios.*') ? 'activo' : '' }}">
                        <i class="fas fa-users-cog icono"></i>
                        Gestión de Usuarios
                    </a>
                </li>

                {{-- ── Solicitudes ── --}}
                <li>
                    <a href="{{ route('admin.solicitudes.index') }}"
                       class="{{ request()->routeIs('admin.solicitudes.*') ? 'activo' : '' }}">
                        <i class="fas fa-envelope-open-text icono"></i>
                        Solicitudes
                    </a>
                </li>

                {{-- ── Biblioteca ── --}}
                <li>
                    <a href="{{ route('admin.biblioteca.materias.index') }}"
                       class="{{ request()->routeIs('admin.biblioteca.*') ? 'activo' : '' }}">
                        <i class="fas fa-book icono"></i>
                        Biblioteca
                    </a>
                </li>

                {{-- ══════════════════════════════════════════
                     PORTAL PÚBLICO (desplegable)
                ══════════════════════════════════════════ --}}
                <li class="menu-padre">
                    <button type="button" class="menu-toggle">
                        <span>
                            <i class="fas fa-globe icono"></i>
                            Portal Público
                        </span>
                        <i class="fas fa-chevron-down flecha"></i>
                    </button>

                    <ul class="submenu">
                        <li>
                            <a href="{{ route('admin.carrusel.index') }}"
                               class="{{ request()->routeIs('admin.carrusel.*') ? 'activo' : '' }}">
                                <i class="fas fa-images icono"></i> Carrusel
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.eventos.index') }}"
                               class="{{ request()->routeIs('admin.eventos.*') ? 'activo' : '' }}">
                                <i class="fas fa-calendar-alt icono"></i> Eventos
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>

        {{-- ── Cerrar sesión ── --}}
        <div class="barra-salida">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="boton-salir">
                    <i class="fas fa-right-from-bracket"></i> Cerrar sesión
                </button>
            </form>
        </div>

    </aside>

    {{-- ══════════════════════════════════════════════════════
         CONTENIDO PRINCIPAL
    ══════════════════════════════════════════════════════ --}}
    <main class="contenido">
        @yield('content')
    </main>

    {{-- ══════════════════════════════════════════════════════
         JS GLOBALES
         SweetAlert2 primero, luego notificaciones.js que lo usa.
    ══════════════════════════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/global/notificaciones.js') }}"></script>
    <script src="{{ asset('js/layout/menu.js') }}"></script>

    {{-- ── Datos de alertas inyectados desde el servidor ── --}}
    <script>
        window.APP_ALERTS = {
            exito      : @json(session('exito')),
            error      : @json(session('error')),
            advertencia: @json(session('advertencia')),
            info       : @json(session('info')),
            errores    : @json($errors->all()),
        };
    </script>

    {{-- ── JS específico de cada vista ── --}}
    @stack('scripts')

    @include('components.accesibilidad')
</body>
</html>
