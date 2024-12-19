<?php
/**
 * @file project-form.php
 * @description Formulaire pour ajouter/modifier un projet
 */
?>
<form id="projectForm" class="modal-form" enctype="multipart/form-data">
    <div class="form-content">
        <div class="form-left-column">
            <input type="hidden" name="id_project" id="projectId">
            
            <div class="form-group">
                <label for="title">Titre du projet *</label>
                <input type="text" id="title" name="title" required maxlength="100" 
                       placeholder="Nom du projet">
            </div>

            <div class="form-group">
                <label for="summary">Résumé *</label>
                <input type="text" id="summary" name="summary" required maxlength="500"
                       placeholder="Bref résumé du projet">
            </div>

            <div class="form-group">
                <label for="description">Description détaillée *</label>
                <textarea id="description" name="description" rows="6" required
                          placeholder="Description complète du projet"></textarea>
            </div>

            <div class="form-group">
                <label for="completed_at">Date de réalisation</label>
                <input type="date" id="completed_at" name="completed_at">
            </div>

            <div class="form-group">
                <label for="status">Statut du projet</label>
                <select id="status" name="status" required>
                    <option value="in_progress">En cours</option>
                    <option value="completed">Terminé</option>
                    <option value="paused">En pause</option>
                </select>
            </div>
        </div>
        <div class="form-right-column">
            <div class="form-group">
                <label>Image du projet</label>
                <div class="dropzone" id="imageDropzone">
                    <input type="file" id="screenshot" name="screenshot" accept="image/*" hidden>
                    <div class="dropzone-content">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Glissez votre image ici ou cliquez pour sélectionner</p>
                    </div>
                    <div class="preview-container" id="imagePreview"></div>
                </div>
                <!-- Modal de recadrage -->
                <div class="crop-modal" id="cropModal">
                    <div class="crop-container">
                        <div class="crop-area">
                            <img id="cropImage" src="">
                        </div>
                        <div class="crop-controls">
                            <button type="button" class="btn btn-secondary" id="cancelCrop">Annuler</button>
                            <button type="button" class="btn btn-primary" id="applyCrop">Appliquer</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Technologies utilisées</label>
                <div class="tech-selector">
                    <?php
                    // Récupérer toutes les technologies
                    $techQuery = $pdo->query("SELECT * FROM technology ORDER BY name");
                    while ($tech = $techQuery->fetch()): ?>
                        <label class="tech-checkbox">
                            <input type="checkbox" name="technologies[]" 
                                   value="<?php echo $tech['id_technology']; ?>">
                            <span class="tech-name"><?php echo htmlspecialchars($tech['name']); ?></span>
                        </label>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <button type="button" class="btn btn-secondary" data-dismiss="panel">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form> 