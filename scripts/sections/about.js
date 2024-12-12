/**
 * @file about.js
 * @description Gestion de la section À propos et ses animations
 */

export class AboutSection {
    constructor(config) {
        this.DOM = {
            image: document.querySelector('.profile-image'),
            items: document.querySelectorAll('.about__list-item'),
            elements: document.querySelectorAll('.about .slide-in')
        };
        this.config = config;
        this.mousePosition = { x: 0, y: 0 };
        this.currentPosition = { x: 0, y: 0 };
        this.isAnimating = false;
        this.init();
        this.handleMouseMove = this.debounce(this.updateMousePosition.bind(this), 16);
    }

    init() {
        this.initializeListAnimations();
        this.initializeParallaxEffect();
        this.initializeScrollAnimations();
    }

    initializeListAnimations() {
        this.DOM.items.forEach((item, index) => {
            item.style.transitionDelay = `${index * this.config.animation.staggerDelay}s`;
        });
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    updateMousePosition = (e) => {
        this.mousePosition = {
            x: (e.clientX / window.innerWidth - 0.5) * 10,
            y: (e.clientY / window.innerHeight - 0.5) * 10
        };

        if (!this.isAnimating) {
            this.isAnimating = true;
            this.animateParallax();
        }
    };

    animateParallax = () => {
        // Calcul du mouvement fluide avec easing
        this.currentPosition.x += (this.mousePosition.x - this.currentPosition.x) * 0.1;
        this.currentPosition.y += (this.mousePosition.y - this.currentPosition.y) * 0.1;

        // Appliquer la transformation avec des valeurs arrondies pour de meilleures performances
        const x = Math.round(this.currentPosition.x * 100) / 100;
        const y = Math.round(this.currentPosition.y * 100) / 100;

        this.DOM.image.style.transform = `translate3d(${x}px, ${y}px, 0)`;

        // Continuer l'animation tant que le mouvement est significatif
        if (
            Math.abs(this.mousePosition.x - this.currentPosition.x) > 0.01 ||
            Math.abs(this.mousePosition.y - this.currentPosition.y) > 0.01
        ) {
            requestAnimationFrame(this.animateParallax);
        } else {
            this.isAnimating = false;
        }
    };

    initializeParallaxEffect() {
        if (this.DOM.image) {
            // Ajouter un délai initial pour laisser le temps au chargement
            setTimeout(() => {
                window.addEventListener('mousemove', this.handleMouseMove, { passive: true });
            }, 100);
        }
    }

    initializeScrollAnimations() {
        this.DOM.elements.forEach((element, index) => {
            element.style.transitionDelay = `${index * this.config.animation.staggerDelay}s`;
        });
    }

    // Nettoyage des événements
    cleanup() {
        if (this.DOM.image) {
            window.removeEventListener('mousemove', this.handleMouseMove);
        }
    }
} 