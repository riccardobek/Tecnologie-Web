<?php
require_once "database.php";
$db->beginTransaction();


/*
 * Da finire -> bisogna creare la pagina utente dalla quale si può eliminare la prenotazione.
 */
$idutente  = $_SESSION["utente"]["ID"];


$deleteStatement = $db->prepare("DELETE FROM Prenotazioni WHERE IDAttivita = ? AND IDUtente = ? AND Giorno = ?");

if($deleteStatement->execute($idattivita, $idutente, $data))
    $db->commit();
else{
    $db->rollBack();
}
?>