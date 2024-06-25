-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 20 juin 2024 à 14:39
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `trombinoscope`
--

-- --------------------------------------------------------

--
-- Structure de la table `bureau`
--

DROP TABLE IF EXISTS `bureau`;
CREATE TABLE IF NOT EXISTS `bureau` (
  `id_bureau` int NOT NULL,
  `numero` varchar(255) NOT NULL,
  PRIMARY KEY (`id_bureau`),
  KEY `numero` (`id_bureau`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `bureau`
--

INSERT INTO `bureau` (`id_bureau`, `numero`) VALUES
(1, '5-B-030'),
(2, '5-C-009');

-- --------------------------------------------------------

--
-- Structure de la table `employeur`
--

DROP TABLE IF EXISTS `employeur`;
CREATE TABLE IF NOT EXISTS `employeur` (
  `id_employeur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `nom_court` varchar(255) NOT NULL,
  PRIMARY KEY (`id_employeur`),
  UNIQUE KEY `id_employeur` (`id_employeur`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `employeur`
--

INSERT INTO `employeur` (`id_employeur`, `nom`, `nom_court`) VALUES
(1, 'Grenoble INP', 'G-INP'),
(2, 'Université Grenoble Alpes', 'UGA'),
(3, 'Université de Savoie Mont Blanc', 'USMB'),
(4, 'Autre établissement d\'enseignement supérieur français', 'Enseignement Sup FR'),
(5, 'CNRS', 'CNRS'),
(6, 'INRIA', 'INRIA'),
(7, 'INSERM', 'INSERM'),
(8, 'CHU Grenoble Alpes', 'CHU-GA'),
(9, 'Etablissement Français du Sang', 'EFS'),
(10, 'CEA', 'CEA'),
(11, 'Ambassade française à l\'étranger', 'Ambassade Française'),
(12, 'Autre établissement public français', 'Autre Etab FR'),
(13, 'INPG Entreprise SA', 'INPG-SA'),
(14, 'FLORALIS', 'FLORALIS'),
(15, 'Autre entreprise privée', 'Autre Privé'),
(16, 'Établissement d\'enseignement supérieur à l\'étranger', 'Enseignement Sup Etranger'),
(17, 'Autre établissement ou organisme à l\'étranger', 'Autre Etab Etranger'),
(18, 'Autre', 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `encadrant`
--

DROP TABLE IF EXISTS `encadrant`;
CREATE TABLE IF NOT EXISTS `encadrant` (
  `id_encadrant` int NOT NULL,
  `id_sejour` int NOT NULL,
  `id_personne` int DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_encadrant`),
  KEY `id_personne` (`id_personne`),
  KEY `id_sejour` (`id_sejour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `encadrant`
--

INSERT INTO `encadrant` (`id_encadrant`, `id_sejour`, `id_personne`, `nom`, `prenom`) VALUES
(1, 2, 11, NULL, NULL),
(2, 4, NULL, 'Pierre', 'Pascal'),
(3, 6, 5, NULL, NULL),
(4, 8, 8, NULL, NULL),
(5, 12, 8, NULL, NULL),
(6, 13, 8, NULL, NULL),
(7, 14, 9, NULL, NULL),
(8, 15, 9, NULL, NULL),
(9, 18, 16, NULL, NULL),
(10, 21, 5, NULL, NULL),
(12, 4, 25, NULL, NULL),
(13, 2, 1, NULL, NULL),
(14, 31, 29, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `equipe`
--

DROP TABLE IF EXISTS `equipe`;
CREATE TABLE IF NOT EXISTS `equipe` (
  `id_equipe` int NOT NULL,
  `nom_court` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nom_long` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id_equipe`),
  KEY `id_equipe` (`id_equipe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `equipe`
--

INSERT INTO `equipe` (`id_equipe`, `nom_court`, `nom_long`) VALUES
(1, 'G2ELAB', 'G2ELAB'),
(2, 'Informatique', NULL),
(3, 'Physique des matériaux', NULL),
(4, 'Instrumentation', NULL),
(5, 'Mécatronique', NULL),
(6, 'Informatique Industrielle', NULL),
(8, 'Champs Magnétiques Faibles', NULL),
(10, 'Administratif et Financier', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `financement`
--

DROP TABLE IF EXISTS `financement`;
CREATE TABLE IF NOT EXISTS `financement` (
  `id_financement` int NOT NULL,
  `id_sejour` int NOT NULL,
  `id_employeur` int NOT NULL,
  PRIMARY KEY (`id_financement`),
  KEY `id_sejour` (`id_sejour`),
  KEY `id_employeur` (`id_employeur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `financement`
--

INSERT INTO `financement` (`id_financement`, `id_sejour`, `id_employeur`) VALUES
(2, 2, 5),
(3, 3, 5),
(4, 4, 5),
(5, 5, 5),
(6, 6, 10),
(7, 7, 1),
(8, 8, 1),
(9, 9, 10),
(10, 10, 1),
(11, 11, 10),
(12, 12, 5),
(13, 13, 5),
(14, 14, 2),
(15, 15, 5),
(16, 16, 1),
(17, 17, 5),
(18, 18, 7),
(19, 19, 5),
(20, 20, 1),
(21, 21, 1),
(22, 22, 2),
(23, 23, 2),
(24, 24, 2),
(25, 25, 1),
(26, 26, 1),
(27, 27, 1),
(29, 29, 5),
(30, 30, 5),
(31, 1, 10),
(33, 31, 1),
(35, 33, 10);

-- --------------------------------------------------------

--
-- Structure de la table `mail`
--

DROP TABLE IF EXISTS `mail`;
CREATE TABLE IF NOT EXISTS `mail` (
  `id_mail` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `id_personne` int NOT NULL,
  PRIMARY KEY (`id_mail`),
  UNIQUE KEY `id_mail` (`id_mail`),
  KEY `id_personne` (`id_personne`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `mail`
--

INSERT INTO `mail` (`id_mail`, `libelle`, `type`, `id_personne`) VALUES
(1, 'vince.danguillaume@g2elab.grenoble-inp.fr', 'Unité', 1),
(3, 'rachelle.leroux@g2elab.grenoble-inp.fr', 'Unité', 3),
(4, 'marcelle.rate@g2elab.grenoble-inp.fr', 'Unité', 4),
(6, 'almarick.Derbey@g2elab.grenoble-inp.fr', 'Unité', 6),
(7, 'rachelle.Polizzi@g2elab.grenoble-inp.fr', 'Unité', 7),
(8, 'emilie.Cuilla@g2elab.grenoble-inp.fr', 'Unité', 8),
(9, 'jean.Ferrari@g2elab.grenoble-inp.fr', 'Unité', 9),
(10, 'thierry.Braconnier@g2elab.grenoble-inp.fr', 'Unité', 10),
(11, 'delphine.Dargaud@g2elab.grenoble-inp.fr', 'Unité', 11),
(12, 'alphonse.Grimonet@g2elab.grenoble-inp.fr', 'Unité', 12),
(13, 'anna.Labonne@g2elab.grenoble-inp.fr', 'Unité', 13),
(14, 'colette.Pollet@g2elab.grenoble-inp.fr', 'Unité', 14),
(15, 'valentin.Imard@g2elab.grenoble-inp.fr', 'Unité', 15),
(16, 'corine.Marcon@g2elab.grenoble-inp.fr', 'Unité', 16),
(17, 'laurent.Estrabaut@g2elab.grenoble-inp.fr', 'Unité', 17),
(18, 'agathe.braos@g2elab.grenoble-inp.fr', 'Unité', 18),
(19, 'evan.Roche@g2elab.grenoble-inp.fr', 'Unité', 19),
(22, 'samuel.Flury@g2elab.grenoble-inp.fr', 'Unité', 22),
(23, 'mathieu.Vaillant@g2elab.grenoble-inp.fr', 'Unité', 23),
(24, 'sophie.Garcia@g2elab.grenoble-inp.fr', 'Unité', 24),
(25, 'rachelle.Auvergne@g2elab.grenoble-inp.fr', 'Unité', 25),
(26, 'francois.Dumas@g2elab.grenoble-inp.fr', 'Unité', 26),
(27, 'william.Norris@g2elab.grenoble-inp.fr', 'Unité', 27),
(28, 'sabrine.Loubet@g2elab.grenoble-inp.fr', 'Unité', 28),
(29, 'marie.Debrieux@g2elab.grenoble-inp.fr', 'Unité', 29),
(44, 'noe.peutot@g2elab.grenoble-inp.fr', 'Unité', 2),
(50, 'henry.colasuonno@g2elab.grenoble-inp.fr', 'Unité', 5),
(53, 'beatrice.Arrieula@g2elab.grenoble-inp.fr', 'Unité', 21),
(54, 'patrick.sebastien@grenoble-inp.fr', 'Institutionnel', 32),
(56, 'henry.pierre@g2elab.grenoble-inp.fr', 'Institutionnel', 33);

-- --------------------------------------------------------

--
-- Structure de la table `modification`
--

DROP TABLE IF EXISTS `modification`;
CREATE TABLE IF NOT EXISTS `modification` (
  `id_modification` int NOT NULL AUTO_INCREMENT,
  `id_personne` int NOT NULL,
  `attribut` enum('Nom','Prénom','Mail','Téléphone','Bureau','Statut','Activité','Photo','Equipe','Employeur') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `avant` text NOT NULL,
  `apres` text NOT NULL,
  `statut` enum('attente','valide','annule') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `commentaire` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id_modification`),
  KEY `id_personne` (`id_personne`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `modification`
--

INSERT INTO `modification` (`id_modification`, `id_personne`, `attribut`, `avant`, `apres`, `statut`, `commentaire`) VALUES
(102, 2, 'Photo', 'http://localhost/trombi/public/assets/images/profile/valide/2', 'http://localhost/trombi/public/assets/images/profile/en_attente/2', 'annule', ''),
(103, 2, 'Equipe', '2', '2, 6', 'valide', ''),
(104, 2, 'Nom', 'PEUTOT', 'PEUTOTt', 'attente', ''),
(105, 2, 'Activité', 'Trombinoscope', 'Réalisation d\'un trombinoscope moderne', 'attente', '');

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

DROP TABLE IF EXISTS `personne`;
CREATE TABLE IF NOT EXISTS `personne` (
  `id_personne` int NOT NULL,
  `login` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `role` enum('normal','admin','modo') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `telephone` varchar(10) DEFAULT NULL,
  `statut` int DEFAULT NULL,
  `bureau` int DEFAULT NULL,
  PRIMARY KEY (`id_personne`),
  UNIQUE KEY `id_personne` (`id_personne`),
  UNIQUE KEY `id_personne_2` (`id_personne`),
  KEY `cle_etrangere_statut` (`statut`),
  KEY `cle_etrangere_bureau` (`bureau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `personne`
--

INSERT INTO `personne` (`id_personne`, `login`, `role`, `nom`, `prenom`, `telephone`, `statut`, `bureau`) VALUES
(1, 'danguilv', 'modo', 'DANGUILLAUME', 'Vincent', '0123456789', 5, 1),
(2, 'peutotn', 'admin', 'PEUTOT', 'Noé', '0987654321', 33, 2),
(3, NULL, 'normal', 'LEROUX', 'Rachelle', NULL, 4, NULL),
(4, NULL, 'normal', 'RATTÉ', 'Marcelle', NULL, 30, NULL),
(5, NULL, 'normal', 'COLASUONNO', 'Henry', NULL, 8, NULL),
(6, NULL, 'normal', 'DERBEY', 'Almarick', NULL, 31, NULL),
(7, NULL, 'normal', 'POLIZZI', 'Rachelle', NULL, 33, NULL),
(8, NULL, 'normal', 'CUILLA', 'Emilie', NULL, 2, NULL),
(9, NULL, 'normal', 'FERRARI', 'Jean', NULL, 5, NULL),
(10, NULL, 'normal', 'BRACONNIER', 'Thierry', NULL, 5, NULL),
(11, NULL, 'normal', 'DARGAUD', 'Delphine', NULL, 4, NULL),
(12, NULL, 'normal', 'GRIMONET', 'Alfonse', NULL, 25, NULL),
(13, NULL, 'normal', 'LABONNE', 'Anna', NULL, 30, NULL),
(14, NULL, 'normal', 'POLLET', 'Colette', NULL, 31, NULL),
(15, NULL, 'normal', 'IMARD', 'Valentin', NULL, 24, NULL),
(16, NULL, 'normal', 'MARCON', 'Corine', NULL, 5, NULL),
(17, NULL, 'normal', 'ESTRABAUT', 'Laurent', NULL, 5, NULL),
(18, NULL, 'normal', 'BRAOS', 'Agathe', NULL, 35, NULL),
(19, NULL, 'normal', 'ROCHE', 'Evan', NULL, 14, NULL),
(20, NULL, 'normal', 'GIRARD', 'Fabrice', NULL, 2, NULL),
(21, NULL, 'normal', 'ARRIEULA', 'Beatrice', NULL, 24, NULL),
(22, NULL, 'normal', 'FLURY', 'Samuel', NULL, 4, NULL),
(23, NULL, 'normal', 'VAILLANT', 'Mathieu', NULL, 8, NULL),
(24, NULL, 'normal', 'GARCIA', 'Sophie', NULL, 6, NULL),
(25, NULL, 'normal', 'AUVERGNE', 'Rachelle', NULL, 3, NULL),
(26, NULL, 'normal', 'DUMAS', 'François', NULL, 3, NULL),
(27, NULL, 'normal', 'NORRIS', 'William', NULL, 4, NULL),
(28, NULL, 'normal', 'LOUBET', 'Sabrine', NULL, 7, NULL),
(29, NULL, 'normal', 'DEBRIEUX', 'Marie', NULL, 7, NULL),
(32, NULL, 'normal', 'PATRICK', 'Sebastien', NULL, 20, NULL),
(33, NULL, 'normal', 'HENRY', 'Pierre', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `rattachement`
--

DROP TABLE IF EXISTS `rattachement`;
CREATE TABLE IF NOT EXISTS `rattachement` (
  `id_rattachement` int NOT NULL,
  `id_sejour` int NOT NULL,
  `id_equipe` int NOT NULL,
  PRIMARY KEY (`id_rattachement`),
  KEY `id_sejour` (`id_sejour`),
  KEY `id_equipe` (`id_equipe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `rattachement`
--

INSERT INTO `rattachement` (`id_rattachement`, `id_sejour`, `id_equipe`) VALUES
(1, 1, 2),
(2, 2, 2),
(3, 3, 2),
(4, 4, 3),
(5, 5, 4),
(6, 6, 4),
(7, 7, 5),
(8, 8, 5),
(9, 9, 6),
(10, 10, 6),
(11, 11, 5),
(12, 12, 5),
(13, 13, 5),
(14, 14, 6),
(15, 15, 6),
(16, 16, 2),
(17, 17, 2),
(18, 18, 2),
(19, 19, 4),
(20, 20, 4),
(21, 21, 4),
(22, 22, 4),
(23, 23, 4),
(24, 24, 5),
(25, 25, 5),
(26, 26, 5),
(27, 27, 6),
(29, 29, 10),
(30, 30, 10),
(31, 31, 10),
(36, 33, 2);

-- --------------------------------------------------------

--
-- Structure de la table `responsabilite`
--

DROP TABLE IF EXISTS `responsabilite`;
CREATE TABLE IF NOT EXISTS `responsabilite` (
  `id_responsabilite` int NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `id_personne` int NOT NULL,
  PRIMARY KEY (`id_responsabilite`),
  UNIQUE KEY `id_responsabilite` (`id_responsabilite`),
  KEY `id_personne` (`id_personne`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `responsabilite`
--

INSERT INTO `responsabilite` (`id_responsabilite`, `libelle`, `id_personne`) VALUES
(2, 'Responsable de stage', 1),
(6, 'Directeur de laboratoire', 25);

-- --------------------------------------------------------

--
-- Structure de la table `sejour`
--

DROP TABLE IF EXISTS `sejour`;
CREATE TABLE IF NOT EXISTS `sejour` (
  `id_sejour` int NOT NULL AUTO_INCREMENT,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `id_personne` int NOT NULL,
  `sujet` text,
  PRIMARY KEY (`id_sejour`),
  UNIQUE KEY `id_sejour` (`id_sejour`),
  KEY `id_personne` (`id_personne`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `sejour`
--

INSERT INTO `sejour` (`id_sejour`, `date_debut`, `date_fin`, `id_personne`, `sujet`) VALUES
(1, '2002-01-01', '2100-01-01', 1, NULL),
(2, '2024-03-25', '2024-06-28', 2, 'Trombinoscope'),
(3, '2000-09-28', '2030-09-28', 3, NULL),
(4, '2023-03-28', '2030-03-28', 4, 'Trouver le matériel invisible'),
(5, '2000-04-29', '2100-01-01', 5, NULL),
(6, '2021-07-18', '2025-07-18', 6, NULL),
(7, '2015-08-21', '2100-01-01', 8, NULL),
(8, '2024-03-21', '2024-09-21', 7, 'Stage mécatronique'),
(9, '2016-09-07', '2050-09-07', 9, NULL),
(10, '1999-04-24', '2034-04-24', 10, NULL),
(11, '2010-12-10', '2060-12-10', 11, NULL),
(12, '2009-09-19', '2049-09-09', 12, NULL),
(13, '2022-09-09', '2025-09-09', 13, 'Thèse sur la mécatronique'),
(14, '2019-02-19', '2024-08-25', 14, NULL),
(15, '2014-02-01', '2026-02-01', 15, NULL),
(16, '2005-04-09', '2035-04-09', 16, NULL),
(17, '2000-08-05', '2100-01-01', 17, NULL),
(18, '2020-02-02', '2050-02-02', 18, NULL),
(19, '2010-09-05', '2030-09-05', 19, NULL),
(20, '2005-01-01', '2035-01-01', 20, NULL),
(21, '2019-01-01', '2025-01-01', 21, NULL),
(22, '2015-01-01', '2030-01-01', 22, NULL),
(23, '2015-05-05', '2025-05-05', 23, NULL),
(24, '2015-01-01', '2100-01-01', 24, NULL),
(25, '2020-01-01', '2100-01-01', 25, NULL),
(26, '2015-01-01', '2100-01-01', 26, NULL),
(27, '2010-01-01', '2100-01-01', 27, NULL),
(29, '2010-01-01', '2100-01-01', 28, NULL),
(30, '2010-01-01', '2100-01-01', 29, NULL),
(31, '2010-01-01', '2025-01-01', 32, NULL),
(33, '2024-01-20', '2100-01-01', 33, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `statut`
--

DROP TABLE IF EXISTS `statut`;
CREATE TABLE IF NOT EXISTS `statut` (
  `id_statut` int NOT NULL,
  `statut` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id_statut`),
  KEY `nom` (`statut`(250))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `statut`
--

INSERT INTO `statut` (`id_statut`, `statut`) VALUES
(1, 'Enseignant-Chercheur Titulaire'),
(2, 'Enseignant-Chercheur CDI'),
(3, 'Chercheur Titulaire'),
(4, 'Chercheur CDI'),
(5, 'ITA Titulaire'),
(6, 'ITA CDI'),
(7, 'BIATSS Titulaire'),
(8, 'BIATSS CDI'),
(9, 'CNAP'),
(10, 'PRAG/PRCE'),
(11, 'PH'),
(12, 'PU PH'),
(13, 'MCU PH'),
(14, 'Attaché scientifiques'),
(15, 'AHU'),
(16, 'Enseignant-Chercheur CDD'),
(17, 'Enseignant-Chercheur Emérite'),
(18, 'Chercheur CDD'),
(19, 'Chercheur Emérite'),
(20, 'Chercheur associé'),
(21, 'Chercheur en délégation ou CRCT'),
(22, 'Enseignant invité (PR/MCF)'),
(23, 'Praticien contractuel'),
(24, 'ITA CDD'),
(25, 'BIATSS CDD'),
(26, 'Collaborateur Bénévole'),
(27, 'ATER non doctorant'),
(28, 'PAST'),
(29, 'Post-Doctorant'),
(30, 'Doctorant'),
(31, 'Alternant'),
(32, 'Vacataire'),
(33, 'Stagiaire'),
(34, 'Doctorant Extérieur'),
(35, 'Jeune recherche'),
(36, 'Etudiant en formation'),
(37, 'Personnel de Recherche Invité'),
(38, 'Visiteur'),
(39, 'Hébergé en lien avec le laboratoire'),
(40, 'Hébergé extérieur');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `id_personne` int DEFAULT NULL,
  `statut` enum('admin','modo') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id_utilisateur`),
  KEY `id_personne` (`id_personne`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `id_personne`, `statut`) VALUES
(1, 2, 'admin');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `encadrant`
--
ALTER TABLE `encadrant`
  ADD CONSTRAINT `encadrant_ibfk_1` FOREIGN KEY (`id_personne`) REFERENCES `personne` (`id_personne`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `encadrant_ibfk_2` FOREIGN KEY (`id_sejour`) REFERENCES `sejour` (`id_sejour`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `financement`
--
ALTER TABLE `financement`
  ADD CONSTRAINT `financement_ibfk_1` FOREIGN KEY (`id_employeur`) REFERENCES `employeur` (`id_employeur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `financement_ibfk_2` FOREIGN KEY (`id_sejour`) REFERENCES `sejour` (`id_sejour`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `mail`
--
ALTER TABLE `mail`
  ADD CONSTRAINT `mail_ibfk_1` FOREIGN KEY (`id_personne`) REFERENCES `personne` (`id_personne`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `modification`
--
ALTER TABLE `modification`
  ADD CONSTRAINT `cle_etrangere_id_pers` FOREIGN KEY (`id_personne`) REFERENCES `personne` (`id_personne`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `personne`
--
ALTER TABLE `personne`
  ADD CONSTRAINT `cle_etrangere_bureau` FOREIGN KEY (`bureau`) REFERENCES `bureau` (`id_bureau`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `cle_etrangere_statut` FOREIGN KEY (`statut`) REFERENCES `statut` (`id_statut`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
