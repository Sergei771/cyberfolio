<?php
/**
 * @file project-handler.php
 * @description Gestionnaire des requêtes AJAX pour les projets
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

function handleImageUpload($file) {
    // Vérification initiale du fichier
    if (!isset($file) || $file['error'] !== 0) {
        throw new Exception('Aucun fichier valide n\'a été fourni');
    }

    error_log("Tentative d'upload d'image : " . print_r($file, true));
    $uploadDir = '../../../assets/projects/';
    
    if (!file_exists($uploadDir)) {
        error_log("Création du dossier : " . $uploadDir);
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($file['name']);
    $uploadFile = $uploadDir . $fileName;
    
    // Vérifier le type MIME
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Type de fichier non autorisé');
    }
    
    // Limiter la taille
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        throw new Exception('Fichier trop volumineux');
    }

    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        return 'assets/projects/' . $fileName;
    } else {
        throw new Exception('Erreur lors de l\'upload de l\'image');
    }
}

try {
    $action = $_POST['action'] ?? '';
    error_log("Action reçue : " . $action);
    error_log("Données POST reçues : " . print_r($_POST, true));
    
    switch ($action) {
        case 'create':
            try {
                error_log("Tentative de création d'un projet");
                // Validation des données
                if (empty($_POST['title']) || empty($_POST['description'])) {
                    throw new Exception('Titre et description obligatoires');
                }

                // Modification de la vérification de l'image
                $imagePath = null;
                if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== 0) {
                    throw new Exception('Une image est requise pour créer un projet');
                }
                $imagePath = handleImageUpload($_FILES['screenshot']);

                // Insertion du projet
                $stmt = $pdo->prepare("
                    INSERT INTO project (
                        title, 
                        summary,
                        description, 
                        screenshot,
                        completed_at,
                        status,
                        id_profile
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $_POST['title'],
                    $_POST['summary'],
                    $_POST['description'],
                    $imagePath,
                    $_POST['completed_at'] ?: null,
                    $_POST['status'],
                    $_SESSION['admin_id']
                ]);
                error_log("Projet créé avec succès");

                $projectId = $pdo->lastInsertId();

                // Association des technologies
                if (!empty($_POST['technologies'])) {
                    $techValues = [];
                    $techParams = [];
                    foreach ($_POST['technologies'] as $i => $techId) {
                        $techValues[] = "(?, ?)";
                        $techParams[] = $projectId;
                        $techParams[] = $techId;
                    }
                    
                    $sql = "INSERT INTO project_technology (id_project, id_technology) VALUES " . 
                           implode(', ', $techValues);
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($techParams);
                }

                echo json_encode(['success' => true, 'message' => 'Projet créé avec succès']);
                break;
            } catch (Exception $e) {
                error_log("Erreur project-handler.php : " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                break;
            }

        case 'update':
            if (empty($_POST['id_project'])) {
                throw new Exception('ID du projet manquant');
            }

            // Récupérer l'ancienne image avant la mise à jour
            $stmt = $pdo->prepare("SELECT screenshot FROM project WHERE id_project = ? AND id_profile = ?");
            $stmt->execute([$_POST['id_project'], $_SESSION['admin_id']]);
            $oldImage = $stmt->fetchColumn();

            $imagePath = null;
            if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === 0) {
                $imagePath = handleImageUpload($_FILES['screenshot']);
                // Supprimer l'ancienne image si elle existe
                if ($oldImage && file_exists(__DIR__ . '/../../../' . $oldImage)) {
                    unlink(__DIR__ . '/../../../' . $oldImage);
                }
            }

            $stmt = $pdo->prepare("
                UPDATE project 
                SET title = ?,
                    summary = ?,
                    description = ?,
                    completed_at = ?,
                    status = ?,
                    screenshot = COALESCE(?, screenshot)
                WHERE id_project = ? AND id_profile = ?
            ");

            $stmt->execute([
                $_POST['title'],
                $_POST['summary'],
                $_POST['description'],
                $_POST['completed_at'] ?: null,
                $_POST['status'],
                $imagePath,
                $_POST['id_project'],
                $_SESSION['admin_id']
            ]);

            // Mise à jour des technologies
            if (isset($_POST['technologies'])) {
                // Supprimer les anciennes associations
                $stmt = $pdo->prepare("DELETE FROM project_technology WHERE id_project = ?");
                $stmt->execute([$_POST['id_project']]);
                
                // Ajouter les nouvelles
                if (!empty($_POST['technologies'])) {
                    $techValues = [];
                    $techParams = [];
                    foreach ($_POST['technologies'] as $techId) {
                        $techValues[] = "(?, ?)";
                        $techParams[] = $_POST['id_project'];
                        $techParams[] = $techId;
                    }
                    
                    $sql = "INSERT INTO project_technology (id_project, id_technology) VALUES " . 
                           implode(', ', $techValues);
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($techParams);
                }
            }

            echo json_encode(['success' => true, 'message' => 'Projet mis à jour avec succès']);
            break;

        case 'delete':
            error_log("Tentative de suppression du projet : " . $_POST['id']);
            if (empty($_POST['id'])) {
                throw new Exception('ID du projet manquant');
            }

            // Supprimer d'abord les associations avec les technologies
            $stmt = $pdo->prepare("DELETE FROM project_technology WHERE id_project = ?");
            $stmt->execute([$_POST['id']]);

            // Récupérer le chemin de l'image avant la suppression
            $stmt = $pdo->prepare("
                SELECT screenshot 
                FROM project 
                WHERE id_project = ? AND id_profile = ?
            ");
            $stmt->execute([$_POST['id'], $_SESSION['admin_id']]);
            $project = $stmt->fetch();

            // Supprimer le fichier physique si existe
            if ($project && $project['screenshot']) {
                $filePath = __DIR__ . '/../../../' . $project['screenshot'];
                error_log("Tentative de suppression du fichier : " . $filePath);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Supprimer l'enregistrement de la base de données
            $stmt = $pdo->prepare("
                DELETE FROM project 
                WHERE id_project = ? AND id_profile = ?
            ");
            
            $stmt->execute([
                $_POST['id'],
                $_SESSION['admin_id']
            ]);

            error_log("Projet supprimé avec succès");
            echo json_encode(['success' => true, 'message' => 'Projet supprimé avec succès']);
            break;

        case 'get':
            if (empty($_GET['id'])) {
                throw new Exception('ID du projet manquant');
            }

            $stmt = $pdo->prepare("
                SELECT p.*, GROUP_CONCAT(pt.id_technology) as technologies
                FROM project p
                LEFT JOIN project_technology pt ON p.id_project = pt.id_project
                WHERE p.id_project = ? AND p.id_profile = ?
                GROUP BY p.id_project
            ");
            
            $stmt->execute([
                $_GET['id'],
                $_SESSION['admin_id']
            ]);

            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$project) {
                throw new Exception('Projet non trouvé');
            }

            // Convertir la chaîne des technologies en tableau
            if ($project['technologies']) {
                $project['technologies'] = array_map('intval', explode(',', $project['technologies']));
            } else {
                $project['technologies'] = [];
            }

            echo json_encode(['success' => true, 'project' => $project]);
            break;

        default:
            throw new Exception('Action non reconnue');
    }

} catch (Exception $e) {
    error_log("Erreur project-handler.php : " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 