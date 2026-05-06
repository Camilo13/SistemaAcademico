/* ============================================================
   public/js/global/session-timeout.js
   Timeout de sesión por inactividad.
   - Avisa a los 18 min con modal SweetAlert2 + cuenta regresiva.
   - Redirige a /login?sesion=expirada si el usuario no responde.
   - Compatible con modo oscuro (html.modo-oscuro).
   Requiere: SweetAlert2 cargado antes que este script.
   ============================================================ */

(function () {

    /* Seguridad: no ejecutar dentro de iframes */
    if (window.self !== window.top) return;
    if (!document.body) return;

    const TIEMPO_AVISO   = 1080000; /* 18 min — dispara el modal              */
    const DURACION_MODAL =  120000; /* 2 min  — cuenta regresiva del modal    */

    let timerInactividad = null;

    /* ── Detecta el tema activo ──────────────────────────── */
    function esModoOscuro() {
        return document.documentElement.classList.contains('modo-oscuro');
    }

    /* ── Sobreescrituras visuales del modal en modo oscuro ── */
    function opcionesTema() {
        if (!esModoOscuro()) return {};
        return {
            background  : '#1a2c26',
            color       : '#e2e8f0',
            customClass : { popup: 'swal-oscuro' },
        };
    }

    /* ── Modal de advertencia con cuenta regresiva ───────── */
    function mostrarAviso() {
        let intervaloRegresiva = null;

        Swal.fire({
            title             : 'Sesión por expirar',
            html              : 'Tu sesión expirará en <strong><span id="cuenta-regresiva">120</span>s</strong>.<br>¿Deseas continuar?',
            icon              : 'warning',
            showConfirmButton : true,
            confirmButtonText : 'Sí, continuar',
            confirmButtonColor: '#065f46',
            showCancelButton  : false,
            allowOutsideClick : false,
            allowEscapeKey    : false,
            timer             : DURACION_MODAL,
            timerProgressBar  : true,
            ...opcionesTema(),

            didOpen() {
                let segundos = 120;
                const span   = document.getElementById('cuenta-regresiva');

                intervaloRegresiva = setInterval(function () {
                    segundos -= 1;
                    if (span) span.textContent = segundos;
                    if (segundos <= 0) clearInterval(intervaloRegresiva);
                }, 1000);
            },

            willClose() {
                clearInterval(intervaloRegresiva);
            },

        }).then(function (resultado) {

            if (resultado.isConfirmed) {
                /* Usuario eligió continuar — renueva el contador y hace ping al servidor */
                reiniciarTimer();
                fetch('/ping-sesion', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                }).catch(function () { /* silencioso: la sesión se renueva igual */ });

            } else {
                /* Timer agotado sin confirmación → redirige al login */
                window.location.href = '/login?sesion=expirada';
            }
        });
    }

    /* ── Reinicia el temporizador de inactividad ─────────── */
    function reiniciarTimer() {
        clearTimeout(timerInactividad);
        timerInactividad = setTimeout(mostrarAviso, TIEMPO_AVISO);
    }

    /* ── Eventos que indican actividad del usuario ───────── */
    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function (evento) {
        document.addEventListener(evento, reiniciarTimer, { passive: true });
    });

    /* ── Arranque inicial ────────────────────────────────── */
    reiniciarTimer();

})();
