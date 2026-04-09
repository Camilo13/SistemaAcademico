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
});
