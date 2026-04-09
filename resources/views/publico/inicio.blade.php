@extends('layouts.menupublico')

@section('title', 'Inicio — I.E. Agroambiental Akwe Uus Yat')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/publico/inicio.css') }}">
@endsection

@section('content')

    {{-- ══════════════════════════════════════════════
         HERO — Carrusel de imágenes
    ══════════════════════════════════════════════ --}}
    <section class="hero">

        {{-- Carrusel de fondo --}}
        <div class="carrusel-hero">
            @forelse ($imagenesCarrusel as $imagen)
                <div class="slide-hero"
                     style="background-image: url('{{ Storage::url($imagen->imagen) }}')">
                </div>
            @empty
                {{-- Imagen de respaldo si no hay registros en el carrusel --}}
                <div class="slide-hero activo"
                     style="background-image: url('{{ asset('img/inicio/hero.webp') }}')">
                </div>
            @endforelse
        </div>

        {{-- Indicadores del carrusel (se generan por JS) --}}
        <div class="carrusel-indicadores" id="carrusel-indicadores"></div>

        {{-- Texto sobre la imagen --}}
        <div class="contenedor-hero">
            <h1>Bienvenidos a la I.E. Agroambiental Akwe Uus Yat La Gaitana</h1>
            <p>Educamos con raíces culturales, saberes ancestrales y compromiso ambiental.</p>
        </div>

    </section>

    {{-- ══════════════════════════════════════════════
         MISIÓN Y VISIÓN
    ══════════════════════════════════════════════ --}}
    <section class="seccion-mision-vision">
        <div class="contenedor-inicio">
            <div class="mision-vision-grid">

                <div class="mision-card">
                    <h2>Nuestra Misión</h2>
                    <p>
                        Somos una institución educativa de carácter étnico agroambiental que
                        <strong>desarrolla procesos pedagógicos propios</strong>, fortaleciendo la identidad cultural,
                        la lengua, la autonomía y la participación comunitaria. Formamos
                        <strong>seres humanos integrales</strong> con pensamiento crítico, capaces de contribuir a la
                        construcción de un proyecto de vida digno en armonía con la Madre Tierra.
                    </p>
                </div>

                <div class="vision-card">
                    <h2>Nuestra Visión</h2>
                    <p>
                        Para el año 2028, seremos reconocida como una institución educativa étnica
                        agroambiental <strong>líder en la formación integral</strong>, basada en el rescate de los
                        saberes ancestrales, el cuidado del territorio y la excelencia académica.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════
         NUESTRA ESENCIA
    ══════════════════════════════════════════════ --}}
    <section class="seccion-nuestra-esencia">
        <div class="contenedor-inicio">

            <h2 class="titulo-seccion-central">Nuestra Esencia: Corazón y Legado</h2>

            <div class="contenido-esencia">

                <div class="imagen-esencia">
                    <img src="{{ asset('img/inicio/4.webp') }}"
                         alt="Niños participando en actividades culturales de la institución"
                         loading="lazy">
                </div>

                <div class="texto-esencia">
                    <p>
                        Nuestra esencia radica en el tejido profundo de la cultura ancestral con
                        el saber agroambiental. Promovemos un vínculo respetuoso con la tierra y
                        las <strong>tradiciones que nos definen</strong>, formando ciudadanos conscientes,
                        responsables y comprometidos con su territorio.
                    </p>
                </div>

            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('js/publico/carrusel.js') }}"></script>
@endsection
