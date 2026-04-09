/**
 * modulos/academico/estructura/grado.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de Grados académicos.
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar desactivar ── */
    confirmarAccion(
        '.form-desactivar',
        '¿Desactivar el grado <strong>${nombre}</strong>?<br>' +
        '<small style="color:#92400e;">Dejará de estar disponible ' +
        'para asignaciones e inscripciones.</small>',
        'Sí, desactivar'
    );

    /* ── Confirmar activar ── */
    confirmarAccion(
        '.form-activar',
        '¿Activar el grado <strong>${nombre}</strong>?<br>' +
        '<small style="color:#374151;">Estará disponible para ' +
        'asignaciones e inscripciones.</small>',
        'Sí, activar'
    );

    /* ── Confirmar eliminar ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar el grado <strong>${nombre}</strong>?<br>' +
        '<small style="color:#b91c1c;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es permanente. Solo es posible si no tiene ' +
        'grupos ni materias asociadas.</small>',
        'Sí, eliminar'
    );

    /* ── Advertir cambios sin guardar ── */
    iniciarGuardaCambios('form[data-form="grado"]', '.btn-neutro[href]');
});
