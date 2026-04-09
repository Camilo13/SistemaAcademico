/**
 * layout/menu.js
 * ─────────────────────────────────────────────────────────────
 * Control universal del menú — funciona en TODOS los layouts:
 * portal público, admin, docente, estudiante.
 *
 * Expone toggleMenu() globalmente para el onclick del botón
 * hamburguesa en el HTML del layout.
 * ─────────────────────────────────────────────────────────────
 */

/**
 * Abre / cierra el menú en móvil.
 * Busca el elemento con [data-menu] y alterna .activa.
 */
function toggleMenu() {
    const menu = document.querySelector('[data-menu]');
    if (!menu) return;
    menu.classList.toggle('activa');
}

/* Cierra el menú al ampliar la ventana */
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        const menu = document.querySelector('[data-menu]');
        if (menu) menu.classList.remove('activa');
    }
});

/* Cierra el menú público al hacer clic fuera de él */
document.addEventListener('click', e => {
    const menu     = document.querySelector('[data-menu]');
    const boton    = document.querySelector('.boton-menu');
    if (!menu || !boton) return;

    /* Solo si el menú está abierto y el clic fue fuera */
    if (
        menu.classList.contains('activa') &&
        !menu.contains(e.target) &&
        !boton.contains(e.target)
    ) {
        menu.classList.remove('activa');
    }
});

/* ── Submenús del panel admin/docente/estudiante ── */
document.addEventListener('DOMContentLoaded', () => {

    /* Abrir submenú activo según la URL */
    const urlActual = window.location.pathname;

    document.querySelectorAll('.submenu').forEach(sub => {
        const tieneActivo = [...sub.querySelectorAll('a')].some(
            a => a.getAttribute('href') && urlActual.startsWith(a.getAttribute('href'))
        );
        if (tieneActivo) {
            sub.classList.add('activo');
            const flecha = sub.closest('.menu-padre')?.querySelector('.flecha');
            if (flecha) flecha.classList.add('rotar');
        }
    });

    /* Toggle manual */
    document.querySelectorAll('.menu-toggle').forEach(toggle => {
        toggle.addEventListener('click', function () {
            const padre  = this.closest('.menu-padre');
            const sub    = padre?.querySelector('.submenu');
            const flecha = this.querySelector('.flecha');
            if (!sub) return;
            sub.classList.toggle('activo');
            flecha?.classList.toggle('rotar');
        });
    });
});
