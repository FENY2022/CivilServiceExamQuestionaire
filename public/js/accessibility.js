(function () {
    'use strict';
    const STORAGE_KEY = 'civserv:fontScale';
    const MIN = 75;
    const MAX = 200;
    const BASE = 16;

    function getStored() {
        try {
            const raw = parseFloat(localStorage.getItem(STORAGE_KEY));
            if (!Number.isNaN(raw) && raw >= MIN && raw <= MAX) return raw;
        } catch (error) { /* ignore */ }
        return 100;
    }

    function apply(scale) {
        document.documentElement.style.fontSize = ((BASE * scale) / 100) + 'px';
    }

    function save(scale) {
        try { localStorage.setItem(STORAGE_KEY, String(scale)); } catch (error) { /* ignore */ }
    }

    function updateFill(range) {
        const fill = ((Number(range.value) - MIN) / (MAX - MIN)) * 100;
        range.style.setProperty('--fill', fill + '%');
    }

    function init() {
        const widget = document.getElementById('textSizeWidget');
        const toggle = document.getElementById('textSizeToggle');
        const panel = document.getElementById('textSizePanel');
        const range = document.getElementById('textSizeRange');
        const value = document.getElementById('textSizeValue');
        const reset = document.getElementById('textSizeReset');
        if (!widget || !toggle || !panel || !range || !value || !reset) return;

        const scale = getStored();
        apply(scale);
        range.value = String(scale);
        value.textContent = scale + '%';
        updateFill(range);

        range.addEventListener('input', () => {
            const current = Number(range.value);
            apply(current);
            save(current);
            value.textContent = current + '%';
            updateFill(range);
        });

        reset.addEventListener('click', () => {
            range.value = '100';
            apply(100);
            save(100);
            value.textContent = '100%';
            updateFill(range);
            range.focus();
        });

        toggle.addEventListener('click', () => {
            if (panel.hidden) {
                panel.hidden = false;
                toggle.setAttribute('aria-expanded', 'true');
            } else {
                panel.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('click', event => {
            if (!widget.contains(event.target)) {
                panel.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                panel.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
