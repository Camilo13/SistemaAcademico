/**
 * modulos/academico/inscripcion_materia.js
 * ─────────────────────────────────────────────────────────────
 * Módulo Materias Inscritas (index y create).
 *
 * Depende de:
 *   componentes/academico.js      — confirmarAccion, mostrarAdvertencia,
 *                                   iniciarGuardaCambios
 *   componentes/acciones-tabla.js — maneja: checkbox-todos, fila-seleccionable,
 *                                   btn-bulk-editar (=Ver Notas), btn-bulk-eliminar,
 *                                   btn-bulk-limpiar, contador
 *
 * Este archivo gestiona exclusivamente:
 *   — btn-bulk-retirar: visible solo cuando 1 fila seleccionada Y estado=activa
 *   — guarda-cambios del form de create
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════════════════
       BOTÓN RETIRAR EN LA BARRA BULK
    ══════════════════════════════════════════ */
    const barra      = document.querySelector('.barra-bulk');
    const btnRetirar = document.querySelector('.btn-bulk-retirar');

    if (barra && btnRetirar) {

        const urlRetirar = barra.dataset.urlRetirar ?? '';

        /**
         * Muestra "Retirar" solo cuando exactamente 1 fila
         * está seleccionada Y su estado es 'activa'.
         */
        function actualizarRetirar() {
            const seleccionados = document.querySelectorAll('.checkbox-tabla:checked');
            const mostrar =
                seleccionados.length === 1 &&
                (seleccionados[0].dataset.estado ?? '') === 'activa';

            btnRetirar.style.display = mostrar ? '' : 'none';
        }

        // Escuchar cambios en checkboxes individuales y "todos"
        document.addEventListener('change', e => {
            if (e.target.matches('.checkbox-tabla, .checkbox-todos')) {
                // Timeout para dejar que acciones-tabla.js procese primero
                setTimeout(actualizarRetirar, 0);
            }
        });

        // Escuchar botón Limpiar de la barra
        const btnLimpiar = document.querySelector('.btn-bulk-limpiar');
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', () => {
                setTimeout(actualizarRetirar, 0);
            });
        }

        // Click en Retirar — confirmar y enviar PATCH
        btnRetirar.addEventListener('click', () => {

            const seleccionados = document.querySelectorAll('.checkbox-tabla:checked');
            if (seleccionados.length !== 1) return;

            const id      = seleccionados[0].dataset.id;
            const fila    = document.querySelector(`.fila-seleccionable[data-id="${id}"]`);
            const materia = fila?.querySelector('td strong')?.textContent?.trim()
                            ?? 'esta materia';
            const url     = urlRetirar.replace(':id', id);

            mostrarAdvertencia(
                `¿Retirar la materia <strong>${materia}</strong>?<br>` +
                `<small style="color:#92400e;">El estudiante dejará de cursar ` +
                `esta materia y sus notas quedarán bloqueadas.</small>`,
                {
                    showCancelButton  : true,
                    confirmButtonText : 'Sí, retirar',
                    cancelButtonText  : 'Cancelar',
                    confirmButtonColor: '#d97706',
                }
            ).then(res => {
                if (!res.isConfirmed) return;
                _enviarPatch(url);
            });
        });

        // Estado inicial
        actualizarRetirar();
    }

    /* ══════════════════════════════════════════
       GUARDAR CAMBIOS (create)
    ══════════════════════════════════════════ */
    iniciarGuardaCambios('form[data-form="inscripcion-materia"]', '.btn-neutro[href]');

    /* ══════════════════════════════════════════
       HELPER — enviar PATCH dinámico
    ══════════════════════════════════════════ */
    function _enviarPatch(url) {
        const form   = document.createElement('form');
        form.method  = 'POST';
        form.action  = url;

        const token  = document.createElement('input');
        token.type   = 'hidden';
        token.name   = '_token';
        token.value  = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        const method = document.createElement('input');
        method.type  = 'hidden';
        method.name  = '_method';
        method.value = 'PATCH';

        form.append(token, method);
        document.body.appendChild(form);
        form.submit();
    }
});
