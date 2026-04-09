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
});
