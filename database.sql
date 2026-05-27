-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 27 mai 2026 à 02:39
-- Version du serveur : 8.3.0
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `portfolio_md`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'admin', '$2y$10$y4y3rqJoukR9IjLoMLRpdONpaX1I9Bh5KZDpWUCaIGfxU6U4pS0ym', '2026-05-01 18:08:36');

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
  `subject` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `diplomas`
--

DROP TABLE IF EXISTS `diplomas`;
CREATE TABLE IF NOT EXISTS `diplomas` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `institution` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `year_start` year NOT NULL,
  `year_end` year DEFAULT NULL COMMENT 'NULL = en cours',
  `description` text COLLATE utf8mb4_general_ci,
  `badge_icon` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 0xF09F8E93,
  `sort_order` smallint NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `diplomas`
--

INSERT INTO `diplomas` (`id`, `title`, `institution`, `year_start`, `year_end`, `description`, `badge_icon`, `sort_order`, `created_at`) VALUES
(1, 'Licence en Génie Informatique', 'EFMI(Ecole de Formations Aux Metiers de l\'Informatique)', '2024', '2026', 'Formation pluridisciplinaire couvrant le développement logiciel, les bases de données, les réseaux et la gestion de projets informatiques et la maintenance informatique.', '🎓', 1, '2026-05-01 14:55:56'),
(2, 'BTS Informatique de Gestion', 'Institut Supérieur d\'Informatique', '2019', '2021', 'Spécialisation en développement d\'applications de gestion et administration des systèmes.', '📜', 2, '2026-05-01 14:55:56'),
(3, 'Certification Adobe Creative Suite', 'Adobe Authorized Training', '2022', '2022', 'Maîtrise certifiée de Photoshop, Illustrator, InDesign, Premiere Pro et After Effects.', '🏅', 3, '2026-05-01 14:55:56');

-- --------------------------------------------------------

--
-- Structure de la table `experiences`
--

DROP TABLE IF EXISTS `experiences`;
CREATE TABLE IF NOT EXISTS `experiences` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `job_title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `company` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_start` date NOT NULL,
  `date_end` date DEFAULT NULL COMMENT 'NULL = poste actuel',
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `tags` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'JSON array de compétences',
  `sort_order` smallint NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `experiences`
--

INSERT INTO `experiences` (`id`, `job_title`, `company`, `location`, `date_start`, `date_end`, `description`, `tags`, `sort_order`, `created_at`) VALUES
(1, 'Développeur Full-Stack & Designer', 'Freelance', 'Dakar, Sénégal', '2022-01-01', NULL, 'Conception et développement de sites web dynamiques, e-commerce et applications de gestion pour une clientèle variée. Création d\'identités visuelles complètes (logo, charte graphique, supports print).', '[\"PHP\",\"MySQL\",\"WINDEV\",\"Photoshop\",\"Illustrator\"]', 1, '2026-05-01 14:55:56'),
(2, 'Développeur WINDEV/WEBDEV', 'TechSolutions SN', 'Dakar, Sénégal', '2023-06-01', '2024-06-01', 'Développement d\'un logiciel de gestion commerciale complet (stocks, facturation, caisse) et d\'un système de gestion scolaire. Maintenance et évolution d\'applications existantes en WLangage.', '[\"WINDEV\",\"WEBDEV\",\"WLangage\",\"MySQL\"]', 2, '2026-05-01 14:55:56'),
(3, 'Designer Graphique', 'Agence Créativa', 'Dakar, Sénégal', '2021-09-01', '2022-12-01', 'Création de supports de communication (affiches, flyers, brochures, packaging). Motion design pour réseaux sociaux et spots publicitaires. Direction artistique de campagnes digitales.', '[\"Photoshop\",\"Illustrator\",\"After Effects\",\"Premiere Pro\",\"Cinema 4D\"]', 3, '2026-05-01 14:55:56');

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `category` enum('web','design','software','other') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'web',
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `tech_stack` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'JSON array',
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_live` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_code` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` smallint NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `title`, `category`, `description`, `tech_stack`, `image`, `link_live`, `link_code`, `is_featured`, `sort_order`, `created_at`) VALUES
(1, 'Système de Gestion Scolaire', 'software', 'Application desktop complète de gestion d\'établissements scolaires : inscriptions, bulletins, emplois du temps, suivi des paiements et génération de rapports PDF.', '[\"WINDEV\",\"WLangage\",\"MySQL\",\"HyperFileSQL\"]', NULL, NULL, NULL, 1, 1, '2026-05-01 14:55:56'),
(2, 'Plateforme E-Commerce', 'web', 'Boutique en ligne responsive avec gestion des produits, panier, paiement intégré et back-office administrateur complet.', '[\"PHP\",\"MySQL\",\"HTML5\",\"CSS3\",\"JavaScript\"]', NULL, NULL, NULL, 1, 2, '2026-05-01 14:55:56'),
(3, 'Identité Visuelle — Restaurant Teranga', 'design', 'Création complète d\'identité de marque : logo, charte graphique, carte de menu, signalétique et supports digitaux pour réseaux sociaux.', '[\"Illustrator\",\"Photoshop\",\"InDesign\"]', NULL, NULL, NULL, 1, 3, '2026-05-01 14:55:56'),
(4, 'Logiciel de Gestion Commerciale', 'software', 'ERP léger couvrant les achats, ventes, stocks et la comptabilité de base. Interface intuitive pensée pour les PME sénégalaises.', '[\"WINDEV\",\"WLangage\",\"MySQL\"]', NULL, NULL, NULL, 0, 4, '2026-05-01 14:55:56'),
(5, 'Portfolio Dynamique', 'web', 'Site portfolio full-stack avec thème sombre/clair, espace admin CRUD et formulaire de contact PHP.', '[\"PHP\",\"MySQL\",\"Tailwind CSS\",\"JavaScript\"]', NULL, NULL, NULL, 0, 5, '2026-05-01 14:55:56');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
