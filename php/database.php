<?php
require_once "impostazioni.inc.php";

try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    print "ERRORE DI CONNESSIONE AL DATABASE:" . $e->getMessage() . "<br/>";
    die();
}

/**
 * Funzioni inerenti al formato dei dati presenti nel database
 */

/**
 * Funzione che converte una data da DD/MM/YYYY a YYYY/MM/DD (formato database)
 * @param $dataDaConvertire la stringa che contiene la data in DD/MM/YYYY da convertire
 * @return bool|string false se la stringa in input non è nel formato corretto, altrimenti una stringa contenente la data
 * nel formato database
 */
function convertiData($dataDaConvertire) {
    //Se l'input non è coinforme a quello che mi aspetto ritorno false
    if(!preg_match("/^(\d{2})\/(\d{2})\/(\d{4})$/",$dataDaConvertire))
        return false;

    $matches = explode("/",$dataDaConvertire);
    $dataCalcolata = new DateTime(intval($matches[2])."-".intval($matches[1])."-".intval($matches[0]));

    //Se la data è nel formato corretto ma non è valida (ad esempio 31/02/2018) ritorno false
    if($dataCalcolata->format("d/m/Y") != $dataDaConvertire)
        return false;

    //Converto la data dal formato dd/mm/yyyy al formato yyyy-mm-dd (accettato da mysql)
    return $dataCalcolata->format("Y-m-d");
}

/**
 * Funzione che converte la data da formato database a formato output
 * @param $dataDaConvertire
 * @return string la data nel formato output
 */
function convertiDataToOutput($dataDaConvertire){
    return (new DateTime($dataDaConvertire))->format('d/m/Y');
}
/**
 * Funzioni inerenti alle attivita
 */

function getMacroattivita() {
    global $db;
    $query = $db->prepare("SELECT Codice, Nome, Descrizione, Immagine, Banner, REPLACE(LOWER(`Nome`), ' ', '-') AS Ancora FROM Macroattivita");
    $query->execute();

    $listaMacro = $query->fetchAll();
    for($i=0; $i<count($listaMacro);$i++) {
        $listaMacro[$i]["Attivita"] = getAttivita($listaMacro[$i]["Codice"]);
    }

    return $listaMacro;
}

function getAttivita($codiceMacro) {
    global $db;
    $query = $db->prepare("SELECT * FROM Attivita WHERE Macro = ?");
    $query->execute(array($codiceMacro));

    $listaAttivita = $query->fetchAll();
    return $listaAttivita;
}

function getAttivitaByCodice($codiceAttivita) {
    global $db;
    $query = $db->prepare("SELECT * FROM Attivita WHERE Codice = ?");
    $query->execute(array($codiceAttivita));

    $attivita = $query->fetch();
    return $attivita;
}

function getMacroattivitaByCodice($codiceMacro) {
    global $db;
    $query = $db->prepare("SELECT Nome, Descrizione, Immagine, Banner, REPLACE(LOWER(`Nome`), ' ', '-') AS Ancora FROM Macroattivita WHERE Codice = ?");
    $query->execute(array($codiceMacro));

    $infoMacro = $query->fetch();
    return $infoMacro;
}

/**
 * Funzioni inerenti alle prenotazioni
 */
