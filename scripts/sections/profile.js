export class ProfileManager {
    constructor() {
        this.form = document.querySelector('.profile-form');
        this.init();
    }

    init() {
        if (this.form) {
            this.setupFormSubmit();
            this.setupImagePreview();
        }
    }

    setupFormSubmit() {
        this.form.addEventListener('submit', async (e) => {
            e.preventDefault();
            console.log("Formulaire soumis");
            const submitButton = this.form.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            try {
                const formData = new FormData(this.form);
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }

                const response = await fetch('includes/dashboard/handlers/profile-handler.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const responseText = await response.text();
                console.log("Réponse brute du serveur:", responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (e) {
                    console.error("Erreur parsing JSON:", e);
                    throw new Error("Réponse invalide du serveur");
                }

                console.log("Résultat parsé:", result);
                
                if (result.success) {
                    alert('Profil mis à jour avec succès');
                    window.location.reload();
                } else {
                    throw new Error(result.message || 'Erreur lors de la mise à jour');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour: ' + error.message);
            } finally {
                submitButton.disabled = false;
            }
        });
    }

    setupImagePreview() {
        const photoInput = this.form.querySelector('input[type="file"][name="photo"]');
        if (photoInput) {
            photoInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const preview = this.form.querySelector('.profile-preview');
                        if (preview) {
                            preview.src = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }
} 