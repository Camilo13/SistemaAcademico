<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'I.E. Agroambiental Akwe Uus Yat')</title>

    {{-- ── CSS base del portal público ── --}}
    <link rel="stylesheet" href="{{ asset('css/layout/menupublico.css') }}">

    {{-- ── FontAwesome 6 ── --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="stylesheet" href="{{ asset('css/accesibilidad.css') }}">

    {{-- ── CSS específico de cada página ── --}}
    @yield('styles')
</head>
<body>

    {{-- ══════════════════════════════════════════════════
         NAVBAR
    ══════════════════════════════════════════════════ --}}
    <header>
        <nav class="navbar">

            {{-- Botón hamburguesa (móvil) --}}
            <button class="boton-menu" onclick="toggleMenu()" aria-label="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>

            {{-- Logo institucional --}}
            <div class="logo">
                <a href="{{ route('inicio') }}">
                    <i class="fas fa-leaf"></i>
                    Institución Educativa Agroambiental
                </a>
            </div>

            {{-- Links de navegación --}}
            <ul class="nav-links" data-menu>

                <li>
                    <a href="{{ route('inicio') }}"
                       class="{{ request()->routeIs('inicio') ? 'activo' : '' }}">
                        <i class="fas fa-house"></i> Inicio
                    </a>
                </li>

                <li>
                    <a href="{{ route('sobrenosotros') }}"
                       class="{{ request()->routeIs('sobrenosotros') ? 'activo' : '' }}">
                        <i class="fas fa-users"></i> Sobre Nosotros
                    </a>
                </li>

                <li>
                    <a href="{{ route('contacto') }}"
                       class="{{ request()->routeIs('contacto') ? 'activo' : '' }}">
                        <i class="fas fa-envelope"></i> Contacto
                    </a>
                </li>

                <li>
                    <a href="{{ route('public.eventos') }}"
                       class="{{ request()->routeIs('public.eventos') ? 'activo' : '' }}">
                        <i class="fas fa-calendar-alt"></i> Eventos
                    </a>
                </li>

                <li>
                    <a href="{{ route('login') }}"
                       class="{{ request()->routeIs('login') ? 'activo' : '' }}">
                        <i class="fas fa-right-to-bracket"></i> Iniciar Sesión
                    </a>
                </li>

            </ul>

        </nav>
    </header>

    {{-- ── Contenido dinámico de cada página ── --}}
    <main>
        @yield('content')
    </main>

    {{-- ══════════════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════════════ --}}
    <footer>
        <p>
            &copy; {{ date('Y') }}
            Institución Educativa Agroambiental Akwe Uus Yat —
            Todos los derechos reservados.
        </p>
    </footer>

    {{-- ── JS del menú ── --}}
    <script src="{{ asset('js/layout/menu.js') }}"></script>

    {{-- ── JS específico de cada página ── --}}
    @yield('scripts')

    @include('components.accesibilidad')
</body>
</html>
