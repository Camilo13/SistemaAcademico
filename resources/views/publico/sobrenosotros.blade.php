@extends('layouts.menupublico')

@section('title', 'Sobre Nosotros — I.E. Agroambiental Akwe Uus Yat')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/publico/sobrenosotros.css') }}">
@endsection

@section('content')

    {{-- ── HERO ── --}}
    <section class="seccion-hero">
        <div class="contenedor-hero">
            <h1>Sobre Nosotros</h1>
            <p>Conoce nuestra historia, nuestra identidad y los valores que guían<br>
               el proceso educativo del Resguardo Indígena La Gaitana.</p>
        </div>
    </section>

    {{-- ── LOGO E IDENTIDAD ── --}}
    <section class="seccion-logo-historia-alternada">
        <div class="contenedor">
            <div class="contenido-alternado">
                <div class="imagen-lado">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo I.E. Agroambiental Akwe Uus Yat" class="imagen-logo-nosotros">
                </div>
                <div class="texto-lado">
                    <h2>Nuestro Nombre, Nuestro Símbolo</h2>
                    <p>
                        El nombre <strong>Akwe Uus Yat</strong> fue otorgado tras un
                        <em>cateo</em> —consulta realizada con mayores y hablantes de
                        <strong>Nasayuwe</strong>, la lengua propia del pueblo Nasa—,
                        como parte del proceso de creación soberana de la institución.
                        Cada elemento de nuestra identidad visual refleja la
                        <strong>conexión profunda con el territorio, la cultura ancestral
                        y la memoria colectiva</strong> del Resguardo Indígena La Gaitana,
                        municipio de Inzá, Cauca.
                    </p>
                </div>
            </div>

            {{-- ── HISTORIA ── --}}
            <div class="contenido-alternado reverso">
                <div class="imagen-lado">
                    <img src="{{ asset('img/sobrenosotros/1.jpg') }}" alt="Institución Educativa Akwe Uus Yat" class="foto-historia">
                </div>
                <div class="texto-lado">
                    <h2>Una Historia de Resistencia y Autonomía</h2>
                    <p>
                        Nuestra historia comienza en <strong>1982</strong> con la conceptualización
                        de un Programa Bilingüe propio en el Resguardo La Gaitana. Entre 1984 y 1985
                        se creó formalmente la <strong>Escuela Bilingüe</strong> en la casa comunal
                        de la vereda El Lago, con clases de Nasayuwe, lectura, escritura, matemáticas,
                        música, danza y tejido impartidas a cerca de 45 estudiantes.
                    </p>
                    <p>
                        Tras años de lucha jurídica y organizativa, en <strong>noviembre de 2010</strong>
                        la comunidad tomó en gran asamblea la decisión soberana de crear su propia
                        institución. En <strong>febrero de 2011</strong> la Corte Constitucional
                        reconoció mediante la <em>Sentencia T-116</em> la existencia del territorio
                        y la comunidad indígena, dando nacimiento legal a la institución que hoy
                        atiende estudiantes de básica primaria, secundaria y media técnica en las
                        sedes de Carmen, Belencito, El Lago, El Escobal y Tierras Blancas.
                    </p>
                    <p class="fuente-texto">
                        <em>Fuente: Palabras de la rectora y sabedora Marciana Quira,
                        Resguardo La Gaitana, 2024.</em>
                    </p>
                </div>
            </div>
        </div>
    </section>

   

    {{-- ── VALORES (7 reales) ── --}}
    <section class="seccion-valores-ilustrados-moderna">
        <div class="contenedor">
            <h2>Los Valores que nos Guían</h2>
            <div class="valores-grid">

                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/respeto.webp') }}" alt="Respeto">
                    <h3>Respeto</h3>
                    <p>Por el ser, el <strong>territorio</strong>, la cultura y los demás
                       miembros de la comunidad.</p>
                </div>

                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/identidadcultural.webp') }}" alt="Responsabilidad">
                    <h3>Responsabilidad</h3>
                    <p>En el cumplimiento de los <strong>compromisos académicos
                       y comunitarios</strong>.</p>
                </div>

                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/solidaridad.webp') }}" alt="Solidaridad">
                    <h3>Solidaridad</h3>
                    <p>Como principio de <strong>ayuda mutua</strong> entre los miembros
                       del resguardo.</p>
                </div>

                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/compromisoambiental.webp') }}" alt="Honestidad">
                    <h3>Honestidad</h3>
                    <p>En el manejo de la <strong>información</strong> y las relaciones
                       institucionales.</p>
                </div>

                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/respeto.webp') }}" alt="Tolerancia">
                    <h3>Tolerancia</h3>
                    <p>Hacia la <strong>diversidad cultural</strong> y de pensamiento.</p>
                </div>

                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/solidaridad.webp') }}" alt="Perseverancia">
                    <h3>Perseverancia</h3>
                    <p>Ante las dificultades del <strong>proceso educativo propio</strong>.</p>
                </div>

                <div class="valor-item">
                    <img src="{{ asset('img/sobrenosotros/valores/identidadcultural.webp') }}" alt="Identidad">
                    <h3>Identidad</h3>
                    <p>Como eje central del <strong>proyecto educativo indígena</strong>.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- ── PROYECTOS DESTACADOS ── --}}
    <section class="seccion-proyectos-destacados">
        <div class="contenedor">
            <h2>Proyectos que Transforman</h2>
            <p class="subtitulo-proyectos">Iniciativas que integran el aprendizaje práctico con el territorio y la cultura.</p>
            <div class="proyectos-grid">
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/huerta.webp') }}" alt="Huerta Escolar">
                    <div class="overlay-proyecto">
                        <h3>Huertas Escolares</h3>
                        <p>Los estudiantes cultivan alimentos orgánicos y aprenden sobre agricultura sostenible.</p>
                    </div>
                </div>
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/tejido.webp') }}" alt="Tejidos">
                    <div class="overlay-proyecto">
                        <h3>Tejidos Ancestrales</h3>
                        <p>Mantenemos viva la cultura a través del arte del tejido ancestral.</p>
                    </div>
                </div>
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/caficultura.webp') }}" alt="Caficultura">
                    <div class="overlay-proyecto">
                        <h3>Caficultura</h3>
                        <p>Desde la siembra hasta la cosecha, los estudiantes aprenden todo el ciclo del café.</p>
                    </div>
                </div>
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/ganaderia.webp') }}" alt="Ganadería">
                    <div class="overlay-proyecto">
                        <h3>Ganadería</h3>
                        <p>Manejo responsable de animales con prácticas de bienestar animal.</p>
                    </div>
                </div>
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/porcicultura.webp') }}" alt="Porcicultura">
                    <div class="overlay-proyecto">
                        <h3>Porcicultura</h3>
                        <p>Cría de cerdos integrada con la huerta y aprovechamiento de residuos como fertilizante.</p>
                    </div>
                </div>
                <div class="proyecto-card">
                    <img src="{{ asset('img/sobrenosotros/proyectos/ovinocultura.webp') }}" alt="Ovinocultura">
                    <div class="overlay-proyecto">
                        <h3>Ovinocultura</h3>
                        <p>Cuidado y manejo de ovejas, obteniendo lana y leche como recursos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── INSTALACIONES ── --}}
    <section class="seccion-instalaciones-galeria">
        <div class="contenedor">
            <h2>Nuestro Espacio, Un Ambiente para Crecer</h2>
            <div class="galeria-moderna">
                <div class="instalacion-card">
                    <img src="{{ asset('img/sobrenosotros/instalaciones/salones-clases.webp') }}" alt="Salón de Clases">
                    <div class="overlay-instalacion">
                        <h3>Salones de Clase</h3>
                        <p>Espacios cómodos para el aprendizaje interactivo.</p>
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
                    <img src="{{ asset('img/sobrenosotros/instalaciones/polideportivo.webp') }}" alt="Polideportivo">
                    <div class="overlay-instalacion">
                        <h3>Canchas Deportivas</h3>
                        <p>Áreas para la actividad física y el desarrollo integral.</p>
                    </div>
                </div>
                <div class="instalacion-card">
                    <img src="{{ asset('img/sobrenosotros/instalaciones/salones-culturales.webp') }}" alt="Salones culturales">
                    <div class="overlay-instalacion">
                        <h3>Salones Culturales</h3>
                        <p>Espacios dedicados a actividades artísticas y culturales.</p>
                    </div>
                </div>
                <div class="instalacion-card">
                    <img src="{{ asset('img/sobrenosotros/instalaciones/laguna.webp') }}" alt="Laguna">
                    <div class="overlay-instalacion">
                        <h3>Laguna</h3>
                        <p>Un espacio natural para recreación y aprendizaje en armonía con la naturaleza.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── MANUAL DE CONVIVENCIA ── --}}
    <section class="seccion-manual">
        <div class="contenedor">
            <button id="abrirManual" class="btn-manual">
                Ver Manual de Convivencia
            </button>
        </div>
    </section>

    <div id="manualModal" class="modal" style="display:none">
        <div class="modal-content">
            <span id="cerrarManual" class="cerrar">&times;</span>
            <h2 class="titulo">Manual de Convivencia</h2>
            <iframe src="{{ asset('manuales/111.pdf') }}"></iframe>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/publico/sobrenosotros.js') }}"></script>
@endsection