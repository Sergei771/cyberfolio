/**
 * @file projects.js
 * @description Gestion des projets dans le dashboard
 */

export class ProjectManager {
    constructor() {
        this.panel = document.getElementById('projectPanel');
        this.form = document.getElementById('projectForm');
        this.searchInput = document.getElementById('projectSearch');
        this.techFilter = document.getElementById('techFilter');
        this.statusFilter = document.getElementById('statusFilter');
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupFilters();
        this.setupImagePreview();
        this.setupImageDropzone();
    }

    setupEventListeners() {
        console.log('Configuration des écouteurs d\'événements');
        // Bouton Nouveau Projet
        document.getElementById('addProjectBtn')?.addEventListener('click', () => {
            this.openPanel();
        });

        // Gestion du formulaire
        this.form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleFormSubmit(e.target);
        });

        // Boutons d'édition
        document.querySelectorAll('.btn-edit').forEach(btn => {
            console.log('Bouton édition trouvé:', btn.dataset.id);
            btn.addEventListener('click', () => {
                const projectId = btn.dataset.id;
                this.editProject(projectId);
            });
        });

        // Boutons de suppression
        document.querySelectorAll('.btn-delete').forEach(btn => {
            console.log('Bouton suppression trouvé:', btn.dataset.id);
            btn.addEventListener('click', () => {
                const projectId = btn.dataset.id;
                this.deleteProject(projectId);
            });
        });

        // Fermeture du modal
        document.querySelector('[data-dismiss="panel"]')?.addEventListener('click', () => {
            this.closePanel();
        });

        document.querySelector('.panel-overlay')?.addEventListener('click', () => {
            this.closePanel();
        });
    }

    setupFilters() {
        let timeout;
        
        // Recherche
        this.searchInput?.addEventListener('input', (e) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.filterProjects();
            }, 300);
        });

        // Filtres
        this.techFilter?.addEventListener('change', () => this.filterProjects());
        this.statusFilter?.addEventListener('change', () => this.filterProjects());
    }

    setupImagePreview() {
        const imageInput = document.getElementById('screenshot');
        const preview = document.getElementById('imagePreview');

        imageInput?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Prévisualisation">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    setupImageDropzone() {
        const dropzone = document.getElementById('imageDropzone');
        const input = document.getElementById('screenshot');
        const preview = document.getElementById('imagePreview');
        let cropper = null;

        // Configuration du cropper
        const setupCropper = (image) => {
            cropper = new Cropper(image, {
                aspectRatio: 16 / 9,
                viewMode: 2,
                autoCropArea: 1,
            });
        };

        // Gestion du recadrage
        const handleCrop = (file, callback) => {
            const cropModal = document.getElementById('cropModal');
            const cropImage = document.getElementById('cropImage');
            
            const reader = new FileReader();
            reader.onload = (e) => {
                cropImage.src = e.target.result;
                cropModal.classList.add('active');
                setupCropper(cropImage);
            };
            reader.readAsDataURL(file);

            // Boutons du modal
            document.getElementById('applyCrop').onclick = () => {
                const canvas = cropper.getCroppedCanvas();
                canvas.toBlob((blob) => {
                    callback(blob);
                    cropModal.classList.remove('active');
                    cropper.destroy();
                });
            };

            document.getElementById('cancelCrop').onclick = () => {
                cropModal.classList.remove('active');
                cropper.destroy();
            };
        };

        // Prévisualisation des images
        const addPreview = (file) => {
            handleCrop(file, (croppedBlob) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Prévisualisation">
                        <button type="button" class="remove-image" onclick="this.parentElement.innerHTML=''">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    
                    // Animation de l'image
                    const img = preview.querySelector('img');
                    img.style.opacity = '0';
                    setTimeout(() => {
                        img.style.opacity = '1';
                    }, 100);

                    // Mettre à jour le fichier dans l'input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(new File([croppedBlob], file.name, { type: file.type }));
                    input.files = dataTransfer.files;
                };
                reader.readAsDataURL(croppedBlob);
            });
        };

        // Gestion du drag & drop et du clic
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('dragover');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                addPreview(file);
            }
        });

        dropzone.addEventListener('click', () => input.click());
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) addPreview(file);
        });
    }

    handleImagePreview(file) {
        const preview = document.getElementById('imagePreview');
        const reader = new FileReader();
        
        reader.onload = (e) => {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Prévisualisation">
                <button type="button" class="remove-image" onclick="this.parentElement.innerHTML=''">
                    <i class="fas fa-times"></i>
                </button>
            `;
            // Animation de l'image
            const img = preview.querySelector('img');
            img.style.opacity = '0';
            setTimeout(() => {
                img.style.opacity = '1';
            }, 100);
        };
        
        reader.readAsDataURL(file);
    }

    async handleFormSubmit(form) {
        try {
            const formData = new FormData(form);
            formData.append('action', form.dataset.mode || 'create');

            // Amélioration de la validation du fichier
            const fileInput = form.querySelector('#screenshot');
            if (!form.dataset.mode && (!fileInput.files.length || !fileInput.files[0].type.startsWith('image/'))) {
                throw new Error('Veuillez sélectionner une image valide pour le projet');
            }

            // Vérification de la taille du fichier
            if (fileInput.files.length && fileInput.files[0].size > 5 * 1024 * 1024) {
                throw new Error('L\'image ne doit pas dépasser 5 Mo');
            }

            const response = await fetch('includes/dashboard/handlers/project-handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                this.closePanel();
                window.dashboardManager.loadSection('projects');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert(error.message);
        }
    }

    async editProject(projectId) {
        try {
            console.log("Tentative de modification du projet : ", projectId);
            const response = await fetch(`includes/dashboard/handlers/project-handler.php?action=get&id=${projectId}`);
            const data = await response.json();

            if (data.success) {
                this.fillForm(data.project);
                this.openPanel();
                document.querySelector('.panel-title').textContent = 'Modifier le projet';
                this.form.dataset.mode = 'update';
                console.log("Formulaire rempli avec les données : ", data.project);
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la modification : ' + error.message);
        }
    }

    async deleteProject(projectId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) {
            try {
                console.log('Tentative de suppression du projet:', projectId);
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', projectId);

                const response = await fetch('includes/dashboard/handlers/project-handler.php', {
                    method: 'POST',
                    body: formData
                });

                console.log('Réponse brute:', await response.clone().text());
                const result = await response.json();
                console.log('Résultat de la suppression:', result);
                
                if (result.success) {
                    window.dashboardManager.loadSection('projects');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erreur détaillée:', error);
                alert('Erreur lors de la suppression : ' + error.message);
            }
        }
    }

    fillForm(project) {
        // Remplir les champs du formulaire
        Object.keys(project).forEach(key => {
            const input = document.getElementById(key);
            if (input) {
                input.value = project[key];
            }
        });

        // Cocher les technologies
        if (project.technologies) {
            const techInputs = document.querySelectorAll('input[name="technologies[]"]');
            techInputs.forEach(input => {
                input.checked = project.technologies.includes(parseInt(input.value));
            });
        }

        // Prévisualiser l'image
        if (project.screenshot) {
            document.getElementById('imagePreview').innerHTML = 
                `<img src="${project.screenshot}" alt="Image du projet">`;
        }
    }

    openPanel() {
        document.querySelector('.panel-overlay').classList.add('active');
        document.querySelector('.slide-panel').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    closePanel() {
        document.querySelector('.panel-overlay').classList.remove('active');
        document.querySelector('.slide-panel').classList.remove('active');
        document.body.style.overflow = '';
        this.form.reset();
        document.getElementById('imagePreview').innerHTML = '';
        document.querySelector('.panel-title').textContent = 'Nouveau Projet';
        this.form.dataset.mode = '';
    }

    async filterProjects() {
        const searchValue = this.searchInput.value.toLowerCase();
        const techValue = this.techFilter.value;
        const statusValue = this.statusFilter.value;

        const projects = document.querySelectorAll('.project-card');
        
        projects.forEach(project => {
            let show = true;
            
            // Filtre de recherche
            if (searchValue) {
                const title = project.querySelector('h3').textContent.toLowerCase();
                const summary = project.querySelector('.project-summary').textContent.toLowerCase();
                show = title.includes(searchValue) || summary.includes(searchValue);
            }

            // Filtre des technologies
            if (show && techValue) {
                const techs = Array.from(project.querySelectorAll('.tech-tag'))
                    .map(tag => tag.textContent);
                show = techs.includes(this.techFilter.options[this.techFilter.selectedIndex].text);
            }

            // Filtre du statut
            if (show && statusValue) {
                show = project.getAttribute('data-status') === statusValue;
            }

            project.style.display = show ? 'flex' : 'none';
        });
    }
} 