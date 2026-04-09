@extends('layouts.menuadmin')

@section('title', 'Grados')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/estructura/grado/grado.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-layer-group"></i>
            Grados
        </h2>
        <a href="{{ route('admin.academico.grados.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nuevo Grado
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_grado')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Filtros ── --}}
    <form method="GET" action="{{ route('admin.academico.grados.index') }}"
          class="grado-filtros">
        <select name="sede" onchange="this.form.submit()">
            <option value="">Todas las sedes</option>
            @foreach($sedes as $sede)
                <option value="{{ $sede->id }}"
                    {{ request('sede') == $sede->id ? 'selected' : '' }}>
                    {{ $sede->nombre }}
                </option>
            @endforeach
        </select>
        <select name="estado" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activos</option>
            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
        </select>
        @if(request('sede') || request('estado'))
            <a href="{{ route('admin.academico.grados.index') }}"
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
         data-entidad="grado(s)"
         data-url-editar="{{ route('admin.academico.grados.edit', ':id') }}"
         data-url-destroy="{{ route('admin.academico.grados.destroy', ':id') }}">

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
                    <th>Nombre</th>
                    <th>Nivel</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grados as $grado)
                    <tr data-id="{{ $grado->id }}" class="fila-seleccionable">

                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $grado->id }}"
                                   title="Seleccionar {{ $grado->nombre }}">
                        </td>

                        <td data-label="Sede">
                            {{ optional($grado->sede)->nombre ?? '—' }}
                        </td>

                        <td data-label="Nombre">
                            <strong>{{ $grado->nombre }}</strong>
                        </td>

                        <td data-label="Nivel">{{ $grado->nivel }}°</td>

                        <td data-label="Tipo">{{ $grado->tipo }}</td>

                        <td data-label="Estado">
                            @if($grado->activo)
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
                        <td colspan="6">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay grados registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Paginación ── --}}
    @if($grados->hasPages())
        <div class="paginacion">{{ $grados->links() }}</div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/estructura/grado.js') }}"></script>
@endpush
