/**
 * dashboard/admin.js
 * ─────────────────────────────────────────────────────────────
 * Animación de entrada escalonada para las tarjetas del
 * dashboard. Es una mejora visual progresiva:
 *
 *   - Si este script NO carga → las tarjetas son visibles
 *     igual porque el CSS no las oculta por defecto.
 *   - Si carga → aparecen una a una con retardo de 70ms.
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    const tarjetas = document.querySelectorAll('.tarjeta');
    if (!tarjetas.length) return;

    tarjetas.forEach((tarjeta, i) => {

        // Ocultar brevemente para animar la entrada
        tarjeta.style.opacity   = '0';
        tarjeta.style.transform = 'translateY(12px)';

        setTimeout(() => {
            tarjeta.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
            tarjeta.style.opacity    = '1';
            tarjeta.style.transform  = 'translateY(0)';
        }, i * 70);
    });
});
