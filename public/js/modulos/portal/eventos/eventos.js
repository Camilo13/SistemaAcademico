/**
 * modulos/portal/eventos/eventos.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de eventos del portal.
 *
 * Responsabilidades:
 *   1. Validación cliente de fecha — avisar si es pasada
 *   2. Confirmaciones: eliminar, activar, desactivar
 *   3. Advertir cambios sin guardar al cancelar
 *
 * ✅ Las notificaciones las maneja el layout globalmente.
 *    No usa Swal.fire() directo — usa los helpers del sistema.
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 *           global/notificaciones.js  (mostrarAdvertencia)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════════════════════════════
       1. VALIDACIÓN CLIENTE — fecha no puede ser pasada
       El servidor también lo valida, pero la UX mejora
       si el admin recibe feedback inmediato.
    ══════════════════════════════════════════════════════ */
    const formEvento  = document.getElementById('formEvento');
    const fechaInput  = document.getElementById('fecha_evento');

    if (formEvento && fechaInput) {

        formEvento.addEventListener('submit', e => {

            const fechaSeleccionada = new Date(fechaInput.value);
            const ahora             = new Date();

            if (fechaSeleccionada < ahora) {
                e.preventDefault();
                // Usar mostrarError del sistema, no Swal.fire() directo
                mostrarError('La fecha del evento no puede ser en el pasado.');
            }
        });
    }

    /* ══════════════════════════════════════════════════════
       2. CONFIRMACIONES
    ══════════════════════════════════════════════════════ */

    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar el evento <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">Esta acción es permanente ' +
        'e irreversible.</small>',
        'Sí, eliminar'
    );

    confirmarAccion(
        '.form-desactivar',
        '¿Desactivar el evento <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">El evento dejará de mostrarse ' +
        'en el portal público.</small>',
        'Sí, desactivar'
    );

    confirmarAccion(
        '.form-activar',
        '¿Activar el evento <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">El evento se mostrará ' +
        'en el portal público.</small>',
        'Sí, activar'
    );

    /* ══════════════════════════════════════════════════════
       3. ADVERTIR CAMBIOS SIN GUARDAR
    ══════════════════════════════════════════════════════ */
    iniciarGuardaCambios('form[data-form="evento"]', '.btn-neutro[href]');
});
