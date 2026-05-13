@extends('layouts.menuadmin')

@section('title', 'Análisis IA')

@section('content')

<div class="contenedor">

    <div class="tarjeta">

        <div class="tarjeta-header">

            <h1>
                <i class="fas fa-brain"></i>
                Análisis Predictivo IA
            </h1>

            <p>
                Generación de reportes analíticos académicos mediante
                inteligencia artificial y aprendizaje automático.
            </p>

        </div>

        {{-- ALERTAS --}}
        @if(session('error'))

            <div class="alerta alerta-error">
                {{ session('error') }}
            </div>

        @endif

        @if(session('exito'))

            <div class="alerta alerta-exito">
                {{ session('exito') }}
            </div>

        @endif

        {{-- FORMULARIO --}}
        <form
            action="{{ route('admin.ia.riesgo.procesar') }}"
            method="POST"
            enctype="multipart/form-data"
            class="formulario-ia"
        >

            @csrf

            <div class="grid-formulario">

                {{-- MATERIA --}}
                <div class="grupo-formulario">

                    <label>
                        Materia
                    </label>

                    <input
                        type="text"
                        name="materia"
                        class="input-formulario"
                        placeholder="Ingrese nombre de la materia"
                        required
                    >

                </div>

                {{-- CORTE --}}
                <div class="grupo-formulario">

                    <label>
                        Corte Académico
                    </label>

                    <select
                        name="corte"
                        class="input-formulario"
                        required
                    >

                        <option value="">
                            Seleccione
                        </option>

                        <option value="1">
                            Corte 1
                        </option>

                        <option value="2">
                            Corte 2
                        </option>

                        <option value="3">
                            Corte 3
                        </option>

                    </select>

                </div>

            </div>

            {{-- PORCENTAJES --}}
            <div class="grid-formulario">

                <div class="grupo-formulario">

                    <label>
                        Porcentaje Nota 1
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        name="p1"
                        class="input-formulario"
                        placeholder="Ej: 30"
                        required
                    >

                </div>

                <div class="grupo-formulario">

                    <label>
                        Porcentaje Nota 2
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        name="p2"
                        class="input-formulario"
                        placeholder="Ej: 30"
                        required
                    >

                </div>

                <div class="grupo-formulario">

                    <label>
                        Porcentaje Nota 3
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        name="p3"
                        class="input-formulario"
                        placeholder="Ej: 40"
                        required
                    >

                </div>

            </div>

            {{-- ARCHIVO --}}
            <div class="grupo-formulario">

                <label>
                    Archivo Excel
                </label>

                <input
                    type="file"
                    name="excel"
                    class="input-formulario"
                    accept=".xlsx,.xls"
                    required
                >

                <small>
                    Formatos permitidos:
                    .xlsx y .xls
                </small>

            </div>

            {{-- BOTÓN --}}
            <div class="acciones-formulario">

                <button
                    type="submit"
                    class="boton boton-primario"
                >

                    <i class="fas fa-file-pdf"></i>

                    Generar Reporte IA

                </button>

            </div>

        </form>

    </div>

</div>

@endsection