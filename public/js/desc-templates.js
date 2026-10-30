/* Comportement des templates de description (onglets). Partagé Daebak / Be.Bright. */
(function () {
    'use strict';
    document.querySelectorAll('[data-pd-tabs]').forEach(function (root) {
        var tabs = root.querySelectorAll('[data-pd-tab]');
        var panels = root.querySelectorAll('[data-pd-panel]');

        function activate(index) {
            tabs.forEach(function (t) {
                var on = t.dataset.pdTab === String(index);
                t.classList.toggle('is-active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
                if (on) t.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
            });
            panels.forEach(function (p) { p.classList.toggle('is-active', p.dataset.pdPanel === String(index)); });
        }

        tabs.forEach(function (t, i) {
            t.addEventListener('click', function () { activate(i); });
            t.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                    var next = (i + (e.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length;
                    activate(next);
                    tabs[next].focus();
                }
            });
        });
    });
})();
