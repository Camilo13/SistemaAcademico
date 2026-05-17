/**
 * modulos/academico/asignacion.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de Asignaciones docentes.
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar desactivar ── */
    confirmarAccion(
        '.form-desactivar',
        '¿Desactivar esta asignación?<br>' +
        '<small style="color:#92400e;">El docente dejará de tener ' +
        'acceso a registrar notas para este grupo y materia.</small>',
        'Sí, desactivar'
    );

    /* ── Confirmar activar ── */
    confirmarAccion(
        '.form-activar',
        '¿Activar esta asignación?<br>' +
        '<small style="color:#374151;">El docente podrá registrar ' +
        'notas para este grupo y materia.</small>',
        'Sí, activar'
    );

    /* ── Confirmar eliminar ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar esta asignación?<br>' +
        '<small style="color:#b91c1c;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es permanente. Solo es posible si no tiene ' +
        'notas registradas.</small>',
        'Sí, eliminar'
    );

    /* ── Advertir cambios sin guardar ── */
    iniciarGuardaCambios('form[data-form="asignacion"]', '.btn-neutro[href]');

    // ── Buscador de docentes ──
    const inputBuscarDoc     = document.getElementById('docente_buscar');
    const inputHiddenDoc     = document.getElementById('docente_id');
    const divResultadosDoc   = document.getElementById('docente_resultados');
    const divSeleccionadoDoc = document.getElementById('docente_seleccionado');

    if (inputBuscarDoc) {

        let timeoutDoc = null;

        inputBuscarDoc.addEventListener('input', () => {
            const q = inputBuscarDoc.value.trim();
            clearTimeout(timeoutDoc);

            if (q.length < 2) {
                divResultadosDoc.classList.remove('visible');
                divResultadosDoc.innerHTML = '';
                return;
            }

            timeoutDoc = setTimeout(async () => {
                const url = window.BUSCAR_DOCENTES_URL + '?q=' + encodeURIComponent(q);
                const res  = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                divResultadosDoc.innerHTML = '';

                if (data.length === 0) {
                    divResultadosDoc.innerHTML =
                        '<div class="buscador-resultado-vacio">Sin resultados.</div>';
                } else {
                    data.forEach(doc => {
                        const item = document.createElement('div');
                        item.className   = 'buscador-resultado-item';
                        item.textContent = doc.texto;
                        item.addEventListener('click', () => {
                            inputHiddenDoc.value              = doc.id;
                            inputBuscarDoc.value              = doc.texto;
                            divResultadosDoc.classList.remove('visible');
                            divResultadosDoc.innerHTML        = '';
                            divSeleccionadoDoc.textContent    = '✓ ' + doc.texto;
                            divSeleccionadoDoc.classList.add('visible');
                        });
                        divResultadosDoc.appendChild(item);
                    });
                }

                divResultadosDoc.classList.add('visible');
            }, 300);
        });

        document.addEventListener('click', e => {
            if (!inputBuscarDoc.contains(e.target) && !divResultadosDoc.contains(e.target)) {
                divResultadosDoc.classList.remove('visible');
            }
        });
    }
});
