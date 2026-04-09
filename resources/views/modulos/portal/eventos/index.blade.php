@extends('layouts.menuadmin')

@section('title', 'Gestión de Eventos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/portal/eventos/eventos.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
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

    {{-- ── Error general ── --}}
    @error('error_evento')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Tabla ── --}}
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Lugar</th>
                    <th>Estado</th>
                    <th class="col-acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>

                @forelse($eventos as $evento)
                    <tr>
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

                        <td class="col-acciones">
                            <div class="acciones">
                                {{-- Solo editar — eliminar desde el edit ── --}}
                                <a href="{{ route('admin.eventos.edit', $evento->id) }}"
                                   class="btn-icono editar"
                                   title="Editar evento">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </div>
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

    {{-- ── Paginación ── --}}
    @if($eventos->hasPages())
        <div class="paginacion">{{ $eventos->links() }}</div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/portal/eventos/eventos.js') }}"></script>
@endpush
