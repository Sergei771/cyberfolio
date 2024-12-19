/**
 * @file animation.js
 * @description Gestion des animations et effets visuels
 */

export class AnimationManager {
    constructor(config) {
        this.DOM = {
            slideElements: document.querySelectorAll('.slide-in'),
            fadeElements: document.querySelectorAll('.fade-in'),
            header: document.querySelector('.header')
        };
        this.config = config;
        this.observer = this.createObserver();
        this.observedElements = new Set();
        this.init();
    }

    init() {
        this.initializeObserver();
        this.staggerAnimation();
        this.initializeHeaderParallax();
    }

    createObserver() {
        return new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    this.observer.unobserve(entry.target);
                }
            });
        }, this.config.observer);
    }

    initializeObserver() {
        this.DOM.slideElements.forEach(element => {
            if (!this.observedElements.has(element)) {
                this.observer.observe(element);
                this.observedElements.add(element);
            }
        });
    }

    staggerAnimation() {
        this.DOM.fadeElements.forEach((element, index) => {
            element.style.animationDelay = `${index * 0.2}s`;
        });
    }

    initializeHeaderParallax() {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            if (this.DOM.header) {
                requestAnimationFrame(() => {
                    this.DOM.header.style.transform = 
                        `translateY(${scrolled * this.config.animation.parallaxFactor}px)`;
                    this.DOM.header.style.opacity = 
                        1 - (scrolled / this.config.animation.opacityThreshold);
                });
            }
        });
    }
} 