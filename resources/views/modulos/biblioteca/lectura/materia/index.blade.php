@extends($layout)

@section('title', 'Biblioteca Digital')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/biblioteca/lectura/lectura.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-book"></i>
                Biblioteca Digital
            </h2>
            <p class="cabecera-subtitulo">
                Explora las materias académicas y accede a sus recursos.
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         GRID DE MATERIAS
    ══════════════════════════════════════════ --}}
    @if($materias->isEmpty())

        <div class="lectura-vacio">
            <i class="fa-solid fa-book-open fa-2x"></i>
            <p>No hay materias disponibles en este momento.</p>
        </div>

    @else

        <div class="lectura-grid">

            @foreach($materias as $materia)

                <article class="lectura-card">

                    {{-- Ícono de materia ── --}}
                    <div class="lectura-card-icono">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>

                    {{-- Nombre ── --}}
                    <h3 class="lectura-card-titulo">
                        {{ $materia->nombre }}
                    </h3>

                    {{-- Descripción ── --}}
                    <p class="lectura-card-desc">
                        {{ $materia->descripcion ?: 'Sin descripción.' }}
                    </p>

                    {{-- Contador de recursos ── --}}
                    <p class="lectura-card-meta">
                        <i class="fa-solid fa-file-lines"></i>
                        {{ $materia->recursos()->where('visible', true)->count() }}
                        {{ $materia->recursos()->where('visible', true)->count() === 1 ? 'recurso' : 'recursos' }}
                    </p>

                    {{-- Acción ── --}}
                    <div class="lectura-card-acciones">
                        <a href="{{ route('biblioteca.materias.recursos.index', $materia->id_materia) }}"
                           class="btn btn-primario">
                            <i class="fa-solid fa-folder-open"></i>
                            Ver recursos
                        </a>
                    </div>

                </article>

            @endforeach

        </div>

    @endif

</div>

@endsection

{{-- ✅ Sin JS propio — el layout maneja APP_ALERTS globalmente --}}
