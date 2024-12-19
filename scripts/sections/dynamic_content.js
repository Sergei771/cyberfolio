/**
 * Fichier : dynamic_content.js
 * Description : Gestion de l'affichage dynamique des informations personnelles
 */

export class DynamicContent {
    constructor() {
        this.modal = document.querySelector('#contactModal');
        this.form = document.querySelector('#contactForm');
        this.messageDiv = document.querySelector('#contactFormMessage');
        this.init();
    }

    init() {
        // Gestionnaire pour ouvrir la modale sur le clic du bouton email
        document.querySelector('[data-action="contact"]')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.openContactModal();
        });

        // Gestionnaire du formulaire
        this.form?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit(e);
        });

        // Gestionnaire du bouton de fermeture
        document.querySelector('.modal-close')?.addEventListener('click', () => {
            this.closeContactModal();
        });

        // Fermeture de la modale en cliquant en dehors
        this.modal?.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeContactModal();
            }
        });
    }

    openContactModal() {
        this.modal?.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    closeContactModal() {
        this.modal?.classList.remove('active');
        document.body.style.overflow = '';
    }

    async handleSubmit(e) {
        try {
            const formData = new FormData(e.target);
            
            const response = await fetch('includes/process_messages.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage('Message envoyé avec succès !', 'success');
                this.form.reset();
                setTimeout(() => this.closeContactModal(), 2000);
            } else {
                throw new Error(data.message || 'Erreur lors de l\'envoi');
            }
        } catch (error) {
            this.showMessage(error.message, 'error');
        }
    }

    showMessage(message, type) {
        if (this.messageDiv) {
            this.messageDiv.textContent = message;
            this.messageDiv.className = `form-message ${type}`;
        }
    }
} 