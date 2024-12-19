<?php
/**
 * @file profile.php
 * @description Gestion du profil administrateur
 */

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier la connexion PDO
if (!isset($pdo)) {
    require_once __DIR__ . '/../../includes/db_connect.php';
}

// Si c'est une requête POST AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    echo "<div class='error-message'>Session expirée</div>";
    return;
}

// Récupération des données du profil
try {
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE id_profile = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $profile = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Erreur profile.php : " . $e->getMessage());
    echo "<div class='error-message'>Une erreur est survenue lors de la récupération du profil.</div>";
    return;
}
?>

<div class="profile-section">
    <div class="section-header">
        <h2>Mon Profil</h2>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form class="profile-form" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Informations personnelles -->
            <div class="form-section">
                <h3>Informations personnelles</h3>
                
                <div class="form-group">
                    <label for="firstname">Prénom</label>
                    <input type="text" id="firstname" name="firstname" 
                           value="<?php echo ($profile['firstname']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="lastname">Nom</label>
                    <input type="text" id="lastname" name="lastname" 
                           value="<?php echo ($profile['lastname']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo ($profile['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe (laisser vide pour ne pas modifier)</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="form-group">
                    <label for="specialisation">Spécialisation</label>
                    <input type="text" id="specialisation" name="spécialisation" 
                           value="<?php echo stripslashes($profile['spécialisation']); ?>">
                </div>
            </div>

            <!-- Présentation -->
            <div class="form-section">
                <h3>Présentation</h3>

                <div class="form-group">
                    <label for="a_propos">À propos</label>
                    <textarea id="a_propos" name="a_propos" rows="4"><?php echo stripslashes($profile['a_propos']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="passion_hero">Phrase d'accroche (Hero)</label>
                    <input type="text" id="passion_hero" name="passion_hero" 
                           value="<?php echo stripslashes($profile['passion_hero']); ?>">
                </div>

                <div class="form-group">
                    <label for="passion">Passions (une par ligne)</label>
                    <textarea id="passion" name="passion" rows="4" 
                              placeholder="Développement d'applications&#10;Automatisation&#10;Scripting"
                    ><?php echo stripslashes($profile['passion']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="photo">Photo de profil</label>
                    <?php if ($profile['photo']): ?>
                        <img src="<?php echo ($profile['photo']); ?>" alt="Photo de profil" class="profile-preview">
                    <?php endif; ?>
                    <input type="file" id="photo" name="photo" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="cv">CV</label>
                    <?php if ($profile['CV']): ?>
                        <a href="<?php echo ($profile['CV']); ?>" target="_blank">CV actuel</a>
                    <?php endif; ?>
                    <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">
                </div>
            </div>

            <!-- Liens professionnels -->
            <div class="form-section">
                <h3>Liens professionnels</h3>
                
                <div class="form-group">
                    <label for="github">GitHub</label>
                    <input type="url" id="github" name="github" 
                           value="<?php echo htmlspecialchars($profile['github']); ?>">
                </div>

                <div class="form-group">
                    <label for="linkedin">LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" 
                           value="<?php echo htmlspecialchars($profile['linkedin']); ?>">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </div>
    </form>
</div> 