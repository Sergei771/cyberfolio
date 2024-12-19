/**
 * @file competences.js
 * @description Gestion des compétences dans le dashboard
 */

export class CompetenceManager {
    constructor() {
        console.log("Initialisation du CompetenceManager");
        this.modal = document.getElementById('competenceModal');
        this.form = document.getElementById('competenceForm');
        this.addButton = document.getElementById('addCompetenceBtn');
        this.scoreSlider = document.getElementById('score');
        this.scoreValue = document.querySelector('.score-value');
        
        this.init();
    }

    init() {
        console.log("Démarrage de l'initialisation");
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Bouton d'ajout
        this.addButton?.addEventListener('click', () => {
            this.openModal();
        });

        // Ajout de l'écouteur pour le slider
        this.scoreSlider?.addEventListener('input', (e) => {
            this.scoreValue.textContent = `${e.target.value}%`;
        });

        // Gestion du formulaire
        this.form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleFormSubmit(e.target);
        });

        // Boutons d'édition et suppression
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const competenceId = btn.dataset.id;
                this.editCompetence(competenceId);
            });
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const competenceId = btn.dataset.id;
                this.deleteCompetence(competenceId);
            });
        });

        // Fermeture du modal
        document.querySelector('[data-dismiss="modal"]')?.addEventListener('click', () => {
            this.closeModal();
        });
    }

    async handleFormSubmit(form) {
        try {
            const formData = new FormData(form);
            formData.append('action', form.dataset.mode || 'create');

            const response = await fetch('includes/dashboard/handlers/competence-handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                this.closeModal();
                window.dashboardManager.loadSection('competences');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert(error.message);
        }
    }

    async editCompetence(competenceId) {
        try {
            const response = await fetch(`includes/dashboard/handlers/competence-handler.php?action=get&id=${competenceId}`);
            const data = await response.json();

            if (data.success) {
                this.fillForm(data.competence);
                this.openModal('Modifier la compétence');
                this.form.dataset.mode = 'update';
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la modification');
        }
    }

    async deleteCompetence(competenceId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette compétence ?')) {
            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', competenceId);

                const response = await fetch('includes/dashboard/handlers/competence-handler.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    window.dashboardManager.loadSection('competences');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert(error.message);
            }
        }
    }

    fillForm(competence) {
        Object.keys(competence).forEach(key => {
            const input = document.getElementById(key);
            if (input) {
                input.value = competence[key];
                // Mise à jour de l'affichage du score si c'est le slider
                if (key === 'score') {
                    this.scoreValue.textContent = `${competence[key]}%`;
                }
            }
        });
    }

    openModal(title = 'Nouvelle Compétence') {
        this.modal.classList.add('active');
        this.modal.querySelector('.modal-title').textContent = title;
        document.body.style.overflow = 'hidden';
        // Réinitialiser la valeur du slider à 50% pour une nouvelle compétence
        if (title === 'Nouvelle Compétence') {
            this.scoreSlider.value = 50;
            this.scoreValue.textContent = '50%';
        }
    }

    closeModal() {
        this.modal.classList.remove('active');
        this.form.reset();
        this.form.dataset.mode = '';
        document.body.style.overflow = '';
    }
} 