<?php
session_start();

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";

$db->beginTransaction();

$idAttivita = $_POST["idAttivita"];
$idAttivita = str_replace("attivita-",'',$idAttivita);
$nomeAttivita = $_POST["nome-attivita"];
$descrizione = $_POST["descrizione"];
$prezzo = $_POST["prezzo"];

if(isAdmin()){
    $queryModifica = $db->prepare("UPDATE Attivita SET Nome = ?, Descrizione = ?, Prezzo = ? WHERE Codice = ?");
    if($queryModifica->execute(array($nomeAttivita,$descrizione,$prezzo,$idAttivita))) {
        $db->commit();
        successoJSON("Attività modificata con successo.");
    }
    else {
        $db->rollBack();
        erroreJSON("Errore nella modifica dell'attività.");
    }
}
else{
    erroreJSON("Non è stato possibile modificare l'attività.");
}