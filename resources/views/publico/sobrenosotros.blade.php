{{-- 📌 Vista: Sobre Nosotros --}}
@extends('layouts.menupublico')

{{-- 🔹 Título de la pestaña --}}
@section('title', 'Sobre Nosotros')

{{-- 🔹 Sección de estilos adicionales --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/publico/sobrenosotros.css') }}">
@endsection

{{-- 🔹 Contenido principal --}}
@section('content')

    {{-- 🔸 HERO --}}
    <section class="seccion-hero">
        <div class="contenedor-hero">
            <h1>Sobre Nosotros</h1>
            <p>Educamos con raíces culturales, saberes ancestrales y compromiso ambiental.</p>
        </div>
    </section>

    {{-- 🔸 LOGO E HISTORIA --}}
    <section class="seccion-logo-historia-alternada">
        <div class="contenedor">
            <div class="contenido-alternado">
                <div class="imagen-lado">
                    <img src="{{ asset('img/logo.png') }}" alt="Nuestro logo" class="imagen-logo-nosotros">
                </div>
                <div class="texto-lado">
                    <h2>El Corazón de Nuestra Identidad,</h2>
                    <h2>Nuestro Logo</h2>
                    <p>
                        Más que una imagen, nuestro logo es un <strong>símbolo viviente</strong> que encapsula la profunda
                        conexión entre la <strong>naturaleza, la cultura y el conocimiento ancestral</strong>.
                        Cada trazo y color representa el espíritu inquebrantable de resiliencia y la sólida unión comunitaria
                        que define a nuestra institución. Es un recordatorio constante de nuestros valores fundamentales
                        y el legado que buscamos preservar y transmitir.
                    </p>
                </div>
            </div>

            <div class="contenido-alternado reverso">
                <div class="imagen-lado">
                    <img src="{{ asset('img/sobrenosotros/1.jpg') }}" alt="Edificio de la institución" class="foto-historia">
                </div>
                <div class="texto-lado">
                    <h2>Nuestra Trayectoria, Forjando el Futuro</h2>
                    <p>
                        Desde nuestros inicios, hemos cultivado una educación integral que no solo prepara a nuestros
                        estudiantes para los desafíos del mañana, sino que también los arraiga en su
                        <strong>patrimonio cultural</strong>. Nuestro compromiso inquebrantable con la
                        <strong>excelencia académica</strong> y el <strong>desarrollo personal</strong> ha sido la piedra
                        angular de nuestro éxito. Con cada generación, hemos sembrado semillas de liderazgo, innovación
                        y un profundo sentido de responsabilidad social, contribuyendo activamente al progreso de la sociedad.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 🔸 VALORES --}}
    <section class="seccion-valores-ilustrados-moderna">
        <div class="contenedor">
            <h2>Pilas de Nuestra Comunidad, Nuestros Valores</h2>
            <div class="valores-grid">
                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/respeto.webp') }}" alt="Respeto">
                    <h3>Respeto</h3>
                    <p>Fomentamos la <strong>convivencia armónica</strong> y el reconocimiento mutuo entre todos los miembros.</p>
                </div>
                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/identidadcultural.webp') }}" alt="Identidad Cultural">
                    <h3>Identidad Cultural</h3>
                    <p>Preservamos nuestras <strong>tradiciones</strong> y lengua, fortaleciendo la riqueza de nuestra identidad cultural.</p>
                </div>
                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/compromisoambiental.webp') }}" alt="Compromiso Ambiental">
                    <h3>Compromiso Ambiental</h3>
                    <p>Protegemos y valoramos nuestro entorno natural como base para una vida y aprendizaje <strong>sostenible</strong>.</p>
                </div>
                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/solidaridad.webp') }}" alt="Solidaridad">
                    <h3>Solidaridad</h3>
                    <p>Fortalecemos la <strong>unión comunitaria</strong> para superar juntos cualquier desafío.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 🔸 PROYECTOS DESTACADOS --}}
    <section class="seccion-proyectos-destacados">
        <div class="contenedor">
            <h2>Manos a la Obra, Proyectos que Transforman</h2>
            <p class="subtitulo-proyectos">Fomentamos iniciativas que impulsan el aprendizaje práctico y la autonomía.</p>
            <div class="proyectos-grid">
                {{-- 🔹 Huertas --}}
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/huerta.webp') }}" alt="Huerta Escolar">
                    <div class="overlay-proyecto">
                        <h3>Huertas Escolares</h3>
                        <p>Los estudiantes cultivan alimentos orgánicos y aprenden sobre agricultura sostenible.</p>
                    </div>
                </div>
                {{-- 🔹 Tejidos --}}
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/tejido.webp') }}" alt="Tejidos">
                    <div class="overlay-proyecto">
                        <h3>Tejidos Ancestrales</h3>
                        <p>Mantenemos viva la cultura a través del arte del tejido ancestral.</p>
                    </div>
                </div>
                {{-- 🔹 Caficultura --}}
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/caficultura.webp') }}" alt="caficultura">
                    <div class="overlay-proyecto">
                        <h3>Caficultura</h3>
                        <p>Desde la siembra hasta la cosecha, los estudiantes aprenden todo el ciclo del café.</p>
                    </div>
                </div>
                {{-- 🔹 Ganadería --}}
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/ganaderia.webp') }}" alt="ganaderia">
                    <div class="overlay-proyecto">
                        <h3>Ganadería</h3>
                        <p>Manejo responsable de animales con prácticas de bienestar animal.</p>
                    </div>
                </div>
                {{-- 🔹 Porcicultura --}}
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/porcicultura.webp') }}" alt="porcicultura">
                    <div class="overlay-proyecto">
                        <h3>Porcicultura</h3>
                        <p>Cría de cerdos integrada con la huerta y aprovechamiento de residuos como fertilizante.</p>
                    </div>
                </div>
                {{-- 🔹 Ovinocultura --}}
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/ovinocultura.webp') }}" alt="ovinocultura">
                    <div class="overlay-proyecto">
                        <h3>Ovinocultura</h3>
                        <p>Cuidado y manejo de ovejas, obteniendo lana y leche como recursos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 🔸 INSTALACIONES --}}
    <section class="seccion-instalaciones-galeria">
        <div class="contenedor">
            <h2>Nuestro Espacio, Un Ambiente para Crecer</h2>
            <div class="galeria-moderna">
                <div class="instalacion-card">
                    <img src="{{ asset('img/sobrenosotros/instalaciones/salones-clases.webp') }}" alt="Salón de Clases">
                    <div class="overlay-instalacion">
                        <h3>Salones de Clase</h3>
                        <p>Espacios modernos y cómodos para el aprendizaje interactivo.</p>
                    </div>
                </div>
                <div class="instalacion-card">
                    <img src="{{ asset('img/sobrenosotros/instalaciones/biblioteca.webp') }}" alt="Biblioteca">
                    <div class="overlay-instalacion">
                        <h3>Biblioteca</h3>
                        <p>Un vasto recurso de conocimiento y lectura para todos.</p>
                    </div>
                </div>
                <div class="instalacion-card">
                    <img src="{{ asset('img/sobrenosotros/instalaciones/laboratorio.webp') }}" alt="Laboratorio">
                    <div class="overlay-instalacion">
                        <h3>Laboratorios</h3>
                        <p>Equipados para la experimentación científica y práctica.</p>
                    </div>
                </div>
                <div class="instalacion-card">
                    <img src="{{ asset('img/sobrenosotros/instalaciones/polideportivo.webp') }}" alt="polideportivo">
                    <div class="overlay-instalacion">
                        <h3>Canchas Deportivas</h3>
                        <p>Áreas para la actividad física y el desarrollo integral.</p>
                    </div>
                </div>
                <div class="instalacion-card">
                    <img src="{{ asset('img/sobrenosotros/instalaciones/salones-culturales.webp') }}" alt="salones culturales">
                    <div class="overlay-instalacion">
                        <h3>Salones culturales</h3>
                        <p>Espacios dedicados a actividades artísticas y culturales para preservar tradiciones.</p>
                    </div>
                </div>
                <div class="instalacion-card">
                    <img src="{{ asset('img/sobrenosotros/instalaciones/laguna.webp') }}" alt="laguna ">
                    <div class="overlay-instalacion">
                        <h3>Laguna</h3>
                        <p>Un espacio natural vital para recreación y aprendizaje en armonía con la naturaleza.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 🔸 MANUAL DE CONVIVENCIA --}}
    <section class="seccion-manual">
        <div class="contenedor">
            <button id="abrirManual" class="btn-manual">
                Ver Manual de Convivencia
            </button>
        </div>
    </section>

    {{-- 🔹 Modal con visor PDF --}}
    <div id="manualModal" class="modal" style="display:none">
        <div class="modal-content">
            <span id="cerrarManual" class="cerrar">&times;</span>
            <h2 class="titulo">Manual de Convivencia</h2>
            <iframe src="{{ asset('manuales/111.pdf') }}"></iframe>
        </div>
    </div>

@endsection

{{-- 🔹 Scripts de la página --}}
@section('scripts')
    <script src="{{ asset('js/publico/sobrenosotros.js') }}"></script>
@endsection
