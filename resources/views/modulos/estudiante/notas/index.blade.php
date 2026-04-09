@extends('layouts.menuestudiante')
@section('title', 'Mis Notas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/estudiante/notas/index.css') }}">
@endpush

@section('content')
<div class="contenedor-notas-est">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-star"></i> Mis Notas</h2>
            <p class="cabecera-subtitulo">
                @if($anioActivo)
                    Año lectivo: <strong>{{ $anioActivo->nombre }}</strong>
                @else
                    No hay año lectivo activo
                @endif
            </p>
        </div>
    </div>

    @if(!$anioActivo || !$inscripcion)
        <div class="aviso-sin-anio">
            <i class="fa-solid fa-circle-info"></i>
            @if(!$anioActivo)
                No hay un año lectivo activo en este momento.
            @else
                No tienes una inscripción activa en el año lectivo actual.
            @endif
        </div>

    @else

        {{-- Tabs de periodos --}}
        @if($periodos->isNotEmpty())
            <div class="tabs-periodos">
                <a href="{{ route('estudiante.notas.index') }}"
                   class="tab {{ request()->routeIs('estudiante.notas.index') ? 'activo' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Todas
                </a>
                @foreach($periodos as $periodo)
                    <a href="{{ route('estudiante.notas.periodo', $periodo->id) }}"
                       class="tab">
                        {{ $periodo->nombre }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Lista de materias con notas --}}
        @php
            $materiasInscritas = $inscripcion->inscripcionMaterias->where('estado', 'activa');
        @endphp

        @if($materiasInscritas->isNotEmpty())
            <div class="materias-notas">
                @foreach($materiasInscritas as $im)
                    @php
                        $notasPorPeriodo = $im->notas->keyBy('periodo_id');
                        $promedio = $im->notas->isNotEmpty()
                            ? round($im->notas->avg('nota'), 2)
                            : null;
                        $aprobada = !is_null($promedio) && $promedio >= 3.0;
                    @endphp
                    <div class="materia-nota-card" data-promedio="{{ $promedio ?? '' }}">

                        <div class="materia-nota-nombre">
                            <div class="materia-nombre">
                                {{ optional($im->asignacion->materia)->nombre ?? '—' }}
                            </div>
                            <div class="materia-docente">
                                {{ optional($im->asignacion->docente)->name ?? '—' }}
                            </div>
                        </div>

                        {{-- Notas por periodo --}}
                        <div class="periodos-chips">
                            @foreach($periodos as $periodo)
                                @php $nota = $notasPorPeriodo[$periodo->id] ?? null; @endphp
                                <div class="periodo-item">
                                    <span class="periodo-label">{{ $periodo->nombre }}</span>
                                    @if($nota)
                                        <span class="nota-chip {{ $nota->nota >= 3.0 ? 'nota-aprobada' : 'nota-reprobada' }}">
                                            {{ number_format($nota->nota, 2) }}
                                        </span>
                                    @else
                                        <span class="nota-chip nota-sin">—</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Promedio --}}
                        <div class="promedio-materia">
                            <span class="promedio-label">Promedio</span>
                            @if(!is_null($promedio))
                                <span class="promedio-chip {{ $aprobada ? 'promedio-aprobado' : 'promedio-reprobado' }}">
                                    {{ number_format($promedio, 2) }}
                                </span>
                            @else
                                <span class="promedio-chip promedio-sin">—</span>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
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
    <script src="{{ asset('js/modulos/estudiante/notas.js') }}"></script>
@endpush
