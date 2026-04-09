@extends('layouts.menuadmin')
@section('title', 'Agregar franja horaria')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/componentes/academico-form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modulos/academico/horario/create.css') }}">
@endpush

@section('content')
<div class="contenedor-horario-form">

    <div class="cabecera">
        <div class="cabecera-info">
            <h2><i class="fa-solid fa-calendar-plus"></i> Agregar franja horaria</h2>
            <p class="cabecera-subtitulo">
                {{ optional($grupo->grado)->nombre }} — Grupo {{ $grupo->nombre }}
                &nbsp;·&nbsp; {{ optional($grupo->anioLectivo)->nombre }}
            </p>
        </div>
        <a href="{{ route('admin.horarios.grupo', $grupo->id) }}" class="btn btn-neutro">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    @if($errors->has('choque'))
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $errors->first('choque') }}
        </div>
    @endif

    <div class="tarjeta-form">
        <form method="POST" action="{{ route('admin.horarios.store') }}"
              data-form="horario">
            @csrf
            <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">

            <div class="grid-campos">

                {{-- Asignación (materia + docente) --}}
                <div class="campo campo-ancho">
                    <label for="asignacion_id">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        Materia / Docente <span>*</span>
                    </label>
                    <select id="asignacion_id" name="asignacion_id" required>
                        <option value="">Seleccione una asignación</option>
                        @foreach($asignaciones as $asig)
                            <option value="{{ $asig->id }}"
                                {{ old('asignacion_id') == $asig->id ? 'selected' : '' }}>
                                {{ optional($asig->materia)->nombre ?? '—' }}
                                — {{ optional($asig->docente)->nombre_completo ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                    @error('asignacion_id')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Día de la semana --}}
                <div class="campo">
                    <label for="dia_semana">
                        <i class="fa-solid fa-calendar-days"></i>
                        Día de la semana <span>*</span>
                    </label>
                    <select id="dia_semana" name="dia_semana" required>
                        <option value="">Seleccione un día</option>
                        @foreach(\App\Models\Horario::DIAS_LABEL as $valor => $etiqueta)
                            <option value="{{ $valor }}"
                                {{ old('dia_semana') === $valor ? 'selected' : '' }}>
                                {{ $etiqueta }}
                            </option>
                        @endforeach
                    </select>
                    @error('dia_semana')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Bloque horario --}}
                <div class="campo">
                    <label for="bloque">
                        <i class="fa-solid fa-clock"></i>
                        Bloque horario <span>*</span>
                    </label>
                    <select id="bloque" name="bloque" required>
                        <option value="">Seleccione un bloque</option>
                        @foreach(\App\Models\Horario::BLOQUES as $num => $info)
                            @php
                                $key = old('dia_semana', '') . '_' . $num;
                                $ocupado = in_array(old('dia_semana','') . '_' . $num, $ocupadas);
                            @endphp
                            <option value="{{ $num }}"
                                {{ old('bloque') == $num ? 'selected' : '' }}>
                                Bloque {{ $num }} · {{ $info['inicio'] }} – {{ $info['fin'] }}
                                ({{ $info['sesion'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('bloque')
                        <span class="error-campo">{{ $message }}</span>
                    @enderror
                    <span class="nota-campo">
                        <i class="fa-solid fa-circle-info"></i>
                        Pausas: Refrigerio 9:00–9:30 · Almuerzo 11:30–13:00
                    </span>
                </div>

            </div>

            <div class="info-bloques">
                <table class="tabla-bloques">
                    <thead>
                        <tr>
                            <th>Bloque</th>
                            <th>Horario</th>
                            <th>Sesión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\Horario::BLOQUES as $num => $info)
                            @if($num === 3)
                                <tr class="pausa-row">
                                    <td colspan="3">
                                        <i class="fa-solid fa-mug-hot"></i>
                                        Refrigerio · 9:00 – 9:30
                                    </td>
                                </tr>
                            @elseif($num === 5)
                                <tr class="pausa-row">
                                    <td colspan="3">
                                        <i class="fa-solid fa-utensils"></i>
                                        Almuerzo · 11:30 – 13:00
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td><strong>{{ $num }}</strong></td>
                                <td>{{ $info['inicio'] }} – {{ $info['fin'] }}</td>
                                <td>{{ $info['sesion'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="acciones-form">
                <a href="{{ route('admin.horarios.grupo', $grupo->id) }}" class="btn btn-neutro">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar franja
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/modulos/academico/horario.js') }}"></script>
@endpush
