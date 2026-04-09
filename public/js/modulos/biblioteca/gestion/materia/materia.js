/**
 * modulos/biblioteca/gestion/materia/materia.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de gestión de materias de biblioteca.
 *
 * Responsabilidades:
 *   - Confirmar eliminación (index/edit)
 *   - Confirmar activar/desactivar visibilidad (edit)
 *   - Advertir cambios sin guardar al cancelar (create/edit)
 *
 * ✅ Las notificaciones (éxito/error) las maneja el layout
 *    globalmente vía notificaciones.js + window.APP_ALERTS.
 *    No se duplica esa lógica aquí.
 *
 * Requiere:
 *   componentes/academico.js  (confirmarAccion, iniciarGuardaCambios)
 *   global/notificaciones.js  (cargado en el layout)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar eliminar ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar la materia <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">Esta acción es permanente. ' +
        'Solo es posible si no tiene recursos asociados.</small>',
        'Sí, eliminar'
    );

    /* ── Confirmar desactivar (ocultar) ── */
    confirmarAccion(
        '.form-desactivar',
        '¿Ocultar la materia <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">Los docentes y estudiantes ' +
        'dejarán de verla hasta que la vuelvas a publicar.</small>',
        'Sí, ocultar'
    );

    /* ── Confirmar activar (publicar) ── */
    confirmarAccion(
        '.form-activar',
        '¿Publicar la materia <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">Será visible para docentes ' +
        'y estudiantes en la biblioteca.</small>',
        'Sí, publicar'
    );

    /* ── Advertir cambios sin guardar (create/edit) ── */
    iniciarGuardaCambios('form[data-form="materia"]', '.btn-neutro[href]');
});
