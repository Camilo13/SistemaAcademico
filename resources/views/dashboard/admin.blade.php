@extends('layouts.menuadmin')

@section('title', 'Panel Administrador')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/dashboard/admin.css') }}">
@endpush

@section('content')

<div class="inicio-admin">

    {{-- ── Banner de bienvenida ── --}}
    <div class="bienvenida">
        <h1 class="bienvenida-titulo">
            Bienvenido, <span class="rol">{{ $usuario->nombre ?? 'Administrador' }}</span>
        </h1>
        <p class="bienvenida-texto">
            Desde este panel puedes gestionar toda la estructura académica,
            los usuarios del sistema, los horarios y el portal institucional.
        </p>
    </div>

    {{-- ── Tarjetas de módulos (contenido estático) ── --}}
    <div class="tarjetas-resumen">

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-sitemap"></i></div>
            <h3>Gestión Académica</h3>
            <p>Administra sedes, grados, grupos, materias, períodos y años lectivos.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-users-cog"></i></div>
            <h3>Usuarios</h3>
            <p>Crea, edita y controla administradores, docentes y estudiantes.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-user-graduate"></i></div>
            <h3>Inscripciones</h3>
            <p>Gestiona las inscripciones de estudiantes a grupos y materias.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-chalkboard-user"></i></div>
            <h3>Asignaciones</h3>
            <p>Asigna docentes a materias y grupos del año lectivo activo.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-clock"></i></div>
            <h3>Horarios</h3>
            <p>Carga el horario semanal de cada grupo por bloques institucionales.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-star-half-stroke"></i></div>
            <h3>Notas</h3>
            <p>Consulta y administra el registro de calificaciones del sistema.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-file-lines"></i></div>
            <h3>Boletines</h3>
            <p>Accede a los boletines académicos de cualquier estudiante.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-book"></i></div>
            <h3>Biblioteca</h3>
            <p>Gestiona las materias y recursos de la biblioteca digital.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-envelope-open-text"></i></div>
            <h3>Solicitudes</h3>
            <p>Revisa y aprueba las solicitudes de ingreso al sistema.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-globe"></i></div>
            <h3>Portal Público</h3>
            <p>Administra el carrusel de imágenes y los eventos institucionales.</p>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/modulos/dashboard/admin.js') }}"></script>
@endpush
