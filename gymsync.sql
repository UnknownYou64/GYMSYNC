-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 03 Avril 2025 à 09:41
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
(1, 'Lundi', '09:30:00', 5, 'Stretching', 'Mundubeltz'),
(3, 'Dimanche', '15:00:00', 25, 'Marche', 'Lotz'),
(11, 'Mardi', '20:00:00', 20, 'Pilates', 'Bree'),
(12, 'Vendredi', '17:00:00', 20, 'tapis', 'Mundubeltz'),
(16, 'Jeudi', '20:01:00', 10, 'gym', 'Lotz'),
(17, 'Jeudi', '20:30:00', 20, 'Yoga', 'Loubery'),
(18, 'Samedi', '20:00:00', 10, 'Stretching', 'Hakiri');

-- --------------------------------------------------------

--
-- Structure de la table `historique`
--

CREATE TABLE `historique` (
  `id` int(11) NOT NULL,
  `Action` varchar(255) NOT NULL,
  `DateAction` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `historique`
--

INSERT INTO `historique` (`id`, `Action`, `DateAction`) VALUES
(23, 'Statut de paiement du membre #12 modifié : à payer', '2025-04-02 12:26:53'),
(24, 'Paiement validé pour le membre #1 : lotz william', '2025-04-02 12:27:55'),
(25, 'Paiement validé pour le membre #10 : walzer wladimir', '2025-04-02 12:27:55'),
(26, 'Paiement validé pour le membre #1 : lotz william', '2025-04-02 12:28:21'),
(27, 'Paiement validé pour le membre #10 : walzer wladimir', '2025-04-02 12:28:21'),
(28, 'Paiement validé pour le membre #1 : lotz william', '2025-04-02 12:28:27'),
(29, 'Paiement validé pour le membre #15 : miquau amaury', '2025-04-02 12:28:27'),
(30, 'Paiement validé pour le membre #3 : miremont bixente', '2025-04-02 12:29:03'),
(31, 'Paiement validé pour le membre #3 : miremont bixente', '2025-04-02 12:29:09'),
(32, 'Paiement validé pour le membre #3 : miremont bixente', '2025-04-02 12:32:06'),
(33, 'Paiement validé pour le membre #3 : miremont bixente', '2025-04-02 12:32:06'),
(34, 'Paiement validé pour le membre #12 : Chipy Thibault', '2025-04-02 12:40:19'),
(35, 'Paiement validé pour le membre #11 : Duthil Maceo', '2025-04-02 12:40:19'),
(36, 'Paiement validé pour le membre #9 : Proust Tom', '2025-04-02 12:40:25'),
(37, 'Paiement validé pour le membre #10 : walzer wladimir', '2025-04-02 12:40:25');

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
  `date_inscription` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tarif_id` int(11) DEFAULT NULL,
  `A_Regler` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `membre`
--

INSERT INTO `membre` (`Identifiant`, `Nom`, `Prenom`, `Mail`, `Code`, `date_inscription`, `tarif_id`, `A_Regler`) VALUES
(1, 'lotz', 'william', 'william.lotz64500@gmail.com', 'db2b8af4', '2025-03-26 14:00:54', 2, 1),
(3, 'miremont', 'bixente', 'm.bixente@gmail.com', NULL, '2025-03-26 14:00:54', NULL, 0),
(4, 'guerin', 'maxime', 'm.guerin@gmail.com', NULL, '2025-03-26 14:00:54', NULL, 0),
(7, 'lotz', 'valerie', 'valerie.lotz03@gmail.com', 'b505da59', '2025-03-26 14:00:54', NULL, 0),
(9, 'Proust', 'Tom', 'william.lotz64@gmail.com', NULL, '2025-03-26 14:00:54', NULL, 1),
(10, 'walzer', 'wladimir', 'wiwilotz64@gmail.com', '47663879', '2025-03-26 14:00:54', NULL, 1),
(11, 'Duthil', 'Maceo', 'Maceo.D@gmail.com', NULL, '2025-03-26 14:00:54', NULL, 1),
(12, 'Chipy', 'Thibault', 'T.Chipy@gmail.com', NULL, '2025-03-26 14:00:54', NULL, 1),
(13, 'Lipsky', 'Ben', 'Lipsky.ben@gmail.com', 'c9e4aae4', '2025-03-26 14:00:54', NULL, 0),
(14, 'betton', 'dimitri', 'D.betton@gmail.com', '43263854', '2025-03-26 14:00:54', NULL, 1),
(15, 'miquau', 'amaury', 'm.miquau@gmail.com', '32744445', '2025-03-26 14:00:54', NULL, 1),
(16, 'Lipsky', 'ben', 'B.lipsky@gmail.com', NULL, '2025-04-02 13:48:58', NULL, 0);

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
(2, 1, 2),
(5, 1, 5),
(10, 3, 10),
(11, 6, 11),
(12, 7, 11),
(13, 6, 12),
(14, 6, 13),
(15, 6, 14),
(16, 7, 15),
(17, 3, 15),
(18, 7, 16),
(19, 3, 16),
(21, 3, 18),
(22, 3, 9),
(23, 11, 9),
(24, 3, 10),
(25, 11, 10),
(26, 12, 1),
(28, 17, 1),
(29, 17, 1),
(30, 11, 1),
(31, 17, 15),
(32, 3, 1),
(33, 16, 1),
(34, 17, 1),
(35, 3, 1),
(36, 16, 1);

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

--
-- Index pour les tables exportées
--

-- --------------------------------------------------------

--
-- Structure de la table `actualites`
--

CREATE TABLE actualites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    texte TEXT NOT NULL,
    couleur VARCHAR(50) DEFAULT NULL,
    gras BOOLEAN DEFAULT FALSE,
    ordre INT NOT NULL,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`IDC`);

--
-- Index pour la table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`Identifiant`),
  ADD KEY `tarif_id` (`tarif_id`),
  ADD KEY `idx_date_inscription` (`date_inscription`);

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
  MODIFY `IDC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT pour la table `membre`
--
ALTER TABLE `membre`
  MODIFY `Identifiant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `IDR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT pour la table `tarifs`
--
ALTER TABLE `tarifs`
  MODIFY `IDT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `membre`
--
ALTER TABLE `membre`
  ADD CONSTRAINT `membre_ibfk_1` FOREIGN KEY (`tarif_id`) REFERENCES `tarifs` (`IDT`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
