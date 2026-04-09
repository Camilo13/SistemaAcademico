@extends('layouts.menuadmin')

@section('title', 'Recursos — ' . $materia->nombre)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/biblioteca/gestion/recurso/index.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Botón volver — .btn.btn-neutro.btn-sm reutilizable ── --}}
    <div>
        <a href="{{ route('admin.biblioteca.materias.index') }}"
           class="btn btn-neutro btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver a materias
        </a>
    </div>

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-folder-open"></i>
                Recursos
            </h2>
            <p class="cabecera-subtitulo">
                Materia: <strong>{{ $materia->nombre }}</strong>
            </p>
        </div>
        <a href="{{ route('admin.biblioteca.materias.recursos.create', $materia->id_materia) }}"
           class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nuevo Recurso
        </a>
    </div>

    {{-- ── Error general ── --}}
    @error('error_recurso')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ══════════════════════════════════════════
         GRID DE RECURSOS
    ══════════════════════════════════════════ --}}
    @if($recursos->isEmpty())

        <div class="tarjeta-form recurso-vacio">
            <i class="fa-solid fa-file-circle-plus fa-2x"></i>
            <p>No hay recursos registrados para esta materia.</p>
            <a href="{{ route('admin.biblioteca.materias.recursos.create', $materia->id_materia) }}"
               class="btn btn-primario btn-sm">
                <i class="fa-solid fa-plus"></i> Agregar primer recurso
            </a>
        </div>

    @else

        <div class="recursos-grid">

            @foreach($recursos as $recurso)

                <article class="recurso-card">

                    {{-- Cabecera: tipo + visibilidad ── --}}
                    <div class="recurso-card-header">
                        <span class="recurso-tipo-badge">
                            <i class="fa-solid {{ $recurso->icono() }}"></i>
                            {{ strtoupper($recurso->tipo) }}
                            &nbsp;·&nbsp;
                            {{ $recurso->origen === 'url' ? 'Enlace' : 'Archivo' }}
                        </span>
                        <span class="materia-badge {{ $recurso->visible ? 'badge-visible' : 'badge-oculta' }}">
                            <i class="fa-solid {{ $recurso->visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                            {{ $recurso->visible ? 'Visible' : 'Oculto' }}
                        </span>
                    </div>

                    {{-- Título ── --}}
                    <h3 class="recurso-titulo">{{ $recurso->titulo }}</h3>

                    {{-- Descripción ── --}}
                    <p class="recurso-descripcion">
                        {{ $recurso->descripcion ?: 'Sin descripción.' }}
                    </p>

                    {{-- Autor ── --}}
                    @if($recurso->autor)
                        <p class="recurso-autor">
                            <i class="fa-solid fa-user-pen"></i>
                            {{ $recurso->autor }}
                        </p>
                    @endif

                    {{-- Acciones — solo .btn reutilizables ── --}}
                    <div class="recurso-acciones">

                        {{-- Ver: .btn-secundario (verde claro) ── --}}
                        @php([$label, $icon] = $recurso->accionAdmin())
                        <a href="{{ $recurso->url_final }}"
                           target="_blank" rel="noopener noreferrer"
                           class="btn btn-secundario btn-sm">
                            <i class="fa-solid {{ $icon }}"></i> {{ $label }}
                        </a>

                        {{-- Editar: .btn-neutro (gris) ── --}}
                        <a href="{{ route('admin.biblioteca.materias.recursos.edit', [$materia->id_materia, $recurso->id_recurso]) }}"
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
    <script src="{{ asset('js/modulos/biblioteca/gestion/recurso/recurso.js') }}"></script>
@endpush
