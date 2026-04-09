/**
 * componentes/academico.js
 * ─────────────────────────────────────────────────────────────
 * Lógica compartida para TODOS los módulos académicos y admin.
 *
 * Exporta (globalmente):
 *   confirmarAccion(selector, mensaje, textoBtn, campoNombre?)
 *     Asocia confirmación SweetAlert a formularios de acción.
 *
 *   iniciarGuardaCambios(selectorForm, selectorCancelar)
 *     Intercepta "Cancelar" y avisa si hay cambios sin guardar.
 *
 *   iniciarConfirmacionesGenericas()
 *     Lee atributos data-confirmar-* del HTML para registrar
 *     confirmaciones sin necesidad de un JS por módulo.
 *     Úsala en módulos genéricos (grado, grupo, materia…).
 *
 * Requiere: global/notificaciones.js cargado antes.
 * Cargado en: cada vista mediante @push('scripts').
 * ─────────────────────────────────────────────────────────────
 */

/* ══════════════════════════════════════════════════════════
   confirmarAccion
   ══════════════════════════════════════════════════════════

   Asocia una confirmación SweetAlert a un conjunto de forms.

   @param {string} selectorForms  Selector CSS de los <form>
   @param {string} mensaje        HTML del cuerpo (acepta ${nombre})
   @param {string} textoBoton     Texto del botón de confirmar
   @param {string} [campoNombre]  data-attr con el nombre del registro
                                  (default: 'nombre')

   Ejemplo en Blade:
     <form class="form-eliminar"
           data-nombre="{{ $grado->nombre }}"
           method="POST" action="...">
       @csrf @method('DELETE')
       <button class="btn-icono eliminar">…</button>
     </form>
   ══════════════════════════════════════════════════════════ */
function confirmarAccion(selectorForms, mensaje, textoBoton, campoNombre = 'nombre') {

    document.querySelectorAll(selectorForms).forEach(form => {

        form.addEventListener('submit', e => {
            e.preventDefault();

            const nombre = form.dataset[campoNombre] ?? 'este registro';

            mostrarAdvertencia(
                mensaje.replace(/\$\{nombre\}/g, nombre),
                {
                    showCancelButton : true,
                    confirmButtonText: textoBoton,
                    cancelButtonText : 'Cancelar',
                }
            ).then(resultado => {
                if (resultado.isConfirmed) form.submit();
            });
        });
    });
}

/* ══════════════════════════════════════════════════════════
   iniciarGuardaCambios
   ══════════════════════════════════════════════════════════

   Vigila un formulario y avisa al usuario si intenta
   abandonar la vista con cambios sin guardar.

   @param {string} selectorForm      Selector del <form data-form="…">
   @param {string} selectorCancelar  Selector del enlace Cancelar

   Ejemplo en Blade:
     <form data-form="grado" method="POST">…</form>
     <a class="btn btn-neutro" href="{{ route('...index') }}">Cancelar</a>
   ══════════════════════════════════════════════════════════ */
function iniciarGuardaCambios(selectorForm, selectorCancelar) {

    const formulario  = document.querySelector(selectorForm);
    const btnCancelar = document.querySelector(selectorCancelar);

    if (!formulario || !btnCancelar) return;

    // Capturar valores iniciales de todos los campos
    const campos = formulario.querySelectorAll('input, select, textarea');
    const inicial = {};

    campos.forEach(c => {
        inicial[c.name] = c.type === 'checkbox' ? c.checked : c.value;
    });

    const hayCambios = () =>
        [...campos].some(c =>
            c.type === 'checkbox'
                ? c.checked !== inicial[c.name]
                : c.value   !== inicial[c.name]
        );

    btnCancelar.addEventListener('click', e => {
        if (!hayCambios()) return;

        e.preventDefault();

        mostrarAdvertencia(
            'Tienes cambios sin guardar. ¿Quieres salir sin guardarlos?',
            {
                showCancelButton : true,
                confirmButtonText: 'Sí, salir',
                cancelButtonText : 'Seguir editando',
            }
        ).then(r => {
            if (r.isConfirmed) window.location.href = btnCancelar.href;
        });
    });
}

/* ══════════════════════════════════════════════════════════
   iniciarConfirmacionesGenericas
   ══════════════════════════════════════════════════════════

   Alternativa sin-JS-por-módulo para entidades genéricas.
   El form en el Blade declara sus mensajes como data-attrs
   y esta función los lee automáticamente.

   Atributos requeridos en el <form>:
     data-confirmar        (presencia activa la lógica)
     data-mensaje          HTML del cuerpo de confirmación
     data-boton            Texto del botón de confirmar
     data-nombre           Nombre del registro (opcional)

   Ejemplo en Blade (grado, grupo, materia, asignacion, nota):
     <form class="form-eliminar"
           data-confirmar
           data-mensaje="¿Eliminar el grado <strong>${nombre}</strong>? Esta acción no se puede deshacer."
           data-boton="Sí, eliminar"
           data-nombre="{{ $grado->nombre }}"
           method="POST" action="…">
       @csrf @method('DELETE')
       <button …>Eliminar</button>
     </form>
   ══════════════════════════════════════════════════════════ */
function iniciarConfirmacionesGenericas() {

    document.querySelectorAll('form[data-confirmar]').forEach(form => {

        form.addEventListener('submit', e => {
            e.preventDefault();

            const nombre  = form.dataset.nombre  ?? 'este registro';
            const mensaje = (form.dataset.mensaje ?? '¿Confirmar esta acción?')
                                .replace(/\$\{nombre\}/g, nombre);
            const boton   = form.dataset.boton   ?? 'Confirmar';

            mostrarAdvertencia(mensaje, {
                showCancelButton : true,
                confirmButtonText: boton,
                cancelButtonText : 'Cancelar',
            }).then(r => {
                if (r.isConfirmed) form.submit();
            });
        });
    });
}
