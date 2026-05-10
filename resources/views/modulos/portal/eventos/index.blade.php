@extends('layouts.menuadmin')

@section('title', 'Gestión de Eventos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/portal/eventos/eventos.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-calendar-alt"></i>
                Gestión de Eventos
            </h2>
            <p class="cabecera-subtitulo">
                Eventos institucionales que se muestran en el portal público.
            </p>
        </div>
        <a href="{{ route('admin.eventos.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nuevo Evento
        </a>
    </div>

    @error('error_evento')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    <div class="barra-bulk"
         data-bulk-modo="usuarios"
         data-entidad="evento(s)"
         data-url-editar="{{ route('admin.eventos.edit', ':id') }}"
         data-url-destroy="{{ route('admin.eventos.destroy', ':id') }}"
         data-url-bulk-destroy="{{ route('admin.eventos.destroyBulk') }}">

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

    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th class="col-check">
                        <input type="checkbox" class="checkbox-todos" title="Seleccionar todos">
                    </th>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Lugar</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eventos as $evento)
                    <tr data-id="{{ $evento->id }}" class="fila-seleccionable">
                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $evento->id }}"
                                   title="Seleccionar evento">
                        </td>
                        <td data-label="Título">
                            <strong>{{ $evento->titulo }}</strong>
                        </td>
                        <td data-label="Fecha">
                            {{ $evento->fecha_evento->format('d/m/Y') }}
                        </td>
                        <td data-label="Hora">
                            {{ $evento->fecha_evento->format('H:i') }}
                        </td>
                        <td data-label="Lugar">
                            {{ $evento->lugar ?? '—' }}
                        </td>
                        <td data-label="Estado">
                            @if($evento->activo)
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
                            No hay eventos registrados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($eventos->hasPages())
        <div class="paginacion">{{ $eventos->links() }}</div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/portal/eventos/eventos.js') }}"></script>
@endpush
