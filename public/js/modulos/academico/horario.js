/**
 * modulos/academico/horario.js
 * ─────────────────────────────────────────────────────────────
 * Módulo Horarios (index, grupo, create).
 *
 * Depende de:
 *   componentes/academico.js — confirmarAccion, iniciarGuardaCambios
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════════════════
       AUTO-SUBMIT al cambiar el año (index)
    ══════════════════════════════════════════ */
    const selectAnio = document.getElementById('select-anio');
    if (selectAnio) {
        selectAnio.addEventListener('change', () => {
            document.getElementById('form-filtro-horario')?.submit();
        });
    }

    /* ══════════════════════════════════════════
       CONFIRMAR eliminar franja (grupo)
    ══════════════════════════════════════════ */
    confirmarAccion(
        '.form-eliminar-franja',
        '¿Quitar la franja de <strong>${nombre}</strong>?<br>' +
        '<small style="color:#92400e;">La clase se eliminará del horario ' +
        'pero la asignación docente se mantendrá.</small>',
        'Sí, quitar'
    );

    /* ══════════════════════════════════════════
       GUARDAR CAMBIOS (create)
    ══════════════════════════════════════════ */
    iniciarGuardaCambios('form[data-form="horario"]', '.btn-neutro[href]');
});
