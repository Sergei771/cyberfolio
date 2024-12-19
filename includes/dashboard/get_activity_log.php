<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/db_connect.php';

try {
    $stmt = $pdo->prepare("
        SELECT 'project' as type,
               title as name,
               completed_at as date,
               'created' as action
        FROM project
        WHERE id_profile = ?
        ORDER BY completed_at DESC
        LIMIT 10
    ");
    $stmt->execute([$_SESSION['admin_id']]);
    $activities = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $activities
    ]);
} catch (PDOException $e) {
    error_log("Erreur get_activity_log.php : " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des activités']);
} 