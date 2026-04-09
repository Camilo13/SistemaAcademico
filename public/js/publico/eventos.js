/**
 * publico/eventos.js
 * ─────────────────────────────────────────────────────────────
 * Colapsar / expandir la descripción de cada evento.
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.btn-ver-mas').forEach(boton => {

        boton.addEventListener('click', function () {
            const descripcion = this.previousElementSibling;
            if (!descripcion) return;

            descripcion.classList.toggle('colapsado');

            this.textContent = descripcion.classList.contains('colapsado')
                ? 'Ver más'
                : 'Ver menos';
        });
    });
});
