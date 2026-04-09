@extends('layouts.menuadmin')
@section('title', 'Horario — ' . optional($grupo->grado)->nombre . ' ' . $grupo->nombre)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/componentes/horario-cuadricula.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/horario/grupo.css') }}">
@endpush

@section('content')
<div class="contenedor-horario-grupo">

    {{-- Cabecera --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2><i class="fa-solid fa-table-cells"></i>
                Horario — {{ optional($grupo->grado)->nombre }} Grupo {{ $grupo->nombre }}
            </h2>
            <p class="cabecera-subtitulo">
                <i class="fa-solid fa-calendar-days"></i>
                {{ optional($grupo->anioLectivo)->nombre }}
            </p>
        </div>
        <div style="display:flex;gap:.6rem;flex-wrap:wrap;">
            <a href="{{ route('admin.horarios.create', $grupo->id) }}" class="btn btn-primario">
                <i class="fa-solid fa-plus"></i> Agregar franja
            </a>
            <a href="{{ route('admin.horarios.index') }}" class="btn btn-neutro">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if($errors->has('choque'))
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $errors->first('choque') }}
        </div>
    @endif

    {{-- Leyenda de pausas --}}
    <div class="leyenda-pausas">
        <span><i class="fa-solid fa-mug-hot"></i> Refrigerio: 9:00 – 9:30</span>
        <span><i class="fa-solid fa-utensils"></i> Almuerzo: 11:30 – 13:00</span>
    </div>

    {{-- Cuadrícula semanal --}}
    <div class="cuadricula-contenedor">
        <table class="cuadricula">
            <thead>
                <tr>
                    <th class="col-bloque">Bloque</th>
                    <th class="col-hora">Horario</th>
                    @foreach(\App\Models\Horario::DIAS_LABEL as $dia => $label)
                        <th class="col-dia">{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Horario::BLOQUES as $num => $info)
                    {{-- Separador de pausa antes del bloque 3 y 5 --}}
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
                        <td class="celda-bloque">
                            <span class="num-bloque">{{ $num }}</span>
                        </td>
                        <td class="celda-hora">
                            {{ $info['inicio'] }} – {{ $info['fin'] }}
                        </td>

                        @foreach(\App\Models\Horario::DIAS as $dia)
                            @php $celda = $cuadricula[$dia][$num]; @endphp
                            <td class="celda-clase {{ $celda ? 'ocupada' : 'libre' }}">
                                @if($celda)
                                    <div class="clase-card">
                                        <span class="clase-materia">
                                            {{ optional($celda['asignacion']->materia)->nombre ?? '—' }}
                                        </span>
                                        <span class="clase-docente">
                                            <i class="fa-solid fa-user-tie"></i>
                                            {{ optional($celda['asignacion']->docente)->nombre_completo ?? '—' }}
                                        </span>
                                        <form method="POST"
                                              action="{{ route('admin.horarios.destroy', $celda['horario']->id) }}"
                                              class="form-eliminar-franja"
                                              data-nombre="{{ optional($celda['asignacion']->materia)->nombre }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-eliminar-franja" title="Quitar franja">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
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

    {{-- Resumen de asignaciones sin horario completo --}}
    @if($asignaciones->isNotEmpty())
        <div class="resumen-asignaciones">
            <h3 class="seccion-titulo">
                <i class="fa-solid fa-chalkboard-teacher"></i>
                Asignaciones del grupo ({{ $asignaciones->count() }})
            </h3>
            <div class="tabla-contenedor">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Docente</th>
                            <th>Franjas cargadas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($asignaciones as $asig)
                            @php $franjas = $asig->horarios->count(); @endphp
                            <tr>
                                <td><strong>{{ optional($asig->materia)->nombre ?? '—' }}</strong></td>
                                <td>{{ optional($asig->docente)->nombre_completo ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $franjas > 0 ? 'badge-activo' : 'badge-inactivo' }}">
                                        {{ $franjas }} franja{{ $franjas !== 1 ? 's' : '' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/horario.js') }}"></script>
@endpush
