/**
 * publico/carrusel.js
 * ─────────────────────────────────────────────────────────────
 * Carrusel automático del hero de la página de inicio.
 *
 * Genera los puntos indicadores dinámicamente, avanza cada
 * 4 segundos y pausa al pasar el cursor sobre el hero.
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    const slides      = document.querySelectorAll('.slide-hero');
    const contenedor  = document.getElementById('carrusel-indicadores');

    if (slides.length === 0) return;

    let indice   = 0;
    let intervalo;

    /* ── Activar el primer slide ── */
    slides[0].classList.add('activo');

    /* ── Crear puntos indicadores ── */
    if (contenedor) {
        slides.forEach((_, i) => {
            const punto = document.createElement('button');
            punto.className = 'indicador' + (i === 0 ? ' activo' : '');
            punto.setAttribute('aria-label', `Ir a imagen ${i + 1}`);
            punto.addEventListener('click', () => irA(i));
            contenedor.appendChild(punto);
        });
    }

    /* ── Ir a un slide específico ── */
    function irA(nuevoIndice) {
        slides[indice].classList.remove('activo');
        actualizarIndicador(indice, false);

        indice = (nuevoIndice + slides.length) % slides.length;

        slides[indice].classList.add('activo');
        actualizarIndicador(indice, true);
    }

    /* ── Actualizar indicador activo ── */
    function actualizarIndicador(i, activo) {
        if (!contenedor) return;
        const puntos = contenedor.querySelectorAll('.indicador');
        if (puntos[i]) puntos[i].classList.toggle('activo', activo);
    }

    /* ── Avanzar automáticamente ── */
    function iniciarIntervalo() {
        intervalo = setInterval(() => irA(indice + 1), 2000);
    }

    function detenerIntervalo() {
        clearInterval(intervalo);
    }

    iniciarIntervalo();

    /* ── IntersectionObserver: tarjetas Misión y Visión ── */
    const mvCards = document.querySelectorAll('.mv-animado');
    if (mvCards.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        mvCards.forEach(card => observer.observe(card));
    }
});
