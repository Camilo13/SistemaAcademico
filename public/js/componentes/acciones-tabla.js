/**
 * componentes/acciones-tabla.js
 * ─────────────────────────────────────────────────────────────
 * Maneja la selección bulk en tablas académicas.
 *
 * MODOS:
 *   data-bulk-modo="usuarios"
 *     — 1 seleccionado  → muestra Editar + Eliminar
 *     — 2+ seleccionados → oculta Editar, solo Eliminar
 *     — Editar navega a: data-url-editar (reemplaza :id)
 *     — Eliminar individual: data-url-destroy (reemplaza :id)
 *     — Eliminar múltiple:  data-url-bulk-destroy (POST ids[])
 *
 *   data-bulk-modo="academico"
 *     — cualquier cantidad → solo Eliminar
 *     — Eliminar múltiple: data-url-bulk-destroy (POST ids[])
 *     — Eliminar individual: data-url-destroy si no hay bulk
 *
 * DATA ATTRIBUTES en .barra-bulk:
 *   data-bulk-modo         : 'usuarios' | 'academico'
 *   data-entidad           : texto para la confirmación ("año(s) lectivo(s)")
 *   data-url-editar        : URL con :id — solo modo usuarios
 *   data-url-destroy       : URL con :id — para individual
 *   data-url-bulk-destroy  : URL del endpoint bulk (POST)
 *
 * REQUISITO en cada <tr>:
 *   class="fila-seleccionable" + data-id="{{ $modelo->id }}"
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    const barra = document.querySelector('.barra-bulk');
    if (!barra) return;

    /* ── Config desde data-attributes ── */
    const modo           = barra.dataset.bulkModo       ?? 'academico';
    const entidad        = barra.dataset.entidad         ?? 'registro(s)';
    const urlEditar      = barra.dataset.urlEditar       ?? null;
    const urlDestroy     = barra.dataset.urlDestroy      ?? null;
    const urlBulkDestroy = barra.dataset.urlBulkDestroy  ?? null;

    /* ── Elementos de la barra ── */
    const contador    = barra.querySelector('.bulk-contador');
    const btnEditar   = barra.querySelector('.btn-bulk-editar');
    const btnEliminar = barra.querySelector('.btn-bulk-eliminar');
    const btnLimpiar  = barra.querySelector('.btn-bulk-limpiar');

    /* ── Checkboxes ── */
    const checkTodos  = document.querySelector('.checkbox-todos');
    const getChecks   = () => [...document.querySelectorAll('.checkbox-tabla:checked')];
    const getTodos    = () => [...document.querySelectorAll('.checkbox-tabla')];

    /* ══════════════════════════════════════════════════════
       1. ACTUALIZAR BARRA según selección
    ══════════════════════════════════════════════════════ */
    function actualizarBarra() {

        const seleccionados = getChecks();
        const n             = seleccionados.length;

        // Mostrar / ocultar barra
        if (n > 0) {
            barra.classList.add('visible');
        } else {
            barra.classList.remove('visible');
        }

        // Actualizar contador
        if (contador) contador.textContent = n;

        // Modo usuarios: editar solo con 1 seleccionado
        if (modo === 'usuarios' && btnEditar) {
            btnEditar.style.display = n === 1 ? '' : 'none';
        }

        // Marcar fila seleccionada
        document.querySelectorAll('.fila-seleccionable').forEach(fila => {
            const check = fila.querySelector('.checkbox-tabla');
            fila.classList.toggle('fila-seleccionada', check?.checked ?? false);
        });

        // Sincronizar checkbox-todos
        if (checkTodos) {
            const todos = getTodos();
            checkTodos.checked       = todos.length > 0 && todos.every(c => c.checked);
            checkTodos.indeterminate = n > 0 && n < todos.length;
        }
    }

    /* ══════════════════════════════════════════════════════
       2. EVENTOS DE CHECKBOXES
    ══════════════════════════════════════════════════════ */

    // Checkbox individual
    document.querySelectorAll('.checkbox-tabla').forEach(check => {
        check.addEventListener('change', actualizarBarra);
    });

    // Checkbox "seleccionar todos"
    if (checkTodos) {
        checkTodos.addEventListener('change', () => {
            getTodos().forEach(c => { c.checked = checkTodos.checked; });
            actualizarBarra();
        });
    }

    // Click en fila también selecciona el checkbox
    document.querySelectorAll('.fila-seleccionable').forEach(fila => {
        fila.addEventListener('click', e => {
            // No disparar si el click fue en un botón, enlace o el propio checkbox
            if (e.target.closest('a, button, form, input')) return;

            const check = fila.querySelector('.checkbox-tabla');
            if (check) {
                check.checked = !check.checked;
                actualizarBarra();
            }
        });
    });

    /* ══════════════════════════════════════════════════════
       3. BOTÓN EDITAR (modo usuarios, 1 seleccionado)
    ══════════════════════════════════════════════════════ */
    if (btnEditar && urlEditar) {
        btnEditar.addEventListener('click', e => {
            e.preventDefault();

            const seleccionados = getChecks();
            if (seleccionados.length !== 1) return;

            const id  = seleccionados[0].dataset.id;
            const url = urlEditar.replace(':id', id);
            window.location.href = url;
        });
    }

    /* ══════════════════════════════════════════════════════
       4. BOTÓN ELIMINAR
    ══════════════════════════════════════════════════════ */
    if (btnEliminar) {
        btnEliminar.addEventListener('click', () => {

            const seleccionados = getChecks();
            const n             = seleccionados.length;
            if (n === 0) return;

            const texto = n === 1
                ? `¿Eliminar este ${entidad.replace('(s)', '')}?<br>` +
                  '<small style="color:#b91c1c;">Esta acción es permanente.</small>'
                : `¿Eliminar <strong>${n} ${entidad}</strong>?<br>` +
                  '<small style="color:#b91c1c;">Esta acción es permanente e irreversible.</small>';

            mostrarAdvertencia(texto, {
                showCancelButton  : true,
                confirmButtonText : 'Sí, eliminar',
                cancelButtonText  : 'Cancelar',
            }).then(result => {

                if (!result.isConfirmed) return;

                // ── Eliminar individual (1 registro) ──
                if (n === 1 && urlDestroy) {
                    const id  = seleccionados[0].dataset.id;
                    const url = urlDestroy.replace(':id', id);
                    _enviarFormDelete(url);
                    return;
                }

                // ── Eliminar bulk (varios registros) ──
                if (urlBulkDestroy) {
                    const ids = seleccionados.map(c => c.dataset.id);
                    _enviarFormBulk(urlBulkDestroy, ids);
                    return;
                }

                // ── Fallback: eliminar uno a uno si no hay endpoint bulk ──
                if (urlDestroy) {
                    const ids = seleccionados.map(c => c.dataset.id);
                    _eliminarSecuencial(ids, urlDestroy);
                }
            });
        });
    }

    /* ══════════════════════════════════════════════════════
       5. BOTÓN LIMPIAR
    ══════════════════════════════════════════════════════ */
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', () => {
            getTodos().forEach(c => { c.checked = false; });
            if (checkTodos) {
                checkTodos.checked       = false;
                checkTodos.indeterminate = false;
            }
            actualizarBarra();
        });
    }

    /* ══════════════════════════════════════════════════════
       HELPERS INTERNOS
    ══════════════════════════════════════════════════════ */

    /** Envía un formulario DELETE a una URL */
    function _enviarFormDelete(url) {
        const form   = document.createElement('form');
        form.method  = 'POST';
        form.action  = url;

        const csrf   = document.createElement('input');
        csrf.type    = 'hidden';
        csrf.name    = '_token';
        csrf.value   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        const method = document.createElement('input');
        method.type  = 'hidden';
        method.name  = '_method';
        method.value = 'DELETE';

        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }

    /** Envía un formulario POST con array de IDs al endpoint bulk */
    function _enviarFormBulk(url, ids) {
        const form   = document.createElement('form');
        form.method  = 'POST';
        form.action  = url;

        const csrf   = document.createElement('input');
        csrf.type    = 'hidden';
        csrf.name    = '_token';
        csrf.value   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        const method = document.createElement('input');
        method.type  = 'hidden';
        method.name  = '_method';
        method.value = 'DELETE';

        ids.forEach(id => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });

        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }

    /** Elimina registros uno a uno (fallback sin endpoint bulk) */
    function _eliminarSecuencial(ids, urlTemplate) {
        // Solo elimina el primero y recarga — para el caso de que
        // no haya endpoint bulk. En la práctica se debe tener el endpoint.
        const url = urlTemplate.replace(':id', ids[0]);
        _enviarFormDelete(url);
    }

    /* ── Inicializar estado de la barra ── */
    actualizarBarra();
});
