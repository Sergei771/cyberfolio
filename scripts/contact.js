/**
 * Fichier : contact.js
 * Description : Gestion du formulaire de contact
 */

class ContactForm {
    constructor() {
        this.form = document.querySelector('#contactForm');
        this.messageDiv = document.querySelector('#contactFormMessage');
        this.inputs = document.querySelectorAll('.contact-input');
        this.submitButton = document.querySelector('.contact-submit');
        this.init();
    }

    init() {
        this.addInputAnimations();
        this.form?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit(e);
        });
    }

    addInputAnimations() {
        this.inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.parentElement.classList.remove('focused');
                }
            });

            if (input.value) {
                input.parentElement.classList.add('focused');
            }
        });
    }

    async handleSubmit(e) {
        const button = this.submitButton;
        button.disabled = true;
        button.innerHTML = '<span class="button-text">Envoi en cours...</span>';

        try {
            const formData = new FormData(e.target);
            
            const response = await fetch('contact.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage('Message envoyé avec succès !', 'success');
                this.form.reset();
                this.inputs.forEach(input => {
                    input.parentElement.classList.remove('focused');
                });
            } else {
                throw new Error(data.message || 'Erreur lors de l\'envoi');
            }
        } catch (error) {
            this.showMessage(error.message, 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = '<span class="button-text">Envoyer</span><span class="button-icon">→</span>';
        }
    }

    showMessage(message, type) {
        if (this.messageDiv) {
            this.messageDiv.textContent = message;
            this.messageDiv.className = `form-message ${type}`;
            requestAnimationFrame(() => {
                this.messageDiv.classList.add('show');
            });

            if (type === 'success') {
                setTimeout(() => {
                    this.messageDiv.classList.remove('show');
                }, 5000);
            }
        }
    }
}

// Initialisation
new ContactForm(); 