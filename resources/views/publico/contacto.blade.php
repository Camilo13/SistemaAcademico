@extends('layouts.menupublico')

@section('title', 'Contáctanos')

{{-- 🔹 Estilos propios para esta página --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/publico/contacto.css') }}">
@endsection

{{-- 🔹 Contenido principal --}}
@section('content')
<main class="contenido-principal">
    
    {{-- 🟢 Título e introducción --}}
    <div class="titulo-centrado">
        <h1 class="titulo">Contáctanos</h1>
        <p class="intro">
            Estamos aquí para responder tus preguntas y ayudarte cuando lo necesites. 
            Tu opinión es importante para nosotros.
        </p>
    </div>

    {{-- 🟢 Sección principal de contacto con 2 columnas --}}
    <div class="tarjeta-contacto">
        <div class="columnas">

            {{-- 🔹 Columna izquierda: información y redes --}}
            <div class="columna izquierda">

                {{-- 📍 Información de contacto --}}
                <div>
                    <h2 class="subtitulo">Información de Contacto</h2>
                    <div class="info-contacto">
                        <p><i class="fas fa-map-marker-alt icono"></i> 
                            <strong>Dirección:</strong> Calle Principal #123, Vereda El Lago, Inzá-Cauca, Colombia</p>
                        <p><i class="fas fa-phone-alt icono"></i> 
                            <strong>Teléfono:</strong> 
                            <a href="tel:+576021234567" class="link-contacto">+57 (602) 123-4567</a></p>
                        <p><i class="fas fa-envelope icono"></i> 
                            <strong>Email:</strong> 
                            <a href="mailto:info@akweuusyat.edu.co" class="link-contacto">info@akweuusyat.edu.co</a></p>
                        <p><i class="fas fa-clock icono"></i> 
                            <strong>Horario:</strong> Lunes - Viernes: 7:00 AM - 4:00 PM</p>
                    </div>
                </div>

                {{-- 📱 Botón de WhatsApp (solo dentro de la sección) --}}
                <div class="espacio-arriba">
                    <h2 class="subtitulo">Comunícate Directamente</h2>
                    <p class="texto">¿Necesitas una respuesta rápida? Envíanos un mensaje por WhatsApp.</p>
                    <a href="https://wa.me/573148941876?text=Hola%2C%20necesito%20información%20sobre%20la%20institución." 
                    target="_blank" class="btn-social whatsapp">
                        <i class="fab fa-whatsapp icono-btn"></i> 
                        <span>Enviar WhatsApp</span>
                    </a>
                </div>

                {{-- 🌐 Redes Sociales --}}
                <div class="espacio-arriba">
                    <h2 class="subtitulo">Síguenos en Redes Sociales</h2>
                    <p class="texto">Mantente al tanto de nuestras noticias y actividades.</p>
                    <div class="grupo-botones">
                        <a href="https://www.facebook.com/InstitucionEducativaAgroambientalAkweUusYatGaitana/?locale=es_LA" 
                        target="_blank" class="btn-social facebook">
                            <i class="fab fa-facebook-f icono-btn"></i> 
                            <span>Facebook</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- 🔹 Columna derecha: mapa --}}
            <div class="columna derecha">
                <h2 class="subtitulo">Nuestra Ubicación</h2>
                <img src="{{ asset('img/contacto/2.jpg') }}" alt="Mapa de Ubicación" class="mapa">
                <p class="link-mapa">
                    <a href="https://maps.app.goo.gl/XtwUsEYJKiKfCktW9" 
                    target="_blank">📍 Ver en Google Maps</a>
                </p>
            </div>
        </div>
    </div>
</main>
@endsection
