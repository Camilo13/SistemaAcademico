/**
 * modulos/perfil/perfil.js
 * ─────────────────────────────────────────────────────────────
 * Lógica de edición del perfil de usuario.
 *
 * Responsabilidades:
 *   - Habilitar / deshabilitar campos al presionar Editar
 *   - Advertir antes de cancelar si hay cambios sin guardar
 *   - Evitar doble envío del formulario
 *
 *    Las notificaciones (éxito / error) las maneja el layout
 *    globalmente vía notificaciones.js + window.APP_ALERTS.
 *    No se duplica esa lógica aquí.
 *
 * Requiere: global/notificaciones.js (cargado en el layout)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    const form       = document.getElementById('perfilForm');
    if (!form) return;

    const btnEditar   = document.getElementById('btnEditar');
    const btnGuardar  = document.getElementById('btnGuardar');
    const btnCancelar = document.getElementById('btnCancelar');
    const campos      = form.querySelectorAll('input:not([type="hidden"])');

    let enviando = false;

    /* ── Capturar valores iniciales ── */
    const inicial = {};
    campos.forEach(c => { inicial[c.name] = c.value; });

    /* ── Estado inicial: campos bloqueados ── */
    bloquear();

    /* ── Botón Editar ── */
    btnEditar?.addEventListener('click', () => {
        desbloquear();
        toggleBotones(true);
        // Enfocar el primer campo editable
        campos[0]?.focus();
    });

    /* ── Botón Cancelar ── */
    btnCancelar?.addEventListener('click', () => {
        if (!huboCambios()) {
            restaurar();
            return;
        }

        mostrarAdvertencia(
            '¿Descartar los cambios realizados?',
            {
                showCancelButton : true,
                confirmButtonText: 'Sí, descartar',
                cancelButtonText : 'Continuar editando',
            }
        ).then(r => {
            if (r.isConfirmed) restaurar();
        });
    });

    /* ── Envío del formulario ── */
    form.addEventListener('submit', e => {

        // Evitar doble clic en Guardar
        if (enviando) {
            e.preventDefault();
            return;
        }

        // Avisar si no hay cambios
        if (!huboCambios()) {
            e.preventDefault();
            mostrarInfo('No realizaste ningún cambio.');
            return;
        }

        // Bloquear el botón para evitar doble submit
        enviando = true;
        if (btnGuardar) {
            btnGuardar.disabled = true;
            btnGuardar.textContent = 'Guardando…';
        }
    });

    /* ══════════════════════════════════════════
       Funciones auxiliares
    ══════════════════════════════════════════ */

    function bloquear() {
        campos.forEach(c => { c.disabled = true; });
        toggleBotones(false);
    }

    function desbloquear() {
        campos.forEach(c => { c.disabled = false; });
    }

    function restaurar() {
        campos.forEach(c => {
            c.value    = inicial[c.name] ?? '';
            c.disabled = true;
        });
        toggleBotones(false);
        enviando = false;
    }

    function toggleBotones(editando) {
        btnEditar?.classList.toggle('oculto', editando);
        btnGuardar?.classList.toggle('oculto', !editando);
        btnCancelar?.classList.toggle('oculto', !editando);
    }

    function huboCambios() {
        return [...campos].some(c => c.value !== (inicial[c.name] ?? ''));
    }
});
