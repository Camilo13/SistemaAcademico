/**
 * modulos/academico/estructura/sede.js
 * ─────────────────────────────────────────────────────────────
 * Módulo: Sedes Académicas.
 * Mantenemos archivo propio porque los mensajes de sede
 * incluyen advertencias sobre consecuencias en grados/grupos.
 *
 * Requiere: componentes/academico.js · global/notificaciones.js
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Desactivar sede ─────────────────────────────────── */
    confirmarAccion(
        '.form-desactivar',
        '¿Desactivar la sede <strong>${nombre}</strong>?<br><br>' +
        '<span style="color:#92400e;font-size:0.88rem;">' +
        '<i class="fa-solid fa-circle-info"></i> ' +
        'Los grados y grupos asociados seguirán activos, pero la sede ' +
        'no estará disponible para nuevos registros.' +
        '</span>',
        'Sí, desactivar'
    );

    /* ── Activar sede ────────────────────────────────────── */
    confirmarAccion(
        '.form-activar',
        '¿Activar la sede <strong>${nombre}</strong>?',
        'Sí, activar'
    );

    /* ── Eliminar sede ───────────────────────────────────── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar permanentemente la sede <strong>${nombre}</strong>?<br><br>' +
        '<span style="color:#b91c1c;font-size:0.88rem;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es irreversible. Solo es posible si la sede no tiene ' +
        'grados asociados. Si los tiene, elimínalos primero.' +
        '</span>',
        'Sí, eliminar'
    );

    /* ── Guardar cambios en edit ─────────────────────────── */
    iniciarGuardaCambios('form[data-form="sede"]', '.btn-neutro[href]');
});
