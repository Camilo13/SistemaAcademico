@extends('layouts.menuestudiante')
@section('title', 'Mi Horario')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/componentes/horario-cuadricula.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modulos/estudiante/horario/index.css') }}">
@endpush

@section('content')
<div class="contenedor-mi-horario">

    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-clock"></i> Mi Horario</h2>
            <p class="cabecera-subtitulo">
                @if($inscripcion)
                    {{ optional($inscripcion->grupo->grado)->nombre }} —
                    Grupo {{ optional($inscripcion->grupo)->nombre }}
                    &nbsp;·&nbsp;
                    <strong>{{ optional($anio)->nombre }}</strong>
                @else
                    Sin inscripción activa
                @endif
            </p>
        </div>
    </div>

    @if(!$inscripcion)
        <div class="alerta-info">
            <i class="fa-solid fa-circle-info"></i>
            No tienes una inscripción activa en el año lectivo actual.
        </div>
    @else
        <div class="cuadricula-contenedor">
            <table class="cuadricula">
                <thead>
                    <tr>
                        <th class="col-bloque">Bloque</th>
                        <th class="col-hora">Horario</th>
                        @foreach(\App\Models\Horario::DIAS_LABEL as $label)
                            <th class="col-dia">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Horario::BLOQUES as $num => $info)
                        @if($num === 3)
                            <tr class="fila-pausa">
                                <td colspan="7">
                                    <i class="fa-solid fa-mug-hot"></i>
                                    Refrigerio &nbsp;·&nbsp; 9:00 – 9:30
                                </td>
                            </tr>
                        @elseif($num === 5)
                            <tr class="fila-pausa almuerzo">
                                <td colspan="7">
                                    <i class="fa-solid fa-utensils"></i>
                                    Almuerzo &nbsp;·&nbsp; 11:30 – 13:00
                                </td>
                            </tr>
                        @endif

                        <tr class="fila-bloque">
                            <td class="celda-bloque"><span class="num-bloque">{{ $num }}</span></td>
                            <td class="celda-hora">{{ $info['inicio'] }} – {{ $info['fin'] }}</td>

                            @foreach(\App\Models\Horario::DIAS as $dia)
                                @php $h = $cuadricula[$dia][$num]; @endphp
                                <td class="celda-clase {{ $h ? 'ocupada' : 'libre' }}">
                                    @if($h)
                                        <div class="clase-card">
                                            <span class="clase-materia">
                                                {{ optional($h->asignacion->materia)->nombre ?? '—' }}
                                            </span>
                                            <span class="clase-docente">
                                                <i class="fa-solid fa-user-tie"></i>
                                                {{ optional($h->asignacion->docente)->nombre_completo ?? '—' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="celda-vacia">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/estudiante/horario.js') }}"></script>
@endpush
