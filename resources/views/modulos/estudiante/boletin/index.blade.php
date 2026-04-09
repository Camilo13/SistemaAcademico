@extends('layouts.menuestudiante')
@section('title', 'Mis Boletines')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/estudiante/boletin/index.css') }}">
@endpush

@section('content')
<div class="contenedor-boletines">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-file-lines"></i> Mis Boletines</h2>
            <p class="cabecera-subtitulo">Consulta tu boletín académico por año lectivo</p>
        </div>
    </div>

    @if($inscripciones->isNotEmpty())
        <div class="boletines-lista">
            @foreach($inscripciones as $inscripcion)
                <div class="boletin-card">

                    {{-- Indicador de año --}}
                    <div class="boletin-anio">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>{{ optional($inscripcion->grupo->anioLectivo)->nombre ?? '—' }}</span>
                    </div>

                    {{-- Datos --}}
                    <div class="boletin-datos">
                        <div class="boletin-titulo">
                            Boletín {{ optional($inscripcion->grupo->anioLectivo)->nombre ?? '—' }}
                            @if(optional($inscripcion->grupo->anioLectivo)->activo ?? false)
                                <span class="badge-vigente">
                                    <i class="fa-solid fa-circle-dot"></i> Vigente
                                </span>
                            @endif
                        </div>
                        <div class="boletin-grupo">
                            {{ optional($inscripcion->grupo->grado)->nombre ?? '—' }}
                            — Grupo {{ optional($inscripcion->grupo)->nombre ?? '—' }}
                            &nbsp;·&nbsp;
                            <span class="estado estado-{{ $inscripcion->estado }}">
                                {{ ucfirst($inscripcion->estado) }}
                            </span>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="boletin-acciones">
                        <a href="{{ route('estudiante.boletin.show', $inscripcion->id) }}"
                           class="btn btn-secundario btn-sm">
                            <i class="fa-solid fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('estudiante.boletin.pdf', $inscripcion->id) }}"
                           id="btn-descargar-pdf"
                           class="btn btn-neutro btn-sm"
                           target="_blank">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="sin-registros">
            <i class="fa-solid fa-inbox"></i>
            No tienes boletines disponibles todavía.
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/estudiante/boletin.js') }}"></script>
@endpush
