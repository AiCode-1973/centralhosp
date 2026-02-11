// Theme Toggle System
(function() {
    'use strict';
    
    const THEME_KEY = 'centralhosp_theme';
    
    // Inicializar tema ao carregar a página
    function initTheme() {
        const savedTheme = localStorage.getItem(THEME_KEY);
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        updateToggleIcon();
    }
    
    // Alternar tema
    function toggleTheme() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem(THEME_KEY, isDark ? 'dark' : 'light');
        updateToggleIcon();
    }
    
    // Atualizar ícone do botão
    function updateToggleIcon() {
        const isDark = document.documentElement.classList.contains('dark');
        const sunIcon = document.getElementById('theme-toggle-sun-icon');
        const moonIcon = document.getElementById('theme-toggle-moon-icon');
        
        if (sunIcon && moonIcon) {
            if (isDark) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        }
    }
    
    // Expor função global para o botão
    window.toggleTheme = toggleTheme;
    
    // Inicializar tema quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }
})();
