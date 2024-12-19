<?php
/**
 * @file competence-handler.php
 * @description Gestionnaire des requêtes AJAX pour les compétences
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
            if (empty($_POST['nom']) || empty($_POST['categorie_id']) || !isset($_POST['score'])) {
                throw new Exception('Tous les champs sont requis');
            }

            $stmt = $pdo->prepare("
                INSERT INTO competences (
                    nom,
                    categorie_id,
                    score,
                    date_mise_a_jour
                ) VALUES (?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $_POST['nom'],
                $_POST['categorie_id'],
                $_POST['score']
            ]);

            echo json_encode([
                'success' => true, 
                'message' => 'Compétence ajoutée avec succès',
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'update':
            if (empty($_POST['id_competences'])) {
                throw new Exception('ID de la compétence manquant');
            }

            $stmt = $pdo->prepare("
                UPDATE competences 
                SET nom = ?,
                    categorie_id = ?,
                    score = ?,
                    date_mise_a_jour = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([
                $_POST['nom'],
                $_POST['categorie_id'],
                $_POST['score'],
                $_POST['id_competences']
            ]);

            echo json_encode([
                'success' => true, 
                'message' => 'Compétence mise à jour avec succès'
            ]);
            break;

        case 'delete':
            if (empty($_POST['id'])) {
                throw new Exception('ID de la compétence manquant');
            }

            $stmt = $pdo->prepare("DELETE FROM competences WHERE id_competences = ?");
            $stmt->execute([$_POST['id']]);

            echo json_encode([
                'success' => true, 
                'message' => 'Compétence supprimée avec succès'
            ]);
            break;

        case 'get':
            if (empty($_GET['id'])) {
                throw new Exception('ID de la compétence manquant');
            }

            $stmt = $pdo->prepare("
                SELECT * FROM competences 
                WHERE id_competences = ?
            ");
            
            $stmt->execute([$_GET['id']]);
            $competence = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$competence) {
                throw new Exception('Compétence non trouvée');
            }

            echo json_encode([
                'success' => true, 
                'competence' => $competence
            ]);
            break;

        default:
            throw new Exception('Action non reconnue');
    }

} catch (Exception $e) {
    error_log("Erreur competence-handler.php : " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
} 