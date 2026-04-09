@extends('layouts.menuestudiante')
@section('title', 'Mis Inscripciones')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/estudiante/academico/inscripcion.css') }}">
@endpush

@section('content')
<div class="contenedor-inscripciones">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-graduation-cap"></i> Mis Inscripciones</h2>
            <p class="cabecera-subtitulo">Historial de tus años escolares registrados</p>
        </div>
    </div>

    @if($inscripciones->isNotEmpty())
        <div class="inscripciones-lista">
            @foreach($inscripciones as $inscripcion)
                <div class="inscripcion-card">

                    {{-- Año --}}
                    <div class="inscripcion-anio">
                        <span>Año</span>
                        <strong>{{ optional($inscripcion->grupo->anioLectivo)->nombre ?? '—' }}</strong>
                    </div>

                    {{-- Datos --}}
                    <div class="inscripcion-datos">
                        <div class="inscripcion-grupo">
                            {{ optional($inscripcion->grupo->grado)->nombre ?? '—' }}
                            — Grupo {{ optional($inscripcion->grupo)->nombre ?? '—' }}
                        </div>
                        <div class="inscripcion-grado">
                            <span class="estado estado-{{ $inscripcion->estado }}">
                                {{ ucfirst($inscripcion->estado) }}
                            </span>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="inscripcion-acciones">
                        <a href="{{ route('estudiante.boletin.show', $inscripcion->id) }}"
                           class="btn btn-secundario btn-sm">
                            <i class="fa-solid fa-file-lines"></i> Boletín
                        </a>
                        <a href="{{ route('estudiante.notas.index') }}"
                           class="btn btn-neutro btn-sm">
                            <i class="fa-solid fa-star"></i> Notas
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="sin-registros">
            <i class="fa-solid fa-inbox"></i>
            Aún no tienes inscripciones registradas.
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/estudiante/academico.js') }}"></script>
@endpush
