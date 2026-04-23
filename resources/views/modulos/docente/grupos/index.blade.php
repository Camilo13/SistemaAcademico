@extends('layouts.menudocente')
@section('title', 'Mis Grupos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/grupos/index.css') }}">
@endpush

@section('content')
<div class="contenedor-grupos">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-chalkboard-teacher"></i> Mis Grupos</h2>
            <p class="cabecera-subtitulo">
                @if($anioActivo)
                    Año lectivo: <strong>{{ $anioActivo->nombre }}</strong>
                    &nbsp;·&nbsp; {{ $asignaciones->count() }} asignación(es)
                @else
                    No hay año lectivo activo actualmente
                @endif
            </p>
        </div>
        <input id="buscador-grupos"
               type="text"
               class="input-buscar"
               placeholder="Buscar materia o grupo…"
               autocomplete="off">
    </div>

    @if(!$anioActivo)
        <div class="alerta-advertencia">
            <i class="fa-solid fa-triangle-exclamation"></i>
            No hay un año lectivo activo. Contacta al administrador.
        </div>
    @endif

    @if($asignaciones->isNotEmpty())

        <div class="grupos-grid">
            @foreach($asignaciones as $asignacion)
                <div class="grupo-card">

                    <div class="grupo-card-header">
                        <div class="grupo-card-icono">
                            <i class="fa-solid fa-book-open-reader"></i>
                        </div>
                        <div>
                            <div class="grupo-card-titulo">
                                {{ optional($asignacion->materia)->nombre ?? '—' }}
                            </div>
                            <div class="grupo-card-subtitulo">
                                {{ optional($asignacion->grupo->grado)->nombre ?? '—' }}
                                — Grupo {{ optional($asignacion->grupo)->nombre ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="grupo-card-datos">
                        <div class="grupo-dato">
                            <i class="fa-solid fa-calendar-days"></i>
                            {{ optional($asignacion->grupo->anioLectivo)->nombre ?? '—' }}
                        </div>
                        <div class="grupo-dato">
                            <i class="fa-solid fa-users"></i>
                            {{ $asignacion->inscripcionMaterias->count() }} estudiante(s)
                        </div>
                    </div>

                    {{-- Acciones: Notas · Asistencia · Boletín --}}
                    <div class="grupo-card-acciones">
                        <a href="{{ route('docente.notas.estudiantes', $asignacion->id) }}"
                           class="btn btn-primario btn-sm"
                           title="Registrar o consultar notas">
                            <i class="fa-solid fa-clipboard-list"></i> Notas
                        </a>

                        <a href="{{ route('docente.asistencia.estudiantes', $asignacion->id) }}"
                           class="btn btn-secundario btn-sm"
                           title="Registrar asistencia">
                            <i class="fa-solid fa-user-check"></i> Asistencia
                        </a>

                        <a href="{{ route('docente.grupos.resumen', $asignacion->grupo_id) }}"
                           class="btn btn-neutro btn-sm"
                           title="Ver resumen académico del grupo">
                            <i class="fa-solid fa-chart-bar"></i> Resumen
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        <p id="sin-resultados-grupos" class="sin-grupos" style="display:none">
            <i class="fa-solid fa-magnifying-glass"></i>
            Sin resultados para tu búsqueda.
        </p>

    @else
        <div class="sin-grupos">
            <i class="fa-solid fa-inbox"></i>
            No tienes grupos asignados en el año lectivo activo.
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/docente/grupos.js') }}"></script>
@endpush