@extends('layouts.menuestudiante')
@section('title', 'Mis Materias')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/estudiante/academico/materias.css') }}">
@endpush

@section('content')
<div class="contenedor-materias">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-book-open"></i> Mis Materias</h2>
            <p class="cabecera-subtitulo">
                @if($anioActivo)
                    Año lectivo: <strong>{{ $anioActivo->nombre }}</strong>
                @else
                    No hay año lectivo activo
                @endif
            </p>
        </div>
        @if(isset($materiasInscritas) && $materiasInscritas->isNotEmpty())
            <input id="filtro-materias"
                   type="text"
                   class="input-buscar"
                   placeholder="Buscar materia…"
                   autocomplete="off">
        @endif
    </div>

    @if(!$anioActivo)
        <div class="aviso-sin-anio">
            <i class="fa-solid fa-triangle-exclamation"></i>
            No hay un año lectivo activo en este momento.
        </div>

    @elseif(!$inscripcion)
        <div class="aviso-sin-anio">
            <i class="fa-solid fa-circle-info"></i>
            No tienes una inscripción activa en el año lectivo actual.
        </div>

    @else
        {{-- Ficha del grupo --}}
        <div class="ficha-grupo">
            <div class="ficha-dato">
                <span><i class="fa-solid fa-users"></i> Grupo</span>
                <strong>
                    {{ optional($inscripcion->grupo->grado)->nombre ?? '—' }}
                    — {{ optional($inscripcion->grupo)->nombre ?? '—' }}
                </strong>
            </div>
            <div class="ficha-dato">
                <span><i class="fa-solid fa-hashtag"></i> Materias activas</span>
                <strong>{{ $materiasInscritas->count() }}</strong>
            </div>
        </div>

        {{-- Grid de materias --}}
        @if($materiasInscritas->isNotEmpty())
            <div class="materias-grid">
                @foreach($materiasInscritas as $im)
                    <div class="materia-card">
                        <div class="materia-nombre">
                            {{ optional($im->asignacion->materia)->nombre ?? '—' }}
                        </div>
                        <div class="materia-docente">
                            <i class="fa-solid fa-chalkboard-teacher"></i>
                            {{ optional($im->asignacion->docente)->name ?? 'Sin docente asignado' }}
                        </div>
                    </div>
                @endforeach
            </div>

            <p id="sin-materias" class="sin-registros" style="display:none">
                <i class="fa-solid fa-magnifying-glass"></i>
                Sin resultados para tu búsqueda.
            </p>
        @else
            <p class="sin-registros">
                <i class="fa-solid fa-circle-info"></i>
                No tienes materias activas en este año lectivo.
            </p>
        @endif
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/estudiante/academico.js') }}"></script>
@endpush
