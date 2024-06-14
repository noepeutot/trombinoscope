-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 14 juin 2024 à 15:26
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `statut`
--

DROP TABLE IF EXISTS `statut`;
CREATE TABLE IF NOT EXISTS `statut` (
  `id_statut` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id_statut`),
  KEY `nom` (`nom`(250))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

--
-- Contraintes pour la table `rattachement`
--
ALTER TABLE `rattachement`
  ADD CONSTRAINT `cle_etrangere_id_equipe` FOREIGN KEY (`id_equipe`) REFERENCES `equipe` (`id_equipe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cle_etrangere_id_sejour` FOREIGN KEY (`id_sejour`) REFERENCES `sejour` (`id_sejour`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `responsabilite`
--
ALTER TABLE `responsabilite`
  ADD CONSTRAINT `responsabilite_cle_etrangere` FOREIGN KEY (`id_personne`) REFERENCES `personne` (`id_personne`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sejour`
--
ALTER TABLE `sejour`
  ADD CONSTRAINT `sejour_clé_etrangere` FOREIGN KEY (`id_personne`) REFERENCES `personne` (`id_personne`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `cle_etrangere_id_personne` FOREIGN KEY (`id_personne`) REFERENCES `personne` (`id_personne`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
