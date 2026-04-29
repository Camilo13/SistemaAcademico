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
                <div class="slide-hero activo"
                     style="background-image: url('{{ asset('img/inicio/hero.webp') }}')">
                </div>
            @endforelse
        </div>

        {{-- Flecha de scroll ── --}}
        <div class="scroll-indicador" aria-hidden="true">
            <i class="fas fa-chevron-down"></i>
        </div>

        {{-- Indicadores del carrusel (se generan por JS) --}}
        <div class="carrusel-indicadores" id="carrusel-indicadores"></div>

        {{-- Texto sobre la imagen --}}
        <div class="contenedor-hero">
            <h1 class="hero-titulo">Bienvenidos a la I.E. Agroambiental<br>Akwe Uus Yat La Gaitana</h1>
            <p class="hero-subtitulo">Formamos líderes comunitarios en defensa del territorio,<br>
               la identidad cultural y los derechos de nuestro pueblo.</p>
        </div>

    </section>

    {{-- ══════════════════════════════════════════════
         MISIÓN Y VISIÓN
    ══════════════════════════════════════════════ --}}
    <section class="seccion-mision-vision">
        <div class="contenedor-inicio">
            <div class="mision-vision-grid">

                <div class="mision-card mv-animado">
                    <h2>Nuestra Misión</h2>
                    <p>
                        La Institución Educativa Agroambiental A´Kwe Úus Yat del Resguardo
                        Indígena La Gaitana <strong>orienta y forma estudiantes con espíritu de
                        liderazgo comunitario</strong>, en defensa del territorio y de la identidad
                        cultural, fundamentada en el marco de la reivindicación del derecho mayor
                        y de los derechos fundamentales que <strong>guíen el camino de nuestras
                        futuras generaciones</strong>.
                    </p>
                </div>

                <div class="vision-card mv-animado">
                    <h2>Nuestra Visión</h2>
                    <p>
                        En nuestro proceso de formación pretendemos la
                        <strong>consolidación de una educación propia de calidad</strong>, en el
                        marco del desarrollo del SEIP, acorde a las necesidades del contexto
                        basado en el querer de la Nasa Wala <em>"asamblea"</em>, formando
                        personas competentes e íntegras que fortalezcan la defensa de
                        <em>uma kiwe</em> <strong>"territorio"</strong> y la pervivencia de
                        nuestro pueblo.
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

            <h2 class="titulo-seccion-central">Raíces Vivas, Futuro Propio</h2>

            <div class="contenido-esencia">

                <div class="imagen-esencia">
                    <img src="{{ asset('img/inicio/4.webp') }}"
                         alt="Niños participando en actividades culturales de la institución"
                         loading="lazy">
                </div>

                <div class="texto-esencia">
                    <p>
                        Nacida de la decisión soberana del Resguardo Indígena La Gaitana,
                        nuestra institución lleva el nombre <strong>Akwe Uus Yat</strong>,
                        dado por mayores y hablantes de Nasayuwe tras un proceso de
                        consulta comunitaria. Somos el resultado de años de resistencia,
                        organización y lucha por el <strong>derecho a una educación propia</strong>
                        que respete la lengua, el territorio y la cultura Nasa.
                    </p>
                    <p>
                        Desde la vereda El Lago del Resguardo La Gaitana, en el municipio
                        de Inzá, Cauca, formamos estudiantes de básica primaria, secundaria
                        y media técnica con un currículo que integra el
                        <strong>conocimiento ancestral y científico</strong>, el cuidado
                        de la Madre Tierra y el desarrollo de la capacidad crítica e investigativa.
                    </p>
                </div>

            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('js/publico/carrusel.js') }}"></script>
@endsection