@extends('layouts.menuadmin')

@section('title', 'Grupos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/estructura/grupo/grupo.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-users"></i>
                Grupos
            </h2>
            <p class="cabecera-subtitulo">
                Unidades académicas por grado y año lectivo
            </p>
        </div>
        <a href="{{ route('admin.academico.estructura.grupos.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nuevo Grupo
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_grupo')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Filtros ── --}}
    <form method="GET" action="{{ route('admin.academico.estructura.grupos.index') }}"
          class="grupo-filtros">

        <select name="anio" onchange="this.form.submit()">
            <option value="">Todos los años</option>
            @foreach($anios as $anio)
                <option value="{{ $anio->id }}"
                    {{ request('anio') == $anio->id ? 'selected' : '' }}>
                    {{ $anio->nombre }}
                    @if($anio->activo) (Activo) @endif
                </option>
            @endforeach
        </select>

        <select name="grado" onchange="this.form.submit()">
            <option value="">Todos los grados</option>
            @foreach($grados as $grado)
                <option value="{{ $grado->id }}"
                    {{ request('grado') == $grado->id ? 'selected' : '' }}>
                    {{ $grado->nombre }}
                    @if($grado->sede) — {{ $grado->sede->nombre }} @endif
                </option>
            @endforeach
        </select>

        <select name="estado" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activos</option>
            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
        </select>

        @if(request('anio') || request('grado') || request('estado'))
            <a href="{{ route('admin.academico.estructura.grupos.index') }}"
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
         data-entidad="grupo(s)"
         data-url-editar="{{ route('admin.academico.estructura.grupos.edit', ':id') }}"
         data-url-destroy="{{ route('admin.academico.estructura.grupos.destroy', ':id') }}">

        <div class="bulk-info">
            <i class="fa-solid fa-check-square"></i>
            <span class="bulk-contador">0</span>
            seleccionado(s)
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
                    <th>Sede</th>
                    <th>Grado</th>
                    <th>Año</th>
                    <th>Grupo</th>
                    <th>Cupo Máx.</th>
                    <th>Disponibles</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grupos as $grupo)
                    <tr data-id="{{ $grupo->id }}" class="fila-seleccionable">

                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $grupo->id }}"
                                   title="Seleccionar grupo {{ $grupo->nombre }}">
                        </td>

                        <td data-label="Sede">
                            {{ optional($grupo->grado->sede)->nombre ?? '—' }}
                        </td>

                        <td data-label="Grado">
                            {{ optional($grupo->grado)->nombre ?? '—' }}
                        </td>

                        <td data-label="Año">
                            {{ optional($grupo->anioLectivo)->nombre ?? '—' }}
                        </td>

                        <td data-label="Grupo">
                            <strong>{{ $grupo->nombre }}</strong>
                        </td>

                        <td data-label="Cupo Máx.">
                            {{ $grupo->cupo_maximo ?? '∞' }}
                        </td>

                        <td data-label="Disponibles">
                            @if(is_null($grupo->cupo_maximo))
                                —
                            @else
                                {{ $grupo->cupoDisponible() }}
                            @endif
                        </td>

                        <td data-label="Estado">
                            @if($grupo->activo)
                                <span class="estado estado-activo">
                                    <i class="fa-solid fa-circle-check"></i> Activo
                                </span>
                            @else
                                <span class="estado estado-inactivo">
                                    <i class="fa-solid fa-circle-xmark"></i> Inactivo
                                </span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="8">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay grupos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Paginación ── --}}
    @if($grupos->hasPages())
        <div class="paginacion">{{ $grupos->links() }}</div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/estructura/grupo.js') }}"></script>
@endpush
