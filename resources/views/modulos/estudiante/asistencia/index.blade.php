@extends('layouts.menuestudiante')
@section('title', 'Mis Faltas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/estudiante/asistencia/index.css') }}">
@endpush

@section('content')
<div class="contenedor-asistencia-est">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-calendar-check"></i> Mis Faltas</h2>
            <p class="cabecera-subtitulo">
                @if($anioActivo)
                    Año lectivo: <strong>{{ $anioActivo->nombre }}</strong>
                @else
                    No hay año lectivo activo actualmente
                @endif
            </p>
        </div>
    </div>

    {{-- Sin año activo --}}
    @if(!$anioActivo || !$inscripcion)
        <div class="aviso-sin-anio">
            <i class="fa-solid fa-triangle-exclamation"></i>
            @if(!$anioActivo)
                No hay un año lectivo activo. Consulta con el administrador.
            @else
                No tienes una inscripción activa en el año lectivo vigente.
            @endif
        </div>

    @else

        {{-- Tabs de periodos --}}
        @if($periodos->isNotEmpty())
            <div class="tabs-periodos">
                <a href="{{ route('estudiante.asistencia.index') }}"
                   class="tab activo">
                    Resumen anual
                </a>
                @foreach($periodos as $periodo)
                    <a href="{{ route('estudiante.asistencia.periodo', $periodo->id) }}"
                       class="tab">
                        {{ $periodo->nombre }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Tarjetas de faltas por materia --}}
        @php
            $materiasActivas = $inscripcion->inscripcionMaterias->where('estado', 'activa');
        @endphp

        @if($materiasActivas->isNotEmpty())
            <div class="materias-asistencia">
                @foreach($materiasActivas as $im)
                    @php
                        $asistencias = $im->asistencias->keyBy('periodo_id');
                        $totalAnual  = $im->asistencias->sum(fn($a) => $a->totalFaltas());
                        $tieneRegistros = $im->asistencias->isNotEmpty();
                    @endphp
                    <div class="materia-asistencia-card">

                        {{-- Info de la materia --}}
                        <div class="materia-info">
                            <div class="materia-nombre">
                                {{ $im->asignacion->materia->nombre ?? '—' }}
                            </div>
                            <div class="materia-docente">
                                <i class="fa-solid fa-chalkboard-teacher" style="color:#10b981;font-size:0.78rem;"></i>
                                {{ $im->asignacion->docente->nombre_completo ?? '—' }}
                            </div>
                        </div>

                        {{-- Faltas por periodo --}}
                        <div class="periodos-chips">
                            @foreach($periodos as $periodo)
                                @php
                                    $reg   = $asistencias->get($periodo->id);
                                    $total = $reg ? $reg->totalFaltas() : null;
                                    $clase = is_null($total) ? 'faltas-sin'
                                        : ($total === 0 ? 'faltas-ok'
                                        : ($total <= 3 ? 'faltas-bajo' : 'faltas-alto'));
                                @endphp
                                <div class="periodo-item">
                                    <span class="periodo-label">{{ $periodo->nombre }}</span>
                                    <span class="faltas-chip {{ $clase }}"
                                          @if($reg) title="Justif.: {{ $reg->faltas_justificadas }} | Injustif.: {{ $reg->faltas_injustificadas }}" @endif>
                                        {{ is_null($total) ? '—' : $total }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total anual --}}
                        <div class="total-asistencia">
                            <span class="total-label">Total</span>
                            @php
                                $claseTotal = !$tieneRegistros ? 'total-sin'
                                    : ($totalAnual === 0 ? 'total-ok'
                                    : ($totalAnual <= 8 ? 'total-medio' : 'total-alto'));
                            @endphp
                            <span class="total-chip {{ $claseTotal }}">
                                {{ $tieneRegistros ? $totalAnual : '—' }}
                            </span>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="sin-registros">No tienes materias activas en este año lectivo.</div>
        @endif

    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/estudiante/asistencia.js') }}"></script>
@endpush
