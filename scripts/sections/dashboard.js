/**
 * @file dashboard.js
 * @description Gestion du tableau de bord administrateur
 */

import { ProjectManager } from './projects.js';

class DashboardManager {
    constructor() {
        this.DOM = {
            sidebar: document.querySelector('.dashboard-sidebar'),
            menuToggle: document.querySelector('.menu-toggle'),
            themeToggle: document.querySelector('.theme-toggle'),
            navLinks: document.querySelectorAll('.nav-link'),
            contentArea: document.querySelector('.dashboard-content')
        };

        this.currentSection = new URLSearchParams(window.location.search).get('section') || 'home';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadSection(this.currentSection);
        this.setupThemeToggle();
        this.setupResponsiveMenu();
    }

    setupEventListeners() {
        // Gestion de la navigation
        this.DOM.navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const section = new URL(link.href).searchParams.get('section') || 'home';
                this.loadSection(section);
                this.updateActiveLink(link);
                history.pushState({}, '', link.href);
            });
        });

        // Gestion du retour arrière navigateur
        window.addEventListener('popstate', () => {
            const section = new URLSearchParams(window.location.search).get('section') || 'home';
            this.loadSection(section);
        });
    }

    async loadSection(section) {
        console.log(`Chargement de la section: ${section}`);
        try {
            const response = await fetch(`dashboard.php?section=${section}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                console.error(`Erreur HTTP: ${response.status}`);
                throw new Error('Section non trouvée');
            }
            
            const content = await response.text();
            this.DOM.contentArea.innerHTML = content;
            
            // Mise à jour du titre
            document.querySelector('.header-title h1').textContent = 
                this.getSectionTitle(section);
            
            // Initialisation des fonctionnalités spécifiques à la section
            console.log(`Initialisation des fonctionnalités pour: ${section}`);
            this.initSectionFeatures(section);
        } catch (error) {
            console.error('Erreur de chargement:', error);
            this.DOM.contentArea.innerHTML = '<div class="error-message">Erreur de chargement du contenu</div>';
        }
    }

    getSectionTitle(section) {
        const titles = {
            'home': 'Tableau de bord',
            'profile': 'Gestion du profil',
            'projects': 'Gestion des projets',
            'technologies': 'Gestion des technologies',
            'compétences': 'Gestion des compétences'
        };
        return titles[section] || 'Tableau de bord';
    }

    initSectionFeatures(section) {
        switch(section) {
            case 'home':
                this.initHomeSection();
                break;
            case 'profile':
                this.initProfileSection();
                break;
            case 'projects':
                this.initProjectsSection();
                break;
            case 'technologies':
                this.initTechnologiesSection();
                break;
            case 'competences':
                this.initCompetencesSection();
                break;
        }
    }

    setupThemeToggle() {
        this.DOM.themeToggle?.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', 
                document.body.classList.contains('dark-mode'));
        });
    }

    setupResponsiveMenu() {
        // Gestion du menu responsive
        this.DOM.menuToggle?.addEventListener('click', () => {
            this.DOM.sidebar.classList.toggle('active');
        });

        // Fermer le menu au clic en dehors
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !this.DOM.sidebar.contains(e.target) && 
                !this.DOM.menuToggle.contains(e.target)) {
                this.DOM.sidebar.classList.remove('active');
            }
        });
    }

    updateActiveLink(activeLink) {
        this.DOM.navLinks.forEach(link => {
            link.parentElement.classList.remove('active');
        });
        activeLink.parentElement.classList.add('active');
    }

    // Méthodes spécifiques aux sections
    initHomeSection() {
        // Initialisation des graphiques et statistiques
        this.loadStatistics();
        this.initActivityLog();
    }

    async loadStatistics() {
        try {
            const response = await fetch('includes/dashboard/get_statistics.php');
            const stats = await response.json();
            
            // Mise à jour des compteurs
            document.querySelectorAll('[data-stat]').forEach(element => {
                const stat = element.dataset.stat;
                if (stats[stat]) {
                    element.textContent = stats[stat];
                }
            });
        } catch (error) {
            console.error('Erreur de chargement des statistiques:', error);
        }
    }

    initActivityLog() {
        // Initialisation du log d'activité
        const activityLog = document.querySelector('.activity-log');
        if (activityLog) {
            this.loadActivityLog();
        }
    }

    async loadActivityLog() {
        try {
            const response = await fetch('includes/dashboard/get_activity_log.php');
            const activities = await response.json();
            
            // Mise à jour du log d'activité
            const logContainer = document.querySelector('.activity-log__content');
            if (logContainer && activities.length) {
                logContainer.innerHTML = activities.map(activity => `
                    <div class="activity-item">
                        <span class="activity-time">${activity.time}</span>
                        <span class="activity-text">${activity.description}</span>
                    </div>
                `).join('');
            }
        } catch (error) {
            console.error('Erreur de chargement du log:', error);
        }
    }

    initActivityFilters() {
        const filters = document.querySelectorAll('.activity-filter');
        filters?.forEach(filter => {
            filter.addEventListener('click', () => {
                const type = filter.dataset.type;
                const activities = document.querySelectorAll('.activity-item');
                
                activities.forEach(activity => {
                    if (type === 'all' || activity.dataset.type === type) {
                        activity.style.display = 'flex';
                    } else {
                        activity.style.display = 'none';
                    }
                });
            });
        });
    }

    initProfileSection() {
        import('./profile.js').then(module => {
            new module.ProfileManager();
        }).catch(error => {
            console.error("Erreur lors du chargement du module profile:", error);
        });
    }

    initProjectsSection() {
        new ProjectManager();
    }

    initTechnologiesSection() {
        console.log("Initialisation de la section Technologies");
        import('./technologies.js').then(module => {
            console.log("Module technologies.js chargé");
            new module.TechnologyManager();
        }).catch(error => {
            console.error("Erreur lors du chargement du module technologies:", error);
        });
    }

    initCompetencesSection() {
        console.log("Initialisation de la section Compétences");
        import('./competences.js').then(module => {
            console.log("Module competences.js chargé");
            new module.CompetenceManager();
        }).catch(error => {
            console.error("Erreur lors du chargement du module competences:", error);
        });
    }
}

// Garder uniquement cette initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.dashboardManager = new DashboardManager();
}); 