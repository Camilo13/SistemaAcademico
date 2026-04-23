/**
 * js/modulos/docente/grupos.js
 * ─────────────────────────────────────────────────────────
 * Filtro de búsqueda en tiempo real para las tarjetas de
 * la vista "Mis Grupos" del docente.
 *
 * No requiere academico.js — autónomo.
 * ─────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    const buscador     = document.getElementById('buscador-grupos');
    const cards        = document.querySelectorAll('.grupo-card');
    const sinResultados = document.getElementById('sin-resultados-grupos');

    if (!buscador) return;

    buscador.addEventListener('input', () => {
        const termino = buscador.value.trim().toLowerCase();
        let visibles  = 0;

        cards.forEach(card => {
            const texto = card.textContent.toLowerCase();
            const mostrar = !termino || texto.includes(termino);
            card.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        if (sinResultados) {
            sinResultados.style.display = visibles === 0 ? 'flex' : 'none';
        }
    });
});