-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Client :  localhost:3306
-- Généré le :  Jeu 11 Juin 2015 à 14:14
-- Version du serveur :  5.5.38
-- Version de PHP :  5.6.2

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `videoGamesNew`
--
CREATE DATABASE IF NOT EXISTS `videoGamesNew` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `videoGamesNew`;

-- --------------------------------------------------------

--
-- Structure de la table `analyse`
--

DROP TABLE IF EXISTS `analyse`;
CREATE TABLE `analyse` (
`idAnalyse` int(11) NOT NULL,
  `analyse` longtext NOT NULL,
  `type` varchar(255) NOT NULL,
  `test_idTest` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
`idArticle` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `console_names` varchar(255) NOT NULL,
  `game_idGame` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
`idComment` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `note` int(11) NOT NULL,
  `like` int(11) NOT NULL,
  `dislike` int(11) NOT NULL,
  `text` longtext NOT NULL,
  `test_idTest` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
`idConfig` int(11) NOT NULL,
  `config` longtext NOT NULL,
  `type` varchar(255) NOT NULL,
  `console_idConsole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `console`
--

DROP TABLE IF EXISTS `console`;
CREATE TABLE `console` (
`idConsole` int(11) NOT NULL,
  `business_model` varchar(255) NOT NULL,
  `pegi` varchar(255) NOT NULL,
  `release` date NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `cover_front` varchar(255) NOT NULL,
  `cover_back` varchar(255) NOT NULL,
  `game_idGame` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `console_has_mode`
--

DROP TABLE IF EXISTS `console_has_mode`;
CREATE TABLE `console_has_mode` (
  `console_idConsole` int(11) NOT NULL,
  `mode_idMode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `console_has_support`
--

DROP TABLE IF EXISTS `console_has_support`;
CREATE TABLE `console_has_support` (
  `console_idConsole` int(11) NOT NULL,
  `support_idSupport` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `dlc`
--

DROP TABLE IF EXISTS `dlc`;
CREATE TABLE `dlc` (
`idDlc` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `price` float NOT NULL,
  `devise` varchar(255) NOT NULL,
  `console_idConsole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `edition`
--

DROP TABLE IF EXISTS `edition`;
CREATE TABLE `edition` (
`idEdition` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `console_idConsole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `editor`
--

DROP TABLE IF EXISTS `editor`;
CREATE TABLE `editor` (
`idEditor` int(11) NOT NULL,
  `editor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE `game` (
`idGame` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `site` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `game_has_editor`
--

DROP TABLE IF EXISTS `game_has_editor`;
CREATE TABLE `game_has_editor` (
  `game_idGame` int(11) NOT NULL,
  `editor_idEditor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `game_has_gender`
--

DROP TABLE IF EXISTS `game_has_gender`;
CREATE TABLE `game_has_gender` (
  `game_idGame` int(11) NOT NULL,
  `gender_idGender` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `game_has_language`
--

DROP TABLE IF EXISTS `game_has_language`;
CREATE TABLE `game_has_language` (
  `game_idGame` int(11) NOT NULL,
  `language_idLanguage` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `game_has_theme`
--

DROP TABLE IF EXISTS `game_has_theme`;
CREATE TABLE `game_has_theme` (
  `game_idGame` int(11) NOT NULL,
  `theme_idTheme` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gender`
--

DROP TABLE IF EXISTS `gender`;
CREATE TABLE `gender` (
`idGender` int(11) NOT NULL,
  `gender` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
`idLanguage` int(11) NOT NULL,
  `language` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
`idMedia` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `width` float NOT NULL,
  `height` float NOT NULL,
  `console_names` varchar(255) NOT NULL,
  `game_idGame` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mode`
--

DROP TABLE IF EXISTS `mode`;
CREATE TABLE `mode` (
`idMode` int(11) NOT NULL,
  `mode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
`idRole` int(11) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `role`
--

INSERT INTO `role` (`idRole`, `role`) VALUES
(1, 'User'),
(2, 'Admin');

-- --------------------------------------------------------

--
-- Structure de la table `shop`
--

DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop` (
`idShop` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `devise` varchar(255) NOT NULL,
  `edition_idEdition` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `support`
--

DROP TABLE IF EXISTS `support`;
CREATE TABLE `support` (
`idSupport` int(11) NOT NULL,
  `support` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `test`
--

DROP TABLE IF EXISTS `test`;
CREATE TABLE `test` (
`idTest` int(11) NOT NULL,
  `report` longtext NOT NULL,
  `date` datetime NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `note` int(2) NOT NULL,
  `console_idConsole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `theme`
--

DROP TABLE IF EXISTS `theme`;
CREATE TABLE `theme` (
`idTheme` int(11) NOT NULL,
  `theme` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tip`
--

DROP TABLE IF EXISTS `tip`;
CREATE TABLE `tip` (
`idTip` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `console_names` varchar(255) NOT NULL,
  `game_idGame` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
`idUser` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `apiKey` varchar(255) DEFAULT NULL,
  `apiSecret` varchar(255) DEFAULT NULL,
  `role` int(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`idUser`, `email`, `username`, `password`, `apiKey`, `apiSecret`, `role`) VALUES
(1, 'axel.bouaziz@hotmail.fr', 'Axel29', 'azerty', 'fdjfsdhfsdjfkn', 'jfdljkhqepiyezh3893IYHnds', 2);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `analyse`
--
ALTER TABLE `analyse`
 ADD PRIMARY KEY (`idAnalyse`), ADD KEY `fk_analyse_test1_idx` (`test_idTest`);

--
-- Index pour la table `article`
--
ALTER TABLE `article`
 ADD PRIMARY KEY (`idArticle`), ADD KEY `fk_article_game1_idx` (`game_idGame`);

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
 ADD PRIMARY KEY (`idComment`), ADD KEY `fk_comment_test1_idx` (`test_idTest`);

--
-- Index pour la table `config`
--
ALTER TABLE `config`
 ADD PRIMARY KEY (`idConfig`), ADD KEY `fk_config_console1_idx` (`console_idConsole`);

--
-- Index pour la table `console`
--
ALTER TABLE `console`
 ADD PRIMARY KEY (`idConsole`), ADD KEY `fk_console_game1_idx` (`game_idGame`);

--
-- Index pour la table `console_has_mode`
--
ALTER TABLE `console_has_mode`
 ADD PRIMARY KEY (`console_idConsole`,`mode_idMode`), ADD KEY `fk_console_has_mode_mode1_idx` (`mode_idMode`), ADD KEY `fk_console_has_mode_console1_idx` (`console_idConsole`);

--
-- Index pour la table `console_has_support`
--
ALTER TABLE `console_has_support`
 ADD PRIMARY KEY (`console_idConsole`,`support_idSupport`), ADD KEY `fk_console_has_support_support1` (`support_idSupport`);

--
-- Index pour la table `dlc`
--
ALTER TABLE `dlc`
 ADD PRIMARY KEY (`idDlc`), ADD KEY `fk_dlc_console1_idx` (`console_idConsole`);

--
-- Index pour la table `edition`
--
ALTER TABLE `edition`
 ADD PRIMARY KEY (`idEdition`), ADD KEY `fk_edition_console1_idx` (`console_idConsole`);

--
-- Index pour la table `editor`
--
ALTER TABLE `editor`
 ADD PRIMARY KEY (`idEditor`), ADD UNIQUE KEY `editor` (`editor`);

--
-- Index pour la table `game`
--
ALTER TABLE `game`
 ADD PRIMARY KEY (`idGame`);

--
-- Index pour la table `game_has_editor`
--
ALTER TABLE `game_has_editor`
 ADD PRIMARY KEY (`game_idGame`,`editor_idEditor`), ADD KEY `fk_game_has_editor_editor1_idx` (`editor_idEditor`), ADD KEY `fk_game_has_editor_game1_idx` (`game_idGame`);

--
-- Index pour la table `game_has_gender`
--
ALTER TABLE `game_has_gender`
 ADD PRIMARY KEY (`game_idGame`,`gender_idGender`), ADD KEY `fk_game_has_gender_gender1_idx` (`gender_idGender`), ADD KEY `fk_game_has_gender_game1_idx` (`game_idGame`);

--
-- Index pour la table `game_has_language`
--
ALTER TABLE `game_has_language`
 ADD PRIMARY KEY (`game_idGame`,`language_idLanguage`), ADD KEY `fk_game_has_language_language1_idx` (`language_idLanguage`), ADD KEY `fk_game_has_language_game1_idx` (`game_idGame`);

--
-- Index pour la table `game_has_theme`
--
ALTER TABLE `game_has_theme`
 ADD PRIMARY KEY (`game_idGame`,`theme_idTheme`), ADD KEY `fk_game_has_theme_theme1_idx` (`theme_idTheme`), ADD KEY `fk_game_has_theme_game1_idx` (`game_idGame`);

--
-- Index pour la table `gender`
--
ALTER TABLE `gender`
 ADD PRIMARY KEY (`idGender`), ADD UNIQUE KEY `gender` (`gender`);

--
-- Index pour la table `language`
--
ALTER TABLE `language`
 ADD PRIMARY KEY (`idLanguage`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
 ADD PRIMARY KEY (`idMedia`), ADD KEY `fk_media_game1_idx` (`game_idGame`);

--
-- Index pour la table `mode`
--
ALTER TABLE `mode`
 ADD PRIMARY KEY (`idMode`), ADD UNIQUE KEY `mode` (`mode`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
 ADD PRIMARY KEY (`idRole`);

--
-- Index pour la table `shop`
--
ALTER TABLE `shop`
 ADD PRIMARY KEY (`idShop`), ADD KEY `fk_shop_edition1_idx` (`edition_idEdition`);

--
-- Index pour la table `support`
--
ALTER TABLE `support`
 ADD PRIMARY KEY (`idSupport`), ADD UNIQUE KEY `support` (`support`);

--
-- Index pour la table `test`
--
ALTER TABLE `test`
 ADD PRIMARY KEY (`idTest`), ADD KEY `fk_test_console1_idx` (`console_idConsole`);

--
-- Index pour la table `theme`
--
ALTER TABLE `theme`
 ADD PRIMARY KEY (`idTheme`), ADD UNIQUE KEY `theme` (`theme`);

--
-- Index pour la table `tip`
--
ALTER TABLE `tip`
 ADD PRIMARY KEY (`idTip`), ADD KEY `fk_tip_game1_idx` (`game_idGame`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`idUser`), ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `analyse`
--
ALTER TABLE `analyse`
MODIFY `idAnalyse` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
MODIFY `idArticle` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
MODIFY `idComment` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `config`
--
ALTER TABLE `config`
MODIFY `idConfig` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `console`
--
ALTER TABLE `console`
MODIFY `idConsole` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `dlc`
--
ALTER TABLE `dlc`
MODIFY `idDlc` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `edition`
--
ALTER TABLE `edition`
MODIFY `idEdition` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `editor`
--
ALTER TABLE `editor`
MODIFY `idEditor` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `game`
--
ALTER TABLE `game`
MODIFY `idGame` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `gender`
--
ALTER TABLE `gender`
MODIFY `idGender` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `language`
--
ALTER TABLE `language`
MODIFY `idLanguage` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
MODIFY `idMedia` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mode`
--
ALTER TABLE `mode`
MODIFY `idMode` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
MODIFY `idRole` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `shop`
--
ALTER TABLE `shop`
MODIFY `idShop` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `support`
--
ALTER TABLE `support`
MODIFY `idSupport` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `test`
--
ALTER TABLE `test`
MODIFY `idTest` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `theme`
--
ALTER TABLE `theme`
MODIFY `idTheme` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `tip`
--
ALTER TABLE `tip`
MODIFY `idTip` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `analyse`
--
ALTER TABLE `analyse`
ADD CONSTRAINT `fk_analyse_test1` FOREIGN KEY (`test_idTest`) REFERENCES `test` (`idTest`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
ADD CONSTRAINT `fk_article_game1` FOREIGN KEY (`game_idGame`) REFERENCES `game` (`idGame`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
ADD CONSTRAINT `fk_comment_test1` FOREIGN KEY (`test_idTest`) REFERENCES `test` (`idTest`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `config`
--
ALTER TABLE `config`
ADD CONSTRAINT `fk_config_console1` FOREIGN KEY (`console_idConsole`) REFERENCES `console` (`idConsole`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `console`
--
ALTER TABLE `console`
ADD CONSTRAINT `fk_console_game1` FOREIGN KEY (`game_idGame`) REFERENCES `game` (`idGame`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `console_has_mode`
--
ALTER TABLE `console_has_mode`
ADD CONSTRAINT `fk_console_has_mode_console1` FOREIGN KEY (`console_idConsole`) REFERENCES `console` (`idConsole`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_console_has_mode_mode1` FOREIGN KEY (`mode_idMode`) REFERENCES `mode` (`idMode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `console_has_support`
--
ALTER TABLE `console_has_support`
ADD CONSTRAINT `fk_console_has_support_console1` FOREIGN KEY (`console_idConsole`) REFERENCES `console` (`idConsole`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_console_has_support_support1` FOREIGN KEY (`support_idSupport`) REFERENCES `support` (`idSupport`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `dlc`
--
ALTER TABLE `dlc`
ADD CONSTRAINT `fk_dlc_console1` FOREIGN KEY (`console_idConsole`) REFERENCES `console` (`idConsole`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `edition`
--
ALTER TABLE `edition`
ADD CONSTRAINT `fk_edition_console1` FOREIGN KEY (`console_idConsole`) REFERENCES `console` (`idConsole`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `game_has_editor`
--
ALTER TABLE `game_has_editor`
ADD CONSTRAINT `fk_game_has_editor_editor1` FOREIGN KEY (`editor_idEditor`) REFERENCES `editor` (`idEditor`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_game_has_editor_game1` FOREIGN KEY (`game_idGame`) REFERENCES `game` (`idGame`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `game_has_gender`
--
ALTER TABLE `game_has_gender`
ADD CONSTRAINT `fk_game_has_gender_game1` FOREIGN KEY (`game_idGame`) REFERENCES `game` (`idGame`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_game_has_gender_gender1` FOREIGN KEY (`gender_idGender`) REFERENCES `gender` (`idGender`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `game_has_language`
--
ALTER TABLE `game_has_language`
ADD CONSTRAINT `fk_game_has_language_game1` FOREIGN KEY (`game_idGame`) REFERENCES `game` (`idGame`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_game_has_language_language1` FOREIGN KEY (`language_idLanguage`) REFERENCES `language` (`idLanguage`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `game_has_theme`
--
ALTER TABLE `game_has_theme`
ADD CONSTRAINT `fk_game_has_theme_game1` FOREIGN KEY (`game_idGame`) REFERENCES `game` (`idGame`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_game_has_theme_theme1` FOREIGN KEY (`theme_idTheme`) REFERENCES `theme` (`idTheme`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
ADD CONSTRAINT `fk_media_game1` FOREIGN KEY (`game_idGame`) REFERENCES `game` (`idGame`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `shop`
--
ALTER TABLE `shop`
ADD CONSTRAINT `fk_shop_edition1` FOREIGN KEY (`edition_idEdition`) REFERENCES `edition` (`idEdition`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `test`
--
ALTER TABLE `test`
ADD CONSTRAINT `fk_test_console1` FOREIGN KEY (`console_idConsole`) REFERENCES `console` (`idConsole`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tip`
--
ALTER TABLE `tip`
ADD CONSTRAINT `fk_tip_game1` FOREIGN KEY (`game_idGame`) REFERENCES `game` (`idGame`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
ADD CONSTRAINT `FK_USER_ROLE` FOREIGN KEY (`role`) REFERENCES `role` (`idRole`);
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
