/**
 * modulos/academico/nota.js
 * ─────────────────────────────────────────────────────────────
 * Requiere: componentes/academico.js
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar eliminar nota ── */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar <strong>${nombre}</strong>?<br>' +
        '<small style="color:#b91c1c;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Esta acción es permanente. No se puede eliminar si el periodo está cerrado.' +
        '</small>',
        'Sí, eliminar'
    );

    /* ── Advertir cambios sin guardar ── */
    iniciarGuardaCambios('form[data-form="nota"]', '.btn-neutro[href]');

    /* ── Validación visual de rango de nota en tiempo real ── */
    const inputNota = document.getElementById('nota');
    if (inputNota) {
        inputNota.addEventListener('input', () => {
            const val = parseFloat(inputNota.value);
            if (!isNaN(val)) {
                if (val < 0 || val > 5) {
                    inputNota.style.borderColor = '#dc2626';
                } else if (val >= 3.0) {
                    inputNota.style.borderColor = '#10b981';
                } else {
                    inputNota.style.borderColor = '#f59e0b';
                }
            } else {
                inputNota.style.borderColor = '';
            }
        });
    }
});
