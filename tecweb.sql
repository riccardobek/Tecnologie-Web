-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 30, 2018 at 10:33 AM
-- Server version: 5.7.22-0ubuntu0.16.04.1
-- PHP Version: 7.0.30-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tecweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `Attivita`
--

CREATE TABLE `Attivita` (
  `Codice` int(11) NOT NULL,
  `Macro` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Descrizione` varchar(500) NOT NULL,
  `Prezzo` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Attivita`
--

INSERT INTO `Attivita` (`Codice`, `Macro`, `Nome`, `Descrizione`, `Prezzo`) VALUES
(3, 3, 'Hydro Advanced', '8 km attraversando le più belle rapide del Brenta.\r\nDurata circa 3 ore e 30.', '48.00'),
(4, 1, 'Active Plus', 'Oltre alla discesa, la Guida Rafting vi roporrà alcune attività strettamente legate al fiume: esplorazione alla Grotta ell’elefante Bianco, tuffi nelle marmitte carsiche dei Calieroni, nuoto e sfide tra i vari componenti ell’equipaggio! - Durata circa 3h. ', '45.00'),
(6, 1, 'Explorer Raft', 'E\' il percorso più lungo, circa 13 o 15km, in cui percorrere le\r\npiù belle rapide del Brenta, visitare la Grotta dell’Elefante\r\nBianco, risalire il fiume per andare alle Cascate dei\r\nCalieroni dove tuffarsi in pozze color smeraldo! Durata - 3h', '48.00'),
(7, 1, 'Wild Raft', 'Rafting Selvaggio scendendo 10 km del fiume Brenta sfidando le onde, esplorando il lago carsico\r\ndella Grotta dell’Elefante Bianco con i tuffi nella pozza Smeraldo e nuoto nella rapida delle 1000\r\nOnde per vivere un’avventura al 100%! Durata attività 3h 30min.', '45.00'),
(8, 1, 'Rafting by Night', 'Rafting by Night Discesa in notturna del fiume con sosta a sorpresa e cena\r\nin riva al fiume.', '38.00'),
(10, 2, 'Prova la canoa', 'Se non volete impegnarvi con un corso completo, potete scegliere questa opzione per imparare a piccoli passi.\r\nDurata 3h.', '50.00');

-- --------------------------------------------------------

--
-- Table structure for table `Disponibilita`
--

CREATE TABLE `Disponibilita` (
  `Giorno` date NOT NULL,
  `PostiDisponibili` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Disponibilita`
--

INSERT INTO `Disponibilita` (`Giorno`, `PostiDisponibili`) VALUES
('2018-02-14', 1),
('2018-02-15', 32),
('2018-02-17', 25),
('2018-02-22', 23);

-- --------------------------------------------------------

--
-- Table structure for table `Macroattivita`
--

CREATE TABLE `Macroattivita` (
  `Codice` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Descrizione` varchar(500) NOT NULL,
  `Immagine` varchar(255) DEFAULT NULL,
  `Banner` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Macroattivita`
--

INSERT INTO `Macroattivita` (`Codice`, `Nome`, `Descrizione`, `Immagine`, `Banner`) VALUES
(1, 'Rafting', 'Entusiasmante discesa percorrendo rapide lunghe di acqua cristallina lungo un corso d&#39;acqua che conserva un ambiente naturale ancora intatto. E&#39; un buon inizio adatto anche a coloro che non hanno mai provato questo tipo di sport perché non manca proprio nulla : onde, rapide, rulli….pozze d&#39;acqua chiara in cui tuffarsi... Insomma siamo sicuri che non vi annoierete! Anche chi non è un abile nuotatore può partecipare basta volersi divertire e non essere allergici all&#39;acqua!', 'rafting.png', 'rafting-banner.png'),
(2, 'Canoa', 'Corsi per tutti i livelli, esperti, principianti e di specializzazione tenuti da Maestri di Canoa Federali (Federazione Italiana Canoa Kayak). Armonia, elasticit&agrave;, sensibilità, equilibrio…sono solo alcune delle caratteristiche che contraddistinguono lo sport della canoa... Preparatevi ad apprendere molto più del solo pagaiare! ', 'canoa.png', 'canoa-banner.png'),
(3, 'Hydrospeed', 'E&#39; la nuova disciplina che vi entusiasmerà!!! A bordo di una specie di bob da neve studiato apposta per affrontare la discesa fluviale e utilizzando le pinne come &#34;motore&#34;, vi divertirete completamente immersi nell&#39;acqua. Non è difficile, basta seguire le istruzioni che la Guida vi impartirà prima e durante l&#39;escursione. Imparerete così anche delle divertenti evoluzioni come il surf, le candele, l&#39;eskimo e... il resto è tutto da scoprire!!!a a\r a 2\r ', 'hydrospeed.jpg', 'hydro-banner.png');

-- --------------------------------------------------------

--
-- Table structure for table `Prenotazioni`
--

CREATE TABLE `Prenotazioni` (
  `Codice` int(11) NOT NULL,
  `IDAttivita` int(11) NOT NULL,
  `IDUtente` int(11) NOT NULL,
  `Giorno` date NOT NULL,
  `PostiPrenotati` int(10) UNSIGNED NOT NULL,
  `Stato` enum('Sospesa','Confermata','') NOT NULL DEFAULT 'Sospesa',
  `Pagamento` tinyint(1) NOT NULL DEFAULT '0',
  `Valutazione` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Prenotazioni`
--

INSERT INTO `Prenotazioni` (`Codice`, `IDAttivita`, `IDUtente`, `Giorno`, `PostiPrenotati`, `Stato`, `Pagamento`, `Valutazione`) VALUES
(23, 6, 5, '2018-09-21', 6, 'Sospesa', 1, 5),
(24, 4, 5, '2018-06-11', 4, 'Sospesa', 1, 1),
(35, 4, 5, '2018-06-29', 1, 'Sospesa', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Utenti`
--

CREATE TABLE `Utenti` (
  `ID` int(20) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Cognome` varchar(50) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(512) NOT NULL,
  `Indirizzo` varchar(50) DEFAULT NULL,
  `Civico` text,
  `Citta` varchar(255) DEFAULT NULL,
  `CAP` int(11) DEFAULT NULL,
  `Tipo` enum('Utente','Admin') DEFAULT 'Utente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Utenti`
--

INSERT INTO `Utenti` (`ID`, `Nome`, `Cognome`, `Username`, `Email`, `Password`, `Indirizzo`, `Civico`, `Citta`, `CAP`, `Tipo`) VALUES
(2, 'Admin', 'Admin', 'admin', 'mikitg.michele2@gmail.com', 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec', 'VIA MARCO LANDO', '6', 'Padova', 35133, 'Admin'),
(5, 'User', 'User', 'user', 'rikibek96@gmail.com', 'b14361404c078ffd549c03db443c3fede2f3e534d73f78f77301ed97d4a436a9fd9db05ee8b325c0ad36438b43fec8510c204fc1c1edb21d0941c00e9e2c1ce2', 'Via A. Mantegna', '7', 'Albignasego', 35020, 'Utente');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Attivita`
--
ALTER TABLE `Attivita`
  ADD PRIMARY KEY (`Codice`),
  ADD KEY `Macro` (`Macro`);

--
-- Indexes for table `Disponibilita`
--
ALTER TABLE `Disponibilita`
  ADD PRIMARY KEY (`Giorno`);

--
-- Indexes for table `Macroattivita`
--
ALTER TABLE `Macroattivita`
  ADD PRIMARY KEY (`Codice`);

--
-- Indexes for table `Prenotazioni`
--
ALTER TABLE `Prenotazioni`
  ADD PRIMARY KEY (`Codice`),
  ADD KEY `Prenotazioni_ibfk_1` (`IDAttivita`),
  ADD KEY `Prenotazioni_ibfk_2` (`IDUtente`);

--
-- Indexes for table `Utenti`
--
ALTER TABLE `Utenti`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Attivita`
--
ALTER TABLE `Attivita`
  MODIFY `Codice` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `Macroattivita`
--
ALTER TABLE `Macroattivita`
  MODIFY `Codice` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Prenotazioni`
--
ALTER TABLE `Prenotazioni`
  MODIFY `Codice` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT for table `Utenti`
--
ALTER TABLE `Utenti`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Attivita`
--
ALTER TABLE `Attivita`
  ADD CONSTRAINT `Attivita_ibfk_1` FOREIGN KEY (`Macro`) REFERENCES `Macroattivita` (`Codice`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Prenotazioni`
--
ALTER TABLE `Prenotazioni`
  ADD CONSTRAINT `Prenotazioni_ibfk_1` FOREIGN KEY (`IDAttivita`) REFERENCES `Attivita` (`Codice`),
  ADD CONSTRAINT `Prenotazioni_ibfk_2` FOREIGN KEY (`IDUtente`) REFERENCES `Utenti` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
