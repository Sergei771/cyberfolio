-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 19 Décembre 2024 à 21:16
-- Version du serveur :  5.7.11
-- Version de PHP :  7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `cyberfolio`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(1, 'Cybersécurité'),
(2, 'Développement'),
(3, 'Outils & Technologies');

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

CREATE TABLE `competences` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `date_mise_a_jour` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `competences`
--

INSERT INTO `competences` (`id`, `nom`, `score`, `categorie_id`, `date_mise_a_jour`) VALUES
(1, 'Analyse de vulnérabilités', 85, 1, '2024-12-19 18:09:10'),
(2, 'Tests d\'intrusion', 75, 1, '2024-12-19 18:09:10'),
(3, 'Sécurité des réseaux', 80, 1, '2024-12-19 18:09:10'),
(4, 'HTML/CSS', 90, 2, '2024-12-19 18:09:10'),
(5, 'JavaScript', 85, 2, '2024-12-19 18:09:10'),
(6, 'Python', 80, 2, '2024-12-19 18:09:10'),
(7, 'SQL', 75, 2, '2024-12-19 18:09:10'),
(8, 'Git/GitHub', 85, 3, '2024-12-19 18:09:10'),
(9, 'Linux', 80, 3, '2024-12-19 18:09:10'),
(10, 'Wireshark', 75, 3, '2024-12-19 18:15:59'),
(11, 'Metasploit', 70, 3, '2024-12-19 18:09:10'),
(12, 'Analyse Forensic', 80, 1, '2024-12-19 18:40:13'),
(13, 'Analyse SOC', 65, 1, '2024-12-19 18:41:08');

-- --------------------------------------------------------

--
-- Structure de la table `profile`
--

CREATE TABLE `profile` (
  `id_profile` int(11) NOT NULL,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spécialisation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_propos` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CV` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passion_hero` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `profile`
--

INSERT INTO `profile` (`id_profile`, `firstname`, `lastname`, `email`, `password`, `spécialisation`, `a_propos`, `CV`, `photo`, `github`, `linkedin`, `passion`, `passion_hero`) VALUES
(1, 'Drivshnofer', 'Marilov', 'marilov@gmail.com', '$2y$10$9W5RPVeHyZQs2R8Z0LK6NOfIDXdujBO0kNGLGKXK7ic2uroT6QB2u', 'Cybersécurité, Scripting, LLM', 'Passionné par le développement web, j&#039;aime concevoir des applications performantes et intuitives en combinant le front end et le back end. Actuellement en fin de formation de développeur full stack, mon objectif est d&#039;apporter des solutions numériques innovantes, adaptées aux besoins des utilisateurs.', 'assets/profile/cv_1734629549.pdf', 'assets/profile/photo_1734606794.png', 'https://github.com/Marilov33', 'https://www.linkedin.com/in/drivshnofer-marilov-3a8a04340/', 'Développement web\r\nDéveloppement front end\r\nDéveloppement back end', 'En attente de la phrase d&#039;accroche');

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

CREATE TABLE `project` (
  `id_project` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `screenshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completed_at` date NOT NULL,
  `id_profile` int(11) NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'in_progress'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `project`
--

INSERT INTO `project` (`id_project`, `title`, `summary`, `description`, `screenshot`, `completed_at`, `id_profile`, `status`) VALUES
(2, 'Analyse de Sécurité', 'Details', 'Développement d\'un outil d\'analyse automatisée des vulnérabilités pour applications web. Implémentation de tests de pénétration et génération de rapports détaillés.', 'assets/projects/67646b92aa830_test12.jpg', '2024-12-19', 1, 'completed'),
(3, 'Bot Discord Sécurité', 'Details', 'Développement d\'un bot Discord pour la gestion automatisée de la sécurité des serveurs : détection de spam, vérification des permissions, logs d\'activités suspectes.', 'assets/projects/67646ce82de6b_test13.jpg', '2024-12-19', 1, 'completed'),
(4, 'Script d\'Automatisation Réseau', 'Details', 'Création d\'un ensemble de scripts pour l\'automatisation de tâches réseau : surveillance du trafic, détection d\'intrusion, sauvegarde automatique des configurations.', 'assets/projects/67646d7cba196_test14.jpg', '2024-12-19', 1, 'in_progress'),
(5, 'test1', 'azerzetezr', 'Je suis une descriptions détaillée', 'assets/projects/67647a0f729c3_test12.jpg', '2024-12-19', 1, 'completed');

-- --------------------------------------------------------

--
-- Structure de la table `project_technology`
--

CREATE TABLE `project_technology` (
  `id_technology` int(11) NOT NULL,
  `id_project` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `project_technology`
--

INSERT INTO `project_technology` (`id_technology`, `id_project`) VALUES
(2, 2),
(5, 2),
(7, 2),
(1, 3),
(2, 3),
(5, 3),
(7, 3),
(2, 4),
(5, 4),
(7, 4),
(1, 5),
(5, 5),
(7, 5);

-- --------------------------------------------------------

--
-- Structure de la table `technology`
--

CREATE TABLE `technology` (
  `id_technology` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etc_` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int(11) DEFAULT '50'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `technology`
--

INSERT INTO `technology` (`id_technology`, `name`, `logo`, `version`, `etc_`, `level`) VALUES
(1, 'Python', 'https://www.python.org/static/img/python-logo.png', '3.13.1', NULL, 50),
(2, 'HTML', 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/HTML5_logo_and_wordmark.svg/langfr-130px-HTML5_logo_and_wordmark.svg.png', '5.3', NULL, 50),
(4, 'PHP', 'https://www.php.net//images/logos/new-php-logo.svg', '7.3.7', NULL, 50),
(5, 'JavaScript', 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/JavaScript-logo.png/600px-JavaScript-logo.png?20120221235433', NULL, NULL, 50),
(6, 'MySQL', 'https://www.svgrepo.com/show/303251/mysql-logo.svg', NULL, NULL, 50),
(7, 'css', 'https://img.icons8.com/color/50/css3.png', '8.9.2', '', 80);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `competences`
--
ALTER TABLE `competences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Index pour la table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id_profile`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id_project`),
  ADD KEY `id_profile` (`id_profile`);

--
-- Index pour la table `project_technology`
--
ALTER TABLE `project_technology`
  ADD PRIMARY KEY (`id_technology`,`id_project`),
  ADD KEY `id_project` (`id_project`);

--
-- Index pour la table `technology`
--
ALTER TABLE `technology`
  ADD PRIMARY KEY (`id_technology`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `competences`
--
ALTER TABLE `competences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT pour la table `profile`
--
ALTER TABLE `profile`
  MODIFY `id_profile` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `project`
--
ALTER TABLE `project`
  MODIFY `id_project` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `technology`
--
ALTER TABLE `technology`
  MODIFY `id_technology` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `competences`
--
ALTER TABLE `competences`
  ADD CONSTRAINT `competences_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`id_profile`) REFERENCES `profile` (`id_profile`);

--
-- Contraintes pour la table `project_technology`
--
ALTER TABLE `project_technology`
  ADD CONSTRAINT `project_technology_ibfk_1` FOREIGN KEY (`id_technology`) REFERENCES `technology` (`id_technology`),
  ADD CONSTRAINT `project_technology_ibfk_2` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
