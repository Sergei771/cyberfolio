<?php
/**
 * @file home.php
 * @description Vue d'ensemble du tableau de bord
 */

// Vérifier la connexion PDO
if (!isset($pdo)) {
    require_once __DIR__ . '/../../includes/db_connect.php';
}

try {
    // Récupération des statistiques depuis la DB
    $stats = [
        'projects' => $pdo->query("SELECT COUNT(*) FROM project")->fetchColumn(),
        'technologies' => $pdo->query("SELECT COUNT(*) FROM technology")->fetchColumn(),
        'competences' => $pdo->query("SELECT COUNT(*) FROM competences")->fetchColumn()
    ];

    // Récupération des activités récentes
    $stmt = $pdo->prepare("
        SELECT p.*, GROUP_CONCAT(t.name) as tech_names
        FROM project p
        LEFT JOIN project_technology pt ON p.id_project = pt.id_project
        LEFT JOIN technology t ON pt.id_technology = t.id_technology
        GROUP BY p.id_project
        ORDER BY p.completed_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur home.php : " . $e->getMessage());
    $stats = ['projects' => 0, 'technologies' => 0, 'competences' => 0];
    $recentActivities = [];
}
?>

<div class="dashboard-overview">
    <!-- Cartes de statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <svg class="nav-icon" viewBox="0 0 24 24">
                    <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
                </svg>
            </div>
            <div class="stat-info">
                <span class="stat-value" data-stat="projects"><?php echo $stats['projects']; ?></span>
                <span class="stat-label">Projets</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon technologies">
                <svg class="nav-icon" viewBox="0 0 24 24">
                    <path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/>
                </svg>
            </div>
            <div class="stat-info">
                <span class="stat-value" data-stat="technologies"><?php echo $stats['technologies']; ?></span>
                <span class="stat-label">Technologies</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon competences">
                <svg class="nav-icon" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                </svg>
            </div>
            <div class="stat-info">
                <span class="stat-value" data-stat="competences"><?php echo $stats['competences']; ?></span>
                <span class="stat-label">Compétences</span>
            </div>
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="activity-section">
        <h2>Activités récentes</h2>
        <div class="activity-log">
            <div class="activity-log__content">
                <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-item">
                        <span class="activity-time">
                            <?php echo date('d/m/Y', strtotime($activity['completed_at'])); ?>
                        </span>
                        <span class="activity-text">
                            <?php echo "Projet : " . htmlspecialchars($activity['title']); ?>
                            <?php if ($activity['tech_names']): ?>
                                <span class="tech-tags">
                                    <?php foreach(explode(',', $activity['tech_names']) as $tech): ?>
                                        <span class="tech-tag"><?php echo htmlspecialchars($tech); ?></span>
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div> 