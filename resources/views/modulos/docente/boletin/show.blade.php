@extends('layouts.menudocente')
@section('title', 'Boletín — ' . ($boletin['estudiante']['nombre'] ?? ''))

@push('styles')
    {{-- Reutiliza el CSS del boletín admin: misma paleta, mismas clases --}}
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/boletin/boletin.css') }}">
@endpush

@section('content')
<div class="contenedor-modulo">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-file-lines"></i> Boletín Académico</h2>
            <p class="cabecera-subtitulo">
                Generado el {{ \Carbon\Carbon::parse($boletin['fecha_generacion'])->format('d/m/Y H:i') }}
            </p>
        </div>
        <a href="{{ route('docente.grupos.index') }}" class="btn btn-neutro">
            <i class="fa-solid fa-arrow-left"></i> Mis Grupos
        </a>
    </div>

    {{-- Ficha del estudiante --}}
    <div class="ficha-estudiante">
        <div class="ficha-dato">
            <span><i class="fa-solid fa-user-graduate"></i> Estudiante</span>
            <strong>{{ $boletin['estudiante']['nombre'] ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-users"></i> Grupo</span>
            <strong>{{ $boletin['grupo'] ?? '—' }}</strong>
        </div>
        <div class="ficha-dato">
            <span><i class="fa-solid fa-calendar-days"></i> Año Lectivo</span>
            <strong>{{ $boletin['anio_lectivo'] ?? '—' }}</strong>
        </div>
    </div>

    {{-- Resumen general --}}
    <div class="resumen-boletin">
        <div class="resumen-card {{ $boletin['aprobado_anio'] ? 'aprobado' : 'reprobado' }}">
            <i class="fa-solid {{ $boletin['aprobado_anio'] ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
            <div>
                <span>Resultado anual</span>
                <strong>{{ $boletin['aprobado_anio'] ? 'APROBADO' : 'REPROBADO' }}</strong>
            </div>
        </div>
        <div class="resumen-card">
            <i class="fa-solid fa-calculator"></i>
            <div>
                <span>Promedio general</span>
                <strong>
                    {{ !is_null($boletin['promedio_general'])
                        ? number_format($boletin['promedio_general'], 2)
                        : '—' }}
                </strong>
            </div>
        </div>
        <div class="resumen-card">
            <i class="fa-solid fa-book-open"></i>
            <div>
                <span>Total materias</span>
                <strong>{{ $boletin['total_materias'] }}</strong>
            </div>
        </div>
        <div class="resumen-card aprobadas">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <span>Aprobadas</span>
                <strong>{{ $boletin['materias_aprobadas'] }}</strong>
            </div>
        </div>
        <div class="resumen-card reprobadas">
            <i class="fa-solid fa-circle-xmark"></i>
            <div>
                <span>Reprobadas</span>
                <strong>{{ $boletin['materias_reprobadas'] }}</strong>
            </div>
        </div>
    </div>

    {{-- Tabla de materias --}}
    <div class="tabla-contenedor">
        <table class="tabla tabla-boletin">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Docente</th>
                    <th>Promedio</th>
                    <th>Desempeño</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boletin['materias'] as $materia)
                    @php
                        $cls = match($materia['estado_academico']) {
                            'Desempeño Superior' => 'des-superior',
                            'Desempeño Alto'     => 'des-alto',
                            'Desempeño Básico'   => 'des-basico',
                            'Desempeño Bajo'     => 'des-bajo',
                            default              => 'des-sin',
                        };
                    @endphp
                    <tr class="{{ $materia['aprobada'] ? '' : 'fila-alerta' }}">
                        <td data-label="Materia">
                            <strong>{{ $materia['materia_nombre'] }}</strong>
                        </td>
                        <td data-label="Docente">{{ $materia['docente_nombre'] }}</td>
                        <td data-label="Promedio">
                            @if(!is_null($materia['promedio']))
                                <span class="nota-chip {{ $materia['aprobada'] ? 'aprobada' : 'reprobada' }}">
                                    {{ number_format($materia['promedio'], 2) }}
                                </span>
                            @else
                                <span class="badge-sin-calificar">Sin notas</span>
                            @endif
                        </td>
                        <td data-label="Desempeño">
                            <span class="badge-desempeno {{ $cls }}">
                                {{ $materia['estado_academico'] }}
                            </span>
                        </td>
                        <td data-label="Resultado">
                            @if($materia['aprobada'])
                                <span class="badge badge-aprobada">
                                    <i class="fa-solid fa-check"></i> Aprobada
                                </span>
                            @else
                                <span class="badge badge-reprobada">
                                    <i class="fa-solid fa-xmark"></i> Reprobada
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="5">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay materias registradas en esta inscripción.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/docente/notas.js') }}"></script>
@endpush
