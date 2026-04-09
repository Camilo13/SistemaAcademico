@extends('layouts.menuestudiante')
@section('title', 'Notas — ' . $periodo->nombre)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/estudiante/notas/periodo.css') }}">
@endpush

@section('content')
<div class="contenedor-notas-periodo">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-layer-group"></i> Notas — {{ $periodo->nombre }}</h2>
            <p class="cabecera-subtitulo">
                {{ $anioActivo->nombre }}
                &nbsp;·&nbsp;
                <span class="badge-estado-periodo {{ $periodo->abierto ? 'periodo-abierto' : 'periodo-cerrado' }}">
                    <i class="fa-solid {{ $periodo->abierto ? 'fa-lock-open' : 'fa-lock' }}"></i>
                    {{ $periodo->abierto ? 'Abierto' : 'Cerrado' }}
                </span>
            </p>
        </div>
        <a href="{{ route('estudiante.notas.index') }}" class="btn btn-neutro">
            <i class="fa-solid fa-arrow-left"></i> Todas las notas
        </a>
    </div>

    {{-- Navegación entre periodos --}}
    <div class="nav-periodos">
        <a href="{{ route('estudiante.notas.index') }}" class="nav-periodo">
            <i class="fa-solid fa-layer-group"></i> Todas
        </a>
        @foreach($periodos as $p)
            <a href="{{ route('estudiante.notas.periodo', $p->id) }}"
               class="nav-periodo {{ $p->id === $periodo->id ? 'activo' : '' }}">
                {{ $p->nombre }}
            </a>
        @endforeach
    </div>

    @if($inscripcion)
        @php
            $materiasInscritas  = $inscripcion->inscripcionMaterias->where('estado', 'activa');
            $totalRegistradas   = 0;
            $totalAprobadas     = 0;
            $sumaNotas          = 0;
        @endphp

        {{-- Pre-calcular resumen --}}
        @foreach($materiasInscritas as $im)
            @php
                $nota = $im->notas->where('periodo_id', $periodo->id)->first();
                if ($nota) {
                    $totalRegistradas++;
                    $sumaNotas += $nota->nota;
                    if ($nota->nota >= 3.0) $totalAprobadas++;
                }
            @endphp
        @endforeach

        @php
            $promedioGeneral = $totalRegistradas > 0
                ? round($sumaNotas / $totalRegistradas, 2)
                : null;
        @endphp

        {{-- Resumen del periodo --}}
        <div class="resumen-periodo">
            <div class="resumen-card">
                <i class="fa-solid fa-book-open"></i>
                <div>
                    <span>Materias</span>
                    <strong>{{ $materiasInscritas->count() }}</strong>
                </div>
            </div>
            <div class="resumen-card">
                <i class="fa-solid fa-clipboard-check"></i>
                <div>
                    <span>Calificadas</span>
                    <strong>{{ $totalRegistradas }}</strong>
                </div>
            </div>
            <div class="resumen-card">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <span>Aprobadas</span>
                    <strong>{{ $totalAprobadas }}</strong>
                </div>
            </div>
            <div class="resumen-card">
                <i class="fa-solid fa-calculator"></i>
                <div>
                    <span>Promedio</span>
                    <strong>{{ !is_null($promedioGeneral) ? number_format($promedioGeneral, 2) : '—' }}</strong>
                </div>
            </div>
        </div>

        {{-- Tabla de notas del periodo --}}
        <div class="tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Docente</th>
                        <th>Nota</th>
                        <th>Desempeño</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materiasInscritas as $im)
                        @php
                            $nota = $im->notas->where('periodo_id', $periodo->id)->first();
                        @endphp
                        <tr data-promedio="{{ $nota?->nota ?? '' }}"
                            class="{{ $nota && $nota->nota < 3.0 ? 'fila-alerta' : '' }}">

                            <td data-label="Materia">
                                <strong>{{ optional($im->asignacion->materia)->nombre ?? '—' }}</strong>
                            </td>
                            <td data-label="Docente">
                                {{ optional($im->asignacion->docente)->name ?? '—' }}
                            </td>
                            <td data-label="Nota">
                                @if($nota)
                                    <span class="nota-chip {{ $nota->nota >= 3.0 ? 'nota-aprobada' : 'nota-reprobada' }}">
                                        {{ number_format($nota->nota, 2) }}
                                    </span>
                                @else
                                    <span class="nota-chip nota-sin">Sin calificar</span>
                                @endif
                            </td>
                            <td data-label="Desempeño">
                                @if($nota)
                                    @php
                                        [$cls, $label] = match(true) {
                                            $nota->nota >= 4.5 => ['des-superior', 'Superior'],
                                            $nota->nota >= 4.0 => ['des-alto',     'Alto'],
                                            $nota->nota >= 3.0 => ['des-basico',   'Básico'],
                                            default            => ['des-bajo',     'Bajo'],
                                        };
                                    @endphp
                                    <span class="badge-desempeno {{ $cls }}">{{ $label }}</span>
                                @else
                                    <span class="badge-desempeno" style="background:#f3f4f6;color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td data-label="Observación">
                                {{ $nota?->observacion ?? '—' }}
                            </td>

                        </tr>
                    @empty
                        <tr class="fila-vacia">
                            <td colspan="5">
                                <i class="fa-solid fa-circle-info"></i>
                                No tienes materias activas en este periodo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @else
        <div class="aviso-sin-anio">
            <i class="fa-solid fa-circle-info"></i>
            No tienes inscripción activa en este año lectivo.
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/estudiante/notas.js') }}"></script>
@endpush
