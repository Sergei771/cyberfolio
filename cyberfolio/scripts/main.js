// Sélection des éléments
const navToggle = document.querySelector('.nav__toggle');
const navLinks = document.querySelector('.nav__links');
const nav = document.querySelector('.nav');
const slideElements = document.querySelectorAll('.slide-in');
let lastScroll = 0;

// Fonction pour gérer le menu mobile
function toggleMenu() {
    navLinks.classList.toggle('active');
    navToggle.classList.toggle('active');
    // Ajouter/Retirer la classe 'no-scroll' au body quand le menu est ouvert/fermé
    document.body.classList.toggle('no-scroll');
}

// Gestionnaire d'événement pour le bouton du menu
navToggle.addEventListener('click', toggleMenu);

// Fermer le menu quand on clique sur un lien
navLinks.addEventListener('click', (e) => {
    if (e.target.classList.contains('nav__link')) {
        toggleMenu();
    }
});

// Fonction pour gérer l'apparition/disparition de la navigation au scroll
function handleNavScroll() {
    const currentScroll = window.pageYOffset;
    
    // Ajouter/Retirer la classe 'nav-scrolled' selon la position du scroll
    if (currentScroll > 100) {
        nav.classList.add('nav-scrolled');
    } else {
        nav.classList.remove('nav-scrolled');
    }
    
    // Cacher/Montrer la navigation selon la direction du scroll
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
            // Désinscrire l'élément une fois qu'il est visible
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observer tous les éléments avec la classe 'slide-in'
slideElements.forEach(element => {
    observer.observe(element);
});

// Smooth scroll pour les liens d'ancrage
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Gestionnaires d'événements
window.addEventListener('scroll', handleNavScroll);
window.addEventListener('resize', () => {
    // Réinitialiser le menu mobile si la fenêtre est redimensionnée
    if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
        toggleMenu();
    }
});

// Fonction pour retarder l'animation des éléments de la page d'accueil
function staggerAnimation() {
    const elements = document.querySelectorAll('.fade-in');
    elements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.2}s`;
    });
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    staggerAnimation();
}); 