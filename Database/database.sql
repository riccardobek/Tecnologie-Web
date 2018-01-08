DROP TABLE IF EXISTS Disponibilita;
DROP TABLE IF EXISTS Prenotazioni;
DROP TABLE IF EXISTS Commenti;
DROP TABLE IF EXISTS Attivita;
DROP TABLE IF EXISTS Utenti;


CREATE TABLE Utenti(
ID VARCHAR(20) PRIMARY KEY,
Nome VARCHAR(50) NOT NULL,
Cognome VARCHAR(50) NOT NULL,
Username VARCHAR(20) NOT NULL UNIQUE,
Email VARCHAR(50) NOT NULL UNIQUE,
Password VARCHAR(20) NOT NULL,
Indirizzo VARCHAR(50) NOT NULL,
Tipo ENUM('Utente','Admin') DEFAULT 'Utente'
);

CREATE TABLE Macroattivita(
Codice INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
Nome VARCHAR(50) NOT NULL,
Descrizione VARCHAR(500) NOT NULL,
Immagine VARCHAR(255)
);

CREATE TABLE Attivita(
Codice INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
Macro INT NOT NULL,
Nome VARCHAR(50) NOT NULL,
Descrizione VARCHAR(500) NOT NULL,
Prezzo DECIMAL(8,2) NOT NULL,

FOREIGN KEY(Macro) REFERENCES Macroattivita(Codice)
ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE Commenti(
Attivita VARCHAR(20),
Utente VARCHAR(50),
Giorno DATE,
Testo VARCHAR(500) NOT NULL,
PRIMARY KEY(Attivita,Utente,Giorno),
FOREIGN KEY (Attivita) REFERENCES Attivita(Codice),
FOREIGN KEY (Utente) REFERENCES Utenti(ID) 
);


CREATE TABLE `Prenotazioni` (
  Codice int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `IDAttivita` int(11) NOT NULL,
  `IDUtente` int(11) NOT NULL,
  `Giorno` date NOT NULL,
  `PostiPrenotati` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Limiti per la tabella `Prenotazioni`
--
ALTER TABLE `Prenotazioni`
  ADD CONSTRAINT `Prenotazioni_ibfk_1` FOREIGN KEY (`IDAttivita`) REFERENCES `Attivita` (`Codice`),
  ADD CONSTRAINT `Prenotazioni_ibfk_2` FOREIGN KEY (`IDUtente`) REFERENCES `Utenti` (`ID`);



CREATE TABLE Disponibilita(
Attivita VARCHAR(20),
Giorno DATE,
PostiDisponibili INTEGER NOT NULL,
PRIMARY KEY(Attivita,Giorno),
FOREIGN KEY (Attivita) REFERENCES Attivita(Codice), 
);















