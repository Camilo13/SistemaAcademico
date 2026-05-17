/**
 * modulos/academico/inscripcion.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de Inscripciones.
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar retirar ── */
    confirmarAccion(
        '.form-retirar',
        '¿Retirar la inscripción de <strong>${nombre}</strong>?<br>' +
        '<small style="color:#92400e;">El estudiante quedará con estado ' +
        '<strong>Retirado</strong>. Puede volver a inscribirse si es necesario.</small>',
        'Sí, retirar'
    );

    /* ── Confirmar finalizar ── */
    confirmarAccion(
        '.form-finalizar',
        '¿Finalizar la inscripción de <strong>${nombre}</strong>?<br>' +
        '<small style="color:#374151;">La inscripción quedará marcada como ' +
        '<strong>Finalizada</strong> al término del año lectivo.</small>',
        'Sí, finalizar'
    );

    /* ── Confirmar eliminar ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar la inscripción de <strong>${nombre}</strong>?<br>' +
        '<small style="color:#b91c1c;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es permanente. Solo es posible si no tiene ' +
        'materias ni notas registradas.</small>',
        'Sí, eliminar'
    );

    /* ── Advertir cambios sin guardar ── */
    iniciarGuardaCambios('form[data-form="inscripcion"]', '.btn-neutro[href]');

    // ── Buscador de estudiantes ──
    const inputBuscar     = document.getElementById('estudiante_buscar');
    const inputHidden     = document.getElementById('estudiante_id');
    const divResultados   = document.getElementById('estudiante_resultados');
    const divSeleccionado = document.getElementById('estudiante_seleccionado');

    if (inputBuscar) {

        let timeoutBuscar = null;

        inputBuscar.addEventListener('input', () => {
            const q = inputBuscar.value.trim();
            clearTimeout(timeoutBuscar);

            if (q.length < 2) {
                divResultados.classList.remove('visible');
                divResultados.innerHTML = '';
                return;
            }

            timeoutBuscar = setTimeout(async () => {
                const url = window.BUSCAR_ESTUDIANTES_URL + '?q=' + encodeURIComponent(q);
                const res  = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                divResultados.innerHTML = '';

                if (data.length === 0) {
                    divResultados.innerHTML =
                        '<div class="buscador-resultado-vacio">Sin resultados.</div>';
                } else {
                    data.forEach(est => {
                        const item = document.createElement('div');
                        item.className   = 'buscador-resultado-item';
                        item.textContent = est.texto;
                        item.addEventListener('click', () => {
                            inputHidden.value         = est.id;
                            inputBuscar.value         = est.texto;
                            divResultados.classList.remove('visible');
                            divResultados.innerHTML   = '';
                            divSeleccionado.textContent = '✓ ' + est.texto;
                            divSeleccionado.classList.add('visible');
                        });
                        divResultados.appendChild(item);
                    });
                }

                divResultados.classList.add('visible');
            }, 300);
        });

        // Cerrar resultados al hacer click fuera
        document.addEventListener('click', e => {
            if (!inputBuscar.contains(e.target) && !divResultados.contains(e.target)) {
                divResultados.classList.remove('visible');
            }
        });
    }
});
