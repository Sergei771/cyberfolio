<?php
/**
 * @file technologies.php
 * @description Gestion des technologies dans le dashboard
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier la connexion PDO
if (!isset($pdo)) {
    require_once __DIR__ . '/../../includes/db_connect.php';
}

// Au début du fichier, après la connexion PDO
error_log("Démarrage du chargement des technologies");

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

try {
    // Compte total des technologies
    $total = $pdo->query("SELECT COUNT(*) FROM technology")->fetchColumn();
    error_log("Nombre total de technologies : " . $total);
    
    // Récupération des technologies avec pagination
    $stmt = $pdo->prepare("
        SELECT 
            id_technology,
            name,
            logo,
            version,
            etc_,
            level
        FROM technology 
        ORDER BY name ASC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$perPage, $offset]);
    $technologies = $stmt->fetchAll();
    error_log("Technologies récupérées : " . json_encode($technologies));
    
} catch (PDOException $e) {
    error_log("Erreur technologies.php : " . $e->getMessage());
    error_log("Stack trace : " . $e->getTraceAsString());
    $technologies = [];
    $total = 0;
}

// Traitement AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Vérifier si c'est une requête AJAX
error_log("Type de requête : " . ($isAjax ? 'AJAX' : 'Normal'));

if ($isAjax && isset($_GET['action'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'technologies' => $technologies,
        'total' => $total
    ]);
    exit;
}
?>

<div class="technologies-section">
    <div class="section-header">
        <h2>Gestion des Technologies</h2>
        <button class="btn btn-primary" id="addTechBtn">
            <i class="fas fa-plus"></i>
            <span>Nouvelle Technologie</span>
        </button>
    </div>

    <!-- Tableau des technologies -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Nom</th>
                    <th>Version</th>
                    <th>Niveau</th>
                    <th>Informations</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($technologies as $tech): ?>
                    <tr data-id="<?php echo $tech['id_technology']; ?>">
                        <td class="tech-logo">
                            <?php if ($tech['logo']): ?>
                                <img src="<?php echo htmlspecialchars($tech['logo']); ?>" 
                                     alt="Logo <?php echo htmlspecialchars($tech['name']); ?>"
                                     width="30" height="30">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($tech['name']); ?></td>
                        <td><?php echo htmlspecialchars($tech['version'] ?? '-'); ?></td>
                        <td>
                            <div class="level-bar">
                                <div class="level-progress" style="width: <?php echo $tech['level'] ?? 50; ?>%"></div>
                                <span class="level-text"><?php echo $tech['level'] ?? 50; ?>%</span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($tech['etc_'] ?? ''); ?></td>
                        <td class="actions">
                            <button class="btn btn-edit" data-id="<?php echo $tech['id_technology']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-delete" data-id="<?php echo $tech['id_technology']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total > $perPage): ?>
        <div class="pagination">
            <?php
            $totalPages = ceil($total / $perPage);
            for ($i = 1; $i <= $totalPages; $i++):
            ?>
                <a href="?section=technologies&page=<?php echo $i; ?>" 
                   class="page-link <?php echo $page === $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal pour ajouter/modifier une technologie -->
<div class="modal" id="techModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Nouvelle Technologie</h3>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form id="techForm">
            <div class="modal-body">
                <input type="hidden" name="id_technology" id="techId">
                
                <div class="form-group">
                    <label for="name">Nom *</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="logo">Logo URL</label>
                    <input type="url" id="logo" name="logo" 
                           placeholder="https://exemple.com/logo.svg">
                </div>

                <div class="form-group">
                    <label for="version">Version</label>
                    <input type="text" id="version" name="version" 
                           placeholder="ex: 3.13.1">
                </div>

                <div class="form-group">
                    <label for="level">Niveau (%) *</label>
                    <input type="range" id="level" name="level" min="0" max="100" value="50">
                    <span class="level-value">50%</span>
                </div>

                <div class="form-group">
                    <label for="etc">Informations supplémentaires</label>
                    <textarea id="etc" name="etc_" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div> 