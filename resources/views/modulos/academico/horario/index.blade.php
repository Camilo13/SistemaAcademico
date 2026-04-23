@extends('layouts.menuadmin')

@section('title', 'Horarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/horario/index.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-clock"></i>
                Horarios
            </h2>
            <p class="cabecera-subtitulo">
                Selecciona un año lectivo y un grupo para gestionar su horario
            </p>
        </div>
    </div>

    {{-- ── Filtro de año ── --}}
    <form method="GET"
          action="{{ route('admin.academico.horarios.index') }}"
          class="panel-filtros"
          id="form-filtro-horario">

        <div class="filtro">
            <label>
                <i class="fa-solid fa-calendar-days"></i>
                Año Lectivo
            </label>
            <select name="anio" id="select-anio">
                <option value="">Seleccione un año</option>
                @foreach($anios as $a)
                    <option value="{{ $a->id }}"
                        {{ optional($anioSeleccionado)->id == $a->id ? 'selected' : '' }}>
                        {{ $a->nombre }}
                        @if($a->activo) (Activo) @endif
                    </option>
                @endforeach
            </select>
        </div>

    </form>

    @if($grupos->isEmpty())
        <div class="alerta-info">
            <i class="fa-solid fa-circle-info"></i>
            @if($anioSeleccionado)
                No hay grupos activos en el año <strong>{{ $anioSeleccionado->nombre }}</strong>.
            @else
                No hay grupos activos registrados en el sistema.
            @endif
        </div>
    @else
        <div class="grupos-grid">
                @foreach($grupos as $grupo)
                    @php
                        $totalFranjas  = \App\Models\Horario::whereHas('asignacion',
                            fn($q) => $q->where('grupo_id', $grupo->id)
                        )->count();
                        $totalPosibles = 5 * 6;
                        $porcentaje    = $totalPosibles > 0
                            ? round(($totalFranjas / $totalPosibles) * 100) : 0;
                    @endphp

                    <div class="grupo-card">
                        <div class="grupo-card-header">
                            <div class="grupo-icon">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="grupo-info">
                                <h3>{{ optional($grupo->grado)->nombre }} — Grupo {{ $grupo->nombre }}</h3>
                                <span class="grupo-meta">
                                    @unless($anioSeleccionado)
                                        <i class="fa-solid fa-calendar-days"></i>
                                        {{ optional($grupo->anioLectivo)->nombre }}
                                        &nbsp;·&nbsp;
                                    @endunless
                                    <i class="fa-solid fa-calendar-check"></i>
                                    {{ $totalFranjas }} / {{ $totalPosibles }} franjas cargadas
                                </span>
                            </div>
                        </div>

                        <div class="progreso-barra">
                            <div class="progreso-fill" style="width: {{ $porcentaje }}%"></div>
                        </div>
                        <span class="progreso-label">{{ $porcentaje }}% completado</span>

                        <a href="{{ route('admin.academico.horarios.grupo', $grupo->id) }}"
                           class="btn btn-secundario btn-sm btn-bloque">
                            <i class="fa-solid fa-table-cells"></i> Ver / Editar horario
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/academico/horario.js') }}"></script>
@endpush