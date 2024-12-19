<?php
/**
 * @file technology-handler.php
 * @description Gestionnaire des requêtes AJAX pour les technologies
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../includes/db_connect.php';

// Vérification de la session admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Session expirée']);
    exit;
}

try {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            // Validation des données
            if (empty($_POST['name'])) {
                throw new Exception('Le nom est requis');
            }

            $stmt = $pdo->prepare("
                INSERT INTO technology (
                    name, 
                    logo, 
                    version, 
                    level, 
                    etc_
                ) VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $_POST['name'],
                $_POST['logo'] ?? null,
                $_POST['version'] ?? null,
                $_POST['level'] ?? 50,
                $_POST['etc_'] ?? null
            ]);

            echo json_encode([
                'success' => true, 
                'message' => 'Technologie ajoutée avec succès',
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'update':
            if (empty($_POST['id_technology'])) {
                throw new Exception('ID de la technologie manquant');
            }

            $stmt = $pdo->prepare("
                UPDATE technology 
                SET name = ?, 
                    logo = ?,
                    version = ?,
                    level = ?, 
                    etc_ = ?
                WHERE id_technology = ?
            ");
            
            $stmt->execute([
                $_POST['name'],
                $_POST['logo'] ?? null,
                $_POST['version'] ?? null,
                $_POST['level'] ?? 50,
                $_POST['etc_'] ?? null,
                $_POST['id_technology']
            ]);

            echo json_encode([
                'success' => true, 
                'message' => 'Technologie mise à jour avec succès'
            ]);
            break;

        case 'delete':
            if (empty($_POST['id'])) {
                throw new Exception('ID de la technologie manquant');
            }

            // Vérifier si la technologie est utilisée dans des projets
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM project_technology 
                WHERE id_technology = ?
            ");
            $stmt->execute([$_POST['id']]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Cette technologie est utilisée dans un ou plusieurs projets');
            }

            $stmt = $pdo->prepare("DELETE FROM technology WHERE id_technology = ?");
            $stmt->execute([$_POST['id']]);

            echo json_encode([
                'success' => true, 
                'message' => 'Technologie supprimée avec succès'
            ]);
            break;

        case 'get':
            if (empty($_GET['id'])) {
                throw new Exception('ID de la technologie manquant');
            }

            $stmt = $pdo->prepare("
                SELECT * FROM technology 
                WHERE id_technology = ?
            ");
            
            $stmt->execute([$_GET['id']]);
            $tech = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tech) {
                throw new Exception('Technologie non trouvée');
            }

            echo json_encode([
                'success' => true, 
                'technology' => $tech
            ]);
            break;

        default:
            throw new Exception('Action non reconnue');
    }

} catch (Exception $e) {
    error_log("Erreur technology-handler.php : " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
} 