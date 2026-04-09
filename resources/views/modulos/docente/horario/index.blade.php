@extends('layouts.menudocente')
@section('title', 'Mi Horario')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/componentes/horario-cuadricula.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modulos/docente/horario/index.css') }}">
@endpush

@section('content')
<div class="contenedor-mi-horario">

    <div class="cabecera">
        <div>
            <h2><i class="fa-solid fa-clock"></i> Mi Horario</h2>
            <p class="cabecera-subtitulo">
                Año lectivo: <strong>{{ optional($anio)->nombre ?? 'Sin año activo' }}</strong>
            </p>
        </div>
    </div>

    @if(!$anio)
        <div class="alerta-info">
            <i class="fa-solid fa-circle-info"></i>
            No hay un año lectivo activo en el sistema.
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
                                            <span class="clase-grupo">
                                                <i class="fa-solid fa-users"></i>
                                                {{ optional($h->asignacion->grupo->grado)->nombre ?? '—' }}
                                                Gpo. {{ optional($h->asignacion->grupo)->nombre ?? '—' }}
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
    <script src="{{ asset('js/modulos/docente/horario.js') }}"></script>
@endpush
