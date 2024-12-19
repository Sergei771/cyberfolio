/**
 * @file admin_login.js
 * @description Gestion du formulaire de connexion administrateur
 */

class AdminLogin {
    constructor() {
        this.form = document.querySelector('#adminLoginForm');
        this.emailInput = document.querySelector('#adminEmail');
        this.passwordInput = document.querySelector('#adminPassword');
        this.submitButton = document.querySelector('.admin-submit');
        this.messageDiv = document.querySelector('.form-message');
        
        this.init();
    }

    init() {
        this.form?.addEventListener('submit', (e) => this.handleSubmit(e));
        this.addInputValidation();
    }

    addInputValidation() {
        const inputs = [this.emailInput, this.passwordInput];
        
        inputs.forEach(input => {
            input?.addEventListener('input', () => {
                this.validateInput(input);
            });

            input?.addEventListener('blur', () => {
                this.validateInput(input);
            });
        });
    }

    validateInput(input) {
        if (!input) return;

        const wrapper = input.closest('.input-wrapper');
        const isValid = input.value.trim() !== '';
        
        wrapper.classList.toggle('error', !isValid);
        
        if (input.type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isValidEmail = emailRegex.test(input.value);
            wrapper.classList.toggle('error', !isValidEmail);
        }
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        if (!this.validateForm()) {
            this.showMessage('Veuillez remplir tous les champs correctement.', 'error');
            return;
        }

        this.submitButton.disabled = true;
        this.submitButton.innerHTML = '<span class="button-text">Connexion en cours...</span>';

        try {
            const formData = new FormData(this.form);
            this.form.submit();
        } catch (error) {
            this.showMessage('Une erreur est survenue. Veuillez réessayer.', 'error');
            this.submitButton.disabled = false;
            this.submitButton.innerHTML = '<span class="button-text">Connexion</span><span class="button-icon">→</span>';
        }
    }

    validateForm() {
        const inputs = [this.emailInput, this.passwordInput];
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.validateInput(input);
                isValid = false;
            }
        });

        return isValid;
    }

    showMessage(message, type) {
        if (!this.messageDiv) {
            this.messageDiv = document.createElement('div');
            this.messageDiv.className = 'form-message';
            this.form.insertBefore(this.messageDiv, this.form.firstChild);
        }

        this.messageDiv.textContent = message;
        this.messageDiv.className = `form-message ${type} show`;
    }
}

// Initialisation
new AdminLogin(); 