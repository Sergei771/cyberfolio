// Nouveau fichier pour la gestion du thème
export class ThemeManager {
    constructor() {
        this.isDark = localStorage.getItem('darkMode') === 'true';
        this.toggleBtn = document.createElement('button');
        this.init();
    }

    init() {
        this.createToggleButton();
        this.applyTheme();
        this.toggleBtn.addEventListener('click', () => this.toggleTheme());
        
        // Vérifier la préférence système
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        prefersDark.addEventListener('change', (e) => {
            if (localStorage.getItem('darkMode') === null) {
                this.isDark = e.matches;
                this.applyTheme();
            }
        });
    }

    createToggleButton() {
        this.toggleBtn.className = 'theme-toggle';
        this.toggleBtn.setAttribute('aria-label', 'Basculer le mode sombre');
        this.toggleBtn.innerHTML = `
            <svg class="theme-icon" viewBox="0 0 24 24">
                <path class="sun" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                <path class="moon" d="M12 3v2M12 19v2M19 12h2M3 12h2"/>
            </svg>
        `;
        document.querySelector('.nav__container').appendChild(this.toggleBtn);
    }

    toggleTheme() {
        this.isDark = !this.isDark;
        localStorage.setItem('darkMode', this.isDark);
        this.applyTheme();
    }

    applyTheme() {
        document.body.classList.toggle('dark-mode', this.isDark);
        this.toggleBtn.classList.toggle('dark', this.isDark);
    }
} 