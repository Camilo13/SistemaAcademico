@extends('layouts.menudocente')

@section('title', 'Inicio Docente')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard/docente.css') }}">
@endpush

@section('content')

<div class="inicio-docente">

    <div class="bienvenida">
        <h1 class="bienvenida-titulo">
            Bienvenido, <span class="rol">Docente</span>
        </h1>
        <p class="bienvenida-texto">
            Desde este panel puedes consultar tus grupos, registrar notas y asistencia,
            revisar el boletín de tus estudiantes y consultar tu horario semanal.
        </p>
    </div>

    <div class="tarjetas-resumen">

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-users"></i></div>
            <h3>Mis Grupos</h3>
            <p>Consulta los grupos y materias a tu cargo en el año lectivo activo.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-clipboard-list"></i></div>
            <h3>Notas</h3>
            <p>Registra y consulta las calificaciones de tus estudiantes por período.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-user-check"></i></div>
            <h3>Asistencia</h3>
            <p>Registra y revisa las faltas de asistencia de cada grupo.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-file-lines"></i></div>
            <h3>Boletines</h3>
            <p>Consulta el boletín académico de los estudiantes de tus grupos.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-calendar-week"></i></div>
            <h3>Mi Horario</h3>
            <p>Revisa tu horario semanal con los bloques y grupos asignados.</p>
        </div>

        <div class="tarjeta">
            <div class="tarjeta-icono"><i class="fa-solid fa-book"></i></div>
            <h3>Biblioteca Digital</h3>
            <p>Accede a los recursos y materiales educativos disponibles.</p>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('js/modulos/dashboard/docente.js') }}"></script>
@endpush
