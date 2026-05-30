(function () {
    var STORAGE_KEY = 'playgg-theme';

    function getTheme() {
        try {
            var stored = localStorage.getItem(STORAGE_KEY);
            return stored === 'light' ? 'light' : 'dark';
        } catch (e) {
            return 'dark';
        }
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {}
        updateToggleButtons(theme);
    }

    function updateToggleButtons(theme) {
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            var isLight = theme === 'light';
            btn.setAttribute('aria-pressed', isLight ? 'true' : 'false');
            btn.setAttribute('aria-label', isLight ? 'Включить тёмную тему' : 'Включить светлую тему');
            btn.setAttribute('title', isLight ? 'Тёмная тема' : 'Светлая тема');
        });
    }

    function toggleTheme() {
        applyTheme(getTheme() === 'dark' ? 'light' : 'dark');
    }

    document.addEventListener('DOMContentLoaded', function () {
        applyTheme(getTheme());
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            btn.addEventListener('click', toggleTheme);
        });
    });
})();
