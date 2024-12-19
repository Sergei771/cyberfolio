<?php
/**
 * @file admin.php
 * @description Page de connexion administrateur du Cyberfolio
 */

session_start();
require_once 'includes/db_connect.php';

// Redirection si déjà connecté
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT id_profile, password FROM profile WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Débogage
        error_log("Email tenté: " . $email);
        error_log("Utilisateur trouvé: " . ($user ? 'oui' : 'non'));
        error_log("Mot de passe reçu: " . $password);
        if ($user) {
            error_log("Hash stocké: " . $user['password']);
            error_log("Vérification mot de passe: " . (password_verify($password, $user['password']) ? 'succès' : 'échec'));
        }

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id_profile'];
            header('Location: dashboard.php');
            exit;
        } else {
            if (!$user) {
                $error = "Email non trouvé dans la base de données.";
            } else {
                $error = "Mot de passe incorrect.";
            }
        }
    } catch (PDOException $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        $error = "Une erreur est survenue. Veuillez réessayer plus tard.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Cyberfolio</title>
    <link rel="stylesheet" href="styles/style.css">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
    <div class="admin-page">
        <div class="admin-container">
            <div class="admin-header">
                <h1>Administration</h1>
                <p class="admin-subtitle">Connectez-vous pour gérer votre portfolio</p>
            </div>

            <form id="adminLoginForm" class="admin-form" method="POST" action="">
                <?php if (isset($error)): ?>
                    <div class="form-message error show"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="email" 
                               id="adminEmail" 
                               name="email" 
                               class="admin-input" 
                               required 
                               autocomplete="email"
                               placeholder="Email">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password" 
                               id="adminPassword" 
                               name="password" 
                               class="admin-input" 
                               required
                               placeholder="Mot de passe ">
                    </div>
                </div>

                <button type="submit" class="admin-submit">
                    <span class="button-text">Connexion</span>
                    <span class="button-icon">→</span>
                </button>
            </form>

            <a href="/" class="back-link">
                <span>Retour au portfolio</span>
            </a>
        </div>
    </div>

    <script type="module" src="scripts/sections/admin_login.js"></script>
</body>
</html> 