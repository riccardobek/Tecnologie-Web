<?php
require_once "database.php";
require_once "funzioni/funzioni_pagina.php";
$db->beginTransaction();



$idutente  = $_SESSION["utente"]["ID"];


$deleteStatement = $db->prepare("DELETE FROM Prenotazioni WHERE IDAttivita = ? AND IDUtente = ? AND Giorno = ?");
if($deleteStatement->execute(array($idattivita, $idutente, $data)))
    $db->commit();
else{
    $db->rollBack();
}
?>