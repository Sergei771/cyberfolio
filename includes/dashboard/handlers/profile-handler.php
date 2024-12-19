<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../includes/db_connect.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expirée']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Données POST reçues : " . print_r($_POST, true));
    try {
        // Validation et nettoyage des données textuelles
        $firstname = htmlspecialchars($_POST['firstname'] ?? '', ENT_QUOTES, 'UTF-8');
        $lastname = htmlspecialchars($_POST['lastname'] ?? '', ENT_QUOTES, 'UTF-8');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $spécialisation = htmlspecialchars($_POST['spécialisation'] ?? '', ENT_QUOTES, 'UTF-8');
        $a_propos = htmlspecialchars($_POST['a_propos'] ?? '', ENT_QUOTES, 'UTF-8');
        $a_propos = addslashes($a_propos);
        $github = filter_var($_POST['github'] ?? '', FILTER_SANITIZE_URL);
        $linkedin = filter_var($_POST['linkedin'] ?? '', FILTER_SANITIZE_URL);
        $passion = htmlspecialchars($_POST['passion'] ?? '', ENT_QUOTES, 'UTF-8');
        $passion_hero = htmlspecialchars($_POST['passion_hero'] ?? '', ENT_QUOTES, 'UTF-8');

        // Gestion de l'upload de la photo
        $photo_path = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $upload_dir = __DIR__ . '/../../../assets/profile/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                throw new Exception('Format de photo non autorisé');
            }

            $photo_filename = 'photo_' . time() . '.' . $file_extension;
            $photo_path = 'assets/profile/' . $photo_filename;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_filename)) {
                throw new Exception('Erreur lors de l\'upload de la photo');
            }
        }

        // Gestion de l'upload du CV
        $cv_path = null;
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {
            $upload_dir = __DIR__ . '/../../../assets/profile/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['pdf', 'doc', 'docx'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                throw new Exception('Format de CV non autorisé');
            }

            $cv_filename = 'cv_' . time() . '.' . $file_extension;
            $cv_path = 'assets/profile/' . $cv_filename;

            if (!move_uploaded_file($_FILES['cv']['tmp_name'], $upload_dir . $cv_filename)) {
                throw new Exception('Erreur lors de l\'upload du CV');
            }
        }

        // Construction de la requête SQL dynamique
        $sql_parts = [
            'firstname = ?',
            'lastname = ?',
            'email = ?',
            'spécialisation = ?',
            'a_propos = ?',
            'github = ?',
            'linkedin = ?',
            'passion = ?',
            'passion_hero = ?'
        ];
        $params = [
            $firstname,
            $lastname,
            $email,
            $spécialisation,
            $a_propos,
            $github,
            $linkedin,
            $passion,
            $passion_hero
        ];

        // Ajout conditionnel des champs fichiers
        if ($photo_path) {
            $sql_parts[] = 'photo = ?';
            $params[] = $photo_path;
        }
        if ($cv_path) {
            $sql_parts[] = 'CV = ?';
            $params[] = $cv_path;
        }

        // Ajout de l'ID pour la clause WHERE
        $params[] = $_SESSION['admin_id'];

        $sql = "UPDATE profile SET " . implode(', ', $sql_parts) . " WHERE id_profile = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);

        error_log("Résultat de l'exécution : " . ($result ? "succès" : "échec"));

        if ($result) {
            error_log("Mise à jour réussie");
            echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès']);
        } else {
            error_log("Échec de la mise à jour");
            throw new Exception('Erreur lors de la mise à jour du profil');
        }
    } catch (Exception $e) {
        error_log("Erreur : " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
} 