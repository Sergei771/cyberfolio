/**
 * @file technologies.js
 * @description Gestion des technologies dans le dashboard
 */

export class TechnologyManager {
    constructor() {
        console.log("Initialisation du TechnologyManager");
        this.modal = document.getElementById('techModal');
        this.form = document.getElementById('techForm');
        this.addButton = document.getElementById('addTechBtn');
        this.levelInput = document.getElementById('level');
        this.levelValue = document.querySelector('.level-value');
        this.tableContainer = document.querySelector('.table-container tbody');
        
        this.init();
    }

    init() {
        console.log("Démarrage de l'initialisation");
        this.setupEventListeners();
        this.setupLevelInput();
    }

    setupEventListeners() {
        // Bouton d'ajout
        this.addButton?.addEventListener('click', () => {
            this.openModal();
        });

        // Gestion du formulaire
        this.form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleFormSubmit(e.target);
        });

        // Boutons d'édition et suppression
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const techId = btn.dataset.id;
                this.editTechnology(techId);
            });
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const techId = btn.dataset.id;
                this.deleteTechnology(techId);
            });
        });

        // Fermeture du modal
        document.querySelector('[data-dismiss="modal"]')?.addEventListener('click', () => {
            this.closeModal();
        });
    }

    setupLevelInput() {
        this.levelInput?.addEventListener('input', (e) => {
            this.levelValue.textContent = `${e.target.value}%`;
        });
    }

    async handleFormSubmit(form) {
        try {
            const formData = new FormData(form);
            formData.append('action', form.dataset.mode || 'create');

            const response = await fetch('includes/dashboard/handlers/technology-handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                this.closeModal();
                window.dashboardManager.loadSection('technologies');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert(error.message);
        }
    }

    async editTechnology(techId) {
        try {
            const response = await fetch(`includes/dashboard/handlers/technology-handler.php?action=get&id=${techId}`);
            const data = await response.json();

            if (data.success) {
                this.fillForm(data.technology);
                this.openModal('Modifier la technologie');
                this.form.dataset.mode = 'update';
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la modification');
        }
    }

    async deleteTechnology(techId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette technologie ?')) {
            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', techId);

                const response = await fetch('includes/dashboard/handlers/technology-handler.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    window.dashboardManager.loadSection('technologies');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert(error.message);
            }
        }
    }

    fillForm(tech) {
        Object.keys(tech).forEach(key => {
            const input = document.getElementById(key);
            if (input) {
                input.value = tech[key];
                if (input.type === 'range') {
                    this.levelValue.textContent = `${tech[key]}%`;
                }
            }
        });
    }

    openModal(title = 'Nouvelle Technologie') {
        this.modal.classList.add('active');
        this.modal.querySelector('.modal-title').textContent = title;
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        this.modal.classList.remove('active');
        this.form.reset();
        this.form.dataset.mode = '';
        document.body.style.overflow = '';
        this.levelValue.textContent = '50%';
    }
} 