function getDettagliPrenotazione($codicePrenotazione) {
    global $db;

    $query = $db->prepare("SELECT Prenotazioni.IDUtente, Prenotazioni.Giorno AS Data, Prenotazioni.PostiPrenotati as Posti, 
          Attivita.Nome AS NomeAttivita, Attivita.Prezzo AS Prezzo, Utenti.Nome AS Nome, Utenti.Cognome AS Cognome,
          Utenti.ID AS IDUtente
          FROM Attivita JOIN (Utenti JOIN Prenotazioni ON Utenti.ID = Prenotazioni.IDUtente)
            ON Attivita.Codice = Prenotazioni.IDAttivita WHERE Prenotazioni.Codice = ?");

    $query->execute(array($codicePrenotazione));
    return $query->fetch();
}

/**
 * Funzione che convalida la prenotazione passata come parametro
 * @param $codicePrenotazione il codice prenotazione nel formato contenuto nel qr
 */
function convalidaPrenotazione($codicePrenotazione) {
    global $db;

    try {
        $partiCodicePrenotazione = explode("-",$codicePrenotazione);
        if(count($partiCodicePrenotazione) != 3) {
            throw new Exception("Codice prenotazione non valido");
        }
        /*
         * Il codice prenotazione è formato da 3 parti:
         * 1) La stringa "pr"
         * 2) L'id della prenotazione
         * 3) L'id dell'utente che ha compiuto quella prenotazione (per far si che uno non falsifichi il codice attribuendosi
         * prenotazioni non sue
         */
        $prenotazione = getDettagliPrenotazione($partiCodicePrenotazione[1]);
        if(!$prenotazione)
            throw new Exception("Prenotazione non trovata");

        if(intval($prenotazione["IDUtente"]) != intval($partiCodicePrenotazione[2]))
            throw new Exception("Prenotazione non corrispondente all'ID utente segnato");

        $db->beginTransaction();
        $updateQ = $db->prepare("UPDATE Prenotazioni SET Stato = 'Confermata' WHERE Codice = ?");
        if($updateQ->execute(array($partiCodicePrenotazione[1]))) {
            $db->commit();
            successoJSON("Prenotazione convalidata con successo");
            return;
        }
        $db->rollBack();
        throw new Exception("Errore nell'update");
    }
    catch(Exception $e) {
        erroreJSON("Codice prenotazione non valido");
    }
}


define("POSTI_DISPONIBILI_DEFAULT",50);

/**
 * Funzione che restituisce il numero di posti ancora disponibili per una data attività in un dato giorno
 * @param $data (formato YYYY/MM/DD) la data di cui si vuole controllare la dispoonibilità
 * @return int il numero di posti residui in quella determinata data
 */
function getNumeroPostiDisponibili($data) {
    global $db;

    $capienzaGiornalieraQuery = $db->prepare("SELECT PostiDisponibili FROM Disponibilita WHERE Giorno = ?");
    $capienzaGiornalieraQuery->execute(array($data));

    $postiPrenotati = $db->prepare("SELECT SUM(PostiPrenotati) AS PostiOccupati FROM Prenotazioni WHERE Giorno = ?");
    $postiPrenotati->execute(array($data));


    $capienzaTotale = ($capienzaGiornalieraQuery->rowCount() == 0 ) ?
        POSTI_DISPONIBILI_DEFAULT :
        intval($capienzaGiornalieraQuery->fetch()["PostiDisponibili"]);


    return $capienzaTotale - intval($postiPrenotati->fetch()["PostiOccupati"]);
}

/**
 * La funzione per eliminare l'attività elimina anche le prenotazioni relative a quell'attività
 * @param $idAttivita ID dell'attività da eliminare
 */
function eliminaAttivita($idAttivita,$commit = true) {
    global $db;
    if($commit) $db->beginTransaction();

    $queryDeletePrenotazioni = $db->prepare("DELETE FROM Prenotazioni WHERE IDAttivita = ?");
    if($queryDeletePrenotazioni->execute(array($idAttivita))) {
        $queryDeleteAttivita =  $db->prepare("DELETE FROM Attivita WHERE Codice = ?");
        if($queryDeleteAttivita->execute(array($idAttivita))) {
            if($commit) {
                $db->commit();
                successoJSON("Attività eliminata con successo.");
            }

            return true;
        }
        $db->rollBack();
        if($commit)
            erroreJSON("Non è stato possibile eliminare l'attività.");

        return false;
    }
    else {
        $db->rollBack();
        if($commit)
            erroreJSON("Non è stato possibile eliminare l'attività.");

        return false;
    }
}
?>