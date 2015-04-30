-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Client :  localhost:3306
-- Généré le :  Jeu 30 Avril 2015 à 09:22
-- Version du serveur :  5.5.38
-- Version de PHP :  5.6.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `videoGames`
--
DROP DATABASE IF EXISTS `videoGames`;
CREATE DATABASE IF NOT EXISTS `videoGames` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `videoGames`;

-- --------------------------------------------------------

--
-- Structure de la table `analyse`
--

DROP TABLE IF EXISTS `analyse`;
CREATE TABLE IF NOT EXISTS `analyse` (
`idAnalyse` int(11) NOT NULL,
  `analyse` longtext NOT NULL,
  `type` varchar(255) NOT NULL,
  `test_idTest` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `analyse`
--

INSERT INTO `analyse` (`idAnalyse`, `analyse`, `type`, `test_idTest`) VALUES
(1, 'Très bon jeu, graphismes époustouflants', 'positive', 1),
(2, 'Répétitif', 'négative', 1),
(3, 'Bon jeu, addictif\r\n', 'positive', 2),
(4, 'Trop peu d''occupation', 'négative', 1);

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
`idArticle` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `consoles_names` varchar(255) NOT NULL,
  `game_idGame` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `article`
--

INSERT INTO `article` (`idArticle`, `type`, `title`, `user_name`, `date`, `consoles_names`, `game_idGame`) VALUES
(1, 'news', 'Sortie d''Assassin''s Creed Unity', 'Axel29', '2015-04-23 08:35:38', 'PS4,PC,Xbox One', 1),
(2, 'news', 'Clash Of Clans 2 bientôt !', 'Axel29', '2015-04-23 08:35:38', 'iOS,Android', 2);

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
`idComment` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `note` int(11) NOT NULL,
  `like` int(11) NOT NULL,
  `dislike` int(11) NOT NULL,
  `text` longtext NOT NULL,
  `test_idTest` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `comment`
--

INSERT INTO `comment` (`idComment`, `date`, `user_name`, `note`, `like`, `dislike`, `text`, `test_idTest`) VALUES
(1, '2015-04-06 09:12:24', 'Axel29', 5, 0, 0, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam non est eget risus scelerisque viverra. Proin dapibus magna eget pharetra dictum. Fusce pulvinar nec metus ut tempus. Integer ultricies laoreet diam, in malesuada orci faucibus at. In hendrerit lectus nulla, at sollicitudin ante faucibus sed. Etiam eu luctus libero. Nunc ligula urna, dictum cursus nunc vitae, gravida efficitur mauris. Phasellus sed hendrerit velit.', 1),
(2, '2015-04-22 16:18:17', 'Yoyo', 5, 0, 0, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam non est eget risus scelerisque viverra. Proin dapibus magna eget pharetra dictum. Fusce pulvinar nec metus ut tempus. Integer ultricies laoreet diam, in malesuada orci faucibus at. In hendrerit lectus nulla, at sollicitudin ante faucibus sed. Etiam eu luctus libero. Nunc ligula urna, dictum cursus nunc vitae, gravida efficitur mauris. Phasellus sed hendrerit velit.', 2);

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
`idConfig` int(11) NOT NULL,
  `config` longtext NOT NULL,
  `type` varchar(255) NOT NULL,
  `console_idConsole` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `config`
--

INSERT INTO `config` (`idConfig`, `config`, `type`, `console_idConsole`) VALUES
(1, 'Système 64-bit uniquement\r\n\r\nOS: Windows 7 SP1, Windows ® 8 / 8.1 (uniquement 64 bits)\r\n\r\nProcesseur :\r\n\r\nMinimum : Intel Core ® i5-2500K @ 3,3 GHz ou AMD FX-8350 @ 4,0 GHz \r\n\r\nRecommandé : Intel Core ® i7-3770 @ 3,4 GHz ou AMD FX-8350 @ 4,0 GHz ou plus\r\n\r\nRAM :\r\n\r\nMinimum : 6 Go ou plus\r\n\r\nRecommandé : 8 Go ou plus\r\n\r\nCarte graphique :\r\n\r\nMinimum : NVIDIA GeForce GTX 680 ou AMD Radeon HD 7970 (2 GB VRAM)\r\n\r\nRecommandé : NVIDIA GeForce GTX 780 ou AMD Radeon R9 290X (3 GB VRAM)\r\n\r\nCarte Son :\r\n\r\nCompatible directX 9.0c \r\n\r\nDisque Dur :\r\n\r\n50 Go disponibles\r\n\r\nPériphériques compatibles :\r\n\r\nClavier et souris compatibles Windows requis\r\n\r\nMultijoueur :\r\n\r\n256 kbps ou plus de bande passante de chargement \r\n\r\n\r\n\r\n*Cartes graphiques supportées lors du lancement:\r\n\r\nNVIDIA GeForce GTX 680 ou mieux, GeForce GTX 700 series; AMD Radeon HD7970 ou mieux, Radeon R9 200 series\r\n\r\nNote: Les versions PC portables de ces cartes peuvent fonctionner, mais ne sont pas officiellement compatibles.', 'optimale', 1),
(2, 'Système d''exploitation Windows 7 et 8 64 bits.\r\n\r\nProcesseur quatre cœurs Intel Core i5 / i7 3.0 GHz ou AMD FX 3.5 GHz\r\n\r\nMémoire système 6 Go\r\n\r\nCartes graphiques recommandées:\r\n\r\n _ Séries NVIDIA GeForce: GTX 750 ti, GTX 465, GTX 460, GTX 560, GTX 660, GTX 470, GTX560 ti, GTX 570, GTX 480, GTX 580, GTX 660 ti, GTX 760, GTX 670, GTX 680, GTX 770, GTX 780, GTX Titan, GTX 690.\r\n\r\n_ Séries AMD Radeon: R7 260X, HD 6850, HD 5850, HD 6870, HD 5870, HD 6950, HD 7850, HD 6970, HD 7870, HD 5970, R9 270X, HD 7950, HD 7970, R9 280X, HD 6990.', 'minimale', 1),
(3, 'Android / iOS', 'obligatoire', 2);

-- --------------------------------------------------------

--
-- Structure de la table `console`
--

DROP TABLE IF EXISTS `console`;
CREATE TABLE IF NOT EXISTS `console` (
`idConsole` int(11) NOT NULL,
  `business_model` varchar(255) NOT NULL,
  `pegi` varchar(255) NOT NULL,
  `release` date NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `cover_front` varchar(255) NOT NULL,
  `cover_back` varchar(255) NOT NULL,
  `game_idGame` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `console`
--

INSERT INTO `console` (`idConsole`, `business_model`, `pegi`, `release`, `name`, `description`, `cover_front`, `cover_back`, `game_idGame`) VALUES
(1, 'Free to play', '+18', '2014-11-11', 'PS4', 'Assassin’s Creed Unity est un jeu vidéo d''action-aventure et d''infiltration développé par Ubisoft Montréal et édité par la société Ubisoft. Le jeu est sorti officiellement le 13 novembre 2014 sur Windows, PlayStation 4 et Xbox One.', '', '', 1),
(2, 'Freemium', '', '2012-08-02', 'Tablette / Mobile', 'Clash of Clans est un jeu vidéo sur appareil mobile de stratégie en temps réel développé et édité par le studio finlandais Supercell. Il est sorti le 2 août 2012 sur iOS et le 7 novembre 2013 sur Android. ', '', '', 2);

-- --------------------------------------------------------

--
-- Structure de la table `console_has_config`
--

DROP TABLE IF EXISTS `console_has_config`;
CREATE TABLE IF NOT EXISTS `console_has_config` (
  `console_idConsole` int(11) NOT NULL,
  `config_idConfig` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `console_has_config`
--

INSERT INTO `console_has_config` (`console_idConsole`, `config_idConfig`) VALUES
(1, 1),
(1, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Structure de la table `console_has_mode`
--

DROP TABLE IF EXISTS `console_has_mode`;
CREATE TABLE IF NOT EXISTS `console_has_mode` (
  `console_idConsole` int(11) NOT NULL,
  `mode_idMode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `console_has_mode`
--

INSERT INTO `console_has_mode` (`console_idConsole`, `mode_idMode`) VALUES
(1, 1),
(1, 2),
(1, 5),
(2, 5);

-- --------------------------------------------------------

--
-- Structure de la table `console_has_support`
--

DROP TABLE IF EXISTS `console_has_support`;
CREATE TABLE IF NOT EXISTS `console_has_support` (
  `console_idConsole` int(11) NOT NULL,
  `support_idSupport` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `console_has_support`
--

INSERT INTO `console_has_support` (`console_idConsole`, `support_idSupport`) VALUES
(1, 1),
(1, 2),
(2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `dlc`
--

DROP TABLE IF EXISTS `dlc`;
CREATE TABLE IF NOT EXISTS `dlc` (
`idDlc` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `price` float NOT NULL,
  `devise` varchar(255) NOT NULL,
  `console_idConsole` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `dlc`
--

INSERT INTO `dlc` (`idDlc`, `title`, `description`, `price`, `devise`, `console_idConsole`) VALUES
(1, 'Pack de maps', 'Pack contenant 5 maps exclusives', 10, '€', 1),
(2, 'Pack de 1000 gemmes', 'Pack de 1000 gemmes, gagnez 20% !', 19.9, '€', 2);

-- --------------------------------------------------------

--
-- Structure de la table `edition`
--

DROP TABLE IF EXISTS `edition`;
CREATE TABLE IF NOT EXISTS `edition` (
`idEdition` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `console_idConsole` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `edition`
--

INSERT INTO `edition` (`idEdition`, `name`, `content`, `console_idConsole`) VALUES
(1, 'Standard', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla commodo urna vel bibendum bibendum. Suspendisse finibus, nibh vel aliquam maximus, diam ligula viverra leo, non cursus nulla risus sit amet urna. Curabitur quam tellus, facilisis et bibendum sed, tincidunt nec eros. Donec interdum sagittis lacus vel venenatis. Maecenas condimentum neque elit, euismod varius leo placerat vel. Nulla ac orci ut ante tincidunt vestibulum. Fusce eu ipsum mi. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nulla gravida eleifend purus nec faucibus. Praesent aliquam volutpat iaculis. Sed libero est, accumsan sed scelerisque at, efficitur vitae massa.', 1),
(2, 'Collector', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a elementum quam. Fusce ipsum libero, bibendum sit amet elit in, blandit mollis purus. Maecenas rutrum arcu auctor lectus interdum interdum. Aliquam elementum porta ipsum sit amet facilisis. Nullam eu odio quis nulla volutpat posuere. Ut tincidunt, elit nec ullamcorper dictum, magna urna hendrerit tortor, sed auctor est metus vitae lacus. Sed sollicitudin sem a elit laoreet, vel semper diam rhoncus. Nunc vel porttitor mauris.\r\n\r\nMauris ultricies urna turpis, malesuada aliquam diam ultrices placerat. Sed eu erat et quam maximus pharetra ac at nulla. Integer semper lacus nec tellus posuere, nec rhoncus enim tempor. Donec maximus in est eget laoreet. Aenean a commodo eros. Suspendisse egestas cursus laoreet. Phasellus finibus velit eget diam venenatis euismod eu id purus.', 1),
(3, 'Standard', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam feugiat elementum rhoncus. Fusce lectus magna, feugiat vitae cursus sed, hendrerit nec tortor. Duis ut turpis ac purus commodo consequat. Vestibulum facilisis nibh sed erat pellentesque, ut molestie elit maximus. Cras maximus metus commodo orci dictum malesuada. Donec eu mauris quis lorem sodales malesuada. Suspendisse vitae lorem non lacus venenatis egestas. Phasellus commodo dignissim ipsum, a gravida diam pellentesque at.', 2);

-- --------------------------------------------------------

--
-- Structure de la table `editor`
--

DROP TABLE IF EXISTS `editor`;
CREATE TABLE IF NOT EXISTS `editor` (
`idEditor` int(11) NOT NULL,
  `editor` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `editor`
--

INSERT INTO `editor` (`idEditor`, `editor`) VALUES
(1, 'Capcom'),
(2, 'EA Sports'),
(7, 'editors editor'),
(3, 'Epic Games'),
(4, 'Gameloft'),
(5, 'Supercell'),
(6, 'Ubisoft');

-- --------------------------------------------------------

--
-- Structure de la table `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE IF NOT EXISTS `game` (
`idGame` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `site` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `game`
--

INSERT INTO `game` (`idGame`, `title`, `site`) VALUES
(1, 'Assassin''s Creed Unity', 'http://assassinscreed.ubi.com/fr-fr/games/assassins-creed-unity.aspx'),
(2, 'Clash Of Clans', 'http://supercell.com/en/games/clashofclans/');

-- --------------------------------------------------------

--
-- Structure de la table `game_has_editor`
--

DROP TABLE IF EXISTS `game_has_editor`;
CREATE TABLE IF NOT EXISTS `game_has_editor` (
  `game_idGame` int(11) NOT NULL,
  `editor_idEditor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `game_has_editor`
--

INSERT INTO `game_has_editor` (`game_idGame`, `editor_idEditor`) VALUES
(2, 5),
(1, 6);

-- --------------------------------------------------------

--
-- Structure de la table `game_has_gender`
--

DROP TABLE IF EXISTS `game_has_gender`;
CREATE TABLE IF NOT EXISTS `game_has_gender` (
  `game_idGame` int(11) NOT NULL,
  `gender_idGender` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `game_has_gender`
--

INSERT INTO `game_has_gender` (`game_idGame`, `gender_idGender`) VALUES
(1, 1),
(2, 1),
(1, 3);

-- --------------------------------------------------------

--
-- Structure de la table `game_has_language`
--

DROP TABLE IF EXISTS `game_has_language`;
CREATE TABLE IF NOT EXISTS `game_has_language` (
  `game_idGame` int(11) NOT NULL,
  `language_idLanguage` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `game_has_language`
--

INSERT INTO `game_has_language` (`game_idGame`, `language_idLanguage`) VALUES
(1, 1),
(2, 1),
(1, 2),
(2, 2),
(1, 3),
(1, 4);

-- --------------------------------------------------------

--
-- Structure de la table `game_has_theme`
--

DROP TABLE IF EXISTS `game_has_theme`;
CREATE TABLE IF NOT EXISTS `game_has_theme` (
  `game_idGame` int(11) NOT NULL,
  `theme_idTheme` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `game_has_theme`
--

INSERT INTO `game_has_theme` (`game_idGame`, `theme_idTheme`) VALUES
(2, 1),
(1, 4);

-- --------------------------------------------------------

--
-- Structure de la table `gender`
--

DROP TABLE IF EXISTS `gender`;
CREATE TABLE IF NOT EXISTS `gender` (
`idGender` int(11) NOT NULL,
  `gender` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `gender`
--

INSERT INTO `gender` (`idGender`, `gender`) VALUES
(1, 'Action'),
(5, 'Course'),
(2, 'FPS'),
(3, 'Infiltration'),
(6, 'Puzzle'),
(4, 'Sport');

-- --------------------------------------------------------

--
-- Structure de la table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
`idLanguage` int(11) NOT NULL,
  `language` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `language`
--

INSERT INTO `language` (`idLanguage`, `language`) VALUES
(1, 'Français'),
(2, 'Anglais'),
(3, 'Espagnol'),
(4, 'Italien');

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
`idMedia` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `width` float NOT NULL,
  `height` float NOT NULL,
  `consoles_names` varchar(255) NOT NULL,
  `game_idGame` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `media`
--

INSERT INTO `media` (`idMedia`, `type`, `url`, `unit`, `width`, `height`, `consoles_names`, `game_idGame`) VALUES
(1, 'image', 'http://lorempixel.com/400/200/', 'px', 400, 200, 'PS4,PC', 1),
(2, 'image', 'http://lorempixel.com/450/210/', 'px', 450, 210, 'iOS,Android', 2);

-- --------------------------------------------------------

--
-- Structure de la table `mode`
--

DROP TABLE IF EXISTS `mode`;
CREATE TABLE IF NOT EXISTS `mode` (
`idMode` int(11) NOT NULL,
  `mode` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `mode`
--

INSERT INTO `mode` (`idMode`, `mode`) VALUES
(4, 'MMO'),
(6, 'mode name'),
(2, 'Multijoueur'),
(1, 'Solo'),
(5, 'Stratégie'),
(3, 'Team-Play');

-- --------------------------------------------------------

--
-- Structure de la table `shop`
--

DROP TABLE IF EXISTS `shop`;
CREATE TABLE IF NOT EXISTS `shop` (
`idShop` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `devise` varchar(255) NOT NULL,
  `edition_idEdition` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `shop`
--

INSERT INTO `shop` (`idShop`, `url`, `name`, `price`, `devise`, `edition_idEdition`) VALUES
(1, 'http://www.micromania.fr/assassin-s-creed-unity-57047.html', 'Micromania', '69,99', '€', 2),
(2, 'http://www.amazon.fr/Ubisoft-Assassins-Creed-Unity/dp/B00J7GDOPQ', 'Amazon', '49,90', '€', 1),
(3, 'https://play.google.com/store/apps/details?id=com.supercell.clashofclans&hl=fr', 'Google Play', '0.00', '€', 3),
(4, 'https://itunes.apple.com/fr/app/clash-of-clans/id529479190?mt=8', 'App Store', '0.00', '€', 3);

-- --------------------------------------------------------

--
-- Structure de la table `support`
--

DROP TABLE IF EXISTS `support`;
CREATE TABLE IF NOT EXISTS `support` (
`idSupport` int(11) NOT NULL,
  `support` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `support`
--

INSERT INTO `support` (`idSupport`, `support`) VALUES
(2, 'Dématérialisé'),
(1, 'Physique'),
(3, 'supports support');

-- --------------------------------------------------------

--
-- Structure de la table `test`
--

DROP TABLE IF EXISTS `test`;
CREATE TABLE IF NOT EXISTS `test` (
`idTest` int(11) NOT NULL,
  `report` longtext NOT NULL,
  `date` datetime NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `note` int(2) NOT NULL,
  `console_idConsole` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `test`
--

INSERT INTO `test` (`idTest`, `report`, `date`, `user_name`, `note`, `console_idConsole`) VALUES
(1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam volutpat finibus lacus vitae molestie. Proin volutpat, eros eu varius varius, nisl neque hendrerit nunc, sed tristique lacus elit id nunc. Sed aliquet mollis euismod. Morbi lobortis erat ut diam lacinia suscipit. Ut tristique ornare tellus, at ornare eros tristique vitae. Donec luctus odio sit amet sem semper, ac semper nibh vestibulum. Proin ligula mauris, mollis ut magna non, sodales vehicula magna. Vestibulum urna est, pellentesque sed justo in, venenatis ultricies enim. Suspendisse congue elit vel euismod scelerisque. Aliquam eget molestie nibh, eu suscipit ipsum. Phasellus vitae luctus arcu.', '2015-04-21 13:08:44', 'Axel29', 5, 1),
(2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam non est eget risus scelerisque viverra. Proin dapibus magna eget pharetra dictum. Fusce pulvinar nec metus ut tempus. Integer ultricies laoreet diam, in malesuada orci faucibus at. In hendrerit lectus nulla, at sollicitudin ante faucibus sed. Etiam eu luctus libero. Nunc ligula urna, dictum cursus nunc vitae, gravida efficitur mauris. Phasellus sed hendrerit velit.', '2015-04-22 15:12:35', 'Axel29', 3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `theme`
--

DROP TABLE IF EXISTS `theme`;
CREATE TABLE IF NOT EXISTS `theme` (
`idTheme` int(11) NOT NULL,
  `theme` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `theme`
--

INSERT INTO `theme` (`idTheme`, `theme`) VALUES
(1, 'Fantasy'),
(4, 'Historique'),
(2, 'Horreur'),
(3, 'Science-Fiction'),
(5, 'themes theme');

-- --------------------------------------------------------

--
-- Structure de la table `tip`
--

DROP TABLE IF EXISTS `tip`;
CREATE TABLE IF NOT EXISTS `tip` (
`idTip` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `consoles_names` varchar(255) NOT NULL,
  `game_idGame` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tip`
--

INSERT INTO `tip` (`idTip`, `content`, `consoles_names`, `game_idGame`) VALUES
(1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vestibulum tellus ut rutrum fringilla. In cursus lacinia lorem, eu vestibulum mauris mollis lobortis. Pellentesque ut massa ut magna consequat ornare.', 'PS4,PC', 1),
(2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu fermentum est, sit amet porta nibh. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus.', 'iOS,Android', 2);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
`idUser` int(11) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `apiKey` varchar(255) DEFAULT NULL,
  `apiSecret` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Index pour la table `console_has_config`
--
ALTER TABLE `console_has_config`
 ADD PRIMARY KEY (`console_idConsole`,`config_idConfig`), ADD KEY `fk_console_has_config_config1_idx` (`config_idConfig`), ADD KEY `fk_console_has_config_console1_idx` (`console_idConsole`);

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
 ADD PRIMARY KEY (`idUser`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `analyse`
--
ALTER TABLE `analyse`
MODIFY `idAnalyse` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
MODIFY `idArticle` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
MODIFY `idComment` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `config`
--
ALTER TABLE `config`
MODIFY `idConfig` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `console`
--
ALTER TABLE `console`
MODIFY `idConsole` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `dlc`
--
ALTER TABLE `dlc`
MODIFY `idDlc` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `edition`
--
ALTER TABLE `edition`
MODIFY `idEdition` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `editor`
--
ALTER TABLE `editor`
MODIFY `idEditor` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `game`
--
ALTER TABLE `game`
MODIFY `idGame` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `gender`
--
ALTER TABLE `gender`
MODIFY `idGender` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `language`
--
ALTER TABLE `language`
MODIFY `idLanguage` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
MODIFY `idMedia` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `mode`
--
ALTER TABLE `mode`
MODIFY `idMode` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `shop`
--
ALTER TABLE `shop`
MODIFY `idShop` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `support`
--
ALTER TABLE `support`
MODIFY `idSupport` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `test`
--
ALTER TABLE `test`
MODIFY `idTest` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `theme`
--
ALTER TABLE `theme`
MODIFY `idTheme` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `tip`
--
ALTER TABLE `tip`
MODIFY `idTip` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT;
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
-- Contraintes pour la table `console_has_config`
--
ALTER TABLE `console_has_config`
ADD CONSTRAINT `fk_console_has_config_config1` FOREIGN KEY (`config_idConfig`) REFERENCES `config` (`idConfig`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_console_has_config_console1` FOREIGN KEY (`console_idConsole`) REFERENCES `console` (`idConsole`) ON DELETE CASCADE ON UPDATE CASCADE;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
