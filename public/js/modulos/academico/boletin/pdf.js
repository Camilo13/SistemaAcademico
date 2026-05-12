/**
 * modulos/academico/boletin/pdf.js
 * ─────────────────────────────────────────────────────────────
 * Lógica de la vista de previsualización del boletín PDF.
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    document.getElementById('btn-imprimir')?.addEventListener('click', () => {
        window.print();
    });

});
