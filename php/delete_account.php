<?php
session_start();
require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";



$db->beginTransaction();

$idUtente  = $_SESSION["Utente"]["ID"];

$idUtenteDaEliminare = abs(filter_var($_POST["IDUtente"], FILTER_SANITIZE_NUMBER_INT));


if($idUtenteDaEliminare != 0) {
    //se è stato passato l'id dell'utente (!= 0 ) da eliminare allora è la richiesta è arrivata dalla pagina admin, in ogni caso
    //si fa un controllo per essere sicuri che effettivamente sia effettivamente l'admin ad eliminare un account
    if (!(isAdmin())) {
        erroreJSON("Non è stato possibile eliminare l'account.");
        return;
    }
    eliminaAccount($idUtenteDaEliminare);
}
else {
    eliminaAccount($idUtente);
}

function eliminaAccount($utenteDaEliminare) {
    global $db;

    $query = $db->prepare("SELECT Codice FROM Prenotazioni  WHERE IDUtente = ?");
    $query->execute(array($utenteDaEliminare));
    $errore = false;
    $risQuery=$query->fetchAll();
    foreach ($risQuery as $attivita) {
        if(!eliminaAttivita($attivita["Codice"],false)) {
            $errore = true;
            break;
        }
    }
    if($errore) {
        $db->rollBack();
        erroreJSON("Errore durante l'eliminazione dell'account");
        return;
    }

    $queryDelete = $db->prepare("DELETE FROM Utenti WHERE ID = ?");

    if ($queryDelete->execute(array($utenteDaEliminare))) {
        $db->commit();
        successoJSON("Account eliminato con successo");
        return;
    }
    else {
        $db->rollBack();
        erroreJSON("Errore nel processo di eliminazione dell'account.");
        return;
    }

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
