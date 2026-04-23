/**
 * modulos/docente/asistencia.js
 * ─────────────────────────────────────────────────────────────
 * Depende de: js/componentes/academico.js
 *             (confirmarAccion, iniciarGuardaCambios)
 * Usado en  : index, estudiantes, create, edit
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════════════════════
       1. BUSCADOR — index
       Filtra las tarjetas de asignación por texto
    ══════════════════════════════════════════════ */
    const buscador = document.getElementById('buscador');
    const lista    = document.getElementById('lista-asignaciones');
    const sinRes   = document.getElementById('sin-resultados');

    if (buscador && lista) {
        buscador.addEventListener('input', () => {
            const q       = buscador.value.toLowerCase().trim();
            const items   = lista.querySelectorAll('.asignacion-item');
            let  visibles = 0;

            items.forEach(item => {
                const texto  = item.dataset.buscar ?? '';
                const mostrar = q === '' || texto.includes(q);
                item.style.display = mostrar ? '' : 'none';
                if (mostrar) visibles++;
            });

            if (sinRes) sinRes.style.display = visibles === 0 ? '' : 'none';
        });
    }

    /* ══════════════════════════════════════════════
       2. TOTAL EN TIEMPO REAL — create y edit
       Suma justificadas + injustificadas y actualiza
       el preview #total-faltas-preview
    ══════════════════════════════════════════════ */
    const inputJust   = document.getElementById('faltas_justificadas');
    const inputInjust = document.getElementById('faltas_injustificadas');
    const totalPreview = document.getElementById('total-faltas-preview');

    if (inputJust && inputInjust && totalPreview) {

        function actualizarTotal() {
            const j = parseInt(inputJust.value)   || 0;
            const i = parseInt(inputInjust.value) || 0;
            const total = j + i;

            totalPreview.textContent = total;

            // Color según cantidad
            if (total === 0) {
                totalPreview.style.color = '#065f46';
            } else if (total <= 3) {
                totalPreview.style.color = '#854d0e';
            } else {
                totalPreview.style.color = '#991b1b';
            }
        }

        inputJust.addEventListener('input',  actualizarTotal);
        inputInjust.addEventListener('input', actualizarTotal);

        // Inicializar al cargar (create con 0, edit con valores existentes)
        actualizarTotal();
    }

    /* ══════════════════════════════════════════════
       3. CONFIRMAR ELIMINAR REGISTRO — estudiantes
       Usa confirmarAccion() de academico.js
    ══════════════════════════════════════════════ */
    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar el registro de asistencia de <strong>${nombre}</strong>?<br>' +
        '<small style="color:#b91c1c;">' +
        '<i class="fa-solid fa-triangle-exclamation"></i> ' +
        'Solo se pueden eliminar registros de periodos abiertos.' +
        '</small>',
        'Sí, eliminar',
        'nombre'
    );

    /* ══════════════════════════════════════════════
       4. GUARDAR CAMBIOS — create y edit
       Avisa si hay cambios sin guardar al cancelar
    ══════════════════════════════════════════════ */
    iniciarGuardaCambios(
        'form[data-form="asistencia-docente"]',
        '.btn-neutro[href]'
    );

});