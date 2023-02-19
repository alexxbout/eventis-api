-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : dim. 19 fév. 2023 à 21:01
-- Version du serveur : 8.0.32
-- Version de PHP : 8.2.3

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
  `since` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `code`
--

CREATE TABLE `code` (
  `id` int NOT NULL,
  `code` varchar(4) NOT NULL,
  `idUser` int DEFAULT NULL,
  `idFoyer` int NOT NULL,
  `idRef` int NOT NULL,
  `expire` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `educators`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `educators` (
`id` int
,`idFoyer` int
,`idRef` int
,`idRefSub` int
,`idRole` int
,`joined` datetime
,`lastLogin` datetime
,`lastLogout` datetime
,`login` varchar(30)
,`nom` varchar(30)
,`password` varchar(100)
,`prenom` varchar(30)
);

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

CREATE TABLE `event` (
  `id` int NOT NULL,
  `zip` varchar(5) NOT NULL,
  `canceled` tinyint(1) NOT NULL DEFAULT '0',
  `reason` varchar(50) DEFAULT NULL,
  `dateDebut` date NOT NULL,
  `dateFin` date NOT NULL,
  `title` varchar(20) NOT NULL,
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pic` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(0, 'rennes', '35000', 'Général Leclerc Bâtiment ', '12 Av.'),
(1, 'paris', '75008', 'Pl. Charles de Gaulle', '');

-- --------------------------------------------------------

--
-- Structure de la table `friend`
--

CREATE TABLE `friend` (
  `id` int NOT NULL,
  `idUser1` int NOT NULL,
  `idUser2` int NOT NULL,
  `since` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `friend`
--

INSERT INTO `friend` (`id`, `idUser1`, `idUser2`, `since`) VALUES
(0, 2, 1, '2023-02-19 21:00:37'),
(1, 0, 2, '2023-02-19 21:00:37');

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
(2, 'user');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `nom` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prenom` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `login` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `idRole` int NOT NULL,
  `idRef` int DEFAULT NULL,
  `idRefSub` int DEFAULT NULL,
  `idFoyer` int NOT NULL,
  `lastLogin` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastLogout` datetime DEFAULT NULL,
  `joined` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `nom`, `prenom`, `login`, `password`, `idRole`, `idRef`, `idRefSub`, `idFoyer`, `lastLogin`, `lastLogout`, `joined`) VALUES
(0, 'Dupont', 'Pierre', 'dpierre', '$2y$10$J2oRxcKyGAVYGbARti.gouHSpl7s/J/JsXR.uCjRy/fhgYW8mG9.y', 2, NULL, NULL, 0, '2023-02-19 21:53:11', NULL, '2023-02-19 21:53:11'),
(1, 'Dupont', 'Marie', 'dmarie', '$2y$10$MN74UYbkMNZqJ3CKBYdZRuL.2IqHOWzZfqJrSiHooVnq2Zedb8H16', 2, NULL, NULL, 0, '2023-02-19 21:53:56', NULL, '2023-02-19 21:53:56'),
(2, 'Dupont', 'Jean', 'djean', '$2y$10$BIzBVJiu2/5M9eci4m1rU.elCFo5jXhliRBH2Jb96H1HApTqReJwO', 2, NULL, NULL, 0, '2023-02-19 21:54:08', NULL, '2023-02-19 21:54:08'),
(3, 'Paul', 'Guillard', 'gpaul', '$2y$10$mmuIxL4hMiL1z1XCWrDGDen4pEFhreAbvDFkzhOhOSCy22t04uQHK', 1, NULL, NULL, 0, '2023-02-19 21:54:59', NULL, '2023-02-19 21:54:59');

-- --------------------------------------------------------

--
-- Structure de la vue `educators`
--
DROP TABLE IF EXISTS `educators`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `educators`  AS SELECT `user`.`id` AS `id`, `user`.`nom` AS `nom`, `user`.`prenom` AS `prenom`, `user`.`login` AS `login`, `user`.`password` AS `password`, `user`.`idRole` AS `idRole`, `user`.`idRef` AS `idRef`, `user`.`idRefSub` AS `idRefSub`, `user`.`idFoyer` AS `idFoyer`, `user`.`lastLogin` AS `lastLogin`, `user`.`lastLogout` AS `lastLogout`, `user`.`joined` AS `joined` FROM `user` WHERE (`user`.`idRole` = 1) ;

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
  ADD PRIMARY KEY (`id`,`code`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_code_idFoyer` (`idFoyer`),
  ADD KEY `fk_code_idRef` (`idRef`),
  ADD KEY `fk_code_idUser` (`idUser`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_friend_idUser1` (`idUser1`),
  ADD KEY `fk_friend_idUser2` (`idUser2`);

--
-- Index pour la table `participant`
--
ALTER TABLE `participant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_participant_idEvent` (`idEvent`),
  ADD KEY `fk_participant_idUser` (`idUser`);

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
  ADD KEY `fk_user_idRefSub` (`idRefSub`),
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
  ADD CONSTRAINT `fk_code_idFoyer` FOREIGN KEY (`idFoyer`) REFERENCES `foyer` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_code_idRef` FOREIGN KEY (`idRef`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_code_idUser` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `friend`
--
ALTER TABLE `friend`
  ADD CONSTRAINT `fk_friend_idUser1` FOREIGN KEY (`idUser1`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_friend_idUser2` FOREIGN KEY (`idUser2`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `participant`
--
ALTER TABLE `participant`
  ADD CONSTRAINT `fk_participant_idEvent` FOREIGN KEY (`idEvent`) REFERENCES `event` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_participant_idUser` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_idFoyer` FOREIGN KEY (`idFoyer`) REFERENCES `foyer` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_user_idRef` FOREIGN KEY (`idRef`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_user_idRefSub` FOREIGN KEY (`idRefSub`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_user_idRole` FOREIGN KEY (`idRole`) REFERENCES `role` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
