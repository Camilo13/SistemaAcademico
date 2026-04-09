@extends('layouts.menuadmin')

@section('title', 'Inscripciones')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/inscripcion/inscripcion.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-clipboard-list"></i>
                Inscripciones
            </h2>
            <p class="cabecera-subtitulo">
                Matrícula de estudiantes por grupo y año lectivo
            </p>
        </div>
        <a href="{{ route('admin.academico.inscripciones.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nueva Inscripción
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_inscripcion')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Filtros ── --}}
    <form method="GET" action="{{ route('admin.academico.inscripciones.index') }}"
          class="inscripcion-filtros">

        <select name="anio">
            <option value="">Todos los años</option>
            @foreach($anios as $anio)
                <option value="{{ $anio->id }}"
                    {{ request('anio') == $anio->id ? 'selected' : '' }}>
                    {{ $anio->nombre }}
                    @if($anio->activo) (Activo) @endif
                </option>
            @endforeach
        </select>

        <select name="grupo">
            <option value="">Todos los grupos</option>
            @foreach($grupos as $grupo)
                <option value="{{ $grupo->id }}"
                    {{ request('grupo') == $grupo->id ? 'selected' : '' }}>
                    {{ optional($grupo->grado)->nombre }} {{ $grupo->nombre }}
                    — {{ optional($grupo->anioLectivo)->nombre }}
                </option>
            @endforeach
        </select>

        <select name="estado">
            <option value="">Todos los estados</option>
            <option value="activa"     {{ request('estado') === 'activa'     ? 'selected' : '' }}>Activa</option>
            <option value="retirada"   {{ request('estado') === 'retirada'   ? 'selected' : '' }}>Retirada</option>
            <option value="finalizada" {{ request('estado') === 'finalizada' ? 'selected' : '' }}>Finalizada</option>
        </select>

        <button type="submit" class="btn btn-secundario btn-sm">
            <i class="fa-solid fa-magnifying-glass"></i> Filtrar
        </button>

        @if(request('anio') || request('grupo') || request('estado'))
            <a href="{{ route('admin.academico.inscripciones.index') }}"
               class="btn btn-neutro btn-sm">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </a>
        @endif

    </form>

    {{-- ══════════════════════════════════════════
         Barra bulk — modo 'usuarios':
           1 seleccionado  → Editar + Eliminar
           2+ seleccionados → solo Eliminar
    ══════════════════════════════════════════ --}}
    <div class="barra-bulk"
         data-bulk-modo="usuarios"
         data-entidad="inscripción(es)"
         data-url-editar="{{ route('admin.academico.inscripciones.edit', ':id') }}"
         data-url-destroy="{{ route('admin.academico.inscripciones.destroy', ':id') }}">

        <div class="bulk-info">
            <i class="fa-solid fa-check-square"></i>
            <span class="bulk-contador">0</span>
            seleccionada(s)
        </div>

        <div class="bulk-acciones">
            <a href="#" class="btn-bulk btn-bulk-editar">
                <i class="fa-solid fa-pen"></i> Editar
            </a>
            <button type="button" class="btn-bulk btn-bulk-eliminar">
                <i class="fa-solid fa-trash"></i> Eliminar
            </button>
            <div class="bulk-separador"></div>
            <button type="button" class="btn-bulk btn-bulk-limpiar">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </button>
        </div>

    </div>

    {{-- ── Tabla — sin columna de acciones ── --}}
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th class="col-check">
                        <input type="checkbox" class="checkbox-todos" title="Seleccionar todos">
                    </th>
                    <th>Estudiante</th>
                    <th>Grado</th>
                    <th>Grupo</th>
                    <th>Año Lectivo</th>
                    <th>F. Inscripción</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inscripciones as $inscripcion)
                    <tr data-id="{{ $inscripcion->id }}" class="fila-seleccionable">

                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $inscripcion->id }}"
                                   title="Seleccionar inscripción">
                        </td>

                        <td data-label="Estudiante">
                            {{ optional($inscripcion->estudiante)->nombre }}
                            {{ optional($inscripcion->estudiante)->apellidos }}
                        </td>

                        <td data-label="Grado">
                            {{ optional($inscripcion->grupo->grado)->nombre ?? '—' }}
                        </td>

                        <td data-label="Grupo">
                            <strong>{{ optional($inscripcion->grupo)->nombre ?? '—' }}</strong>
                        </td>

                        <td data-label="Año Lectivo">
                            {{ optional($inscripcion->grupo->anioLectivo)->nombre ?? '—' }}
                        </td>

                        <td data-label="F. Inscripción">
                            {{ $inscripcion->fecha_inscripcion
                                ? $inscripcion->fecha_inscripcion->format('d/m/Y')
                                : '—' }}
                        </td>

                        <td data-label="Estado">
                            @php
                                $estadoClase = match($inscripcion->estado) {
                                    'activa'     => 'estado-activo',
                                    'retirada'   => 'estado-inactivo',
                                    'finalizada' => 'estado-finalizado',
                                    default      => 'estado-pendiente',
                                };
                                $estadoIcono = match($inscripcion->estado) {
                                    'activa'     => 'fa-circle-check',
                                    'retirada'   => 'fa-circle-xmark',
                                    'finalizada' => 'fa-circle-dot',
                                    default      => 'fa-circle',
                                };
                            @endphp
                            <span class="estado {{ $estadoClase }}">
                                <i class="fa-solid {{ $estadoIcono }}"></i>
                                {{ ucfirst($inscripcion->estado) }}
                            </span>
                        </td>

                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="7">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay inscripciones con los filtros aplicados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Paginación ── --}}
    @if($inscripciones->hasPages())
        <div class="paginacion">{{ $inscripciones->links() }}</div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/inscripcion.js') }}"></script>
@endpush
