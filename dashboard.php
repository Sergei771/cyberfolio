<?php
/**
 * @file dashboard.php
 * @description Tableau de bord administrateur du Cyberfolio
 */

session_start();
require_once 'includes/db_connect.php';

// Définition des sections autorisées et section courante
$allowed_sections = ['home', 'profile', 'projects', 'technologies', 'competences'];
$section = $_GET['section'] ?? 'home';

// Vérification si c'est une requête AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax) {
    // Pour les requêtes AJAX, on vérifie si c'est un POST ou un GET
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Laisser le fichier de section gérer le POST
        include "includes/dashboard/{$section}.php";
        exit;
    } else {
        // Pour les GET, on renvoie le contenu de la section
        if (in_array($section, $allowed_sections)) {
            include "includes/dashboard/{$section}.php";
            exit;
        }
    }
}

// Vérification si l'admin est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin.php');
    exit;
}

// Récupération des données de l'admin
try {
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE id_profile = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération du profil : " . $e->getMessage());
    exit("Une erreur est survenue");
}

// Vérifier que les fichiers existent
$statisticsFile = __DIR__ . '/includes/dashboard/get_statistics.php';
$activityLogFile = __DIR__ . '/includes/dashboard/get_activity_log.php';

if (!file_exists($statisticsFile)) {
    error_log("Fichier manquant : " . $statisticsFile);
}
if (!file_exists($activityLogFile)) {
    error_log("Fichier manquant : " . $activityLogFile);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cyberfolio</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
</head>
<body class="dashboard-body">
    <!-- Menu latéral -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-header">
            <h2 class="sidebar-title">Cyberfolio</h2>
            <span class="admin-name"><?php echo htmlspecialchars($admin['firstname']); ?></span>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item <?php echo !isset($_GET['section']) ? 'active' : ''; ?>">
                    <a href="dashboard.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        Tableau de bord
                    </a>
                </li>

                <li class="nav-item <?php echo ($_GET['section'] ?? '') === 'profile' ? 'active' : ''; ?>">
                    <a href="dashboard.php?section=profile" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Profil
                    </a>
                </li>

                <li class="nav-item">
                    <a href="dashboard.php?section=projects" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
                        </svg>
                        Projets
                    </a>
                </li>

                <li class="nav-item">
                    <a href="dashboard.php?section=technologies" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/>
                        </svg>
                        Technologies
                    </a>
                </li>

                <li class="nav-item">
                    <a href="dashboard.php?section=competences" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        </svg>
                        Compétences
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="includes/dashboard/handlers/logout-handler.php" class="nav-link logout-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- Contenu principal -->
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="header-title">
                <h1><?php 
                    switch($section) {
                        case 'profile':
                            echo 'Mon Profil';
                            break;
                        case 'projects':
                            echo 'Mes Projets';
                            break;
                        case 'technologies':
                            echo 'Technologies';
                            break;
                        case 'competences':
                            echo 'Compétences';
                            break;
                        default:
                            echo 'Tableau de bord';
                    }
                ?></h1>
            </div>
            <div class="header-actions">
                <button class="theme-toggle">
                    <svg class="theme-icon" viewBox="0 0 24 24">
                        <path class="sun" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                        <path class="moon" d="M12 3v2M12 19v2M19 12h2M3 12h2"/>
                    </svg>
                </button>
            </div>
        </header>

        <div class="dashboard-content" id="dashboardContent">
            <?php
            if (in_array($section, $allowed_sections)) {
                error_log("Chargement de la section: " . $section);
                $file = "includes/dashboard/{$section}.php";
                if (file_exists($file)) {
                    error_log("Fichier trouvé: " . $file);
                    include $file;
                } else {
                    error_log("Fichier non trouvé: " . $file);
                    echo "<p>Section en cours de développement</p>";
                }
            } else {
                include "includes/dashboard/home.php";
            }
            ?>
        </div>
    </main>

    <script type="module" src="scripts/sections/dashboard.js"></script>
</body>
</html> 