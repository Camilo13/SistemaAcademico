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
                <strong>{{ $inscripcion->estudiante->nombre_completo ?? '—' }}</strong>
                &nbsp;·&nbsp;
                Grupo {{ optional($inscripcion->grupo)->nombre ?? '—' }}
                — {{ optional($inscripcion->grupo->anioLectivo)->nombre ?? '—' }}
            </p>
        </div>
        <div class="im-cabecera-acciones">
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

    {{-- ── Ficha resumen ── --}}
    <div class="im-ficha">
        <div class="im-ficha-item">
            <span>Estado inscripción</span>
            @php
                $clsInscripcion = match($inscripcion->estado) {
                    'activa'     => 'estado-activo',
                    'retirada'   => 'estado-inactivo',
                    'finalizada' => 'estado-finalizado',
                    default      => 'estado-pendiente',
                };
            @endphp
            <span class="estado {{ $clsInscripcion }}">{{ ucfirst($inscripcion->estado) }}</span>
        </div>
        <div class="im-ficha-item">
            <span>Total</span>
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

    {{-- ══════════════════════════════════════════
         Barra bulk — modo 'usuarios':
           acciones-tabla.js maneja: btn-bulk-editar (Ver Notas, 1 sel)
                                     + btn-bulk-eliminar (1 o más)
           inscripcion_materia.js maneja: btn-bulk-retirar (1 sel + activa)
    ══════════════════════════════════════════ --}}
    <div class="barra-bulk"
         data-bulk-modo="usuarios"
         data-entidad="materia(s)"
         data-url-editar="{{ route('admin.academico.notas.index', ':id') }}"
         data-url-destroy="{{ str_replace('PHID', ':id', route('admin.academico.inscripciones.materias.destroy', [$inscripcion->id, 'PHID'])) }}"
         data-url-retirar="{{ str_replace('PHID', ':id', route('admin.academico.inscripciones.materias.retirar', [$inscripcion->id, 'PHID'])) }}">

        <div class="bulk-info">
            <i class="fa-solid fa-check-square"></i>
            <span class="bulk-contador">0</span>
            seleccionada(s)
        </div>

        <div class="bulk-acciones">

            {{-- Ver Notas — acciones-tabla.js lo oculta en 2+ (usa btn-bulk-editar) --}}
            <a href="#" class="btn-bulk btn-bulk-editar">
                <i class="fa-solid fa-star-half-stroke"></i> Ver Notas
            </a>

            {{-- Retirar — inscripcion_materia.js lo muestra solo si 1 sel + activa --}}
            <button type="button"
                    class="btn-bulk btn-bulk-retirar"
                    style="display:none">
                <i class="fa-solid fa-ban"></i> Retirar
            </button>

            {{-- Eliminar — acciones-tabla.js, 1 o más --}}
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
                        <input type="checkbox" class="checkbox-todos" title="Seleccionar todas">
                    </th>
                    <th>Materia</th>
                    <th>Docente</th>
                    <th>Notas</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materias as $im)
                    <tr data-id="{{ $im->id }}"
                        data-estado="{{ $im->estado }}"
                        class="fila-seleccionable">

                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $im->id }}"
                                   data-estado="{{ $im->estado }}"
                                   title="Seleccionar">
                        </td>

                        <td data-label="Materia">
                            <strong>{{ optional($im->asignacion->materia)->nombre ?? '—' }}</strong>
                        </td>

                        <td data-label="Docente">
                            {{ optional($im->asignacion->docente)->nombre_completo ?? '—' }}
                        </td>

                        <td data-label="Notas" class="celda-notas">
                            <a href="{{ route('admin.academico.notas.index', $im->id) }}"
                               class="im-notas-chip"
                               title="Ver notas de esta materia">
                                <i class="fa-solid fa-star-half-stroke"></i>
                                {{ $im->notas->count() }}
                            </a>
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
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/inscripcion_materia.js') }}"></script>
@endpush
