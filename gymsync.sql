-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Sam 01 Mars 2025 à 09:29
-- Version du serveur :  5.7.11
-- Version de PHP :  7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `gymsync`
--

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `IDC` int(11) NOT NULL,
  `Jour` varchar(255) NOT NULL,
  `Heure` time NOT NULL,
  `Place` int(11) NOT NULL,
  `Nature` varchar(255) NOT NULL,
  `Professeur` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `cours`
--

INSERT INTO `cours` (`IDC`, `Jour`, `Heure`, `Place`, `Nature`, `Professeur`) VALUES
(1, '', '00:00:00', 0, '', ''),
(2, '', '00:00:00', 0, '', ''),
(3, '', '00:00:00', 0, '', ''),
(4, '', '00:00:00', 0, '', ''),
(5, '', '00:00:00', 0, '', ''),
(6, '', '00:00:00', 0, '', ''),
(7, '', '00:00:00', 0, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

CREATE TABLE `membre` (
  `Identifiant` int(11) NOT NULL,
  `Nom` varchar(255) NOT NULL,
  `Prenom` varchar(255) NOT NULL,
  `Mail` varchar(255) NOT NULL,
  `Code` varchar(255) DEFAULT NULL,
  `statut_paiement` BOOLEAN NOT NULL DEFAULT 0,
  `date_inscription` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tarif_id` INT(11)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `membre`
--

INSERT INTO `membre` (`Identifiant`, `Nom`, `Prenom`, `Mail`, `Code`) VALUES
(1, 'lotz', 'william', 'william.lotz64500@gmail.com', 'db2b8af4'),
(3, 'miremont', 'bixente', 'm.bixente@gmail.com', NULL),
(4, 'guerin', 'maxime', 'm.guerin@gmail.com', NULL),
(7, 'lotz', 'valerie', 'valerie.lotz03@gmail.com', 'b505da59'),
(8, 'lotz', 'william', 'william.lotz64@gmail.com', NULL),
(9, 'lotz', 'william', 'william.lotz64@gmail.com', NULL),
(10, 'lotz', 'william', 'william.lotz64@gmail.com', NULL),
(11, 'lotz', 'william', 'william.lotz64@gmail.com', NULL),
(12, 'lotz', 'william', 'william.lotz64@gmail.com', NULL),
(13, 'lotz', 'william', 'william.lotz64@gmail.com', NULL),
(14, 'lotz', 'william', 'william.lotz64@gmail.com', NULL),
(15, 'lotz', 'william', 'william.lotz64@gmail.com', NULL),
(16, 'lotz', 'william', 'william.lotz64@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `IDR` int(11) NOT NULL,
  `IDC` int(11) NOT NULL,
  `Identifiant` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `reservation`
--

INSERT INTO `reservation` (`IDR`, `IDC`, `Identifiant`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 2, 6),
(7, 2, 8),
(8, 2, 8),
(9, 2, 9),
(10, 3, 10),
(11, 6, 11),
(12, 7, 11),
(13, 6, 12),
(14, 6, 13),
(15, 6, 14),
(16, 7, 15),
(17, 3, 15),
(18, 7, 16),
(19, 3, 16);

-- --------------------------------------------------------

--
-- Structure de la table `tarifs`
--

CREATE TABLE `tarifs` (
  `IDT` int(11) NOT NULL,
  `nbcours` int(11) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `prix` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `tarifs`
--

INSERT INTO `tarifs` (`IDT`, `nbcours`, `categorie`, `prix`) VALUES
(1, 1, 'Adulte', 136),
(2, 2, 'Adulte', 166),
(3, 3, 'Adulte', 186),
(4, 4, 'Adulte', 206),
(5, 1, 'Couple', 260),
(6, 2, 'Couple', 308),
(7, 3, 'Couple', 340),
(8, 4, 'Couple', 355),
(9, 1, 'Etudiant', 90),
(10, 2, 'Etudiant', 110),
(11, 3, 'Etudiant', 130),
(12, 4, 'Etudiant', 140);

-- --------------------------------------------------------
-- Structure de la table `tarifs`
--
CREATE TABLE `historique` (
  `idHistorique` int(11) NOT NULL,
  `Identifiant` int(11) DEFAULT NULL,
  `IDC` int(11) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  `DateAction` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- --------------------------------------------------------
-- Modification de la table membre pour ajouter le suivi des paiements
--
ALTER TABLE `membre` 
ADD `statut_paiement` BOOLEAN NOT NULL DEFAULT 0,
ADD `date_inscription` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD `tarif_id` INT(11),
ADD FOREIGN KEY (`tarif_id`) REFERENCES `tarifs`(`IDT`);

-- Ajout d'index pour optimiser les recherches
ALTER TABLE `membre`
ADD INDEX `idx_statut_paiement` (`statut_paiement`),
ADD INDEX `idx_date_inscription` (`date_inscription`);

-- Mise à jour des données existantes
UPDATE `membre` SET `statut_paiement` = 0, `date_inscription` = NOW();

-- Index pour les tables exportées
--

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`IDC`);

--
-- Index pour la table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`Identifiant`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`IDR`,`IDC`,`Identifiant`);

--
-- Index pour la table `tarifs`
--
ALTER TABLE `tarifs`
  ADD PRIMARY KEY (`IDT`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `IDC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `membre`
--
ALTER TABLE `membre`
  MODIFY `Identifiant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `IDR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT pour la table `tarifs`
--
ALTER TABLE `tarifs`
  MODIFY `IDT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

ALTER TABLE `historique`
  ADD CONSTRAINT `historique_ibfk_1` FOREIGN KEY (`Identifiant`) REFERENCES `membre` (`Identifiant`),
  ADD CONSTRAINT `historique_ibfk_2` FOREIGN KEY (`IDC`) REFERENCES `cours` (`IDC`);

