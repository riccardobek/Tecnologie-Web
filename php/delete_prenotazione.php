<?php
session_start();
require_once "database.php";
require_once "funzioni/funzioni_json.php";


$db->beginTransaction();

$idutente  = $_SESSION["Utente"]["ID"];
$idPrenotazione = filter_var($_POST["idPrenotazione"],FILTER_SANITIZE_NUMBER_INT);

$queryControllo = $db->prepare("SELECT Codice FROM Prenotazioni WHERE Codice = ? AND IDUtente = ?");
$queryControllo->execute(array($idPrenotazione, $idutente));

if(!$queryControllo->fetch()) {
    erroreJSON("Prenotazione non trovata.",$queryControllo->errorInfo());
    return;
}

$deleteStatement = $db->prepare("UPDATE Prenotazioni SET Stato = 'Cancellata' WHERE Codice = ?");
if($deleteStatement->execute(array($idPrenotazione))){
    $db->commit();
    successoJSON("Prenotazione eliminata con successo.");
    return;
}
$db->rollBack();
erroreJSON("Non è stato possibile eliminare la prenotazione.");
?>