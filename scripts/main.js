/**
 * @file main.js
 * @description Point d'entrée principal de l'application
 * @module MainApp
 */

import { NavigationManager } from './navigation.js';
import { AnimationManager } from './animation.js';
import { AboutSection } from './sections/about.js';
import { SkillsSection } from './sections/skills.js';
import { TypeWriter } from './typewriter.js';
import { ThemeManager } from './theme.js';
import { Carousel } from './carousel.js';
import { DynamicContent } from './sections/dynamic_content.js';

/**
 * Configuration globale de l'application
 * @constant {Object}
 */
const CONFIG = {
    scroll: {
        threshold: 100,
        mobileBreakpoint: 768,
        animationDelay: 200
    },
    observer: {
        root: null,
        threshold: 0.1,
        rootMargin: '0px'
    },
    animation: {
        staggerDelay: 0.1,
        parallaxFactor: 0.3,
        opacityThreshold: 700
    },
    typewriter: {
        words: document.querySelector('.header__dynamic')?.dataset.specialisations?.split(',') || [
            'Cybersécurité',  // Valeurs par défaut si pas de données
            'Développement'
        ],
        wait: 2000
    }
};

/**
 * Sélecteurs DOM principaux
 * @constant {Object}
 */
const DOM = {
    loader: document.querySelector('.loader'),
    dynamicText: document.querySelector('.header__dynamic-text'),
    carousel: document.querySelector('.carousel')
};

/**
 * Gère les erreurs de manière centralisée
 * @param {Error} error - L'erreur à traiter
 */
function handleError(error) {
    console.error('Une erreur est survenue:', error);
    DOM.loader.classList.add('error');
    
    const errorMessage = document.createElement('p');
    errorMessage.className = 'error-message';
    errorMessage.textContent = "Désolé, une erreur s'est produite. Veuillez rafraîchir la page.";
    document.body.appendChild(errorMessage);
}

/**
 * Initialise l'animation du texte dynamique
 */
function initializeTypeWriter() {
    if (DOM.dynamicText) {
        new TypeWriter(
            DOM.dynamicText,
            CONFIG.typewriter.words,
            CONFIG.typewriter.wait
        );
    }
}

/**
 * Initialise toutes les fonctionnalités de l'application
 */
async function initializeApp() {
    try {
        // Initialisation des gestionnaires principaux
        const navigationManager = new NavigationManager(CONFIG);
        const animationManager = new AnimationManager(CONFIG);
        const themeManager = new ThemeManager();

        // Initialisation des sections
        const aboutSection = new AboutSection(CONFIG);
        const skillsSection = new SkillsSection(CONFIG);

        // Initialisation du carrousel si présent
        if (DOM.carousel) {
            new Carousel(DOM.carousel);
        }

        // Initialisation de l'animation de texte
        initializeTypeWriter();

        // Initialiser DynamicContent uniquement sur la page principale
        if (document.querySelector('.dynamic-section')) {
            new DynamicContent();
        }

        // Masquer le loader une fois tout initialisé
        DOM.loader.classList.add('hidden');

        // Ajouter une gestion du nettoyage lors de la fermeture
        window.addEventListener('unload', () => {
            // Nettoyage des instances
            navigationManager?.cleanup?.();
            animationManager?.cleanup?.();
            aboutSection?.cleanup?.();
            skillsSection?.cleanup?.();
        });

    } catch (error) {
        handleError(error);
    }
}

// Point d'entrée de l'application
document.addEventListener('DOMContentLoaded', initializeApp);

// Export pour les tests ou l'utilisation externe
export { CONFIG, initializeApp };