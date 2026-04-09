@extends($layout)

@section('title', 'Recursos — ' . $materia->nombre)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/biblioteca/lectura/lectura.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Botón volver — .btn.btn-neutro.btn-sm reutilizable ── --}}
    <div>
        <a href="{{ route('biblioteca.materias.index') }}"
           class="btn btn-neutro btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver a materias
        </a>
    </div>

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-folder-open"></i>
                {{ $materia->nombre }}
            </h2>
            <p class="cabecera-subtitulo">
                Recursos disponibles de esta materia
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         GRID DE RECURSOS — solo lectura
    ══════════════════════════════════════════ --}}
    @if($recursos->isEmpty())

        <div class="lectura-vacio">
            <i class="fa-solid fa-folder-open fa-2x"></i>
            <p>No hay recursos disponibles para esta materia.</p>
        </div>

    @else

        <div class="lectura-grid">

            @foreach($recursos as $recurso)

                <article class="lectura-card">

                    {{-- Tipo ── --}}
                    <div class="lectura-recurso-tipo">
                        <i class="fa-solid {{ $recurso->icono() }}"></i>
                        {{ strtoupper($recurso->tipo) }}
                    </div>

                    {{-- Título ── --}}
                    <h3 class="lectura-card-titulo">
                        {{ $recurso->titulo }}
                    </h3>

                    {{-- Descripción ── --}}
                    <p class="lectura-card-desc">
                        {{ $recurso->descripcion ?: 'Sin descripción.' }}
                    </p>

                    {{-- Autor ── --}}
                    @if($recurso->autor)
                        <p class="lectura-card-meta">
                            <i class="fa-solid fa-user-pen"></i>
                            {{ $recurso->autor }}
                        </p>
                    @endif

                    {{-- Acción — .btn-primario reutilizable ── --}}
                    @php([$label, $icon] = $recurso->accionLectura())
                    <div class="lectura-card-acciones">
                        <a href="{{ $recurso->url_final }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           @if($recurso->origen === 'archivo' && !$recurso->esVisualizable()) download @endif
                           class="btn btn-primario">
                            <i class="fa-solid {{ $icon }}"></i>
                            {{ $label }}
                        </a>
                    </div>

                </article>

            @endforeach

        </div>

    @endif

</div>

@endsection

{{-- ✅ Sin JS propio — el layout maneja APP_ALERTS globalmente --}}
