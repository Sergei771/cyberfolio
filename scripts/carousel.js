export class Carousel {
    constructor(element) {
        this.carousel = element;
        this.track = element.querySelector('.carousel__track');
        this.slides = Array.from(element.querySelectorAll('.carousel__slide'));
        this.nav = element.querySelector('.carousel__nav');
        this.dots = element.querySelector('.carousel__dots');
        
        this.currentIndex = 0;
        this.slideCount = this.slides.length;
        this.autoplayInterval = null;
        this.autoplayDelay = 5000;

        this.init();
    }

    init() {
        // Créer les points de navigation
        this.createDots();
        
        // Ajouter les boutons de navigation
        const prevButton = this.carousel.querySelector('.carousel__button--prev');
        const nextButton = this.carousel.querySelector('.carousel__button--next');
        
        prevButton.addEventListener('click', () => this.prev());
        nextButton.addEventListener('click', () => this.next());
        
        // Gérer la navigation au clavier
        this.carousel.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prev();
            if (e.key === 'ArrowRight') this.next();
        });

        // Démarrer l'autoplay
        this.startAutoplay();
        
        // Pause sur hover
        this.carousel.addEventListener('mouseenter', () => this.stopAutoplay());
        this.carousel.addEventListener('mouseleave', () => this.startAutoplay());
        
        // Mise à jour initiale
        this.updateSlides();
    }

    createDots() {
        this.slides.forEach((_, index) => {
            const button = document.createElement('button');
            button.classList.add('carousel__dot');
            button.setAttribute('aria-label', `Aller au projet ${index + 1}`);
            button.addEventListener('click', () => this.goToSlide(index));
            this.dots.appendChild(button);
        });
    }

    updateSlides() {
        // Mettre à jour la position du track
        this.track.style.transform = `translateX(-${this.currentIndex * 100}%)`;
        
        // Mettre à jour les attributs aria-hidden
        this.slides.forEach((slide, index) => {
            slide.setAttribute('aria-hidden', index !== this.currentIndex);
        });
        
        // Mettre à jour les points
        const dotButtons = this.dots.querySelectorAll('.carousel__dot');
        dotButtons.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentIndex);
        });
    }

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.slideCount;
        this.updateSlides();
    }

    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.slideCount) % this.slideCount;
        this.updateSlides();
    }

    goToSlide(index) {
        this.currentIndex = index;
        this.updateSlides();
    }

    startAutoplay() {
        if (this.autoplayInterval) return;
        this.autoplayInterval = setInterval(() => this.next(), this.autoplayDelay);
    }

    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
} 