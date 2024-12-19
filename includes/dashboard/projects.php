<?php
/**
 * @file projects.php
 * @description Gestion des projets dans le dashboard
 */

// Vérifier la session et la connexion PDO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pdo)) {
    require_once __DIR__ . '/../../includes/db_connect.php';
}

// Récupération des projets avec pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

try {
    error_log("Début de la récupération des projets");
    // Compte total des projets
    $total = $pdo->query("SELECT COUNT(*) FROM project")->fetchColumn();
    error_log("Nombre total de projets : " . $total);
    
    // Récupération des projets
    $stmt = $pdo->prepare("
        SELECT p.*, GROUP_CONCAT(t.name) as tech_names
        FROM project p
        LEFT JOIN project_technology pt ON p.id_project = pt.id_project
        LEFT JOIN technology t ON pt.id_technology = t.id_technology
        WHERE p.id_profile = ?
        GROUP BY p.id_project
        ORDER BY p.completed_at DESC, p.id_project DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$_SESSION['admin_id'], $perPage, $offset]);
    $projects = $stmt->fetchAll();
    error_log("Projets récupérés : " . json_encode($projects));

    // Récupération des technologies pour le formulaire
    $technologies = $pdo->query("SELECT * FROM technology ORDER BY name")->fetchAll();
    
} catch (PDOException $e) {
    error_log("Erreur projects.php : " . $e->getMessage());
    $projects = [];
    $technologies = [];
    $total = 0;
}

// Traitement AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Traitement du formulaire
        header('Content-Type: application/json');
        // ... traitement à implémenter
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}
?>

<div class="projects-section">
    <div class="section-header">
        <h2>Gestion des Projets</h2>
        <button class="btn btn-primary" id="addProjectBtn">
            <i class="fas fa-plus"></i>
            <span>Nouveau Projet</span>
        </button>
    </div>

    <!-- Filtres et recherche -->
    <div class="filters-bar">
        <div class="search-box">
            <input type="text" id="projectSearch" placeholder="Rechercher un projet...">
        </div>
        <div class="filters">
            <select id="techFilter">
                <option value="">Toutes les technologies</option>
                <?php foreach ($technologies as $tech): ?>
                    <option value="<?php echo htmlspecialchars($tech['id_technology']); ?>">
                        <?php echo htmlspecialchars($tech['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select id="statusFilter">
                <option value="">Tous les statuts</option>
                <option value="in_progress">En cours</option>
                <option value="completed">Terminé</option>
                <option value="paused">En pause</option>
            </select>
        </div>
    </div>

    <!-- Liste des projets -->
    <div class="projects-grid">
        <?php foreach ($projects as $project): ?>
            <div class="project-card" 
                 data-id="<?php echo $project['id_project']; ?>"
                 data-status="<?php echo $project['status']; ?>">
                <div class="status-indicator <?php echo $project['status']; ?>"
                     title="<?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>">
                </div>
                <div class="project-image">
                    <?php if ($project['screenshot']): ?>
                        <img src="<?php echo htmlspecialchars($project['screenshot']); ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>">
                    <?php else: ?>
                        <div class="no-image">Pas d'image</div>
                    <?php endif; ?>
                </div>
                <div class="project-info">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p class="project-summary"><?php echo htmlspecialchars($project['summary']); ?></p>
                    <?php if ($project['tech_names']): ?>
                        <div class="tech-tags">
                            <?php foreach(explode(',', $project['tech_names']) as $tech): ?>
                                <span class="tech-tag"><?php echo htmlspecialchars($tech); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($project['completed_at']): ?>
                        <div class="project-date">
                            Réalisé le <?php echo date('d/m/Y', strtotime($project['completed_at'])); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="project-actions">
                    <button class="btn btn-edit" data-id="<?php echo $project['id_project']; ?>" 
                            title="Modifier le projet">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-delete" data-id="<?php echo $project['id_project']; ?>"
                            title="Supprimer le projet">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total > $perPage): ?>
        <div class="pagination">
            <?php
            $totalPages = ceil($total / $perPage);
            for ($i = 1; $i <= $totalPages; $i++):
            ?>
                <a href="?section=projects&page=<?php echo $i; ?>" 
                   class="page-link <?php echo $page === $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal pour ajouter/modifier un projet -->
<div class="panel-overlay"></div>
<div class="slide-panel" id="projectPanel">
    <div class="slide-panel-header">
        <h3 class="panel-title">Nouveau Projet</h3>
        <button type="button" class="close" data-dismiss="panel">&times;</button>
    </div>
    <div class="slide-panel-content">
        <?php include __DIR__ . '/components/forms/project-form.php'; ?>
    </div>
</div> 