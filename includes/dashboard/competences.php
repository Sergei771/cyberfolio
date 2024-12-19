<?php
/**
 * @file competences.php
 * @description Gestion des compétences dans le dashboard
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier la connexion PDO
if (!isset($pdo)) {
    require_once __DIR__ . '/../../includes/db_connect.php';
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

try {
    // Compte total des compétences
    $total = $pdo->query("SELECT COUNT(*) FROM competences")->fetchColumn();
    
    // Récupération des compétences avec pagination
    $stmt = $pdo->prepare("
        SELECT 
            c.id as id_competences,
            c.nom,
            c.score,
            c.categorie_id,
            c.date_mise_a_jour,
            cat.nom as categorie_nom
        FROM competences c
        LEFT JOIN categories cat ON c.categorie_id = cat.id
        ORDER BY cat.nom ASC, c.nom ASC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$perPage, $offset]);
    $competences = $stmt->fetchAll();
    
    // Debug - Afficher les données récupérées
    error_log("Compétences récupérées : " . print_r($competences, true));
    
} catch (PDOException $e) {
    error_log("Erreur competences.php : " . $e->getMessage());
    $competences = [];
    $total = 0;
}

// Traitement AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax && isset($_GET['action'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'competences' => $competences,
        'total' => $total
    ]);
    exit;
}
?>

<div class="competences-section">
    <div class="section-header">
        <h2>Gestion des Compétences</h2>
        <button class="btn btn-primary" id="addCompetenceBtn">
            <i class="fas fa-plus"></i>
            <span>Nouvelle Compétence</span>
        </button>
    </div>

    <!-- Tableau des compétences -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Score (%)</th>
                    <th>Date de mise à jour</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($competences as $competence): ?>
                    <tr data-id="<?php echo $competence['id_competences']; ?>">
                        <td><?php echo htmlspecialchars($competence['nom']); ?></td>
                        <td><?php echo htmlspecialchars($competence['categorie_nom']); ?></td>
                        <td>
                            <div class="level-bar">
                                <div class="level-progress" style="width: <?php echo $competence['score']; ?>%"></div>
                                <span class="level-text"><?php echo $competence['score']; ?>%</span>
                            </div>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($competence['date_mise_a_jour'])); ?></td>
                        <td class="actions">
                            <button class="btn btn-edit" data-id="<?php echo $competence['id_competences']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-delete" data-id="<?php echo $competence['id_competences']; ?>">
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
                <a href="?section=competences&page=<?php echo $i; ?>" 
                   class="page-link <?php echo $page === $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal pour ajouter/modifier une compétence -->
<div class="modal" id="competenceModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-plus-circle"></i>
                Nouvelle Compétence
            </h3>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form id="competenceForm" class="competence-form">
            <div class="modal-body">
                <input type="hidden" name="id_competences" id="competenceId">
                
                <div class="form-group">
                    <label for="nom">Nom de la compétence *</label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           class="form-control"
                           placeholder="Ex: HTML/CSS, Python, Linux..."
                           required>
                </div>

                <div class="form-group">
                    <label for="categorie_id">Catégorie *</label>
                    <select id="categorie_id" 
                            name="categorie_id" 
                            class="form-control"
                            required>
                        <option value="">Sélectionner une catégorie</option>
                        <?php
                        $cats = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll();
                        foreach($cats as $cat) {
                            echo '<option value="' . $cat['id'] . '">' . htmlspecialchars($cat['nom']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="score">Niveau de maîtrise (%) *</label>
                    <div class="score-input-container">
                        <input type="range" 
                               id="score" 
                               name="score" 
                               min="0" 
                               max="100" 
                               step="5"
                               class="score-slider"
                               required>
                        <span class="score-value">50%</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div> 