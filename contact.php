<?php
/**
 * Fichier : contact.php
 * Description : Page de formulaire de contact
 */

session_start();
require_once 'includes/db_connect.php';

// Traitement du formulaire si POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Log du début de la requête
    error_log("Nouvelle tentative d'envoi de message");
    
    // Récupération et nettoyage des données
    $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $message = filter_var($_POST['message'] ?? '', FILTER_SANITIZE_STRING);
    
    // Log des données reçues
    error_log("Données reçues - Nom: $name, Email: $email");
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'Tous les champs sont requis.';
        error_log("Erreur: champs manquants");
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Email invalide.';
        error_log("Erreur: email invalide - $email");
        echo json_encode($response);
        exit;
    }

    // Configuration de l'email pour Papercut
    $to = 'test@localhost';
    $subject = "Message de contact - Cyberfolio";
    $messageBody = "De : $name\n";
    $messageBody .= "Email : $email\n\n";
    $messageBody .= "Message :\n$message";

    // Headers simples
    $headers = 'From: contact@cyberfolio.local';

    // Log avant tentative d'envoi
    error_log("Tentative d'envoi de mail à : $to");
    error_log("Configuration SMTP : " . ini_get('SMTP'));
    error_log("Port SMTP : " . ini_get('smtp_port'));

    // Envoi de l'email via Papercut
    if (mail($to, $subject, $messageBody, $headers)) {
        $response['success'] = true;
        $response['message'] = 'Message envoyé avec succès !';
        error_log("Mail envoyé avec succès");
    } else {
        $response['message'] = "Une erreur s'est produite lors de l'envoi du message.";
        $error = error_get_last();
        error_log("Échec de l'envoi du mail - Erreur : " . ($error ? json_encode($error) : "Inconnue"));
    }
    
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Cyberfolio</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <meta name="description" content="Contactez-moi pour vos projets de cybersécurité et développement">
    <meta name="robots" content="index, follow">
</head>
<body class="contact-body">
    <div class="contact-background">
        <div class="gradient-sphere"></div>
        <div class="gradient-sphere secondary"></div>
    </div>

    <main class="contact-page">
        <div class="container">
            <div class="contact-form-container">
                <div class="form-header">
                    <h1 class="glitch" data-text="Me contacter">Me contacter</h1>
                    <p class="contact-intro">Une idée ? Un projet ? N'hésitez pas à me contacter.</p>
                </div>
                
                <form id="contactForm" class="contact-form">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="text" 
                                   id="contactName" 
                                   name="name" 
                                   required 
                                   class="contact-input"
                                   placeholder=" "
                                   autocomplete="name">
                            <label for="contactName">Votre nom</label>
                            <div class="input-highlight"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="email" 
                                   id="contactEmail" 
                                   name="email" 
                                   required 
                                   class="contact-input"
                                   placeholder=" "
                                   autocomplete="email">
                            <label for="contactEmail">Votre email</label>
                            <div class="input-highlight"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <textarea id="contactMessage" 
                                     name="message" 
                                     required 
                                     class="contact-input contact-textarea"
                                     placeholder=" "></textarea>
                            <label for="contactMessage">Votre message</label>
                            <div class="input-highlight"></div>
                        </div>
                    </div>

                    <button type="submit" class="contact-submit">
                        <span class="button-text">Envoyer</span>
                        <span class="button-icon">→</span>
                    </button>
                </form>

                <div id="contactFormMessage" class="form-message"></div>
                <a href="/" class="back-link">
                    <span>Retour au portfolio</span>
                </a>
            </div>
        </div>
    </main>

    <script type="module" src="scripts/contact.js"></script>
</body>
</html> 