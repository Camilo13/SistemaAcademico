{{-- resources/views/modulos/ia/riesgo/index.blade.php --}}
@extends('layouts.menuadmin')

@section('title', 'Análisis Predictivo de Riesgo Académico')

@push('styles')
<style>
/* ── Contenedor principal ───────────────────────────────────────── */
.contenedor-ia {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1rem 3rem;
}

/* ── Cabecera del módulo ────────────────────────────────────────── */
.ia-header {
    background: linear-gradient(135deg, #065f46 0%, #166534 60%, #15803d 100%);
    border-radius: 16px;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    box-shadow: 0 4px 24px rgba(6,95,70,.25);
}
.ia-header-icon {
    width: 56px; height: 56px;
    background: rgba(255,255,255,.15);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem; color: #fff; flex-shrink: 0;
}
.ia-header-texto h1 {
    color: #fff; font-size: 1.4rem; font-weight: 700; margin: 0 0 .25rem;
}
.ia-header-texto p {
    color: rgba(255,255,255,.8); font-size: .85rem; margin: 0;
}

/* ── Tarjeta formulario ─────────────────────────────────────────── */
.ia-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
}
.ia-card-titulo {
    font-size: 1rem; font-weight: 700; color: #065f46;
    margin: 0 0 1.5rem; display: flex; align-items: center; gap: .5rem;
}
.ia-card-titulo i { color: #22c55e; }

/* ── Grid campos ────────────────────────────────────────────────── */
.grid-campos {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
}
.grid-campos-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

/* ── Zona de upload ─────────────────────────────────────────────── */
.upload-zona {
    border: 2px dashed #d1fae5;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    background: #f0fdf4;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    position: relative;
}
.upload-zona:hover, .upload-zona.dragover {
    border-color: #22c55e;
    background: #dcfce7;
}
.upload-zona input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%;
}
.upload-zona i  { font-size: 2rem; color: #16a34a; margin-bottom: .5rem; display: block; }
.upload-zona p  { margin: 0; color: #374151; font-size: .9rem; }
.upload-zona span { font-size: .78rem; color: #6b7280; }
#nombre-archivo { margin-top: .5rem; font-size: .82rem; color: #065f46; font-weight: 600; }

/* ── Ayuda porcentajes ──────────────────────────────────────────── */
.suma-indicador {
    font-size: .82rem; font-weight: 600; margin-top: .5rem;
    padding: .35rem .75rem; border-radius: 20px; display: inline-block;
}
.suma-ok    { background: #dcfce7; color: #166534; }
.suma-error { background: #fee2e2; color: #991b1b; }

/* ── Botón analizar ─────────────────────────────────────────────── */
.btn-analizar {
    background: linear-gradient(135deg, #16a34a, #065f46);
    color: #fff; border: none; border-radius: 10px;
    padding: .85rem 2.5rem; font-size: 1rem; font-weight: 700;
    cursor: pointer; display: inline-flex; align-items: center; gap: .6rem;
    transition: opacity .2s, transform .15s; margin-top: 1.5rem;
}
.btn-analizar:hover { opacity: .92; transform: translateY(-1px); }
.btn-analizar:disabled { opacity: .6; cursor: not-allowed; transform: none; }

/* ── Métricas cards ─────────────────────────────────────────────── */
.metricas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.metrica-card {
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 12px; padding: 1.25rem 1rem; text-align: center;
    box-shadow: 0 1px 6px rgba(0,0,0,.05);
}
.metrica-card .valor { font-size: 1.9rem; font-weight: 800; color: #065f46; }
.metrica-card .label { font-size: .75rem; color: #6b7280; margin-top: .2rem; }
.metrica-card.rojo   .valor { color: #dc2626; }
.metrica-card.naranja .valor { color: #d97706; }
.metrica-card.azul   .valor { color: #2563eb; }

/* ── Tabla resultados ───────────────────────────────────────────── */
.tabla-ia-wrap { overflow-x: auto; }
.tabla-ia { width: 100%; border-collapse: collapse; font-size: .83rem; }
.tabla-ia th {
    background: #065f46; color: #fff; padding: .7rem .9rem;
    text-align: left; font-weight: 600; white-space: nowrap;
}
.tabla-ia td { padding: .65rem .9rem; border-bottom: 1px solid #f3f4f6; }
.tabla-ia tr:hover td { background: #f0fdf4; }

/* ── Badge riesgo ───────────────────────────────────────────────── */
.badge-riesgo {
    display: inline-block; padding: .2rem .7rem;
    border-radius: 20px; font-size: .73rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .04em;
}
.badge-ALTO   { background: #fee2e2; color: #991b1b; }
.badge-MEDIO  { background: #fef9c3; color: #854d0e; }
.badge-BAJO   { background: #dcfce7; color: #166534; }

/* ── Barra probabilidad ─────────────────────────────────────────── */
.prob-bar { width: 80px; height: 8px; background: #e5e7eb; border-radius: 4px; display: inline-block; vertical-align: middle; margin-right: 5px; }
.prob-bar-fill { height: 100%; border-radius: 4px; background: #22c55e; }

/* ── Filtro materias ────────────────────────────────────────────── */
.filtro-wrap { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1rem; }
.btn-filtro {
    padding: .3rem .9rem; border-radius: 20px; border: 1.5px solid #d1fae5;
    background: #fff; color: #374151; font-size: .78rem; cursor: pointer;
    transition: all .15s;
}
.btn-filtro:hover, .btn-filtro.activo {
    background: #065f46; color: #fff; border-color: #065f46;
}

/* ── Botón PDF ──────────────────────────────────────────────────── */
.btn-pdf {
    background: #dc2626; color: #fff; border: none; border-radius: 8px;
    padding: .65rem 1.5rem; font-size: .88rem; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: .5rem;
    text-decoration: none; transition: opacity .2s;
}
.btn-pdf:hover { opacity: .88; }
</style>
@endpush

@section('content')
<div class="contenedor-ia">

    {{-- ── Cabecera ──────────────────────────────────────────────── --}}
    <div class="ia-header">
        <div class="ia-header-icon"><i class="fas fa-brain"></i></div>
        <div class="ia-header-texto">
            <h1>Análisis Predictivo de Riesgo Académico</h1>
            <p>Pipeline de IA · Random Forest · Detección temprana de estudiantes en riesgo</p>
        </div>
    </div>

    {{-- ── Errores ────────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="alerta alerta-error" style="margin-bottom:1.5rem">
            <i class="fas fa-exclamation-triangle"></i>
            @foreach($errors->all() as $error)
                <span>{{ $error }}</span>
            @endforeach
        </div>
    @endif

    {{-- ── Formulario ─────────────────────────────────────────────── --}}
    <div class="ia-card">
        <p class="ia-card-titulo"><i class="fas fa-upload"></i> Cargar datos y configurar análisis</p>

        <form method="POST" action="{{ route('ia.riesgo.analizar') }}"
              enctype="multipart/form-data" id="form-ia">
            @csrf

            {{-- Zona upload --}}
            <div class="upload-zona" id="upload-zona">
                <input type="file" name="excel" accept=".xlsx,.xls"
                       id="input-excel" required>
                <i class="fas fa-file-excel"></i>
                <p>Arrastra el archivo Excel aquí o haz clic para seleccionar</p>
                <span>Columnas requeridas: Codigo · Estudiante · Materia · Nota_Corte1 · Nota_Corte2 · Nota_Corte3</span>
                <div id="nombre-archivo"></div>
            </div>

            {{-- Corte + porcentajes --}}
            <div class="grid-campos" style="margin-top:1.5rem">

                <div>
                    <label class="form-label">Corte a analizar</label>
                    <select name="corte" class="form-control" required>
                        <option value="">Seleccionar...</option>
                        <option value="1" {{ old('corte') == 1 ? 'selected' : '' }}>Corte 1</option>
                        <option value="2" {{ old('corte') == 2 ? 'selected' : '' }}>Corte 2</option>
                        <option value="3" {{ old('corte') == 3 ? 'selected' : '' }}>Corte 3</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">% Actividad 1</label>
                    <input type="number" name="p1" class="form-control porcentaje"
                           min="1" max="98" step="0.1" placeholder="ej: 33"
                           value="{{ old('p1') }}" required>
                </div>

                <div>
                    <label class="form-label">% Actividad 2</label>
                    <input type="number" name="p2" class="form-control porcentaje"
                           min="1" max="98" step="0.1" placeholder="ej: 33"
                           value="{{ old('p2') }}" required>
                </div>

                <div>
                    <label class="form-label">% Actividad 3</label>
                    <input type="number" name="p3" class="form-control porcentaje"
                           min="1" max="98" step="0.1" placeholder="ej: 34"
                           value="{{ old('p3') }}" required>
                </div>

            </div>

            <div id="suma-display"></div>

            <button type="submit" class="btn-analizar" id="btn-analizar">
                <i class="fas fa-flask"></i> Ejecutar análisis
            </button>
        </form>
    </div>

    {{-- ── Resultados ──────────────────────────────────────────────── --}}
    @isset($metricas)
    <div id="resultados">

        {{-- Métricas resumen --}}
        <div class="metricas-grid">
            <div class="metrica-card">
                <div class="valor">{{ $metricas['total_estudiantes'] }}</div>
                <div class="label">Total estudiantes</div>
            </div>
            <div class="metrica-card">
                <div class="valor">{{ $metricas['promedio'] }}</div>
                <div class="label">Promedio general</div>
            </div>
            <div class="metrica-card">
                <div class="valor">{{ number_format($metricas['aprobacion'] * 100, 1) }}%</div>
                <div class="label">Tasa aprobación</div>
            </div>
            <div class="metrica-card rojo">
                <div class="valor">{{ $metricas['riesgo_alto'] }}</div>
                <div class="label">Riesgo alto</div>
            </div>
            <div class="metrica-card naranja">
                <div class="valor">{{ $metricas['riesgo_medio'] }}</div>
                <div class="label">Riesgo medio</div>
            </div>
            <div class="metrica-card azul">
                <div class="valor">{{ $metricas['mae'] }}</div>
                <div class="label">MAE modelo RF</div>
            </div>
            <div class="metrica-card azul">
                <div class="valor">{{ $metricas['r2'] }}</div>
                <div class="label">R² modelo RF</div>
            </div>
        </div>

        {{-- Tabla detallada --}}
        <div class="ia-card">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:1rem">
                <p class="ia-card-titulo" style="margin:0">
                    <i class="fas fa-table"></i> Resultados por estudiante — Corte {{ $corte }}
                </p>
                <a href="{{ route('ia.riesgo.pdf') }}" class="btn-pdf">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>
            </div>

            {{-- Filtro por materia --}}
            <div class="filtro-wrap">
                <button class="btn-filtro activo" data-materia="todos">Todos</button>
                @foreach($metricas['materias'] as $materia)
                    <button class="btn-filtro" data-materia="{{ $materia }}">{{ $materia }}</button>
                @endforeach
            </div>

            {{-- Filtro por riesgo --}}
            <div class="filtro-wrap" style="margin-bottom:1.25rem">
                <button class="btn-filtro activo" data-riesgo="todos">Todos los riesgos</button>
                <button class="btn-filtro" data-riesgo="ALTO">🔴 Alto</button>
                <button class="btn-filtro" data-riesgo="MEDIO">🟡 Medio</button>
                <button class="btn-filtro" data-riesgo="BAJO">🟢 Bajo</button>
            </div>

            <div class="tabla-ia-wrap">
                <table class="tabla-ia" id="tabla-estudiantes">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Estudiante</th>
                            <th>Materia</th>
                            <th>Corte 1</th>
                            <th>Corte 2</th>
                            <th>Corte 3</th>
                            <th>Nota Final</th>
                            <th>Predicción RF</th>
                            <th>Prob. Aprobación</th>
                            <th>Riesgo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estudiantes as $est)
                        <tr data-materia="{{ $est['materia'] }}" data-riesgo="{{ $est['riesgo'] }}">
                            <td>{{ $est['codigo'] }}</td>
                            <td>{{ $est['estudiante'] }}</td>
                            <td>{{ $est['materia'] }}</td>
                            <td>{{ $est['corte1'] }}</td>
                            <td>{{ $est['corte2'] }}</td>
                            <td>{{ $est['corte3'] }}</td>
                            <td><strong>{{ $est['nota_final'] }}</strong></td>
                            <td>{{ $est['prediccion'] }}</td>
                            <td>
                                <div class="prob-bar">
                                    <div class="prob-bar-fill"
                                         style="width:{{ min(100, round($est['probabilidad']*100)) }}%;
                                                background: {{ $est['probabilidad'] >= 0.7 ? '#22c55e' : ($est['probabilidad'] >= 0.4 ? '#f59e0b' : '#ef4444') }}">
                                    </div>
                                </div>
                                {{ number_format($est['probabilidad']*100, 1) }}%
                            </td>
                            <td>
                                <span class="badge-riesgo badge-{{ $est['riesgo'] }}">
                                    {{ $est['riesgo'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @endisset

</div>
@endsection

@push('scripts')
<script>
// -- Mostrar nombre del archivo seleccionado
document.getElementById('input-excel').addEventListener('change', function() {
    const nombre = this.files[0] ? this.files[0].name : '';
    document.getElementById('nombre-archivo').textContent = nombre ? '📎 ' + nombre : '';
});

// -- Drag & drop visual
const zona = document.getElementById('upload-zona');
zona.addEventListener('dragover', e => { e.preventDefault(); zona.classList.add('dragover'); });
zona.addEventListener('dragleave', () => zona.classList.remove('dragover'));
zona.addEventListener('drop', () => zona.classList.remove('dragover'));

// -- Validar suma de porcentajes en tiempo real
const inputs = document.querySelectorAll('.porcentaje');
const display = document.getElementById('suma-display');

function actualizarSuma() {
    let suma = 0;
    inputs.forEach(i => suma += parseFloat(i.value || 0));
    suma = Math.round(suma * 10) / 10;
    const ok = suma === 100;
    display.innerHTML = `<span class="suma-indicador ${ok ? 'suma-ok' : 'suma-error'}">
        Suma actual: ${suma}% ${ok ? '✔ Correcto' : '← Debe ser 100%'}
    </span>`;
}
inputs.forEach(i => i.addEventListener('input', actualizarSuma));

// -- Deshabilitar botón mientras procesa
document.getElementById('form-ia').addEventListener('submit', function() {
    const btn = document.getElementById('btn-analizar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ejecutando modelo...';
});

// -- Filtros de tabla
function aplicarFiltros() {
    const materia = document.querySelector('.btn-filtro[data-materia].activo')?.dataset.materia || 'todos';
    const riesgo  = document.querySelector('.btn-filtro[data-riesgo].activo')?.dataset.riesgo   || 'todos';

    document.querySelectorAll('#tabla-estudiantes tbody tr').forEach(tr => {
        const mOk = materia === 'todos' || tr.dataset.materia === materia;
        const rOk = riesgo  === 'todos' || tr.dataset.riesgo  === riesgo;
        tr.style.display = (mOk && rOk) ? '' : 'none';
    });
}

document.querySelectorAll('.btn-filtro[data-materia]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-filtro[data-materia]').forEach(b => b.classList.remove('activo'));
        this.classList.add('activo');
        aplicarFiltros();
    });
});

document.querySelectorAll('.btn-filtro[data-riesgo]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-filtro[data-riesgo]').forEach(b => b.classList.remove('activo'));
        this.classList.add('activo');
        aplicarFiltros();
    });
});
</script>
@endpush