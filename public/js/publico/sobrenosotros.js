/**
 * publico/sobrenosotros.js
 * ─────────────────────────────────────────────────────────────
 * Modal del Manual de Convivencia.
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    const modal    = document.getElementById('manualModal');
    const btnAbrir = document.getElementById('abrirManual');
    const btnCerrar = document.getElementById('cerrarManual');

    if (!modal) return;

    /* ── Abrir ── */
    btnAbrir?.addEventListener('click', () => {
        modal.style.display = 'flex';
        /* Pequeño delay para que la transición CSS se active */
        requestAnimationFrame(() => {
            requestAnimationFrame(() => modal.classList.add('mostrar-modal'));
        });
    });

    /* ── Cerrar ── */
    function cerrarModal() {
        modal.classList.remove('mostrar-modal');
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    btnCerrar?.addEventListener('click', cerrarModal);

    /* Clic fuera del contenido */
    modal.addEventListener('click', e => {
        if (e.target === modal) cerrarModal();
    });

    /* Tecla ESC */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('mostrar-modal')) {
            cerrarModal();
        }
    });
});
