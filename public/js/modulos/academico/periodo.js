/**
 * modulos/academico/periodo.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de Periodos académicos.
 *
 * Responsabilidades:
 *   - Confirmar cerrar periodo (advierte consecuencias en notas)
 *   - Confirmar reabrir periodo
 *   - Confirmar eliminar periodo
 *   - Advertir cambios sin guardar al cancelar
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Cerrar periodo ── */
    confirmarAccion(
        '.form-cerrar',
        '¿Cerrar el periodo <strong>${nombre}</strong>?<br>' +
        '<small style="color:#92400e;">' +
        '<i class="fa-solid fa-circle-info"></i> ' +
        'Las notas quedarán bloqueadas. Los docentes no podrán ' +
        'registrar ni modificar notas hasta que lo reabras.' +
        '</small>',
        'Sí, cerrar'
    );

    /* ── Reabrir periodo ── */
    confirmarAccion(
        '.form-reabrir',
        '¿Reabrir el periodo <strong>${nombre}</strong>?<br>' +
        '<small style="color:#374151;">' +
        'Los docentes podrán volver a registrar y modificar notas.' +
        '</small>',
        'Sí, reabrir'
    );

    /* ── Eliminar periodo ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar el periodo <strong>${nombre}</strong>?<br>' +
        '<small style="color:#b91c1c;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es permanente e irreversible. ' +
        'Solo es posible si no tiene notas registradas.' +
        '</small>',
        'Sí, eliminar'
    );

    /* ── Advertir cambios sin guardar ── */
    iniciarGuardaCambios('form[data-form="periodo"]', '.btn-neutro[href]');
});
