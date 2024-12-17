-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mar 17 Décembre 2024 à 11:34
-- Version du serveur :  5.7.11
-- Version de PHP :  7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `portfolio`
--

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

CREATE TABLE `competences` (
  `id_competences` int(11) NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `competences`
--

INSERT INTO `competences` (`id_competences`, `nom`, `description`) VALUES
(1, 'Front-end', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `competences_technology`
--

CREATE TABLE `competences_technology` (
  `id_technology` int(11) NOT NULL,
  `id_competences` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `competences_technology`
--

INSERT INTO `competences_technology` (`id_technology`, `id_competences`) VALUES
(2, 1);

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
  `passion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `profile`
--

INSERT INTO `profile` (`id_profile`, `firstname`, `lastname`, `email`, `password`, `spécialisation`, `a_propos`, `CV`, `photo`, `github`, `linkedin`, `passion`) VALUES
(1, 'Drivshnofer ', 'Marilov', 'marilov@gmail.com', '$2y$10$9W5RPVeHyZQs2R8Z0LK6NOfIDXdujBO0kNGLGKXK7ic2uroT6QB2u', 'Front-end\r\n', 'Passionné par le développement web, j\'aime concevoir des applications performantes et intuitives en combinant le front end et le back end. Actuellement en fin de formation de développeur full stack, mon objectif est d\'apporter des solutions numériques innovantes, adaptées aux besoins des utilisateurs.', NULL, NULL, 'https://github.com/Marilov33', 'https://www.linkedin.com/in/drivshnofer-marilov-3a8a04340/', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

CREATE TABLE `project` (
  `id_project` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `screenshot` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed_at` date NOT NULL,
  `id_profile` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `project`
--

INSERT INTO `project` (`id_project`, `title`, `summary`, `description`, `screenshot`, `completed_at`, `id_profile`) VALUES
(1, 'test', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas convallis vitae erat non interdum. Nunc non velit consectetur, lobortis leo vel, imperdiet eros. Quisque lacinia id leo ac semper. In facilisis blandit risus quis porttitor. Donec quis elementum enim. Vestibulum efficitur molestie quam, eget tincidunt diam auctor vel. Nullam sit amet scelerisque justo. Proin eget felis eu dolor volutpat molestie lacinia non tortor. Nullam quis rhoncus velit. Cras in nibh est. Mauris at sodales purus. Curabitur sodales quam a magna faucibus, id pellentesque enim auctor.', '', '2024-09-03', 1);

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
(2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `technology`
--

CREATE TABLE `technology` (
  `id_technology` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etc_` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `technology`
--

INSERT INTO `technology` (`id_technology`, `name`, `logo`, `version`, `etc_`) VALUES
(1, 'Python', 'https://www.python.org/static/img/python-logo.png', '3.13.1', NULL),
(2, 'HTML', 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/HTML5_logo_and_wordmark.svg/langfr-130px-HTML5_logo_and_wordmark.svg.png', '5.3', NULL),
(3, 'CSS', 'https://upload.wikimedia.org/wikipedia/commons/d/d5/CSS3_logo_and_wordmark.svg', NULL, NULL),
(4, 'PHP', 'https://www.php.net//images/logos/new-php-logo.svg', '7.3.7', NULL),
(5, 'JavaScript', 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/JavaScript-logo.png/600px-JavaScript-logo.png?20120221235433', NULL, NULL),
(6, 'MySQL', 'https://www.svgrepo.com/show/303251/mysql-logo.svg', NULL, NULL);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `competences`
--
ALTER TABLE `competences`
  ADD PRIMARY KEY (`id_competences`);

--
-- Index pour la table `competences_technology`
--
ALTER TABLE `competences_technology`
  ADD PRIMARY KEY (`id_technology`,`id_competences`),
  ADD KEY `id_competences` (`id_competences`);

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
-- AUTO_INCREMENT pour la table `competences`
--
ALTER TABLE `competences`
  MODIFY `id_competences` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `profile`
--
ALTER TABLE `profile`
  MODIFY `id_profile` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `project`
--
ALTER TABLE `project`
  MODIFY `id_project` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `technology`
--
ALTER TABLE `technology`
  MODIFY `id_technology` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `competences_technology`
--
ALTER TABLE `competences_technology`
  ADD CONSTRAINT `competences_technology_ibfk_1` FOREIGN KEY (`id_technology`) REFERENCES `technology` (`id_technology`),
  ADD CONSTRAINT `competences_technology_ibfk_2` FOREIGN KEY (`id_competences`) REFERENCES `competences` (`id_competences`);

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
