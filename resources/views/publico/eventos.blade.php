@extends('layouts.menupublico')

@section('title', 'Eventos Institucionales')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/publico/eventos.css') }}">
@endsection

@section('content')

<section class="seccion-eventos">

    <div class="eventos-hero">
        <span class="hero-badge">Calendario institucional</span>
        <h2 class="titulo-seccion">Eventos Institucionales</h2>
        <p class="descripcion-seccion">
            Conoce las actividades académicas, culturales y comunitarias
            que fortalecen nuestra identidad y compromiso con el territorio.
        </p>
    </div>

    <div class="contenedor">
        <div class="eventos-grid">

            @forelse ($eventos as $evento)

                <article class="evento-card">

                    <div class="evento-cabecera">
                        <div class="evento-fecha-bloque">
                            <span class="dia">{{ $evento->fecha_evento->format('d') }}</span>
                            <div class="mes-anio">
                                <span class="mes">{{ $evento->fecha_evento->translatedFormat('M') }}</span>
                                <span class="anio">{{ $evento->fecha_evento->format('Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="evento-contenido">

                        <h3>{{ $evento->titulo }}</h3>

                        <div class="evento-meta">
                            <span class="evento-hora">
                                <i class="fa-solid fa-clock"></i>
                                {{ $evento->fecha_evento->format('H:i') }}
                            </span>
                            @if ($evento->lugar)
                                <span class="evento-lugar">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $evento->lugar }}
                                </span>
                            @endif
                        </div>

                        <p class="evento-descripcion colapsado">{{ $evento->descripcion }}</p>

                        <button class="btn-ver-mas">Ver más</button>

                    </div>

                </article>

            @empty

                <div class="sin-eventos">
                    <i class="fa-solid fa-calendar-xmark sin-eventos-icono"></i>
                    <p>No hay eventos próximos en este momento.</p>
                </div>

            @endforelse

        </div>
    </div>

</section>

@endsection

@section('scripts')
<script src="{{ asset('js/publico/eventos.js') }}"></script>
@endsection
