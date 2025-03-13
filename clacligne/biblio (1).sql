-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 16 fév. 2025 à 11:27
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `biblio`
--

-- --------------------------------------------------------

--
-- Structure de la table `codes_admin`
--

CREATE TABLE `codes_admin` (
  `id` int(11) NOT NULL,
  `code_secret` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `codes_admin`
--

INSERT INTO `codes_admin` (`id`, `code_secret`) VALUES
(1, 'ADMIN2025'),
(2, 'ADMIN_1_2025');

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE `commentaire` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `livre_id` int(11) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `note` int(11) DEFAULT NULL CHECK (`note` between 1 and 5),
  `date_commentaire` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `emprunts`
--

CREATE TABLE `emprunts` (
  `id` int(11) NOT NULL,
  `id_livre` int(11) NOT NULL,
  `id_lecteur` int(11) NOT NULL,
  `date_emprunt` date NOT NULL,
  `date_retour` date NOT NULL,
  `statut` enum('En cours','Rendu','En retard') DEFAULT 'En cours',
  `utilisateurs_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

CREATE TABLE `favoris` (
  `id` int(11) NOT NULL,
  `utilisateurs_id` int(11) NOT NULL,
  `livres_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `favoris`
--

INSERT INTO `favoris` (`id`, `utilisateurs_id`, `livres_id`) VALUES
(4, 8, 2),
(5, 8, 1),
(6, 13, 2),
(8, 12, 2);

-- --------------------------------------------------------

--
-- Structure de la table `lecteurs`
--

CREATE TABLE `lecteurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livres`
--

CREATE TABLE `livres` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `annee_publication` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `couverture` varchar(255) DEFAULT NULL,
  `disponibilite` enum('disponible','emprunté') DEFAULT 'disponible',
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livres`
--

INSERT INTO `livres` (`id`, `titre`, `auteur`, `categorie`, `annee_publication`, `description`, `couverture`, `disponibilite`, `date_ajout`) VALUES
(1, 'Les Misérables', 'Victor Hugo', 'Roman', 1862, 'Une histoire de justice et de rédemption.', 'les_miserables.jfif', 'disponible', '2025-02-11 15:11:17'),
(2, 'L\'Alchimiste', 'Paulo Coelho', 'Roman', 1988, 'Un berger en quête de son destin.', 'l_alchimiste.jfif', 'disponible', '2025-02-11 15:11:17'),
(3, 'Orgueil et Préjugés', 'Jane Austen', 'Roman', 1813, 'Une romance et des conventions sociales.', 'orgueil_prejuges.jfif', 'disponible', '2025-02-11 15:11:17'),
(4, 'Une brève histoire du temps', 'Stephen Hawking', 'Science', 1988, 'Exploration de l\'univers et du temps.', 'histoire_temps.jfif', 'disponible', '2025-02-11 15:11:17'),
(5, 'Le Gène égoïste', 'Richard Dawkins', 'Science', 1976, 'Théorie de l\'évolution centrée sur les gènes.', 'gene_egoiste.png', 'disponible', '2025-02-11 15:11:17'),
(6, 'L’Univers élégant', 'Brian Greene', 'Science', 1999, 'Introduction à la théorie des cordes.', 'univers_elegant.jfif', 'disponible', '2025-02-11 15:11:17'),
(7, 'Sapiens', 'Yuval Noah Harari', 'Histoire', 2011, 'Évolution de l\'humanité.', 'sapiens.jfif', 'disponible', '2025-02-11 15:11:17'),
(8, 'Guerre et Paix', 'Léon Tolstoï', 'Histoire', 1869, 'Chronique des guerres napoléoniennes.', 'guerre_paix.jfif', 'disponible', '2025-02-11 15:11:17'),
(9, 'La Seconde Guerre mondiale', 'Antony Beevor', 'Histoire', 2012, 'Analyse de la Seconde Guerre mondiale.', 'seconde_guerre.jfif', 'disponible', '2025-02-11 15:11:17'),
(12, 'Alice au pays des merveilles', 'Lewis Carroll', 'Roman', 1865, 'Les Aventures d\"Alice au pays des merveilles\"', 'alice_pays_merveille.jpg', '', '0000-00-00 00:00:00'),
(20, 'Naruto', 'Masashi Kishimoto', 'Histoire', 1999, 'Naruto (ナルト?) est un shōnen manga écrit et dessiné par Masashi Kishimoto. Naruto a été prépublié dans l\'hebdomadaire Weekly Shōnen Jump de l\'éditeur Shūeisha entre septembre 1999 et novembre 2014.', 'Naruto.jfif', 'disponible', '2025-02-15 10:56:24'),
(22, 'LA LIGA', 'Espagne', 'Histoire', 2002, NULL, 'livre_1.jfif', 'disponible', '2025-02-15 12:00:46');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('utilisateur','admin') DEFAULT 'utilisateur',
  `code_secret` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `password`, `date_inscription`, `role`, `code_secret`) VALUES
(8, 'az', 'az@gmail.com', '$2y$10$iFqakRQCFd3mTBqADX.3s.wnKmVCmCiNFVlLFBlNleuNrBBrN.qWS', '2025-02-13 13:02:24', 'utilisateur', NULL),
(9, 'SDM', 'sdm@gmail.com', '$2y$10$MzPpaAvYbvqTlKnKm.wC9uT2YlFe3ogthQ8G2.r6sGdPs/z/.LKuK', '2025-02-13 17:15:59', 'utilisateur', NULL),
(10, 'QSD', 'qsd@gmail.com', '$2y$10$379mqKNT0s9WUMqhD2vvwe.0KdbLZ6zdTBhN6WTx7xL/HjNqtUB4W', '2025-02-13 17:19:55', 'admin', 'BIBLIO-ADMIN-2024'),
(11, 'aze', 'aze@gmail.com', '$2y$10$fSCKQJAfsh0VkdnKjblK9ui6.Amm10fwO772i2dvucvdMN2EOJAIG', '2025-02-14 12:06:30', 'admin', 'ADMIN2025'),
(12, 'aqw', 'aqw@gmail.com', '$2y$10$XuTaQq5W1gSvfdYKoN.T6.maUUTsZnAzR0.LaTg/u1kTCvuUJefyG', '2025-02-14 12:25:53', 'admin', 'ADMIN2025'),
(13, 'GOOG', 'good@gmail.com', '$2y$10$aVXEElWU.hZmexafVX4XYOS4Z5J2/QZSRyi8GGA0o9g7mWoAaVCq.', '2025-02-14 13:06:42', 'utilisateur', NULL),
(14, 'GRAF', 'graf@gmail.com', '$2y$10$cfStKHQKZAC3n/pcWsAKoOjDhNzig.AMgo0dPeOL.lW23EFHuKXTG', '2025-02-16 09:24:24', 'utilisateur', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `codes_admin`
--
ALTER TABLE `codes_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_secret` (`code_secret`);

--
-- Index pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `livre_id` (`livre_id`);

--
-- Index pour la table `emprunts`
--
ALTER TABLE `emprunts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_livre` (`id_livre`),
  ADD KEY `id_lecteur` (`id_lecteur`);

--
-- Index pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateurs_id` (`utilisateurs_id`),
  ADD KEY `livres_id` (`livres_id`);

--
-- Index pour la table `lecteurs`
--
ALTER TABLE `lecteurs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `livres`
--
ALTER TABLE `livres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `codes_admin`
--
ALTER TABLE `codes_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `commentaire`
--
ALTER TABLE `commentaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emprunts`
--
ALTER TABLE `emprunts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `lecteurs`
--
ALTER TABLE `lecteurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `livres`
--
ALTER TABLE `livres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `commentaire_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `commentaire_ibfk_2` FOREIGN KEY (`livre_id`) REFERENCES `livres` (`id`);

--
-- Contraintes pour la table `emprunts`
--
ALTER TABLE `emprunts`
  ADD CONSTRAINT `emprunts_ibfk_1` FOREIGN KEY (`id_livre`) REFERENCES `livres` (`id`),
  ADD CONSTRAINT `emprunts_ibfk_2` FOREIGN KEY (`id_lecteur`) REFERENCES `lecteurs` (`id`);

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`utilisateurs_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoris_ibfk_2` FOREIGN KEY (`livres_id`) REFERENCES `livres` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
