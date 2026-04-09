@extends('layouts.menuadmin')

@section('title', 'Periodos — ' . $anioLectivo->nombre)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/periodo/periodo.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Botón volver ── --}}
    <div>
        <a href="{{ route('admin.academico.anios.index') }}"
           class="btn btn-neutro btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver a Años Lectivos
        </a>
    </div>

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-layer-group"></i>
                Periodos — {{ $anioLectivo->nombre }}
            </h2>
            <p class="cabecera-subtitulo">
                {{ $anioLectivo->fecha_inicio->format('d/m/Y') }}
                — {{ $anioLectivo->fecha_fin->format('d/m/Y') }}
            </p>
        </div>
        <a href="{{ route('admin.academico.anios.periodos.create', $anioLectivo->id) }}"
           class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nuevo Periodo
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_academico')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Mini resumen ── --}}
    <div class="periodo-resumen">
        <div class="periodo-chip">
            <i class="fa-solid fa-list-ol"></i>
            <div>
                <span class="periodo-chip-label">Total</span>
                <strong class="periodo-chip-valor">{{ $periodos->count() }}</strong>
            </div>
        </div>
        <div class="periodo-chip">
            <i class="fa-solid fa-lock-open"></i>
            <div>
                <span class="periodo-chip-label">Abiertos</span>
                <strong class="periodo-chip-valor">{{ $periodos->where('abierto', true)->count() }}</strong>
            </div>
        </div>
        <div class="periodo-chip">
            <i class="fa-solid fa-lock"></i>
            <div>
                <span class="periodo-chip-label">Cerrados</span>
                <strong class="periodo-chip-valor">{{ $periodos->where('abierto', false)->count() }}</strong>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         Barra bulk — modo 'usuarios':
           1 seleccionado  → Editar + Eliminar
           2+ seleccionados → solo Eliminar
    ══════════════════════════════════════════ --}}
    <div class="barra-bulk"
         data-bulk-modo="usuarios"
         data-entidad="periodo(s)"
         data-url-editar="{{ route('admin.academico.anios.periodos.edit', [$anioLectivo->id, ':id']) }}"
         data-url-destroy="{{ route('admin.academico.anios.periodos.destroy', [$anioLectivo->id, ':id']) }}">

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
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Estado</th>
                    <th>En Curso</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodos as $periodo)
                    <tr data-id="{{ $periodo->id }}" class="fila-seleccionable">

                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $periodo->id }}"
                                   title="Seleccionar {{ $periodo->nombre }}">
                        </td>

                        <td data-label="#">{{ $periodo->numero }}</td>

                        <td data-label="Nombre">
                            <strong>{{ $periodo->nombre }}</strong>
                        </td>

                        <td data-label="Inicio">
                            {{ $periodo->fecha_inicio->format('d/m/Y') }}
                        </td>

                        <td data-label="Fin">
                            {{ $periodo->fecha_fin->format('d/m/Y') }}
                        </td>

                        <td data-label="Estado">
                            @if($periodo->abierto)
                                <span class="estado estado-abierto">
                                    <i class="fa-solid fa-lock-open"></i> Abierto
                                </span>
                            @else
                                <span class="estado estado-cerrado">
                                    <i class="fa-solid fa-lock"></i> Cerrado
                                </span>
                            @endif
                        </td>

                        <td data-label="En Curso">
                            @if($periodo->estaEnCurso())
                                <span class="estado estado-en-curso">
                                    <i class="fa-solid fa-play"></i> Sí
                                </span>
                            @else
                                <span class="estado estado-pendiente">No</span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="7">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay periodos registrados para este año.
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
    <script src="{{ asset('js/modulos/academico/periodo.js') }}"></script>
@endpush
