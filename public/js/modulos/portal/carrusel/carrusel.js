/**
 * modulos/portal/carrusel/carrusel.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de carrusel del portal.
 *
 * Responsabilidades:
 *   1. Preview de imagen en tiempo real (create/edit)
 *   2. Confirmaciones: eliminar, activar, desactivar
 *   3. Advertir cambios sin guardar al cancelar
 *
 * ✅ Las notificaciones las maneja el layout globalmente.
 *
 * Requiere: componentes/academico.js  (confirmarAccion,
 *                                      iniciarGuardaCambios)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════════════════════════════
       1. PREVIEW DE IMAGEN EN TIEMPO REAL
    ══════════════════════════════════════════════════════ */
    const inputImagen = document.getElementById('imagen');
    const preview     = document.getElementById('previewImagen');

    if (inputImagen && preview) {

        inputImagen.addEventListener('change', () => {

            const archivo = inputImagen.files[0];

            if (!archivo) {
                preview.innerHTML = `
                    <i class="fa-regular fa-image"></i>
                    <span>Vista previa</span>
                `;
                return;
            }

            // Validar que sea imagen — usar mostrarError en lugar de alert()
            if (!archivo.type.startsWith('image/')) {
                mostrarError('El archivo seleccionado no es una imagen válida.');
                inputImagen.value = '';
                preview.innerHTML = `
                    <i class="fa-regular fa-image"></i>
                    <span>Vista previa</span>
                `;
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML = `<img src="${e.target.result}" alt="Vista previa">`;
            };
            reader.readAsDataURL(archivo);
        });
    }

    /* ══════════════════════════════════════════════════════
       2. CONFIRMACIONES
    ══════════════════════════════════════════════════════ */

    confirmarAccion(
        '.form-eliminar',
        '¿Eliminar <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">Esta acción es permanente. ' +
        'El archivo físico también se eliminará.</small>',
        'Sí, eliminar'
    );

    confirmarAccion(
        '.form-desactivar',
        '¿Desactivar <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">La imagen dejará de mostrarse ' +
        'en el carrusel del portal.</small>',
        'Sí, desactivar'
    );

    confirmarAccion(
        '.form-activar',
        '¿Activar <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">La imagen se mostrará ' +
        'en el carrusel del portal público.</small>',
        'Sí, activar'
    );

    /* ══════════════════════════════════════════════════════
       3. ADVERTIR CAMBIOS SIN GUARDAR
    ══════════════════════════════════════════════════════ */
    iniciarGuardaCambios('form[data-form="carrusel"]', '.btn-neutro[href]');
});
