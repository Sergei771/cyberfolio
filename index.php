<?php
/**
 * Fichier : index.php
 * Description : Page principale du portfolio
 * 
 * Ce fichier gère l'affichage de la page d'accueil du portfolio et inclut :
 * - La navigation principale
 * - Les sections de contenu (à propos, compétences, projets)
 * - Le formulaire de contact modal
 */

session_start();
require_once 'includes/db_connect.php';

// Récupération des données utilisateur
try {
    $stmt = $pdo->prepare("
        SELECT firstname, lastname, spécialisation, a_propos, CV, passion, passion_hero, photo 
        FROM profile 
        LIMIT 1
    ");
    $stmt->execute();
    $profile = $stmt->fetch();
    
    if (!$profile) {
        error_log("Aucun profil trouvé dans la base de données");
        $profile = [
            'firstname' => 'Prénom',
            'lastname' => 'Nom',
            'spécialisation' => 'Description par défaut...',
            'a_propos' => 'À propos par défaut...',
            'passion' => 'Développement d\'applications\nAutomatisation\nScripting',
            'passion_hero' => 'Passionné par le développement de solutions de sécurité innovantes...'
        ];
    }
} catch (Exception $e) {
    error_log("Erreur lors de la récupération du profil : " . $e->getMessage());
    $profile = null;
}

// Récupération des compétences
$stmt = $pdo->query("
    SELECT c.nom as competence_nom, c.score, 
           cat.nom as categorie_nom, cat.id as categorie_id
    FROM competences c
    JOIN categories cat ON c.categorie_id = cat.id
    ORDER BY cat.id, c.nom
");

$competences = [];
while($row = $stmt->fetch()) {
    if (!isset($competences[$row['categorie_id']])) {
        $competences[$row['categorie_id']] = [
            'nom' => $row['categorie_nom'],
            'competences' => []
        ];
    }
    $competences[$row['categorie_id']]['competences'][] = [
        'nom' => $row['competence_nom'],
        'score' => $row['score']
    ];
}

// Récupération des projets
try {
    $stmt = $pdo->query("
        SELECT 
            p.id_project,
            p.title,
            p.description,
            p.completed_at,
            p.status,
            p.screenshot,
            GROUP_CONCAT(t.name) as technologies
        FROM project p
        LEFT JOIN project_technology pt ON p.id_project = pt.id_project
        LEFT JOIN technology t ON pt.id_technology = t.id_technology
        GROUP BY p.id_project
        ORDER BY p.completed_at DESC
    ");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des projets : " . $e->getMessage());
    $projects = [];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($profile['description'] ?? 'Portfolio...'); ?>">
    
    <!-- Open Graph / Réseaux sociaux -->
    <meta property="og:title" content="Cyberfolio - Étudiant en Cybersécurité">
    <meta property="og:description" content="Portfolio d'un étudiant passionné par la cybersécurité, l'automatisation et le développement de solutions de sécurité">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://votre-domaine.com">
    <meta property="og:image" content="assets/images/preview.jpg">
    
    <!-- Thème et couleurs -->
    <meta name="theme-color" content="#0a192f">
    
    <!-- Préchargement des polices -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" as="style">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap">
    
    <title>Cyberfolio - Étudiant en Cybersécurité</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="canonical" href="https://votre-domaine.com" />
    <meta name="robots" content="index, follow">
    <meta name="keywords" content="cybersécurité, portfolio, étudiant, Guardia School, développement, automatisation">
</head>
<body>
    <div class="loader">
        <div class="loader__content">
            <div class="loader__spinner"></div>
        </div>
    </div>
    <!-- Navigation fixe avec effet de transparence -->
    <nav class="nav" role="navigation" aria-label="Navigation principale">
        <div class="container nav__container">
            <div class="nav__logo">
                <a href="#" class="logo-link">
                    <?php echo htmlspecialchars($profile['prenom'] ?? 'Mon'); ?>
                    <span class="highlight">Portfolio</span>
                </a>
            </div>
            <button class="nav__toggle" aria-label="Toggle menu">
                <span class="hamburger"></span>
            </button>
            <ul class="nav__links">
                <li><a href="#about" class="nav__link">À propos</a></li>
                <li><a href="#skills" class="nav__link">Compétences</a></li>
                <li><a href="#projects" class="nav__link">Projets</a></li>
                <li><a href="#contact" class="nav__link">Contact</a></li>
            </ul>
        </div>
    </nav>

    <main role="main" class="main">
        <!-- Hero section avec animation de texte -->
        <header class="header">
            <div class="container header__container">
                <div class="header__content fade-in">
                    <p class="header__greeting" aria-label="Salutation">Bonjour, je suis</p>
                    <h1 class="header__title">
                        <span class="header__name highlight">
                            <?php echo htmlspecialchars($profile['firstname'] . ' ' . $profile['lastname']); ?>
                        </span>
                        <span class="header__role">
                            <span class="header__static">Spécialisé en</span>
                            <span class="header__dynamic" 
                                  aria-label="Compétences"
                                  data-specialisations="<?php 
                                      // On suppose que les spécialisations sont séparées par des virgules dans la DB
                                      echo htmlspecialchars($profile['spécialisation']); 
                                  ?>">
                                <span class="header__dynamic-text"></span>
                                <span class="header__cursor">|</span>
                            </span>
                        </span>
                    </h1>
                    <p class="header__subtitle"><?php echo stripslashes($profile['passion_hero']); ?></p>
                    <div class="header__cta">
                        <a href="#projects" class="btn btn-primary">
                            <span class="btn-text">Voir mes projets</span>
                            <span class="btn-icon">→</span>
                        </a>
                        <a href="#contact" class="btn btn-secondary">
                            <span class="btn-text">Me contacter</span>
                            <span class="btn-icon">✉</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Après la section header -->
        <section class="dynamic-section">
            <div class="container">
                <div class="dynamic-content">
                    <!-- Le contenu sera injecté dynamiquement via JavaScript -->
                </div>
            </div>
        </section>

        <!-- Section À propos avec effet de parallaxe subtil -->
        <section id="about" class="about" aria-labelledby="about-title">
            <div class="container">
                <h2 id="about-title" class="section-title slide-in">
                    À propos de moi
                    <span class="section-title__decoration"></span>
                </h2>
                
                <div class="about__content">
                    <div class="about__text-container slide-in">
                        <div class="about__text">
                            <p class="about__description">
                                <?php echo nl2br(stripslashes($profile['a_propos'])); ?>
                            </p>

                            <div class="about__interests">
                                <p class="about__subtitle">Je m'intéresse particulièrement au :</p>
                                <ul class="about__list">
                                    <?php 
                                    // On suppose que les passions sont séparées par des retours à la ligne dans la DB
                                    $passions = explode("\n", $profile['passion']);
                                    foreach($passions as $passion): 
                                        if(trim($passion)): // Vérifie que la ligne n'est pas vide
                                    ?>
                                        <li class="about__list-item">
                                            <span class="about__list-icon">▹</span>
                                            <span class="about__list-text"><?php echo htmlspecialchars(trim($passion)); ?></span>
                                        </li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                            </div>

                            <?php if ($profile['CV']): ?>
                                <div class="about__cv">
                                    <a href="<?php echo htmlspecialchars($profile['CV']); ?>" 
                                       class="about__cv-button" 
                                       target="_blank"
                                       rel="noopener noreferrer">
                                        <span class="about__cv-text">Voir mon CV</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($profile['photo']): ?>
                        <div class="about__image-container slide-in">
                            <div class="profile-image">
                                <img src="<?php echo htmlspecialchars($profile['photo']); ?>" 
                                     alt="Photo de profil" 
                                     class="profile-image__img"
                                     loading="lazy">
                                <div class="profile-image__overlay"></div>
                                <div class="profile-image__frame"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Section Compétences avec effet de carte -->
        <section id="skills" class="skills" aria-labelledby="skills-title">
            <div class="container">
                <h2 id="skills-title" class="section-title slide-in">
                    Mes Compétences
                    <span class="section-title__decoration"></span>
                </h2>

                <div class="skills__filters slide-in">
                    <button class="skills__filter active" data-filter="all">Tous</button>
                    <?php 
                    // Récupérer les catégories pour les boutons
                    $stmt = $pdo->query("SELECT id, nom FROM categories ORDER BY id");
                    while($cat = $stmt->fetch()) {
                        echo '<button class="skills__filter" data-filter="' . $cat['id'] . '">' . 
                             htmlspecialchars($cat['nom']) . '</button>';
                    }
                    ?>
                </div>

                <div class="skills__grid">
                    <?php foreach($competences as $categorie_id => $categorie): ?>
                        <div class="skill-card slide-in" data-category="<?php echo $categorie_id; ?>">
                            <div class="skill-card__header">
                                <div class="skill-card__icon">
                                    <svg viewBox="0 0 24 24">
                                        <!-- Icône selon la catégorie -->
                                    </svg>
                                </div>
                                <h3 class="skill-card__title"><?php echo htmlspecialchars($categorie['nom']); ?></h3>
                            </div>
                            
                            <div class="skill-card__content">
                                <?php foreach($categorie['competences'] as $competence): ?>
                                    <div class="skill-item">
                                        <div class="skill-item__header">
                                            <span class="skill-item__name"><?php echo htmlspecialchars($competence['nom']); ?></span>
                                            <span class="skill-item__level"><?php echo htmlspecialchars($competence['score']); ?>%</span>
                                        </div>
                                        <div class="skill-item__bar">
                                            <div class="skill-item__progress" data-level="<?php echo htmlspecialchars($competence['score']); ?>"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Section Projets avec effet de carte -->
        <section id="projects" class="projects" aria-labelledby="projects-title">
            <div class="container">
                <h2 id="projects-title" class="section-title slide-in">
                    Projets Sélectionnés
                    <span class="section-title__decoration"></span>
                </h2>
                
                <div class="carousel" aria-live="polite">
                    <div class="carousel__track">
                        <?php if (!empty($projects)): ?>
                            <?php foreach($projects as $project): ?>
                                <article class="carousel__slide" aria-hidden="false" data-category="<?php echo htmlspecialchars($project['status']); ?>">
                                    <div class="project-card">
                                        <div class="project-card__content">
                                            <h3 class="project-card__title"><?php echo htmlspecialchars($project['title']); ?></h3>
                                            <p class="project-card__description"><?php echo htmlspecialchars($project['description']); ?></p>
                                            <div class="project-card__tech">
                                                <?php 
                                                if (!empty($project['technologies'])) {
                                                    $technologies = explode(',', $project['technologies']);
                                                    foreach($technologies as $tech): 
                                                        if(trim($tech)):
                                                ?>
                                                    <span class="tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                                <?php 
                                                        endif;
                                                    endforeach;
                                                }
                                                ?>
                                            </div>
                                            <?php if ($project['screenshot']): ?>
                                                <div class="project-card__actions">
                                                    <a href="<?php echo htmlspecialchars($project['screenshot']); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-secondary btn-sm">
                                                        <span class="btn-text">Aperçu</span>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-projects-message">
                                <p>Aucun projet à afficher pour le moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="carousel__nav">
                        <button class="carousel__button carousel__button--prev" aria-label="Projet précédent">
                            <svg viewBox="0 0 24 24">
                                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                            </svg>
                        </button>
                        <div class="carousel__dots" role="tablist" aria-label="Navigation des projets">
                            <!-- Les points seront générés par JS -->
                        </div>
                        <button class="carousel__button carousel__button--next" aria-label="Projet suivant">
                            <svg viewBox="0 0 24 24">
                                <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer avec liens sociaux animés -->
        <footer id="contact" class="footer">
            <div class="container">
                <h2 class="section-title">Me Contacter</h2>
                <div class="contact__content">
                    <p class="contact__text">Je suis actuellement à la recherche de nouvelles opportunités. Vous pouvez me contacter via mes liens ci-dessous !</p>
                    
                    <div class="social-links">
                        <a href="<?php echo htmlspecialchars($profile['github_url'] ?? '#'); ?>" 
                           class="social-link" 
                           aria-label="GitHub"
                           target="_blank" 
                           rel="noopener noreferrer">
                            <svg class="social-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                            <span class="social-text">GitHub</span>
                        </a>
                        <a href="#" class="social-link" aria-label="LinkedIn">
                            <svg class="social-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            <span class="social-text">LinkedIn</span>
                        </a>
                        <a href="contact.php" class="social-link" aria-label="Email">
                            <svg class="social-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M0 3v18h24v-18h-24zm21.518 2l-9.518 7.713-9.518-7.713h19.036zm-19.518 14v-11.817l10 8.104 10-8.104v11.817h-20z"/>
                            </svg>
                            <span class="social-text">Email</span>
                        </a>
                    </div>
                </div>
                <p class="footer__copyright"><?php echo htmlspecialchars("© 2024 - Developpé par un LLM"); ?></p>
            </div>
        </footer>
    </main>
    <script type="module" src="scripts/main.js"></script>
</body>
</html> 



<!-- Section Détecteur de Type de Hash -->
<section id="hash-detector" class="hash-detector" aria-labelledby="hash-detector-title">
    <div class="container">
        <h2 id="hash-detector-title" class="section-title slide-in">
            Détecteur de Type de Hash
            <span class="section-title__decoration"></span>
        </h2>

        <div class="hash-detector__content">
            <p>Entrez un hash pour détecter son type.</p>

            <!-- Formulaire de saisie du hash -->
            <form method="POST" action="index.php#hash-detector-title">
                <label for="hash_string">Entrez un hash :</label><br>
                <input type="text" id="hash_string" name="hash_string" required><br><br>
                <button type="submit" class="btn btn-primary">Détecter</button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                // Récupération du hash soumis via le formulaire
                $hash_string = escapeshellarg($_POST["hash_string"]);

                // Exécution du script Python pour détecter le type de hash
                $command = "python3 " . escapeshellarg(__DIR__ . "\app.py") . " $hash_string";

                $output = shell_exec($command);

                // Affichage du résultat
                if ($output === null) {
                    echo "<p>Erreur lors de l'exécution du script Python.</p>";
                } else {
                    echo "<h3>Type détecté : " . htmlspecialchars($output) . "</h3>";
                }
            }
            ?>

        </div>
    </div>
</section>


