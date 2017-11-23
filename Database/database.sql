DROP DATABASE IF EXISTS progetto;
CREATE DATABASE progetto;

USE progetto;


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
email VARCHAR(50) NOT NULL,
Password VARCHAR(20) NOT NULL,
Indirizzo VARCHAR(50) NOT NULL,
Tipo ENUM('Utente','Admin') DEFAULT 'Utente'
);


CREATE TABLE Attivita(
Codice VARCHAR(20) PRIMARY KEY,
Nome VARCHAR(50) NOT NULL,
Descrizione VARCHAR(500) NOT NULL,
Prezzo DECIMAL(8,2) NOT NULL,
Amministratore VARCHAR(20) REFERENCES Utenti(ID)
);


CREATE TABLE Commenti(
Attivita VARCHAR(20),
Utente VARCHAR(50),
Giorno DATE,
Testo VARCHAR(500) NOT NULL,
PRIMARY KEY(Attivita,Utente,Giorno),
FOREIGN KEY (Attivita) REFERENCES Attivita(Codice),
FOREIGN KEY (Utente) REFERENCES Utenti(ID) 
);


CREATE TABLE Prenotazioni(
Attivita VARCHAR(20),
Utente VARCHAR(50),
Giorno DATE,
PostiPrenotati INTEGER NOT NULL,
PRIMARY KEY(Attivita,Utente,Giorno),
FOREIGN KEY (Attivita) REFERENCES Attivita(Codice),
FOREIGN KEY (Utente) REFERENCES Utenti(ID) 
);


CREATE TABLE Disponibilita(
Attivita VARCHAR(20),
Giorno DATE,
PostiDisponibili INTEGER NOT NULL,
Amministratore VARCHAR(20) NOT NULL,
PRIMARY KEY(Attivita,Giorno),
FOREIGN KEY (Attivita) REFERENCES Attivita(Codice), 
FOREIGN KEY (Amministratore) REFERENCES Utenti(ID)
);















