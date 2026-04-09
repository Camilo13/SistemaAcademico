/**
 * modulos/academico/inscripcion_materia.js
 * ─────────────────────────────────────────────────────────────
 * Requiere: componentes/academico.js
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar retirar materia ── */
    confirmarAccion(
        '.form-retirar',
        '¿Retirar la materia <strong>${nombre}</strong>?<br>' +
        '<small style="color:#92400e;">El estudiante dejará de tener ' +
        'acceso a esta materia y sus notas quedarán bloqueadas.</small>',
        'Sí, retirar'
    );

    /* ── Confirmar eliminar materia ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar la materia <strong>${nombre}</strong> de la inscripción?<br>' +
        '<small style="color:#b91c1c;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es permanente. Solo es posible si no tiene notas.</small>',
        'Sí, eliminar'
    );

    /* ── Advertir cambios sin guardar ── */
    iniciarGuardaCambios('form[data-form="inscripcion-materia"]', '.btn-neutro[href]');
});
