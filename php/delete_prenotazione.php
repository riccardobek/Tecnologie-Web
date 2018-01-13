<?php
session_start();
require_once "database.php";
require_once "funzioni/funzioni_json.php";


$db->beginTransaction();

$idutente  = $_SESSION["Utente"]["ID"];
$idattivita = filter_var($_POST["idPrenotazione"],FILTER_SANITIZE_NUMBER_INT);

$queryControllo = $db->prepare("SELECT Codice FROM Prenotazioni WHERE Codice = ? AND IDUtente = ?");
$queryControllo->execute(array($idattivita, $idutente));

if(!$queryControllo->fetch()) {
    erroreJSON("Prenotazione non trovata.",$queryControllo->errorInfo());
    return;
}

$deleteStatement = $db->prepare("DELETE FROM Prenotazioni WHERE IDAttivita = ?");
if($deleteStatement->execute(array($idattivita))){
    $db->commit();
    successoJSON("Prenotazione eliminata con successo.");
    return;
}
$db->rollBack();
erroreJSON("Non è stato possibile eliminare la prenotazione.");
?>