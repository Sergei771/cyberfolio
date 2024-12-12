/**
 * @file navigation.js
 * @description Gestion de la navigation et des interactions de menu
 */

export class NavigationManager {
    constructor(config) {
        this.DOM = {
            container: document.querySelector('.nav'),
            toggle: document.querySelector('.nav__toggle'),
            links: document.querySelector('.nav__links')
        };
        this.config = config;
        this.lastScroll = 0;
        this.scrollThrottleTimeout = null;
        this.init();
    }

    init() {
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        this.DOM.toggle.addEventListener('click', () => this.toggleMenu());
        this.DOM.links.addEventListener('click', (e) => this.handleNavClick(e));
        window.addEventListener('scroll', () => {
            if (this.scrollTimeout) {
                window.cancelAnimationFrame(this.scrollTimeout);
            }
            this.scrollTimeout = window.requestAnimationFrame(() => {
                this.handleNavScroll(window.pageYOffset);
            });
        });
        window.addEventListener('resize', () => this.handleResize());
    }

    toggleMenu() {
        this.DOM.links.classList.toggle('active');
        this.DOM.toggle.classList.toggle('active');
        document.body.classList.toggle('no-scroll');
    }

    handleNavScroll(currentScroll) {
        if (this.scrollThrottleTimeout) return;

        this.scrollThrottleTimeout = setTimeout(() => {
            this.scrollThrottleTimeout = null;
            
            requestAnimationFrame(() => {
                if (currentScroll > this.config.scroll.threshold) {
                    this.DOM.container.classList.add('nav-scrolled');
                } else {
                    this.DOM.container.classList.remove('nav-scrolled');
                }
                
                if (currentScroll > this.lastScroll && currentScroll > this.config.scroll.threshold) {
                    this.DOM.container.style.transform = 'translateY(-100%)';
                } else {
                    this.DOM.container.style.transform = 'translateY(0)';
                }
                
                this.lastScroll = currentScroll;
            });
        }, 16); // ~60fps
    }

    handleNavClick(e) {
        if (e.target.classList.contains('nav__link')) {
            if (window.innerWidth <= this.config.scroll.mobileBreakpoint) {
                this.toggleMenu();
            }
            const href = e.target.getAttribute('href');
            const targetElement = document.querySelector(href);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    }

    handleResize() {
        if (window.innerWidth > this.config.scroll.mobileBreakpoint && 
            this.DOM.links.classList.contains('active')) {
            this.toggleMenu();
        }
    }
} 