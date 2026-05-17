@extends('layouts.menuadmin')

@section('title', 'Sedes')

@push('styles')
    {{-- academico-index.css ya está en el layout global --}}
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/estructura/sede/sede.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ══════════════════════════════════════════
         CABECERA
    ══════════════════════════════════════════ --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-school"></i>
            Sedes
        </h2>
        <a href="{{ route('admin.academico.sedes.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nueva Sede
        </a>
    </div>

    {{-- ── Filtros ── --}}
    <form method="GET"
          action="{{ route('admin.academico.sedes.index') }}"
          class="sede-filtros">

        <input type="text"
               name="buscar"
               value="{{ $buscar }}"
               placeholder="Buscar por nombre, código o teléfono…"
               autocomplete="off">

        <select name="estado" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            <option value="activa"   {{ $estado === 'activa'   ? 'selected' : '' }}>Activa</option>
            <option value="inactiva" {{ $estado === 'inactiva' ? 'selected' : '' }}>Inactiva</option>
        </select>

        <button type="submit" class="btn btn-secundario btn-sm">
            <i class="fa-solid fa-magnifying-glass"></i> Buscar
        </button>

        @if($buscar || $estado)
            <a href="{{ route('admin.academico.sedes.index') }}"
               class="btn btn-neutro btn-sm">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </a>
        @endif

    </form>

    {{-- ══════════════════════════════════════════
         BARRA DE ACCIONES BULK
         Aparece al seleccionar una o más filas.
         JS (acciones-tabla.js) controla .visible.
    ══════════════════════════════════════════ --}}
    <div class="barra-bulk"
         data-bulk-modo="usuarios"
         data-entidad="sede(s)"
         data-url-editar="{{ route('admin.academico.sedes.edit', ':id') }}"
         data-url-destroy="{{ route('admin.academico.sedes.destroy', ':id') }}">

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
                <i class="fa-solid fa-trash"></i> Eliminar seleccionadas
            </button>
            <div class="bulk-separador"></div>
            <button type="button" class="btn-bulk btn-bulk-limpiar">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </button>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         TABLA
    ══════════════════════════════════════════ --}}
    <div class="tabla-contenedor">
        <table class="tabla">

            <thead>
                <tr>
                    {{-- Checkbox "seleccionar todos" --}}
                    <th class="col-check">
                        <input type="checkbox"
                               class="checkbox-todos"
                               title="Seleccionar todas">
                    </th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sedes as $sede)
                    <tr data-id="{{ $sede->id }}" class="fila-seleccionable">
                        {{-- Checkbox individual --}}
                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $sede->id }}"
                                   title="Seleccionar {{ $sede->nombre }}">
                        </td>

                        <td data-label="Código">
                            {{ $sede->codigo ?? '—' }}
                        </td>

                        <td data-label="Nombre">
                            <strong>{{ $sede->nombre }}</strong>
                        </td>

                        <td data-label="Dirección">
                            {{ $sede->direccion ?? '—' }}
                        </td>

                        <td data-label="Teléfono">
                            {{ $sede->telefono ?? '—' }}
                        </td>

                        <td data-label="Estado">
                            @if($sede->activa)
                                <span class="estado estado-activa">
                                    <i class="fa-solid fa-circle-check"></i> Activa
                                </span>
                            @else
                                <span class="estado estado-inactiva">
                                    <i class="fa-solid fa-circle-xmark"></i> Inactiva
                                </span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="6">
                            <i class="fa-solid fa-circle-info"></i>
                            @if($buscar || $estado)
                                No se encontraron sedes con los filtros aplicados.
                            @else
                                No hay sedes registradas aún.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/estructura/sede.js') }}"></script>
@endpush
