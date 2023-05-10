-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mar. 09 mai 2023 à 21:10
-- Version du serveur : 8.0.32
-- Version de PHP : 8.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `istic_project`
--

-- --------------------------------------------------------

--
-- Structure de la table `blocked`
--

CREATE TABLE `blocked` (
  `id` int NOT NULL,
  `idUser` int NOT NULL,
  `idBlocked` int NOT NULL,
  `since` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET= COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `code`
--

CREATE TABLE `code` (
  `id` int NOT NULL,
  `code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `idFoyer` int NOT NULL,
  `expire` date NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `conversation`
--

CREATE TABLE `conversation` (
  `id` int NOT NULL,
  `idUser1` int NOT NULL,
  `idUser2` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

CREATE TABLE `event` (
  `id` int NOT NULL,
  `zip` varchar(5) NOT NULL,
  `canceled` tinyint(1) NOT NULL DEFAULT '0',
  `reason` varchar(50) DEFAULT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `title` varchar(20) NOT NULL,
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pic` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `event`
--

INSERT INTO `event` (`id`, `zip`, `canceled`, `reason`, `start`, `end`, `title`, `description`, `pic`) VALUES
(1, '12345', 0, NULL, '2023-04-01', '2023-04-02', 'Event 1', 'Description of event 1', 'pic1.jpg'),
(2, '23456', 0, NULL, '2023-05-01', '2023-05-02', 'Event 2', 'Description of event 2', 'pic2.jpg'),
(3, '34567', 1, 'Bad weather', '2023-06-01', '2023-06-02', 'Event 3', 'Description of event 3', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `foyer`
--

CREATE TABLE `foyer` (
  `id` int NOT NULL,
  `city` varchar(10) NOT NULL,
  `zip` varchar(5) NOT NULL,
  `address` varchar(50) NOT NULL,
  `street` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `foyer`
--

INSERT INTO `foyer` (`id`, `city`, `zip`, `address`, `street`) VALUES
(1, 'Paris', '75001', '1 Rue de Rivoli', 'Rivoli'),
(2, 'Lyon', '69001', '1 Place des Terreaux', 'Terreaux'),
(3, 'Marseille', '13001', '1 Rue de la République', 'République');

-- --------------------------------------------------------

--
-- Structure de la table `friend`
--

CREATE TABLE `friend` (
  `idUser1` int NOT NULL,
  `idUser2` int NOT NULL,
  `since` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int NOT NULL,
  `idConversation` int NOT NULL,
  `idSender` int NOT NULL,
  `idReceiver` int NOT NULL,
  `content` varchar(255) NOT NULL,
  `sentAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `participant`
--

CREATE TABLE `participant` (
  `id` int NOT NULL,
  `idEvent` int NOT NULL,
  `idUser` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `registration`
--

CREATE TABLE `registration` (
  `id` int NOT NULL,
  `idCode` int NOT NULL,
  `idUser` int NOT NULL,
  `at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id` int NOT NULL,
  `libelle` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id`, `libelle`) VALUES
(0, 'developer'),
(1, 'educator'),
(2, 'user'),
(3, 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `lastname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `firstname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `login` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `idRole` int NOT NULL,
  `idRef` int DEFAULT NULL,
  `idFoyer` int NOT NULL,
  `lastLogin` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastLogout` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `blocked`
--
ALTER TABLE `blocked`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_blocked_idUser` (`idUser`),
  ADD KEY `fk_blocked_idBlocked` (`idBlocked`);

--
-- Index pour la table `code`
--
ALTER TABLE `code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_code_idFoyer` (`idFoyer`);

--
-- Index pour la table `conversation`
--
ALTER TABLE `conversation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conversation_idUser1` (`idUser1`),
  ADD KEY `fk_conversation_idUser2` (`idUser2`);

--
-- Index pour la table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `foyer`
--
ALTER TABLE `foyer`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `friend`
--
ALTER TABLE `friend`
  ADD PRIMARY KEY (`idUser1`,`idUser2`),
  ADD KEY `fk_friend_idUser2` (`idUser2`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_message_idConversation` (`idConversation`),
  ADD KEY `fk_message_idSender` (`idSender`),
  ADD KEY `fk_message_idReceiver` (`idReceiver`);

--
-- Index pour la table `participant`
--
ALTER TABLE `participant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_participant_idEvent` (`idEvent`),
  ADD KEY `fk_participant_idUser` (`idUser`);

--
-- Index pour la table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_registration_idCode` (`idCode`),
  ADD KEY `fk_registration_idUser` (`idUser`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`,`login`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `fk_user_idRole` (`idRole`),
  ADD KEY `fk_user_idRef` (`idRef`),
  ADD KEY `fk_user_idFoyer` (`idFoyer`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `blocked`
--
ALTER TABLE `blocked`
  ADD CONSTRAINT `fk_blocked_idBlocked` FOREIGN KEY (`idBlocked`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_blocked_idUser` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `code`
--
ALTER TABLE `code`
  ADD CONSTRAINT `fk_code_idFoyer` FOREIGN KEY (`idFoyer`) REFERENCES `foyer` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `conversation`
--
ALTER TABLE `conversation`
  ADD CONSTRAINT `fk_conversation_idUser1` FOREIGN KEY (`idUser1`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_conversation_idUser2` FOREIGN KEY (`idUser2`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `friend`
--
ALTER TABLE `friend`
  ADD CONSTRAINT `fk_friend_idUser1` FOREIGN KEY (`idUser1`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_friend_idUser2` FOREIGN KEY (`idUser2`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_message_idConversation` FOREIGN KEY (`idConversation`) REFERENCES `conversation` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_message_idReceiver` FOREIGN KEY (`idReceiver`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_message_idSender` FOREIGN KEY (`idSender`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `participant`
--
ALTER TABLE `participant`
  ADD CONSTRAINT `fk_participant_idEvent` FOREIGN KEY (`idEvent`) REFERENCES `event` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_participant_idUser` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `fk_registration_idCode` FOREIGN KEY (`idCode`) REFERENCES `code` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_registration_idUser` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_idFoyer` FOREIGN KEY (`idFoyer`) REFERENCES `foyer` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_user_idRef` FOREIGN KEY (`idRef`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_user_idRole` FOREIGN KEY (`idRole`) REFERENCES `role` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
