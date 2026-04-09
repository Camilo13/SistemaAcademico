/**
 * modulos/academico/estructura/materia.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de Materias académicas.
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar desactivar ── */
    confirmarAccion(
        '.form-desactivar',
        '¿Desactivar la materia <strong>${nombre}</strong>?<br>' +
        '<small style="color:#92400e;">Dejará de estar disponible ' +
        'para asignaciones y horarios.</small>',
        'Sí, desactivar'
    );

    /* ── Confirmar activar ── */
    confirmarAccion(
        '.form-activar',
        '¿Activar la materia <strong>${nombre}</strong>?<br>' +
        '<small style="color:#374151;">Estará disponible para ' +
        'asignaciones y horarios.</small>',
        'Sí, activar'
    );

    /* ── Confirmar eliminar ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar la materia <strong>${nombre}</strong>?<br>' +
        '<small style="color:#b91c1c;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es permanente. Solo es posible si no tiene ' +
        'asignaciones registradas.</small>',
        'Sí, eliminar'
    );

    /* ── Advertir cambios sin guardar ── */
    iniciarGuardaCambios('form[data-form="materia"]', '.btn-neutro[href]');
});
