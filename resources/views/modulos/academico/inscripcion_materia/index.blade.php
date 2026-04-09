@extends('layouts.menuadmin')

@section('title', 'Materias Inscritas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/inscripcion_materia/inscripcion_materia.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-book-open-reader"></i>
                Materias Inscritas
            </h2>
            <p class="cabecera-subtitulo">
                <i class="fa-solid fa-user-graduate"></i>
                <strong>
                    {{ optional($inscripcion->estudiante)->nombre }}
                    {{ optional($inscripcion->estudiante)->apellidos }}
                </strong>
                &nbsp;·&nbsp;
                Grupo {{ optional($inscripcion->grupo)->nombre ?? '—' }}
                — {{ optional($inscripcion->grupo->anioLectivo)->nombre ?? '—' }}
            </p>
        </div>
        <div style="display:flex;gap:.6rem;flex-wrap:wrap;">
            <a href="{{ route('admin.academico.inscripciones.index') }}"
               class="btn btn-neutro btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Inscripciones
            </a>
            @if($inscripcion->estado === 'activa')
                <a href="{{ route('admin.academico.inscripciones.materias.create', $inscripcion->id) }}"
                   class="btn btn-primario">
                    <i class="fa-solid fa-plus"></i> Agregar Materia
                </a>
            @endif
        </div>
    </div>

    {{-- ── Error general ── --}}
    @error('error_inscripcion_materia')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ── Ficha resumen de la inscripción ── --}}
    <div class="im-ficha">
        <div class="im-ficha-item">
            <span>Estado inscripción</span>
            @php
                $cls = match($inscripcion->estado) {
                    'activa'     => 'estado-activo',
                    'retirada'   => 'estado-inactivo',
                    'finalizada' => 'estado-finalizado',
                    default      => 'estado-pendiente',
                };
            @endphp
            <span class="estado {{ $cls }}">{{ ucfirst($inscripcion->estado) }}</span>
        </div>
        <div class="im-ficha-item">
            <span>Total materias</span>
            <strong>{{ $materias->count() }}</strong>
        </div>
        <div class="im-ficha-item">
            <span>Activas</span>
            <strong>{{ $materias->where('estado', 'activa')->count() }}</strong>
        </div>
        <div class="im-ficha-item">
            <span>Retiradas</span>
            <strong>{{ $materias->where('estado', 'retirada')->count() }}</strong>
        </div>
    </div>

    {{-- ── Tabla ── --}}
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Docente</th>
                    <th>Notas</th>
                    <th>Estado</th>
                    <th class="col-acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materias as $im)
                    <tr>
                        <td data-label="Materia">
                            <strong>{{ optional($im->asignacion->materia)->nombre ?? '—' }}</strong>
                        </td>

                        <td data-label="Docente">
                            {{ optional($im->asignacion->docente)->nombre }}
                            {{ optional($im->asignacion->docente)->apellidos }}
                        </td>

                        <td data-label="Notas">
                            {{ $im->notas->count() }}
                        </td>

                        <td data-label="Estado">
                            @if($im->estado === 'activa')
                                <span class="estado estado-activo">
                                    <i class="fa-solid fa-circle-check"></i> Activa
                                </span>
                            @else
                                <span class="estado estado-inactivo">
                                    <i class="fa-solid fa-circle-xmark"></i> Retirada
                                </span>
                            @endif
                        </td>

                        <td class="col-acciones">
                            <div class="acciones">
                                {{-- Ver notas ── --}}
                                <a href="{{ route('admin.academico.notas.index', $im->id) }}"
                                   class="btn-icono ver"
                                   title="Ver notas">
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </a>

                                {{-- Retirar (solo si activa) ── --}}
                                @if($im->estado === 'activa')
                                    <form method="POST"
                                          action="{{ route('admin.academico.inscripciones.materias.retirar', [$inscripcion->id, $im->id]) }}"
                                          class="form-retirar"
                                          data-nombre="{{ optional($im->asignacion->materia)->nombre }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="btn-icono desactivar"
                                                title="Retirar materia">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Eliminar ── --}}
                                <form method="POST"
                                      action="{{ route('admin.academico.inscripciones.materias.destroy', [$inscripcion->id, $im->id]) }}"
                                      class="form-eliminar"
                                      data-nombre="{{ optional($im->asignacion->materia)->nombre }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn-icono eliminar"
                                            title="Eliminar materia">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="5">
                            <i class="fa-solid fa-circle-info"></i>
                            No hay materias inscritas aún.
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
    <script src="{{ asset('js/modulos/academico/inscripcion_materia.js') }}"></script>
@endpush
