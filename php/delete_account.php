<?php
session_start();
require_once "database.php";
require_once "funzioni/funzioni_json.php";

$db->beginTransaction();

$idUtente  = $_SESSION["Utente"]["ID"];
$tipoUtente = $_SESSION["Utente"]["Tipo"];

if(isset($_POST["IDUtente"])) {
    $idUtenteDaEliminare = $_POST["IDUtente"];
    //se è stato passato l'id dell'utente da eliminare allora è la richiesta è arrivata dalla pagina admin, in ogni caso
    //si fa un controllo per essere sicuri che effettivamente sia effettivamente l'admin ad eliminare un account
    if($tipoUtente != 'Admin') {
        erroreJSON("Non è stato possibile eliminare l'account.");
        return;
    }
    //l'utente è l'admin quindi posso eliminare
    $query = $db->prepare("SELECT Codice FROM Prenotazioni  AND IDUtente = ?");
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
    $query = $db->prepare("SELECT Codice FROM Prenotazioni  AND IDUtente = ?");
    $query->execute(array($idUtente));
    if(!$query->fetch()) {
        eliminaAccountSenzaPrenotazioni($idUtente);
    }
    else {
        eliminaAccountConPrenotazioni($idUtente);
    }

}

function eliminaAccountSenzaPrenotazioni($idUtenteDaEliminare) {
    global $db;
    $queryDelete = $db->prepare("DELETE FROM Utenti WHERE IDUtente = ?");
    if ($queryDelete->execute(array($idUtenteDaEliminare))) {
        $db->commit();
        successoJSON("Account eliminato con successo");
        return;
    }
    $db->rollBack();
    erroreJSON("Errore nel processo di eliminazione dell'account.");
    return;
}


function eliminaAccountConPrenotazioni($idUtenteDaEliminare) {
    global $db;
    $queryDelete = $db->prepare("UPDATE Utenti SET Stato = 0 WHERE IDUtente = ?");
    if ($queryDelete->execute(array($idUtenteDaEliminare))) {
        $db->commit();
        successoJSON("Account eliminato con successo");
        return;
    }
    $db->rollBack();
    erroreJSON("Errore nel processo di eliminazione dell'account.");
    return;
}
