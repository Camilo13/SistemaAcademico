@extends('layouts.menuadmin')

@section('title', 'Solicitudes de Registro')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/solicitudes/gestion/index.css') }}">
@endpush

@section('content')

<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2>
                <i class="fa-solid fa-envelope-open-text"></i>
                Solicitudes de Registro
            </h2>
            <p class="cabecera-subtitulo">
                Solicitudes pendientes de aprobación
                <strong>({{ $solicitudes->count() }})</strong>
            </p>
        </div>
    </div>

    {{-- ── Error general ── --}}
    @error('error_solicitud')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ══════════════════════════════════════════
         LISTADO
    ══════════════════════════════════════════ --}}
    @if($solicitudes->isEmpty())

        <div class="tarjeta-form solicitudes-vacio">
            <i class="fa-solid fa-circle-check fa-2x"></i>
            <p>No hay solicitudes pendientes en este momento.</p>
        </div>

    @else

        <div class="solicitudes-grid">

            @foreach($solicitudes as $solicitud)

                <article class="solicitud-card">

                    {{-- ── Cabecera de la tarjeta ── --}}
                    <div class="solicitud-card-header">

                        <div class="solicitud-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div>
                            <h3 class="solicitud-nombre">
                                {{ $solicitud->nombre }} {{ $solicitud->apellidos }}
                            </h3>
                            <span class="badge badge-{{ $solicitud->rol === 'docente' ? 'docente' : 'estudiante' }}">
                                <i class="fa-solid {{ $solicitud->rol === 'docente' ? 'fa-chalkboard-user' : 'fa-user-graduate' }}"></i>
                                {{ ucfirst($solicitud->rol) }}
                            </span>
                        </div>

                    </div>

                    {{-- ── Datos ── --}}
                    <ul class="solicitud-datos">
                        <li>
                            <i class="fa-solid fa-id-card"></i>
                            {{ $solicitud->identificacion }}
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope"></i>
                            {{ $solicitud->correo }}
                        </li>
                        <li>
                            <i class="fa-solid fa-phone"></i>
                            {{ $solicitud->contacto }}
                        </li>
                        <li>
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $solicitud->ubicacion }}
                        </li>
                        <li>
                            <i class="fa-regular fa-clock"></i>
                            {{ $solicitud->created_at->format('d/m/Y H:i') }}
                        </li>
                    </ul>

                    {{-- ── Acciones — directas desde la tarjeta ── --}}
                    <div class="solicitud-acciones">

                        {{-- Editar datos antes de aprobar --}}
                        <a href="{{ route('admin.solicitudes.edit', $solicitud) }}"
                           class="btn btn-neutro btn-sm">
                            <i class="fa-solid fa-pen"></i> Editar
                        </a>

                        {{-- Aprobar --}}
                        <form method="POST"
                              action="{{ route('admin.solicitudes.aprobar', $solicitud) }}"
                              class="form-aprobar"
                              data-nombre="{{ $solicitud->nombre }} {{ $solicitud->apellidos }}">
                            @csrf
                            <button type="submit" class="btn btn-primario btn-sm">
                                <i class="fa-solid fa-check"></i> Aprobar
                            </button>
                        </form>

                        {{-- Rechazar --}}
                        <form method="POST"
                              action="{{ route('admin.solicitudes.rechazar', $solicitud) }}"
                              class="form-rechazar"
                              data-nombre="{{ $solicitud->nombre }} {{ $solicitud->apellidos }}">
                            @csrf
                            <button type="submit" class="btn btn-peligro btn-sm">
                                <i class="fa-solid fa-xmark"></i> Rechazar
                            </button>
                        </form>

                    </div>

                </article>

            @endforeach

        </div>

    @endif

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/solicitudes/gestion.js') }}"></script>
@endpush
