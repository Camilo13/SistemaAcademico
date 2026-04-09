@extends('layouts.menupublico')

@section('title', 'Eventos Institucionales')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/publico/eventos.css') }}">
@endsection

@section('content')

<section class="seccion-eventos">
    <div class="contenedor">

        <h2 class="titulo-seccion">Eventos Institucionales</h2>

        <p class="descripcion-seccion">
            Conoce las actividades académicas, culturales y comunitarias que fortalecen
            nuestra identidad y compromiso con el territorio.
        </p>

        <div class="eventos-grid">

            @forelse ($eventos as $evento)

                <article class="evento-card">

                    {{-- FECHA (DÍA + MES) --}}
                    <div class="evento-fecha">
                        <span class="dia">
                            {{ $evento->fecha_evento->format('d') }}
                        </span>
                        <span class="mes">
                            {{ $evento->fecha_evento->translatedFormat('M') }}
                        </span>
                    </div>

                    {{-- CONTENIDO --}}
                    <div class="evento-contenido">

                        <h3>{{ $evento->titulo }}</h3>

                        {{-- META (HORA Y LUGAR) --}}
                        <div class="evento-meta">

                            <span class="evento-hora">
                                🕒 {{ $evento->fecha_evento->format('H:i') }}
                            </span>

                            @if ($evento->lugar)
                                <span class="evento-lugar">
                                    📍 {{ $evento->lugar }}
                                </span>
                            @endif

                        </div>

                        <p class="evento-descripcion colapsado">
                            {{ $evento->descripcion }}
                        </p>

                        <button class="btn-ver-mas">Ver más</button>

                    </div>

                </article>

            @empty

                <div class="sin-eventos">
                    No hay eventos próximos en este momento.
                </div>

            @endforelse

        </div>

    </div>
</section>

@endsection


@section('scripts')
<script src="{{ asset('js/publico/eventos.js') }}"></script>
@endsection
