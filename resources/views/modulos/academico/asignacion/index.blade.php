@extends('layouts.menuadmin')

@section('title', 'Asignaciones')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/asignacion/asignacion.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-chalkboard-user"></i>
                Asignaciones Docentes
            </h2>
            <p class="cabecera-subtitulo">
                Docente → Materia → Grupo
            </p>
        </div>
        <a href="{{ route('admin.academico.asignaciones.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nueva Asignación
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_asignacion')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Filtros ── --}}
    <form method="GET" action="{{ route('admin.academico.asignaciones.index') }}"
          class="asignacion-filtros">

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

        <select name="docente">
            <option value="">Todos los docentes</option>
            @foreach($docentes as $docente)
                <option value="{{ $docente->id }}"
                    {{ request('docente') == $docente->id ? 'selected' : '' }}>
                    {{ $docente->nombre }} {{ $docente->apellidos }}
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
            <option value="activa"   {{ request('estado') === 'activa'   ? 'selected' : '' }}>Activa</option>
            <option value="inactiva" {{ request('estado') === 'inactiva' ? 'selected' : '' }}>Inactiva</option>
        </select>

        <button type="submit" class="btn btn-secundario btn-sm">
            <i class="fa-solid fa-magnifying-glass"></i> Filtrar
        </button>

        @if(request('anio') || request('docente') || request('grupo') || request('estado'))
            <a href="{{ route('admin.academico.asignaciones.index') }}"
               class="btn btn-neutro btn-sm">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </a>
        @endif

    </form>

    {{-- ══════════════════════════════════════════
         Barra bulk — modo 'usuarios'
    ══════════════════════════════════════════ --}}
    <div class="barra-bulk"
         data-bulk-modo="usuarios"
         data-entidad="asignación(es)"
         data-url-editar="{{ route('admin.academico.asignaciones.edit', ':id') }}"
         data-url-destroy="{{ route('admin.academico.asignaciones.destroy', ':id') }}">

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
                    <th>Docente</th>
                    <th>Materia</th>
                    <th>Grado</th>
                    <th>Grupo</th>
                    <th>Año Lectivo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asignaciones as $asignacion)
                    <tr data-id="{{ $asignacion->id }}" class="fila-seleccionable">

                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $asignacion->id }}"
                                   title="Seleccionar asignación">
                        </td>

                        <td data-label="Docente">
                            {{ optional($asignacion->docente)->nombre }}
                            {{ optional($asignacion->docente)->apellidos }}
                        </td>

                        <td data-label="Materia">
                            <strong>{{ optional($asignacion->materia)->nombre ?? '—' }}</strong>
                        </td>

                        <td data-label="Grado">
                            {{ optional($asignacion->grupo->grado)->nombre ?? '—' }}
                        </td>

                        <td data-label="Grupo">
                            {{ optional($asignacion->grupo)->nombre ?? '—' }}
                        </td>

                        <td data-label="Año Lectivo">
                            {{ optional($asignacion->grupo->anioLectivo)->nombre ?? '—' }}
                        </td>

                        <td data-label="Estado">
                            @if($asignacion->activa)
                                <span class="estado estado-activo">
                                    <i class="fa-solid fa-circle-check"></i> Activa
                                </span>
                            @else
                                <span class="estado estado-inactivo">
                                    <i class="fa-solid fa-circle-xmark"></i> Inactiva
                                </span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="7">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay asignaciones con los filtros aplicados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Paginación ── --}}
    @if($asignaciones->hasPages())
        <div class="paginacion">{{ $asignaciones->links() }}</div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/asignacion.js') }}"></script>
@endpush
