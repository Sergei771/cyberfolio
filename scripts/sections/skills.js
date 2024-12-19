/**
 * @file skills.js
 * @description Gestion de la section Compétences, filtres et animations
 */

export class SkillsSection {
    constructor(config) {
        this.DOM = {
            bars: document.querySelectorAll('.skill-item__progress'),
            filters: document.querySelectorAll('.skills__filter'),
            cards: document.querySelectorAll('.skill-card')
        };
        this.config = config;
        this.observer = this.createSkillObserver();
        this.init();
    }

    init() {
        this.initializeProgressBars();
        this.initializeFilters();
        this.activateDefaultFilter();
    }

    createSkillObserver() {
        return new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const level = bar.dataset.level;
                    bar.style.transform = `scaleX(${level / 100})`;
                    this.observer.unobserve(bar);
                }
            });
        }, { threshold: 0.5 });
    }

    initializeProgressBars() {
        this.DOM.bars.forEach(bar => this.observer.observe(bar));
    }

    initializeFilters() {
        this.DOM.filters.forEach(filter => {
            filter.addEventListener('click', () => {
                this.updateActiveFilter(filter);
                this.filterCards(filter.dataset.filter);
            });
        });
    }

    updateActiveFilter(activeFilter) {
        this.DOM.filters.forEach(f => f.classList.remove('active'));
        activeFilter.classList.add('active');
    }

    filterCards(category) {
        this.DOM.cards.forEach(card => {
            const shouldShow = category === 'all' || card.dataset.category === category;
            this.animateCard(card, shouldShow);
        });
    }

    animateCard(card, shouldShow) {
        requestAnimationFrame(() => {
            card.style.transition = 'all 0.5s ease';
            
            if (shouldShow) {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0) scale(1)';
                card.style.display = 'block';
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px) scale(0.95)';
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        if (card.style.opacity === '0') {
                            card.style.display = 'none';
                        }
                    }, 500);
                });
            }
        });
    }

    activateDefaultFilter() {
        const defaultFilter = document.querySelector('.skills__filter[data-filter="all"]');
        if (defaultFilter) {
            defaultFilter.click();
        }
    }
} 