/**
 * global/notificaciones.js
 * ─────────────────────────────────────────────────────────────
 * Sistema global de notificaciones vía SweetAlert2.
 *
 * Responsabilidades:
 *   1. Leer window.APP_ALERTS (inyectado por el layout Blade)
 *      y mostrar la alerta correspondiente al cargar la página.
 *   2. Exponer las funciones base para que cualquier módulo
 *      pueda lanzar alertas programáticamente.
 *
 * Cargado en: todos los layouts autenticados, antes del @stack('scripts').
 * Requiere:   SweetAlert2 CDN cargado ANTES en el <head>.
 *
 * Uso en Blade (layout):
 *   <script>
 *     window.APP_ALERTS = {
 *       exito:      "{{ session('exito') }}",
 *       error:      "{{ $errors->first() }}",
 *       errores:    @json($errors->all()),
 *     };
 *   </script>
 * ─────────────────────────────────────────────────────────────
 */

document.addEventListener('DOMContentLoaded', () => {

    if (!window.APP_ALERTS) return;

    const { exito, error, info, advertencia, errores } = window.APP_ALERTS;

    // Éxito — redirect()->with('exito', '...')
    if (exito)      mostrarExito(exito);

    // Error único — back()->withErrors(['clave' => '...'])
    if (error)      mostrarError(error);

    // Información general
    if (info)       mostrarInfo(info);

    // Advertencia general
    if (advertencia) mostrarAdvertencia(advertencia);

    // Múltiples errores de validación
    if (errores && errores.length > 0) {
        mostrarAdvertencia(
            '<ul style="text-align:left;margin:0;padding-left:1.2rem">' +
            errores.map(e => `<li>${e}</li>`).join('') +
            '</ul>',
            {
                title            : 'Corrige los siguientes errores',
                showConfirmButton : true,
                confirmButtonText: 'Entendido',
            }
        );
    }
});

/* ═══════════════════════════════════════════════════════════
   FUNCIONES BASE — disponibles globalmente
   ═══════════════════════════════════════════════════════════ */

/**
 * Alerta de éxito (verde).
 * @param {string} mensaje - HTML del mensaje.
 */
function mostrarExito(mensaje) {
    return Swal.fire({
        icon             : 'success',
        title            : 'Listo',
        html             : mensaje,
        confirmButtonText: 'Aceptar',
        timer            : 3500,
        timerProgressBar : true,
    });
}

/**
 * Alerta de error (rojo).
 * @param {string} mensaje
 */
function mostrarError(mensaje) {
    return Swal.fire({
        icon             : 'error',
        title            : 'Error',
        html             : mensaje,
        confirmButtonText: 'Aceptar',
    });
}

/**
 * Alerta informativa (azul).
 * @param {string} mensaje
 */
function mostrarInfo(mensaje) {
    return Swal.fire({
        icon             : 'info',
        title            : 'Información',
        html             : mensaje,
        confirmButtonText: 'Aceptar',
    });
}

/**
 * Alerta de advertencia con opciones extendidas.
 * Usada para confirmaciones (confirmarAccion la llama internamente).
 *
 * @param {string} mensaje
 * @param {object} opciones - Sobreescribe valores por defecto de Swal.
 * @returns {Promise<SweetAlertResult>}
 */
function mostrarAdvertencia(mensaje, opciones = {}) {
    return Swal.fire({
        icon             : 'warning',
        title            : opciones.title            ?? 'Advertencia',
        html             : mensaje,
        showCancelButton : opciones.showCancelButton  ?? false,
        confirmButtonText: opciones.confirmButtonText ?? 'Aceptar',
        cancelButtonText : opciones.cancelButtonText  ?? 'Cancelar',
        timer            : opciones.timer             ?? undefined,
        timerProgressBar : !!opciones.timer,
    });
}
