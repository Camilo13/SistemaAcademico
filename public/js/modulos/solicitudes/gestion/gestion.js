/**
 * modulos/solicitudes/gestion.js
 * ─────────────────────────────────────────────────────────────
 * Confirmaciones para aprobar y rechazar solicitudes.
 * Guardado de cambios al salir del formulario de edición.
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 *           global/notificaciones.js  (mostrarAdvertencia)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar aprobar ── */
    confirmarAccion(
        '.form-aprobar',
        '¿Aprobar la solicitud de <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">Se creará un usuario con los datos ' +
        'de esta solicitud y podrá iniciar sesión en el sistema.</small>',
        'Sí, aprobar'
    );

    /* ── Confirmar rechazar ── */
    confirmarAccion(
        '.form-rechazar',
        '¿Rechazar la solicitud de <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">La solicitud quedará marcada como ' +
        'rechazada y no se creará ningún usuario.</small>',
        'Sí, rechazar'
    );

    /* ── Advertir cambios sin guardar en el edit ── */
    iniciarGuardaCambios('form[data-form="solicitud"]', '.btn-neutro[href]');
});
