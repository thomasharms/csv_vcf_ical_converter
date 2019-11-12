-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Erstellungszeit: 12. Jun 2018 um 16:31
-- Server-Version: 5.7.21
-- PHP-Version: 5.6.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `mailtool`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `billing`
--

DROP TABLE IF EXISTS `billing`;
CREATE TABLE IF NOT EXISTS `billing` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `black_list`
--

DROP TABLE IF EXISTS `black_list`;
CREATE TABLE IF NOT EXISTS `black_list` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `black_list_string` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `black_list_string` (`black_list_string`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `counter`
--

DROP TABLE IF EXISTS `counter`;
CREATE TABLE IF NOT EXISTS `counter` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `counter` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mail_accounts`
--

DROP TABLE IF EXISTS `mail_accounts`;
CREATE TABLE IF NOT EXISTS `mail_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `smtp` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `menu`
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `link` varchar(20000) NOT NULL,
  `private` tinyint(1) DEFAULT '0' COMMENT '1 private, 0 public site',
  `preference` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `menu`
--

INSERT INTO `menu` (`id`, `name`, `link`, `private`, `preference`) VALUES
(1, 'Schnellstart', 'ctr_quickstart.php', 0, NULL),
(2, 'Produkte', 'ctr_products.php', 0, NULL),
(3, 'Support', 'ctr_supoort.php', 0, NULL),
(4, 'How To', 'help.php', 0, NULL),
(5, 'Templates', 'ctr_templates.php', 1, NULL),
(6, 'Accountdaten', 'ctr_profile.php?action=view&ent=accountdata&cid=', 1, 1),
(7, 'Postfach', 'ctr_profile.php?action=view&ent=messages&cid=', 1, 1),
(8, 'Rechnungen', 'ctr_profile.php?action=view&ent=bill&cid=', 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `page_variables`
--

DROP TABLE IF EXISTS `page_variables`;
CREATE TABLE IF NOT EXISTS `page_variables` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `block_name` varchar(100) NOT NULL,
  `css` varchar(100) DEFAULT NULL,
  `js` varchar(100) DEFAULT NULL,
  `template` varchar(100) DEFAULT NULL,
  `tpl` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT NULL COMMENT 'public -> 1, sonst private page',
  PRIMARY KEY (`id`),
  UNIQUE KEY `block_name` (`block_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `page_variables`
--

INSERT INTO `page_variables` (`id`, `block_name`, `css`, `js`, `template`, `tpl`, `title`, `is_public`) VALUES
(1, 'header', NULL, NULL, 'header.html', NULL, NULL, NULL),
(2, 'home_view', 'home_view.css', 'home_view.js', 'main_template.html', 'home_view.tpl', 'WorkingTitle', 1),
(3, 'top_bar', 'top_bar.css', 'top_bar.js', NULL, 'top_bar.tpl', NULL, 1),
(4, 'menu_public', 'menu.css', 'menu.js', NULL, 'menu_public.tpl', NULL, 1),
(5, 'register_user', 'register_user.css', 'register_user.js', 'main_template.html', 'register_user.tpl', 'SIGN UP', 1),
(6, 'login_user', 'login_user.css', 'login_user.js', 'main_template.html', 'login_user.tpl', 'Working Title - Log IN', 1),
(8, 'menu_private', 'menu.css', 'menu.js', '', 'menu_private.tpl', NULL, 0),
(9, 'account_preference_menu', 'account_preference_menu.css', 'account_preference_menu.js', NULL, 'account_preference_menu.tpl', NULL, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `company` varchar(200) NOT NULL,
  `street` varchar(150) DEFAULT NULL,
  `hsnumber` smallint(5) UNSIGNED DEFAULT NULL,
  `postal` int(5) UNSIGNED DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `insertion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mail` varchar(100) NOT NULL,
  `password` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `mail`, `password`) VALUES
(12, 'test@test.de', '4dff4ea340f0a823f15d3f4f01ab62eae0e5da579ccb851f8db9dfe84c58b2b37b89903a740e1ee172da793a6e79d560e5f7f9bd058a12a280433ed6fa46510a');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
