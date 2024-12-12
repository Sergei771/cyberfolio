export class Carousel {
    constructor(element) {
        this.carousel = element;
        this.track = element.querySelector('.carousel__track');
        this.slides = Array.from(element.querySelectorAll('.carousel__slide'));
        this.nav = element.querySelector('.carousel__nav');
        this.dots = element.querySelector('.carousel__dots');
        
        this.currentIndex = 0;
        this.slideCount = this.slides.length;
        this.touchStartX = 0;
        this.touchEndX = 0;
        this.autoplayInterval = null;
        this.autoplayDelay = 5000; // 5 secondes entre chaque slide

        this.init();
    }

    init() {
        this.createDots();
        this.setupNavigation();
        this.setupKeyboardNavigation();
        this.setupTouchNavigation();
        this.setupIntersectionObserver();
        this.startAutoplay(); // Démarrer l'autoplay
        this.setupAutoplayPause(); // Ajouter la pause sur hover
        this.updateSlides();
    }

    createDots() {
        this.slides.forEach((_, index) => {
            const button = document.createElement('button');
            button.className = 'carousel__dot';
            button.setAttribute('aria-label', `Aller au projet ${index + 1}`);
            button.setAttribute('role', 'tab');
            button.addEventListener('click', () => this.goToSlide(index));
            this.dots.appendChild(button);
        });
    }

    setupNavigation() {
        const prevButton = this.carousel.querySelector('.carousel__button--prev');
        const nextButton = this.carousel.querySelector('.carousel__button--next');
        
        prevButton.addEventListener('click', () => this.prev());
        nextButton.addEventListener('click', () => this.next());
    }

    setupKeyboardNavigation() {
        this.carousel.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prev();
            if (e.key === 'ArrowRight') this.next();
        });
    }

    setupTouchNavigation() {
        this.track.addEventListener('touchstart', (e) => {
            this.touchStartX = e.touches[0].clientX;
        });

        this.track.addEventListener('touchend', (e) => {
            this.touchEndX = e.changedTouches[0].clientX;
            this.handleSwipe();
        });
    }

    handleSwipe() {
        const diff = this.touchStartX - this.touchEndX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) this.next();
            else this.prev();
        }
    }

    setupIntersectionObserver() {
        const options = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, options);

        this.slides.forEach(slide => observer.observe(slide));
    }

    updateSlides() {
        const offset = -this.currentIndex * 100;
        this.track.style.transform = `translateX(${offset}%)`;
        
        this.slides.forEach((slide, index) => {
            slide.setAttribute('aria-hidden', index !== this.currentIndex);
            slide.setAttribute('tabindex', index === this.currentIndex ? '0' : '-1');
        });
        
        const dots = this.dots.querySelectorAll('.carousel__dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentIndex);
            dot.setAttribute('aria-selected', index === this.currentIndex);
        });
    }

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.slideCount;
        this.updateSlides();
        // Redémarrer l'autoplay après une interaction manuelle
        if (!this.autoplayInterval) {
            this.startAutoplay();
        }
    }

    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.slideCount) % this.slideCount;
        this.updateSlides();
        // Redémarrer l'autoplay après une interaction manuelle
        if (!this.autoplayInterval) {
            this.startAutoplay();
        }
    }

    goToSlide(index) {
        this.currentIndex = index;
        this.updateSlides();
        // Redémarrer l'autoplay après une interaction manuelle
        if (!this.autoplayInterval) {
            this.startAutoplay();
        }
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

    setupAutoplayPause() {
        // Pause sur hover
        this.carousel.addEventListener('mouseenter', () => this.stopAutoplay());
        this.carousel.addEventListener('mouseleave', () => this.startAutoplay());
        
        // Pause quand la page n'est pas visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAutoplay();
            } else {
                this.startAutoplay();
            }
        });
    }
} 