<?php
require_once "database.php";

/*
 * Bisogna prendere i dati in input dal form di registrazione e controllare la loro correttezza.
 * In caso positivo si procede all'inserimento del DB
 * In caso negativo si rigetta con una classe JSON
 */

$data = $_POST["data"];
$nPosti = $_POST["posti"];
$utente = $_SESSION["utente"]["ID"];
$attivita = $_POST["attivita"];
$postiDefault = 50;

$PostiDisponibiliGiornata = $db->prepare("SELECT PostiDisponibili FROM Disponibilita WHERE Attivita = ? AND Giorno = ?");
$PostiDisponibiliGiornata->execute(array($attivita,$data));

$PostiPrenotati = $db->prepare("SELECT SUM(PostiPrenotati) FROM Prenotazioni WHERE Attivita = ? AND Giorno = ?");
$PostiPrenotati->execute(array($attivita, $data));


if($PostiDisponibiliGiornata.rowCount() == 0 ){
    $PostiDisponibiliGiornata = $postiDefault;
}

$PostiDisponibiliEffettivi = $PostiDisponibiliGiornata - $PostiPrenotati;

if($nPosti > $PostiDisponibiliEffettivi){
    prenotazioneFailure();
    return;
}
//allora i posti disponibili sono stati modificati ed eseguo il controllo
else{
    prenotazioneSuccess();
    $db->beginTransaction();
    $insertStatement = $db->prepare("INSERT INTO Prenotazioni VALUES(?,?,?,?)");
    if($insertStatement->execute($attivita,$utente,$data,$nPosti))
        $db->commit();
    else{
        $db->rollBack();
        print($insertStatement->errorInfo());
    }
}

function prenotazioneFailure() {
    $jsonArray = array();
    $jsonArray["state"] = 0;
    $jsonArray["message"] = "Numero posti inserti maggiore del numero posti disponibili";
    echo json_encoce($jsonArray);
}

function prenotazioneSuccess(){
    $jsonArray = array();
    $jsonArray["state"] = 1;
    $jsonArray["message"] = "Dati inseriti validi";
    echo json_encoce($jsonArray);
}
?>


