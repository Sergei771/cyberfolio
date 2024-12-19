<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/db_connect.php';

try {
    // Nombre total de projets
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM project WHERE id_profile = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $totalProjects = $stmt->fetchColumn();
    
    // Projets par statut
    $stmt = $pdo->prepare("
        SELECT status, COUNT(*) as count
        FROM project
        WHERE id_profile = ?
        GROUP BY status
    ");
    $stmt->execute([$_SESSION['admin_id']]);
    $projectsByStatus = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Technologies les plus utilisées
    $stmt = $pdo->prepare("
        SELECT t.name, COUNT(*) as count
        FROM technology t
        JOIN project_technology pt ON t.id_technology = pt.id_technology
        JOIN project p ON pt.id_project = p.id_project
        WHERE p.id_profile = ?
        GROUP BY t.id_technology
        ORDER BY count DESC
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['admin_id']]);
    $topTechnologies = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'totalProjects' => $totalProjects,
            'projectsByStatus' => $projectsByStatus,
            'topTechnologies' => $topTechnologies
        ]
    ]);
} catch (PDOException $e) {
    error_log("Erreur get_statistics.php : " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des statistiques']);
} 