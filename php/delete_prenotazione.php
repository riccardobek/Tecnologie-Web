<?php
session_start();
require_once "database.php";
require_once "funzioni/funzioni_json.php";


$db->beginTransaction();
$idUtente  = $_SESSION["Utente"]["ID"];
$tipoUtente = $_SESSION["Utente"]["Tipo"];

$idPrenotazione = filter_var($_POST["idPrenotazione"],FILTER_SANITIZE_NUMBER_INT);

if($tipoUtente != 'Admin') {
    //l'utente non è admin quindi non puo' cancellare prenotazioni non sue
    $queryControllo = $db->prepare("SELECT Codice FROM Prenotazioni WHERE Codice = ? AND IDUtente = ?");
    $queryControllo->execute(array($idPrenotazione, $idUtente));
    if(!$queryControllo->fetch()) {
        erroreJSON("Prenotazione non trovata.",$queryControllo->errorInfo());
        return;
    }
}

//L'utente o è admin oppure è un utente che vuole cancellare la sua prenotazione
$deleteStatement = $db->prepare("DELETE FROM Prenotazioni WHERE Codice = ?");

if($deleteStatement->execute(array($idPrenotazione))){
    $db->commit();
    successoJSON("Prenotazione eliminata con successo.");
    return;
}
else{
    $db->rollBack();
    erroreJSON("Non è stato possibile eliminare la prenotazione.");
}
?>