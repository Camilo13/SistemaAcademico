@extends('layouts.menudocente')
@section('title', 'Asistencia — Mis Asignaciones')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/asistencia/index.css') }}">
@endpush

@section('content')
<div class="contenedor-asistencia-doc">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-calendar-check"></i> Registrar Asistencia</h2>
            <p class="cabecera-subtitulo">
                Selecciona la materia en la que deseas registrar o consultar faltas
            </p>
        </div>
        <input id="buscador"
               type="text"
               class="input-buscar"
               placeholder="Buscar materia o grupo…"
               autocomplete="off">
    </div>

    @if($asignaciones->isNotEmpty())

        <div class="asignaciones-lista" id="lista-asignaciones">
            @foreach($asignaciones as $asignacion)
                <a href="{{ route('docente.asistencia.estudiantes', $asignacion->id) }}"
                   class="asignacion-item"
                   data-buscar="{{ strtolower($asignacion->materia->nombre ?? '') }} {{ strtolower($asignacion->grupo->grado->nombre ?? '') }} {{ strtolower($asignacion->grupo->nombre ?? '') }}">

                    <div class="asignacion-icono">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="asignacion-datos">
                        <div class="asignacion-materia">
                            {{ $asignacion->materia->nombre ?? '—' }}
                        </div>
                        <div class="asignacion-grupo">
                            {{ $asignacion->grupo->grado->nombre ?? '—' }}
                            — Grupo {{ $asignacion->grupo->nombre ?? '—' }}
                            &nbsp;·&nbsp;
                            {{ $asignacion->grupo->anioLectivo->nombre ?? '—' }}
                        </div>
                    </div>

                    <i class="fa-solid fa-chevron-right asignacion-flecha"></i>
                </a>
            @endforeach
        </div>

        <p id="sin-resultados" class="sin-registros" style="display:none">
            Sin resultados para tu búsqueda.
        </p>

    @else
        <div class="sin-registros">
            No tienes asignaciones activas en el año lectivo vigente.
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/docente/asistencia.js') }}"></script>
@endpush
