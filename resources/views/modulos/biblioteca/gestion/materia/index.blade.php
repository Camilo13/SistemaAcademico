@extends('layouts.menuadmin')

@section('title', 'Gestión de Materias — Biblioteca')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/biblioteca/gestion/materia/index.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <h2>
            <i class="fa-solid fa-layer-group"></i>
            Gestión de Materias
        </h2>
        <a href="{{ route('admin.biblioteca.materias.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nueva Materia
        </a>
    </div>

    {{-- ── Errores generales ── --}}
    @error('error_materia')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ══════════════════════════════════════════
         GRID DE TARJETAS
         El index de biblioteca usa tarjetas (no tabla)
         porque el contenido es un catálogo visual.
         Sin botón eliminar inline — solo desde el edit.
    ══════════════════════════════════════════ --}}
    @if($materias->isEmpty())

        <div class="tarjeta-form materia-vacio">
            <i class="fa-solid fa-book-open fa-2x"></i>
            <p>No hay materias registradas aún.</p>
            <a href="{{ route('admin.biblioteca.materias.create') }}"
               class="btn btn-primario btn-sm">
                <i class="fa-solid fa-plus"></i> Crear primera materia
            </a>
        </div>

    @else

        <div class="materias-grid">

            @foreach($materias as $materia)

                <article class="materia-card">

                    {{-- Badge de visibilidad ── --}}
                    <span class="materia-badge {{ $materia->visible ? 'badge-visible' : 'badge-oculta' }}">
                        <i class="fa-solid {{ $materia->visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        {{ $materia->visible ? 'Visible' : 'Oculta' }}
                    </span>

                    {{-- Nombre ── --}}
                    <h3 class="materia-nombre">{{ $materia->nombre }}</h3>

                    {{-- Descripción ── --}}
                    <p class="materia-descripcion">
                        {{ $materia->descripcion ?: 'Sin descripción.' }}
                    </p>

                    {{-- Contador de recursos ── --}}
                    <p class="materia-recursos-count">
                        <i class="fa-solid fa-file-lines"></i>
                        {{ $materia->recursos()->count() }}
                        {{ $materia->recursos()->count() === 1 ? 'recurso' : 'recursos' }}
                    </p>

                    {{-- Acciones ── solo Recursos y Editar, sin Eliminar inline ── --}}
                    <div class="materia-acciones">

                        <a href="{{ route('admin.biblioteca.materias.recursos.index', $materia->id_materia) }}"
                           class="btn btn-secundario btn-sm">
                            <i class="fa-solid fa-folder-open"></i> Recursos
                        </a>

                        <a href="{{ route('admin.biblioteca.materias.edit', $materia->id_materia) }}"
                           class="btn btn-neutro btn-sm">
                            <i class="fa-solid fa-pen"></i> Editar
                        </a>

                    </div>

                </article>

            @endforeach

        </div>

    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/biblioteca/gestion/materia/materia.js') }}"></script>
@endpush
