/**
 * modulos/academico/estructura/grupo.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de Grupos académicos.
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar desactivar ── */
    confirmarAccion(
        '.form-desactivar',
        '¿Desactivar el grupo <strong>${nombre}</strong>?<br>' +
        '<small style="color:#92400e;">No se aceptarán nuevas ' +
        'inscripciones hasta que lo reactives.</small>',
        'Sí, desactivar'
    );

    /* ── Confirmar activar ── */
    confirmarAccion(
        '.form-activar',
        '¿Activar el grupo <strong>${nombre}</strong>?<br>' +
        '<small style="color:#374151;">Estará disponible para ' +
        'inscripciones y asignaciones.</small>',
        'Sí, activar'
    );

    /* ── Confirmar eliminar ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar el grupo <strong>${nombre}</strong>?<br>' +
        '<small style="color:#b91c1c;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es permanente. Solo es posible si no tiene ' +
        'inscripciones ni asignaciones.</small>',
        'Sí, eliminar'
    );

    /* ── Convertir nombre a mayúsculas en tiempo real ── */
    const inputNombre = document.getElementById('nombre');
    if (inputNombre) {
        inputNombre.addEventListener('input', () => {
            const pos = inputNombre.selectionStart;
            inputNombre.value = inputNombre.value.toUpperCase();
            inputNombre.setSelectionRange(pos, pos);
        });
    }

    /* ── Advertir cambios sin guardar ── */
    iniciarGuardaCambios('form[data-form="grupo"]', '.btn-neutro[href]');
});
