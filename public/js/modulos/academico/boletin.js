/**
 * modulos/academico/boletin.js
 * ─────────────────────────────────────────────────────────────
 * Requiere: componentes/academico.js (ya cargado en el layout)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Advertir si hay materias sin calificar al exportar PDF ── */
    const btnPdf = document.querySelector('a[href*="boletin/pdf"], a[href*="boletin.pdf"]');

    if (btnPdf) {
        const sinCalificar = document.querySelectorAll('.badge-sin-calificar').length;

        if (sinCalificar > 0) {
            btnPdf.addEventListener('click', e => {
                e.preventDefault();
                const destino = btnPdf.href;

                mostrarAdvertencia(
                    `Hay <strong>${sinCalificar} materia(s) sin calificar</strong>.<br>
                     <small>El boletín se generará con los datos disponibles hasta ahora.</small>`,
                    {
                        showCancelButton : true,
                        confirmButtonText: 'Generar de todos modos',
                        cancelButtonText : 'Cancelar',
                    }
                ).then(r => {
                    if (r.isConfirmed) window.open(destino, '_blank');
                });
            });
        }
    }
});
