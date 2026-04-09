@extends('layouts.menuadmin')

@section('title', 'Materias')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/estructura/materia/materia.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-book-open"></i>
            Materias
        </h2>
        <a href="{{ route('admin.academico.estructura.materias.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nueva Materia
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_materia')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Filtros ── --}}
    <form method="GET" action="{{ route('admin.academico.estructura.materias.index') }}"
          class="materia-filtros">
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
            <option value="activa"   {{ request('estado') === 'activa'   ? 'selected' : '' }}>Activas</option>
            <option value="inactiva" {{ request('estado') === 'inactiva' ? 'selected' : '' }}>Inactivas</option>
        </select>
        @if(request('grado') || request('estado'))
            <a href="{{ route('admin.academico.estructura.materias.index') }}"
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
         data-entidad="materia(s)"
         data-url-editar="{{ route('admin.academico.estructura.materias.edit', ':id') }}"
         data-url-destroy="{{ route('admin.academico.estructura.materias.destroy', ':id') }}">

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
                    <th>Sede</th>
                    <th>Grado</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Horas/sem.</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materias as $materia)
                    <tr data-id="{{ $materia->id }}" class="fila-seleccionable">

                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $materia->id }}"
                                   title="Seleccionar {{ $materia->nombre }}">
                        </td>

                        <td data-label="Sede">
                            {{ optional($materia->grado->sede)->nombre ?? '—' }}
                        </td>

                        <td data-label="Grado">
                            {{ optional($materia->grado)->nombre ?? '—' }}
                        </td>

                        <td data-label="Código">
                            {{ $materia->codigo ?? '—' }}
                        </td>

                        <td data-label="Nombre">
                            <strong>{{ $materia->nombre }}</strong>
                        </td>

                        <td data-label="Horas/sem.">
                            {{ $materia->intensidad_horaria ?? '—' }}
                        </td>

                        <td data-label="Estado">
                            @if($materia->activa)
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
                            No hay materias registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Paginación ── --}}
    @if($materias->hasPages())
        <div class="paginacion">{{ $materias->links() }}</div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/estructura/materia.js') }}"></script>
@endpush
