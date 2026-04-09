@extends('layouts.menuadmin')

@section('title', 'Gestión de Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modulos/admin/usuarios/index.css') }}">
@endpush

@section('content')
<div class="contenedor-modulo">

    {{-- ── Cabecera ── --}}
    <div class="cabecera">
        <div class="cabecera-info">
            <h2><i class="fa-solid fa-users-cog"></i> Gestión de Usuarios</h2>
            <p class="cabecera-subtitulo">Administra todos los usuarios del sistema</p>
        </div>
        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primario">
            <i class="fa-solid fa-plus"></i> Nuevo Usuario
        </a>
    </div>

    {{-- ── Chips de resumen ── --}}
    <div class="resumen-usuarios">
        <div class="resumen-chip">
            <i class="fa-solid fa-users"></i>
            <span>{{ $totales['total'] }}</span>
            <label>Total</label>
        </div>
        <div class="resumen-chip chip-admin">
            <i class="fa-solid fa-shield-halved"></i>
            <span>{{ $totales['administradores'] }}</span>
            <label>Admins</label>
        </div>
        <div class="resumen-chip chip-docente">
            <i class="fa-solid fa-chalkboard-user"></i>
            <span>{{ $totales['docentes'] }}</span>
            <label>Docentes</label>
        </div>
        <div class="resumen-chip chip-estudiante">
            <i class="fa-solid fa-user-graduate"></i>
            <span>{{ $totales['estudiantes'] }}</span>
            <label>Estudiantes</label>
        </div>
        <div class="resumen-chip chip-inactivo">
            <i class="fa-solid fa-user-slash"></i>
            <span>{{ $totales['inactivos'] }}</span>
            <label>Inactivos</label>
        </div>
    </div>

    {{-- ── Filtros ── --}}
    <form method="GET" action="{{ route('admin.usuarios.index') }}" class="panel-filtros">

        <div class="filtro filtro-buscar">
            <label><i class="fa-solid fa-magnifying-glass"></i> Buscar</label>
            <input type="text" name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Nombre, apellido, identificación o correo…">
        </div>

        <div class="filtro">
            <label><i class="fa-solid fa-tag"></i> Rol</label>
            <select name="rol">
                <option value="">Todos</option>
                <option value="administrador" {{ request('rol') === 'administrador' ? 'selected' : '' }}>Administrador</option>
                <option value="docente"       {{ request('rol') === 'docente'       ? 'selected' : '' }}>Docente</option>
                <option value="estudiante"    {{ request('rol') === 'estudiante'    ? 'selected' : '' }}>Estudiante</option>
            </select>
        </div>

        <div class="filtro">
            <label><i class="fa-solid fa-circle-dot"></i> Estado</label>
            <select name="activo">
                <option value="">Todos</option>
                <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="filtro-acciones">
            <button type="submit" class="btn btn-secundario btn-sm">
                <i class="fa-solid fa-magnifying-glass"></i> Filtrar
            </button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-neutro btn-sm">
                <i class="fa-solid fa-rotate-left"></i> Limpiar
            </a>
        </div>

    </form>

    {{-- ── Error general ── --}}
    @error('error_usuario')
        <div class="alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </div>
    @enderror

    {{-- ══════════════════════════════════════════
         BARRA BULK — modo usuarios
         1 seleccionado  → Editar + Eliminar
         2+ seleccionados → solo Eliminar
    ══════════════════════════════════════════ --}}
    <div class="barra-bulk"
         data-bulk-modo="usuarios"
         data-entidad="usuario(s)"
         data-url-editar="{{ route('admin.usuarios.edit', ':id') }}"
         data-url-destroy="{{ route('admin.usuarios.destroy', ':id') }}"
         data-url-bulk-destroy="{{ route('admin.usuarios.destroyBulk') }}">

        <div class="bulk-info">
            <i class="fa-solid fa-check-square"></i>
            <span class="bulk-contador">0</span>
            seleccionado(s)
        </div>

        <div class="bulk-acciones">
            {{-- Editar: solo visible si hay exactamente 1 seleccionado --}}
            <a class="btn-bulk btn-bulk-editar"
               href="#">
                <i class="fa-solid fa-pen"></i> Editar
            </a>

            <div class="bulk-separador"></div>

            <button type="button" class="btn-bulk btn-bulk-eliminar">
                <i class="fa-solid fa-trash"></i> Eliminar seleccionados
            </button>

            <div class="bulk-separador"></div>

            <button type="button" class="btn-bulk btn-bulk-limpiar">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </button>
        </div>

    </div>

    {{-- ── Tabla ── --}}
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th class="col-check">
                        <input type="checkbox" class="checkbox-todos" title="Seleccionar todos">
                    </th>
                    <th>Nombre</th>
                    <th>Identificación</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr data-id="{{ $usuario->id }}" class="fila-seleccionable">
                        <td class="col-check">
                            <input type="checkbox"
                                   class="checkbox-tabla"
                                   data-id="{{ $usuario->id }}"
                                   title="Seleccionar {{ $usuario->nombre_completo }}">
                        </td>

                        <td data-label="Nombre">
                            <strong>{{ $usuario->apellidos }}</strong>, {{ $usuario->nombre }}
                        </td>

                        <td data-label="Identificación">{{ $usuario->identificacion }}</td>

                        <td data-label="Correo" class="celda-correo">{{ $usuario->correo }}</td>

                        <td data-label="Rol">
                            @php
                                $rolClase = match($usuario->rol) {
                                    'administrador' => 'badge-admin',
                                    'docente'       => 'badge-docente',
                                    'estudiante'    => 'badge-estudiante',
                                    default         => '',
                                };
                                $rolIcono = match($usuario->rol) {
                                    'administrador' => 'fa-shield-halved',
                                    'docente'       => 'fa-chalkboard-user',
                                    'estudiante'    => 'fa-user-graduate',
                                    default         => 'fa-user',
                                };
                            @endphp
                            <span class="badge {{ $rolClase }}">
                                <i class="fa-solid {{ $rolIcono }}"></i>
                                {{ ucfirst($usuario->rol) }}
                            </span>
                        </td>

                        <td data-label="Estado">
                            @if($usuario->activo)
                                <span class="estado estado-activo">
                                    <i class="fa-solid fa-circle-check"></i> Activo
                                </span>
                            @else
                                <span class="estado estado-inactivo">
                                    <i class="fa-solid fa-circle-xmark"></i> Inactivo
                                </span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr class="fila-vacia">
                        <td colspan="6">
                            <i class="fa-solid fa-circle-info"></i>
                            No se encontraron usuarios con los filtros aplicados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($usuarios->hasPages())
        <div class="paginacion">{{ $usuarios->links() }}</div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/componentes/academico.js') }}"></script>
    <script src="{{ asset('js/componentes/acciones-tabla.js') }}"></script>
    <script src="{{ asset('js/modulos/admin/usuarios.js') }}"></script>
@endpush
