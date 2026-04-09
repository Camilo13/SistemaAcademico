@extends('layouts.menuadmin')

@section('title', 'Años Lectivos')

@push('styles')
    {{-- academico-index.css ya cargado en el layout global --}}
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-calendar-days"></i>
            Años Lectivos
        </h2>
        <a href="{{ route('admin.academico.anios.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nuevo Año
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_academico')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ══════════════════════════════════════════
         Barra bulk — modo 'usuarios':
           1 seleccionado  → editar + eliminar
           2+ seleccionados → solo eliminar
         El JS de acciones-tabla.js maneja la lógica.
    ══════════════════════════════════════════ --}}
    <div class="barra-bulk"
         data-bulk-modo="usuarios"
         data-entidad="año(s) lectivo(s)"
         data-url-editar="{{ route('admin.academico.anios.edit', ':id') }}"
         data-url-destroy="{{ route('admin.academico.anios.destroy', ':id') }}">

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

    {{-- ── Tabla — sin columna Acciones ── --}}
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th class="col-check">
                        <input type="checkbox" class="checkbox-todos" title="Seleccionar todos">
                    </th>
                    <th>Nombre</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Estado</th>
                    <th>En curso</th>
                    <th>Periodos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anios as $anio)
                    <tr data-id="{{ $anio->id }}" class="fila-seleccionable">

                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $anio->id }}"
                                   title="Seleccionar {{ $anio->nombre }}">
                        </td>

                        <td data-label="Nombre">
                            <strong>{{ $anio->nombre }}</strong>
                        </td>

                        <td data-label="Inicio">
                            {{ $anio->fecha_inicio->format('d/m/Y') }}
                        </td>

                        <td data-label="Fin">
                            {{ $anio->fecha_fin->format('d/m/Y') }}
                        </td>

                        <td data-label="Estado">
                            @if($anio->activo)
                                <span class="estado estado-activo">
                                    <i class="fa-solid fa-circle-check"></i> Activo
                                </span>
                            @else
                                <span class="estado estado-inactivo">
                                    <i class="fa-solid fa-circle-xmark"></i> Inactivo
                                </span>
                            @endif
                        </td>

                        <td data-label="En curso">
                            @if($anio->estaEnCurso())
                                <span class="estado estado-en-curso">
                                    <i class="fa-solid fa-play"></i> Sí
                                </span>
                            @else
                                <span class="estado estado-pendiente">
                                    <i class="fa-solid fa-stop"></i> No
                                </span>
                            @endif
                        </td>

                        <td data-label="Periodos">
                            <a href="{{ route('admin.academico.anios.periodos.index', $anio->id) }}"
                               class="btn btn-secundario btn-sm">
                                <i class="fa-solid fa-layer-group"></i>
                                Ver ({{ $anio->periodos->count() }})
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="7">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay años lectivos registrados.
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
    <script src="{{ asset('js/modulos/academico/anio.js') }}"></script>
@endpush
