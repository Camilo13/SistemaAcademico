/**
 * modulos/docente/notas.js
 * ─────────────────────────────────────────────────────────────
 * Depende de: js/componentes/academico.js
 * Usado en  : index, estudiantes, create, edit
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════════════════════
       1. BUSCADOR RÁPIDO — index (asignaciones)
    ══════════════════════════════════════════════ */
    const filtro = document.getElementById('filtro-asignaciones');
    const lista  = document.getElementById('lista-asignaciones');
    const sinRes = document.getElementById('sin-resultados');

    if (filtro && lista) {
        filtro.addEventListener('input', () => {
            const q       = filtro.value.toLowerCase().trim();
            const items   = lista.querySelectorAll('.asignacion-item');
            let  visibles = 0;

            items.forEach(item => {
                const texto  = item.dataset.buscar ?? '';
                const mostrar = q === '' || texto.includes(q);
                item.style.display = mostrar ? '' : 'none';
                if (mostrar) visibles++;
            });

            if (sinRes) sinRes.style.display = visibles === 0 ? '' : 'none';
        });
    }

    /* ══════════════════════════════════════════════
       2. PREVIEW DESEMPEÑO — create y edit
       Actualiza en tiempo real el bloque #preview-nota
    ══════════════════════════════════════════════ */
    const inputNota  = document.getElementById('nota');
    const previewBox = document.getElementById('preview-nota');

    if (inputNota && previewBox) {

        const desempenos = [
            { min: 4.5, max: 5.0,  clase: 'superior', icono: 'fa-trophy',       label: 'Desempeño Superior (4.5 – 5.0)' },
            { min: 4.0, max: 4.49, clase: 'alto',     icono: 'fa-circle-check', label: 'Desempeño Alto (4.0 – 4.4)'     },
            { min: 3.0, max: 3.99, clase: 'basico',   icono: 'fa-circle-minus', label: 'Desempeño Básico (3.0 – 3.9)'   },
            { min: 0,   max: 2.99, clase: 'bajo',     icono: 'fa-circle-xmark', label: 'Desempeño Bajo (0.0 – 2.9)'     },
        ];

        function actualizarPreview() {
            const val = parseFloat(inputNota.value);

            if (isNaN(val) || inputNota.value === '') {
                previewBox.className = 'preview-nota';
                previewBox.innerHTML = '';
                inputNota.style.borderColor = '';
                return;
            }

            if (val < 0 || val > 5) {
                previewBox.className = 'preview-nota bajo';
                previewBox.innerHTML =
                    '<i class="fa-solid fa-triangle-exclamation"></i> Fuera del rango permitido (0.00 – 5.00)';
                inputNota.style.borderColor = '#dc2626';
                return;
            }

            const d = desempenos.find(d => val >= d.min && val <= d.max);
            if (d) {
                previewBox.className = `preview-nota ${d.clase}`;
                previewBox.innerHTML = `<i class="fa-solid ${d.icono}"></i> ${d.label}`;
                inputNota.style.borderColor = d.clase === 'bajo'   ? '#f59e0b'
                                            : d.clase === 'basico' ? '#d97706'
                                            : '#10b981';
            }
        }

        inputNota.addEventListener('input',  actualizarPreview);
        inputNota.addEventListener('change', actualizarPreview);

        // Ejecutar al cargar (si hay valor por old())
        actualizarPreview();
    }

    /* ══════════════════════════════════════════════
       3. GUARDAR CAMBIOS — create y edit
    ══════════════════════════════════════════════ */
    iniciarGuardaCambios('form[data-form="nota-docente"]', '.btn-neutro[href]');

    /* ══════════════════════════════════════════════
       4. CONFIRMAR bulk delete — estudiantes.blade
       (acciones-tabla.js maneja la barra bulk,
        pero el mensaje personalizado para notas
        se define aquí como override del entidad)
    ══════════════════════════════════════════════ */
    // acciones-tabla.js ya maneja el bulk automáticamente
    // gracias a los data-attributes en .barra-bulk
});
