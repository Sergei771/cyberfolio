import { ThemeManager } from './theme.js';
import { Carousel } from './carousel.js';

// Sélection des éléments
const navToggle = document.querySelector('.nav__toggle');
const navLinks = document.querySelector('.nav__links');
const nav = document.querySelector('.nav');
const slideElements = document.querySelectorAll('.slide-in');
const loader = document.querySelector('.loader');
let lastScroll = 0;

// Utiliser des constantes pour les valeurs réutilisées
const SCROLL_THRESHOLD = 100;
const MOBILE_BREAKPOINT = 768;
const ANIMATION_DELAY = 200;

// Fonction pour gérer le menu mobile
function toggleMenu() {
    navLinks.classList.toggle('active');
    navToggle.classList.toggle('active');
    document.body.classList.toggle('no-scroll');
}

// Gestionnaire d'événement pour le bouton du menu
navToggle.addEventListener('click', toggleMenu);

// Fermer le menu quand on clique sur un lien
navLinks.addEventListener('click', (e) => {
    if (e.target.classList.contains('nav__link')) {
        if (window.innerWidth <= 768) {
            toggleMenu();
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
});

// Fonction pour gérer l'apparition/disparition de la navigation au scroll
function handleNavScroll() {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
        nav.classList.add('nav-scrolled');
    } else {
        nav.classList.remove('nav-scrolled');
    }
    
    if (currentScroll > lastScroll && currentScroll > 100) {
        nav.style.transform = 'translateY(-100%)';
    } else {
        nav.style.transform = 'translateY(0)';
    }
    
    lastScroll = currentScroll;
}

// Observer les éléments pour les animations au scroll
const observerOptions = {
    root: null,
    threshold: 0.1,
    rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observer tous les éléments avec la classe 'slide-in'
slideElements.forEach(element => {
    observer.observe(element);
});

// Fonction pour retarder l'animation des éléments de la page d'accueil
function staggerAnimation() {
    const elements = document.querySelectorAll('.fade-in');
    elements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.2}s`;
    });
}

// Ajouter la gestion de l'intersection observer avec une fonction debounce
function debounce(func, wait) {
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

// Optimiser la gestion du scroll avec requestAnimationFrame
let scrollTimeout;
window.addEventListener('scroll', () => {
    if (scrollTimeout) {
        window.cancelAnimationFrame(scrollTimeout);
    }

    scrollTimeout = window.requestAnimationFrame(() => {
        handleNavScroll();
    });
});

window.addEventListener('resize', () => {
    if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
        toggleMenu();
    }
});

// Améliorer la gestion des erreurs
function handleError(error) {
    console.error('Une erreur est survenue:', error);
    loader.classList.add('error');
    // Afficher un message d'erreur convivial à l'utilisateur
    const errorMessage = document.createElement('p');
    errorMessage.className = 'error-message';
    errorMessage.textContent = "Désolé, une erreur s'est produite. Veuillez rafraîchir la page.";
    document.body.appendChild(errorMessage);
}

// Initialisation avec gestion d'erreurs
document.addEventListener('DOMContentLoaded', async () => {
    try {
        await Promise.all([
            staggerAnimation(),
            new ThemeManager()
        ]);
        
        // Initialiser le carrousel
        const carouselElement = document.querySelector('.carousel');
        if (carouselElement) {
            new Carousel(carouselElement);
        }
        
        loader.classList.add('hidden');
    } catch (error) {
        handleError(error);
    }
});

const aboutElements = document.querySelectorAll('.about .slide-in');
aboutElements.forEach((element, index) => {
    element.style.transitionDelay = `${index * 0.2}s`;
    observer.observe(element);
}); 