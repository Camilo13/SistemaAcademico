@extends('layouts.menudocente')

@section('title', 'Registrar Notas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/notas/notas.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-clipboard-list"></i>
                Registrar Notas
            </h2>
            <p class="cabecera-subtitulo">
                Selecciona una asignación para gestionar las notas de tus estudiantes
            </p>
        </div>
        @if($asignaciones->isNotEmpty())
            <input id="filtro-asignaciones"
                   type="text"
                   class="input-buscar"
                   placeholder="Filtrar por materia o grupo…"
                   autocomplete="off">
        @endif
    </div>

    {{-- ── Lista de asignaciones ── --}}
    @if($asignaciones->isNotEmpty())

        <div class="asignaciones-lista" id="lista-asignaciones">
            @foreach($asignaciones as $asignacion)
                <a href="{{ route('docente.notas.estudiantes', $asignacion->id) }}"
                   class="asignacion-item"
                   data-buscar="{{ strtolower(optional($asignacion->materia)->nombre ?? '') }} {{ strtolower(optional($asignacion->grupo->grado)->nombre ?? '') }} {{ strtolower(optional($asignacion->grupo)->nombre ?? '') }}">

                    <div class="asignacion-icono">
                        <i class="fa-solid fa-book-open-reader"></i>
                    </div>

                    <div class="asignacion-datos">
                        <div class="asignacion-materia">
                            {{ optional($asignacion->materia)->nombre ?? '—' }}
                        </div>
                        <div class="asignacion-grupo">
                            {{ optional($asignacion->grupo->grado)->nombre ?? '—' }}
                            — Grupo {{ optional($asignacion->grupo)->nombre ?? '—' }}
                            &nbsp;·&nbsp;
                            {{ optional($asignacion->grupo->anioLectivo)->nombre ?? '—' }}
                        </div>
                    </div>

                    <i class="fa-solid fa-chevron-right asignacion-flecha"></i>
                </a>
            @endforeach
        </div>

        <p id="sin-resultados" class="sin-registros" style="display:none">
            <i class="fa-solid fa-magnifying-glass"></i>
            Sin resultados para tu búsqueda.
        </p>

    @else
        <div class="sin-registros">
            <i class="fa-solid fa-circle-info"></i>
            No tienes asignaciones activas en este momento.
        </div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/docente/notas.js') }}"></script>
@endpush
