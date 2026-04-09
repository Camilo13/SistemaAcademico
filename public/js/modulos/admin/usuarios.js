/**
 * modulos/admin/usuarios.js
 * ─────────────────────────────────────────────────────────────
 * Lógica del módulo de gestión de usuarios (admin).
 *
 * Responsabilidades:
 *   - Confirmaciones de activar / desactivar con consecuencias
 *   - Fortaleza de contraseña en tiempo real (edit y create)
 *   - Guardar cambios al salir del formulario principal
 *
 * Requiere:
 *   componentes/academico.js    (confirmarAccion, iniciarGuardaCambios)
 *   global/notificaciones.js    (mostrarAdvertencia, etc.)
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Confirmar desactivar ── */
    confirmarAccion(
        '.form-desactivar',
        '¿Desactivar a <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">El usuario no podrá iniciar sesión ' +
        'hasta que sea reactivado.</small>',
        'Sí, desactivar'
    );

    /* ── Confirmar activar ── */
    confirmarAccion(
        '.form-activar',
        '¿Activar a <strong>${nombre}</strong>?<br>' +
        '<small style="color:#6b7280;">El usuario podrá volver a iniciar sesión.</small>',
        'Sí, activar'
    );

    /* ── Guardar cambios en formulario principal (edit) ── */
    iniciarGuardaCambios('form[data-form="usuario"]', '.btn-neutro[href]');

    /* ── Fortaleza de contraseña en tiempo real ── */
    iniciarFortalezaPassword();
});

/* ══════════════════════════════════════════════════════════
   Indicador de fortaleza de contraseña
   Evalúa el campo #password y actualiza:
     .fortaleza-barra  → barra de color (débil/media/fuerte)
     .fortaleza-label  → texto descriptivo
══════════════════════════════════════════════════════════ */
function iniciarFortalezaPassword() {

    const campo = document.getElementById('password');
    const barra = document.getElementById('fortaleza-barra');
    const label = document.getElementById('fortaleza-label');

    if (!campo || !barra) return;

    campo.addEventListener('input', () => {
        const valor = campo.value;

        if (!valor) {
            barra.className    = 'fortaleza-barra';
            if (label) label.textContent = '';
            return;
        }

        const { clase, texto } = evaluarFortaleza(valor);

        barra.className    = `fortaleza-barra ${clase}`;
        if (label) {
            label.textContent = texto;
            label.style.color = claseAColor(clase);
        }
    });
}

/**
 * Evalúa la fortaleza de una contraseña.
 * Criterios: longitud, mayúsculas, números, caracteres especiales.
 * @param {string} valor
 * @returns {{ clase: string, texto: string }}
 */
function evaluarFortaleza(valor) {
    let puntos = 0;

    if (valor.length >= 8)                 puntos++;
    if (valor.length >= 12)                puntos++;
    if (/[A-Z]/.test(valor))               puntos++;
    if (/[0-9]/.test(valor))               puntos++;
    if (/[^A-Za-z0-9]/.test(valor))        puntos++;

    if (puntos <= 1) return { clase: 'debil',  texto: 'Contraseña débil'   };
    if (puntos <= 3) return { clase: 'media',  texto: 'Contraseña regular' };
    return               { clase: 'fuerte', texto: 'Contraseña fuerte'  };
}

function claseAColor(clase) {
    return { debil: '#dc2626', media: '#d97706', fuerte: '#16a34a' }[clase] ?? '';
}
