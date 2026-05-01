@extends('layouts.menuadmin')

@section('title', 'Sedes')

@push('styles')
    {{-- academico-index.css ya está en el layout global --}}
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
                            No hay sedes registradas aún.
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
