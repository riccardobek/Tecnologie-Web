<?php
session_start();
require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";



$db->beginTransaction();

$idUtente  = $_SESSION["Utente"]["ID"];

$idUtenteDaEliminare = $_POST["IDUtente"];


if($idUtenteDaEliminare != 0) {
    //se è stato passato l'id dell'utente (!= 0 ) da eliminare allora è la richiesta è arrivata dalla pagina admin, in ogni caso
    //si fa un controllo per essere sicuri che effettivamente sia effettivamente l'admin ad eliminare un account
    if(!(isAdmin())) {
        erroreJSON("Non è stato possibile eliminare l'account.");
        return;
    }
    //l'utente è l'admin quindi posso eliminare
    $query = $db->prepare("SELECT Codice FROM Prenotazioni  WHERE  IDUtente = ?");
    $query->execute(array($idUtenteDaEliminare));

    //se l'utente non ha prenotazioni allora il risultato della query è vuoto quindi si può eliminare l'account definitvamente
    if(!$query->fetch()) {
        eliminaAccountSenzaPrenotazioni($idUtenteDaEliminare);
    }
    else {
        eliminaAccountConPrenotazioni($idUtenteDaEliminare);
    }
}
else {
    //l'utente vuole eliminare il suo account.
    //Controllo se l'utente ha prenotazioni
    $query = $db->prepare("SELECT Codice FROM Prenotazioni  WHERE IDUtente = ?");
    $query->execute(array($idUtente));
    if(!$query->fetch()) {
        eliminaAccountSenzaPrenotazioni($idUtente);
    }
    else {
        eliminaAccountConPrenotazioni($idUtente);
    }

}

function eliminaAccountSenzaPrenotazioni($utenteDaEliminare) {
    global $db;
    $queryDelete = $db->prepare("DELETE FROM Utenti WHERE ID = ?");
    if ($queryDelete->execute(array($utenteDaEliminare))) {
        $db->commit();
        successoJSON("Account eliminato con successo");
        return;
    }
    $db->rollBack();
    erroreJSON("Errore nel processo di eliminazione dell'account.");
    return;
}


function eliminaAccountConPrenotazioni($utenteDaEliminare) {
    global $db;
    $queryDelete = $db->prepare("UPDATE Utenti SET Stato = 0 WHERE ID = ?");
    if ($queryDelete->execute(array($utenteDaEliminare))) {
        $db->commit();
        successoJSON("Account eliminato con successo");
        return;
    }
    $db->rollBack();
    erroreJSON("Errore durante il processo di eliminazione dell'account.");
    return;
}
