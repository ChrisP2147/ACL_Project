-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Apr 2018 um 17:32
-- Server-Version: 10.1.29-MariaDB
-- PHP-Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `mydb`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer`
--

CREATE TABLE `benutzer` (
  `id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `benutzername` varchar(45) NOT NULL,
  `passwort` varchar(45) NOT NULL COMMENT 'Passwort mit md5 hash',
  `name` varchar(45) NOT NULL,
  `vorname` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `benutzer`
--

INSERT INTO `benutzer` (`id`, `active`, `benutzername`, `passwort`, `name`, `vorname`) VALUES
(1, 1, 'admin', 'admin', 'Administrator', 'Admin'),
(2, 1, 'chrisP', '123', 'Pitzner', 'Christian'),
(3, 1, 'dennisP', '123', 'Pohl', 'Dennis'),
(4, 1, 'danielG', '123', 'Gahl', 'Daniel'),
(5, 1, 'markD', '123', 'Deuter', 'Mark'),
(6, 1, 'bennyO', '123', 'Ole', 'Benjamin'),
(32, 1, 'testi', '123', 'test', 'testi');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gruppen`
--

CREATE TABLE `gruppen` (
  `id` int(11) NOT NULL,
  `bezeichnung` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `gruppen`
--

INSERT INTO `gruppen` (`id`, `bezeichnung`) VALUES
(1, 'Geschäftsleitung'),
(2, 'Mitarbeiter'),
(3, 'Besucher'),
(4, 'Praktikanten'),
(5, 'Admin');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rechte`
--

CREATE TABLE `rechte` (
  `id` int(11) NOT NULL,
  `bezeichnung` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `rechte`
--

INSERT INTO `rechte` (`id`, `bezeichnung`) VALUES
(1, 'lesen'),
(2, 'schreiben'),
(3, 'drucken'),
(4, 'Benutzer löschen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rel_benutzer_gruppe`
--

CREATE TABLE `rel_benutzer_gruppe` (
  `id_gruppen` int(11) NOT NULL,
  `id_benutzer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `rel_benutzer_gruppe`
--

INSERT INTO `rel_benutzer_gruppe` (`id_gruppen`, `id_benutzer`) VALUES
(1, 4),
(2, 6),
(2, 32),
(3, 3),
(4, 2),
(4, 5),
(5, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rel_gruppe_recht`
--

CREATE TABLE `rel_gruppe_recht` (
  `id_gruppen` int(11) NOT NULL,
  `id_rechte` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `rel_gruppe_recht`
--

INSERT INTO `rel_gruppe_recht` (`id_gruppen`, `id_rechte`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(1, 2),
(2, 2),
(3, 2),
(5, 2),
(1, 3),
(2, 3),
(5, 3),
(1, 4),
(5, 4);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `benutzer`
--
ALTER TABLE `benutzer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `benutzername_UNIQUE` (`benutzername`);

--
-- Indizes für die Tabelle `gruppen`
--
ALTER TABLE `gruppen`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `rechte`
--
ALTER TABLE `rechte`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `rel_benutzer_gruppe`
--
ALTER TABLE `rel_benutzer_gruppe`
  ADD PRIMARY KEY (`id_gruppen`,`id_benutzer`),
  ADD KEY `fk_benutzer_idx` (`id_benutzer`),
  ADD KEY `fk_gruppe_idx` (`id_gruppen`);

--
-- Indizes für die Tabelle `rel_gruppe_recht`
--
ALTER TABLE `rel_gruppe_recht`
  ADD PRIMARY KEY (`id_rechte`,`id_gruppen`),
  ADD KEY `fk_group_idx` (`id_gruppen`),
  ADD KEY `fk_recht_idx` (`id_rechte`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `benutzer`
--
ALTER TABLE `benutzer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT für Tabelle `gruppen`
--
ALTER TABLE `gruppen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `rechte`
--
ALTER TABLE `rechte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `rel_benutzer_gruppe`
--
ALTER TABLE `rel_benutzer_gruppe`
  ADD CONSTRAINT `fk_benutzer` FOREIGN KEY (`id_benutzer`) REFERENCES `benutzer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_gruppe` FOREIGN KEY (`id_gruppen`) REFERENCES `gruppen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `rel_gruppe_recht`
--
ALTER TABLE `rel_gruppe_recht`
  ADD CONSTRAINT `fk_group` FOREIGN KEY (`id_gruppen`) REFERENCES `gruppen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recht` FOREIGN KEY (`id_rechte`) REFERENCES `rechte` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
