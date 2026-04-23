/**
 * solicitud.js
 * --------------------------------------------------
 * Control del formulario público de solicitud.
 *
 * Responsabilidades:
 * - Navegación por pasos
 * - Validación de campos vacíos por paso
 * - Verificación AJAX de identificación única (tiempo real)
 * - Barra de progreso
 * - Resumen final
 * - Confirmación al cancelar si hay datos ingresados
 *
 * NO maneja mensajes del servidor
 * (eso es responsabilidad de notificaciones.js)
 *
 * NO duplica reglas del controlador:
 *   formato email, regex documento, min contraseña,
 *   confirmación contraseña → servidor.
 */

document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("formSolicitud");
    if (!form) return;

    const pasos = document.querySelectorAll(".registro-paso");

    const btnAnterior  = document.getElementById("btnAnterior");
    const btnSiguiente = document.getElementById("btnSiguiente");
    const btnEnviar    = document.getElementById("btnEnviar");
    const btnCancelar  = document.getElementById("btnCancelar");

    const progresoRelleno = document.querySelector(".progreso-relleno");
    const progresoTexto   = document.querySelector(".progreso-texto");

    const resumen    = document.getElementById("resumenSolicitud");
    const botonesVer = document.querySelectorAll(".btn-ver");

    const totalPasos = pasos.length;
    let pasoActual = 1;
    let enviando   = false;

    actualizarVista();

    /* ══════════════════════════════════════════
       NAVEGACIÓN
    ══════════════════════════════════════════ */
    btnSiguiente.addEventListener("click", async () => {
        if (!(await validarPasoActual())) return;
        pasoActual++;
        actualizarVista();
    });

    btnAnterior.addEventListener("click", () => {
        pasoActual--;
        actualizarVista();
    });

    /* ══════════════════════════════════════════
       SUBMIT CONTROLADO
    ══════════════════════════════════════════ */
    form.addEventListener("submit", async e => {

        if (enviando) {
            e.preventDefault();
            return;
        }

        if (!(await validarPasoActual())) {
            e.preventDefault();
            return;
        }

        enviando = true;
        btnEnviar.disabled    = true;
        btnEnviar.textContent = "Enviando solicitud...";
    });

    /* ══════════════════════════════════════════
       CANCELAR
    ══════════════════════════════════════════ */
    btnCancelar.addEventListener("click", e => {

        if (!formTieneDatos()) return;

        e.preventDefault();

        mostrarAdvertencia(
            "Los datos ingresados se perderán. ¿Deseas continuar?",
            {
                showCancelButton : true,
                confirmButtonText: "Sí, cancelar",
                cancelButtonText : "Continuar",
            }
        ).then(r => {
            if (r.isConfirmed) window.location.href = btnCancelar.href;
        });
    });

    /* ══════════════════════════════════════════
       VER / OCULTAR CONTRASEÑA
    ══════════════════════════════════════════ */
    botonesVer.forEach(btn => {
        btn.addEventListener("click", () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const visible = input.type === "text";
            input.type    = visible ? "password" : "text";
            btn.textContent = visible ? "Ver" : "Ocultar";
        });
    });

    /* ══════════════════════════════════════════
       FUNCIONES UI
    ══════════════════════════════════════════ */
    function actualizarVista() {

        pasos.forEach(paso => {
            const n = Number(paso.dataset.paso);
            paso.classList.toggle("paso-activo", n === pasoActual);
            paso.classList.toggle("paso-oculto",  n !== pasoActual);
        });

        const porcentaje = (pasoActual / totalPasos) * 100;
        progresoRelleno.style.width  = `${porcentaje}%`;
        progresoTexto.textContent    = `Paso ${pasoActual} de ${totalPasos}`;

        btnAnterior.style.display  = pasoActual === 1          ? "none"        : "inline-flex";
        btnSiguiente.style.display = pasoActual === totalPasos ? "none"        : "inline-flex";
        btnEnviar.classList.toggle("oculto", pasoActual !== totalPasos);

        if (pasoActual === totalPasos) generarResumen();
    }

    /* ══════════════════════════════════════════
       VALIDACIONES POR PASO
       Solo verifica campos vacíos + AJAX para
       identificación. Formato y reglas de negocio
       → controlador.
    ══════════════════════════════════════════ */
    async function validarPasoActual() {

        const paso   = document.querySelector(`.registro-paso[data-paso="${pasoActual}"]`);
        const campos = paso.querySelectorAll("input[required], select[required]");

        for (const campo of campos) {
            if (!campo.value.trim()) {
                campo.focus();
                mostrarAdvertencia("Completa todos los campos obligatorios.");
                return false;
            }
        }

        // Paso 2: verificar identificación única en tiempo real
        if (pasoActual === 2) {
            const resultado = await verificarIdentificacion(
                document.getElementById("identificacion").value.trim()
            );
            if (!resultado.valido) {
                mostrarError(resultado.mensaje || "Este documento ya está registrado.");
                return false;
            }
        }

        return true;
    }

    /* ══════════════════════════════════════════
       AJAX — IDENTIFICACIÓN ÚNICA
       Llama al endpoint POST /registro/validar
       que ya existe en el controlador.
    ══════════════════════════════════════════ */
    async function verificarIdentificacion(id) {
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? "";
            const resp = await fetch("/registro/validar", {
                method : "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                    "Accept"      : "application/json",
                },
                body: JSON.stringify({ identificacion: id }),
            });
            if (!resp.ok) throw new Error("Error de red");
            return await resp.json();
        } catch {
            // Si falla la red dejar pasar — el servidor validará al enviar
            return { valido: true };
        }
    }

    /* ══════════════════════════════════════════
       FEEDBACK INLINE — IDENTIFICACIÓN
       Se activa al salir del campo (blur).
    ══════════════════════════════════════════ */
    const campoId = document.getElementById("identificacion");
    if (campoId) {

        campoId.addEventListener("blur", async () => {
            const id = campoId.value.trim();
            quitarFeedback(campoId);
            if (!id) return;

            campoId.style.borderColor = "#f59e0b";
            const resultado = await verificarIdentificacion(id);
            campoId.style.borderColor = "";

            if (!resultado.valido) {
                ponerFeedbackError(campoId, resultado.mensaje || "Este documento ya está registrado.");
            } else {
                ponerFeedbackOk(campoId, "Documento disponible.");
            }
        });

        campoId.addEventListener("input", () => quitarFeedback(campoId));
    }

    function ponerFeedbackError(campo, mensaje) {
        quitarFeedback(campo);
        campo.style.borderColor = "#ef4444";
        const span       = document.createElement("span");
        span.className   = "campo-feedback campo-feedback--error";
        span.textContent = mensaje;
        campo.parentNode.insertBefore(span, campo.nextSibling);
    }

    function ponerFeedbackOk(campo, mensaje) {
        quitarFeedback(campo);
        campo.style.borderColor = "#22c55e";
        const span       = document.createElement("span");
        span.className   = "campo-feedback campo-feedback--ok";
        span.textContent = mensaje;
        campo.parentNode.insertBefore(span, campo.nextSibling);
    }

    function quitarFeedback(campo) {
        campo.style.borderColor = "";
        campo.parentNode.querySelector(".campo-feedback")?.remove();
    }

    /* ══════════════════════════════════════════
       RESUMEN (paso 6)
    ══════════════════════════════════════════ */
    function generarResumen() {
        const data  = new FormData(form);
        const roles = { docente: "Docente", estudiante: "Estudiante" };

        resumen.innerHTML = `
            <div class="resumen-bloque">
                <h4>Datos personales</h4>
                <p><strong>Nombre:</strong> ${data.get("nombre")}</p>
                <p><strong>Apellidos:</strong> ${data.get("apellidos")}</p>
            </div>
            <div class="resumen-bloque">
                <h4>Información institucional</h4>
                <p><strong>Identificación:</strong> ${data.get("identificacion")}</p>
                <p><strong>Ocupación:</strong> ${roles[data.get("rol")] ?? data.get("rol")}</p>
            </div>
            <div class="resumen-bloque">
                <h4>Contacto</h4>
                <p><strong>Correo:</strong> ${data.get("correo")}</p>
                <p><strong>Ubicación:</strong> ${data.get("ubicacion")}</p>
                <p><strong>Contacto:</strong> ${data.get("contacto")}</p>
            </div>
        `;
    }

    /* ══════════════════════════════════════════
       HELPER
    ══════════════════════════════════════════ */
    function formTieneDatos() {
        return [...form.querySelectorAll("input, select")]
            .some(i => i.value.trim() !== "");
    }

});
