/**
 * modulos/biblioteca/gestion/recurso/recurso.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de recursos de biblioteca.
 *
 * Responsabilidades:
 *   1. Formulario dinámico — tipo → método → url/archivo
 *   2. Confirmaciones: eliminar, activar, desactivar
 *   3. Advertir al cancelar si hay cambios sin guardar
 *
 * ✅ Las notificaciones las maneja el layout globalmente.
 *    No se duplica esa lógica aquí.
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════════════════════════════
       1. FORMULARIO DINÁMICO
          tipo → muestra campo-metodo (si no es 'enlace')
          metodo → muestra campo-url o campo-archivo
    ══════════════════════════════════════════════════════ */
    const tipoSelect   = document.getElementById('tipo');
    const campoMetodo  = document.getElementById('campo-metodo');
    const campoUrl     = document.getElementById('campo-url');
    const campoArchivo = document.getElementById('campo-archivo');
    const inputUrl     = document.getElementById('url');
    const inputArchivo = document.getElementById('archivo');
    const radiosMetodo = document.querySelectorAll('input[name="metodo"]');

    if (tipoSelect) {

        /* ── Ocultar y limpiar todos los campos condicionales ── */
        const ocultarCamposCondicionales = () => {
            campoMetodo?.classList.add('oculto');
            campoUrl?.classList.add('oculto');
            campoArchivo?.classList.add('oculto');
            inputUrl?.removeAttribute('required');
            inputArchivo?.removeAttribute('required');
            radiosMetodo.forEach(r => { r.checked = false; });
        };

        /* ── Mostrar campo correcto según el método elegido ── */
        const aplicarMetodo = (metodo) => {
            campoUrl?.classList.add('oculto');
            campoArchivo?.classList.add('oculto');
            inputUrl?.removeAttribute('required');
            inputArchivo?.removeAttribute('required');

            if (metodo === 'url') {
                campoUrl?.classList.remove('oculto');
                inputUrl?.setAttribute('required', 'required');
            }

            if (metodo === 'archivo') {
                campoArchivo?.classList.remove('oculto');
                // Solo required en create, no en edit (puede no cambiar el archivo)
                const esEdit = !!document.querySelector('input[name="_method"]');
                if (!esEdit) inputArchivo?.setAttribute('required', 'required');
            }
        };

        /* ── Evento: cambio de tipo ── */
        tipoSelect.addEventListener('change', () => {
            ocultarCamposCondicionales();

            const tipo = tipoSelect.value;
            if (!tipo) return;

            // El tipo 'enlace' va directo a URL sin elegir método
            if (tipo === 'enlace') {
                campoUrl?.classList.remove('oculto');
                inputUrl?.setAttribute('required', 'required');
                // Forzar metodo = 'url' oculto
                const radioUrl = document.querySelector('input[name="metodo"][value="url"]');
                if (radioUrl) radioUrl.checked = true;
                return;
            }

            campoMetodo?.classList.remove('oculto');
        });

        /* ── Evento: cambio de método ── */
        radiosMetodo.forEach(radio => {
            radio.addEventListener('change', () => aplicarMetodo(radio.value));
        });

        /* ── Inicialización en edit / old() con errores ── */
        if (tipoSelect.value) {
            // Simular change para mostrar los campos correctos
            tipoSelect.dispatchEvent(new Event('change'));

            const metodoChecked = document.querySelector('input[name="metodo"]:checked');
            if (metodoChecked) {
                aplicarMetodo(metodoChecked.value);
            }
        }
    }

    /* ══════════════════════════════════════════════════════
       2. CONFIRMACIONES
    ══════════════════════════════════════════════════════ */

    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar el recurso <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">Esta acción es permanente. ' +
        'Si tiene archivo físico, también se eliminará del servidor.</small>',
        'Sí, eliminar'
    );

    confirmarAccion(
        '.form-desactivar',
        '¿Ocultar el recurso <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">Los docentes y estudiantes ' +
        'dejarán de verlo hasta que lo publiques de nuevo.</small>',
        'Sí, ocultar'
    );

    confirmarAccion(
        '.form-activar',
        '¿Publicar el recurso <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">Será visible para docentes ' +
        'y estudiantes en la biblioteca.</small>',
        'Sí, publicar'
    );

    /* ══════════════════════════════════════════════════════
       3. ADVERTIR CAMBIOS SIN GUARDAR
    ══════════════════════════════════════════════════════ */
    iniciarGuardaCambios('form[data-form="recurso"]', '.btn-neutro[href]');
});
