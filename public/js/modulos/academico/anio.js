/**
 * modulos/academico/anios.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de Años Lectivos.
 *
 * Responsabilidades:
 *   - Confirmar activar año (avisa que desactiva el actual)
 *   - Confirmar eliminar año (avisa de consecuencias)
 *   - Advertir cambios sin guardar al cancelar
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar activar ── */
    confirmarAccion(
        '.form-activar',
        '¿Activar el año lectivo <strong>${nombre}</strong>?<br><br>' +
        '<span style="color:#92400e;font-size:0.88rem;">' +
        '<i class="fa-solid fa-circle-info"></i> ' +
        'El año lectivo que esté activo actualmente será desactivado automáticamente. ' +
        'Solo puede haber un año activo a la vez en el sistema.' +
        '</span>',
        'Sí, activar'
    );

    /* ── Confirmar eliminar ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar permanentemente el año lectivo <strong>${nombre}</strong>?<br><br>' +
        '<span style="color:#b91c1c;font-size:0.88rem;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es irreversible. Solo es posible si el año no tiene periodos ' +
        'ni grupos registrados.' +
        '</span>',
        'Sí, eliminar'
    );

    /* ── Advertir cambios sin guardar ── */
    iniciarGuardaCambios('form[data-form="anio"]', '.btn-neutro[href]');
});
