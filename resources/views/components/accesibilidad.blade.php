{{-- ═══════════════════════════════════════════════════════════
     Componente: Panel flotante de accesibilidad
     Controles: modo oscuro (luna/sol) y tamaño de fuente (A- A A+).
     Preferencias persisten en localStorage y se aplican al <html>
     sin recargar la página.
═══════════════════════════════════════════════════════════ --}}

<div class="acc-widget" id="accWidget" aria-label="Herramientas de accesibilidad">

    {{-- Panel de opciones — se despliega hacia arriba --}}
    <div class="acc-panel"
         id="accPanel"
         role="region"
         aria-label="Opciones de accesibilidad"
         hidden>

        {{-- Control 1: alternar modo oscuro / claro --}}
        <div class="acc-opcion">
            <span class="acc-etiqueta" id="accLabelTema">Modo oscuro</span>
            <button class="acc-toggle"
                    id="accToggleTema"
                    role="switch"
                    aria-checked="false"
                    aria-labelledby="accLabelTema"
                    title="Alternar modo oscuro">
                <i class="fas fa-moon" id="accIconTema" aria-hidden="true"></i>
            </button>
        </div>

        <hr class="acc-separador">

        {{-- Control 2: tamaño de fuente en tres niveles --}}
        <div class="acc-opcion">
            <span class="acc-etiqueta">Tamaño de texto</span>
            <div class="acc-fuente-btns"
                 role="group"
                 aria-label="Ajuste de tamaño de texto">
                <button class="acc-fuente-btn"
                        id="accFuenteS"
                        aria-label="Texto pequeño"
                        title="Texto pequeño">A-</button>
                <button class="acc-fuente-btn"
                        id="accFuenteM"
                        aria-label="Texto normal"
                        title="Texto normal">A</button>
                <button class="acc-fuente-btn"
                        id="accFuenteL"
                        aria-label="Texto grande"
                        title="Texto grande">A+</button>
            </div>
        </div>

    </div>

    {{-- Botón circular disparador --}}
    <button class="acc-btn"
            id="accBtn"
            aria-label="Abrir panel de accesibilidad"
            aria-expanded="false"
            aria-controls="accPanel"
            title="Accesibilidad">
        <i class="fas fa-universal-access" aria-hidden="true"></i>
    </button>

</div>

<script>
(function () {
    'use strict';

    /* ── Constantes ──────────────────────────────────────────── */
    var HTML        = document.documentElement;
    var KEY_TEMA    = 'acc_tema';
    var KEY_FUENTE  = 'acc_fuente';
    var FUENTES     = ['pequena', 'normal', 'grande'];

    /* ── Sincronizar el toggle de tema con el estado actual ─── */
    function sincronizarTema(oscuro) {
        var btn   = document.getElementById('accToggleTema');
        var icono = document.getElementById('accIconTema');
        var label = document.getElementById('accLabelTema');
        if (!btn) return;
        btn.setAttribute('aria-checked', String(oscuro));
        icono.className   = oscuro ? 'fas fa-sun' : 'fas fa-moon';
        label.textContent = oscuro ? 'Modo claro' : 'Modo oscuro';
    }

    /* ── Marcar el botón de fuente activo ───────────────────── */
    function sincronizarFuente(nivel) {
        var mapa = { pequena: 'accFuenteS', normal: 'accFuenteM', grande: 'accFuenteL' };
        FUENTES.forEach(function (f) {
            var el = document.getElementById(mapa[f]);
            if (el) el.classList.toggle('acc-fuente-activo', f === nivel);
        });
    }

    /* ── Aplicar clase de fuente al <html> ──────────────────── */
    function aplicarFuente(nivel) {
        FUENTES.forEach(function (f) { HTML.classList.remove('fuente-' + f); });
        HTML.classList.add('fuente-' + nivel);
    }

    /* ── Inicializar controles interactivos ─────────────────── */
    function init() {
        var btn    = document.getElementById('accBtn');
        var panel  = document.getElementById('accPanel');
        var widget = document.getElementById('accWidget');

        /* Sincronizar UI con estado ya aplicado en el <head> */
        sincronizarTema(HTML.classList.contains('modo-oscuro'));
        sincronizarFuente(localStorage.getItem(KEY_FUENTE) || 'normal');

        /* Abrir / cerrar panel */
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var abrir   = panel.hidden;
            panel.hidden = !abrir;
            btn.setAttribute('aria-expanded', String(abrir));
        });

        /* Cerrar al hacer clic fuera del widget */
        document.addEventListener('click', function (e) {
            if (!widget.contains(e.target)) {
                panel.hidden = true;
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        /* Cerrar con tecla Escape */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !panel.hidden) {
                panel.hidden = true;
                btn.setAttribute('aria-expanded', 'false');
                btn.focus();
            }
        });

        /* Toggle modo oscuro */
        document.getElementById('accToggleTema').addEventListener('click', function () {
            var oscuro = !HTML.classList.contains('modo-oscuro');
            HTML.classList.toggle('modo-oscuro', oscuro);
            localStorage.setItem(KEY_TEMA, oscuro ? 'oscuro' : 'claro');
            sincronizarTema(oscuro);
        });

        /* Botones de fuente */
        document.getElementById('accFuenteS').addEventListener('click', function () {
            aplicarFuente('pequena');
            localStorage.setItem(KEY_FUENTE, 'pequena');
            sincronizarFuente('pequena');
        });
        document.getElementById('accFuenteM').addEventListener('click', function () {
            aplicarFuente('normal');
            localStorage.setItem(KEY_FUENTE, 'normal');
            sincronizarFuente('normal');
        });
        document.getElementById('accFuenteL').addEventListener('click', function () {
            aplicarFuente('grande');
            localStorage.setItem(KEY_FUENTE, 'grande');
            sincronizarFuente('grande');
        });
    }

    /* Ejecutar init sin importar en qué punto del parsing está el DOM */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
</script>
