@extends('layouts.menuestudiante')

@section('title', 'Inicio Estudiante')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard/estudiante.css') }}">
@endpush

@section('content')

<div class="inicio-estudiante">

    <div class="bienvenida">
        <h1 class="bienvenida-titulo">
            Bienvenido, <span class="rol">Estudiante</span>
        </h1>
        <p class="bienvenida-texto">
            Desde este panel puedes consultar tus inscripciones, revisar tus notas,
            ver tu asistencia, descargar tus boletines y consultar tu horario semanal.
        </p>
    </div>

    <div class="tarjetas-resumen">

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-id-card-clip"></i></div>
            <h3>Mi Académico</h3>
            <p>Consulta tus inscripciones activas y las materias del año en curso.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-star-half-stroke"></i></div>
            <h3>Mis Notas</h3>
            <p>Revisa tus calificaciones por período y materia.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-calendar-check"></i></div>
            <h3>Mi Asistencia</h3>
            <p>Consulta tu registro de faltas por período académico.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-file-lines"></i></div>
            <h3>Mis Boletines</h3>
            <p>Descarga tu boletín académico de cada año lectivo cursado.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-calendar-week"></i></div>
            <h3>Mi Horario</h3>
            <p>Consulta el horario semanal de tu grupo con materias y docentes.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-book"></i></div>
            <h3>Biblioteca Digital</h3>
            <p>Accede a libros, guías y material educativo disponible.</p>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('js/modulos/dashboard/estudiante.js') }}"></script>
@endpush
