<?php
session_start();
require_once "database.php";

/*
 * Bisogna prendere i dati in input dal form di registrazione e controllare la loro correttezza.
 * In caso positivo si procede all'inserimento del DB
 * In caso negativo si rigetta con una classe JSON
 */

$data = $_POST["data"];
$nPosti = $_POST["posti"];
$utente = $_SESSION["Utente"]["ID"];
$attivita = $_POST["attivita"];

$postiDefault = 50;

$PostiDisponibiliGiornata = $db->prepare("SELECT PostiDisponibili FROM Disponibilita WHERE Attivita = ? AND Giorno = ?");
$PostiDisponibiliGiornata->execute(array($attivita,$data));

$PostiPrenotati = $db->prepare("SELECT SUM(PostiPrenotati) AS PostiOccupati FROM Prenotazioni WHERE Attivita = ? AND Giorno = ?");
$PostiPrenotati->execute(array($attivita, $data));


($PostiDisponibiliGiornata->rowCount() == 0 )
    ?
    $PostiDisponibiliGiornata = $postiDefault
    :
    $PostiDisponibiliGiornata = $PostiDisponibiliGiornata->fetch()["PostiDisponibili"];


$PostiDisponibiliEffettivi = intval($PostiDisponibiliGiornata) - intval($PostiPrenotati->fetch()["PostiOccupati"]);

if($nPosti > $PostiDisponibiliEffettivi){
    prenotazioneFailure();
    return;
}
//allora i posti disponibili sono stati modificati ed eseguo il controllo
else{
    $db->beginTransaction();
    $insertStatement = $db->prepare("INSERT INTO Prenotazioni VALUES(?,?,?,?)");
    if($insertStatement->execute(array($attivita,$utente,$data,$nPosti))) {
        $db->commit();
        prenotazioneSuccess();
    }
    else{
        $db->rollBack();
        print_r($insertStatement->errorInfo());
        prenotazioneFailure();
    }
}

function prenotazioneFailure() {
    $jsonArray = array();
    $jsonArray["state"] = 0;
    $jsonArray["message"] = "Numero posti inserti maggiore del numero posti disponibili";
    echo json_encode($jsonArray);
}

function prenotazioneSuccess(){
    $jsonArray = array();
    $jsonArray["state"] = 1;
    $jsonArray["message"] = "Prenotazione inserita";
    echo json_encode($jsonArray);
}
?>


