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

// Ajouter cette classe au début du fichier
class TypeWriter {
    constructor(element, words, wait = 3000) {
        this.element = element;
        this.words = words;
        this.wait = wait;
        this.txt = '';
        this.wordIndex = 0;
        this.isDeleting = false;
        this.type();
    }

    type() {
        const current = this.wordIndex % this.words.length;
        const fullTxt = this.words[current];

        if (this.isDeleting) {
            this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
            this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        this.element.textContent = this.txt;

        let typeSpeed = 100;
        if (this.isDeleting) typeSpeed /= 2;

        if (!this.isDeleting && this.txt === fullTxt) {
            typeSpeed = this.wait;
            this.isDeleting = true;
        } else if (this.isDeleting && this.txt === '') {
            this.isDeleting = false;
            this.wordIndex++;
            typeSpeed = 500;
        }

        setTimeout(() => this.type(), typeSpeed);
    }
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

        // Initialiser l'animation de texte
        const dynamicText = document.querySelector('.header__dynamic-text');
        if (dynamicText) {
            new TypeWriter(dynamicText, [
                'Cybersécurité',
                'Automatisation',
                'Développement',
                'Scripting'
            ], 2000);
        }

        // Ajouter l'effet de parallaxe au scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const header = document.querySelector('.header');
            if (header) {
                header.style.transform = `translateY(${scrolled * 0.3}px)`;
                header.style.opacity = 1 - (scrolled / 700);
            }
        });

        initAboutSection();
        initSkillsSection();

    } catch (error) {
        handleError(error);
    }
});

const aboutElements = document.querySelectorAll('.about .slide-in');
aboutElements.forEach((element, index) => {
    element.style.transitionDelay = `${index * 0.2}s`;
    observer.observe(element);
});

function initAboutSection() {
    const aboutImage = document.querySelector('.profile-image');
    const aboutItems = document.querySelectorAll('.about__list-item');
    
    // Effet de parallaxe subtil sur l'image
    if (aboutImage) {
        window.addEventListener('mousemove', (e) => {
            requestAnimationFrame(() => {
                const { clientX, clientY } = e;
                const xPos = (clientX / window.innerWidth - 0.5) * 20;
                const yPos = (clientY / window.innerHeight - 0.5) * 20;
                
                aboutImage.style.transform = `translate(${xPos}px, ${yPos}px)`;
            });
        });
    }

    // Animation séquentielle des éléments de la liste
    aboutItems.forEach((item, index) => {
        item.style.transitionDelay = `${index * 0.1}s`;
    });
}

function initSkillsSection() {
    const skillBars = document.querySelectorAll('.skill-item__progress');
    const skillFilters = document.querySelectorAll('.skills__filter');
    const skillCards = document.querySelectorAll('.skill-card');

    // Animation des barres de progression
    const animateSkillBars = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const level = bar.dataset.level;
                bar.style.transform = `scaleX(${level / 100})`;
                observer.unobserve(bar);
            }
        });
    };

    const skillObserver = new IntersectionObserver(animateSkillBars, {
        threshold: 0.5
    });

    skillBars.forEach(bar => skillObserver.observe(bar));

    // Filtrage des compétences amélioré
    skillFilters.forEach(filter => {
        filter.addEventListener('click', () => {
            // Mise à jour des filtres actifs
            skillFilters.forEach(f => f.classList.remove('active'));
            filter.classList.add('active');

            const category = filter.dataset.filter;

            // Animation des cartes avec transition fluide
            skillCards.forEach(card => {
                card.style.transition = 'all 0.5s ease';
                
                if (category === 'all' || card.dataset.category === category) {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0) scale(1)';
                    card.style.display = 'block';
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px) scale(0.95)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 500);
                }
            });
        });
    });

    // Activer le filtre "Tous" par défaut
    document.querySelector('.skills__filter[data-filter="all"]').click();
} 