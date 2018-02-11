<?php
session_start();

define("PERCORSO_RELATIVO","../");

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";


$jsAbilitato = boolval(filter_var($_POST["JSAbilitato"],FILTER_SANITIZE_NUMBER_INT));

/*
 * Bisogna prendere i dati in input dal form di registrazione e controllare la loro correttezza.
 * In caso positivo si procede all'inserimento del DB
 * In caso negativo si rigetta ritornando una oggetto JSON
 */

//La data passata è nel formato DD/MM/YYYY, mentre la devo convertire nel formato YYYY/MM/DD
$data = convertiData(filter_var($_POST["data"],FILTER_SANITIZE_FULL_SPECIAL_CHARS));
if(!$data) {
    errore(0); //Data non valida
    return;
}

if(!dataFutura($data)) {
    errore(1); //Impossibile prenotare un'attività per tale data
    return;
}

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

if ($nPosti > $PostiDisponibiliEffettivi) {
    errore(2); //Numero posti inserti maggiore del numero posti disponibili
    return;
}

else {
    $db->beginTransaction();
    $insertStatement = $db->prepare("INSERT INTO Prenotazioni VALUES(NULL,?,?,?,?,'Sospesa','0',NULL)");
    if($insertStatement->execute(array($attivita,$utente,$data,$nPosti))) {
        $codicePrenotazione = $db->lastInsertId();
        $db->commit();

        if($jsAbilitato)
            successoJSON("Prenotazione inserita",array("CodicePrenotazione"=>$codicePrenotazione));
        else {
            header("Location: ../conferma_prenotazione.php?successoPrenotazione=".$codicePrenotazione);
        }
    }
    else{
        $db->rollBack();
        errore(3); //Errore nell'inserimento della prenotazione nel database
    }
}

function convertiData($dataDaConvertire) {
    //Se l'input non è coinforme a quello che mi aspetto ritorno false
    if(!preg_match("/^(\d{2})\/(\d{2})\/(\d{4})$/",$dataDaConvertire))
        return false;

    $matches = explode("/",$dataDaConvertire);
    $dataCalcolata = new DateTime(intval($matches[2])."-".intval($matches[1])."-".intval($matches[0]));

    //Se la data è nel formato corretto ma non è valida (ad esempio 31/02/2018) ritorno false
    if($dataCalcolata->format("d/m/Y") != $dataDaConvertire)
        return false;

    //Converto la data dal formato dd/mm/yyyy al formato yyyy-mm-dd (accettato da mysql)
    return $dataCalcolata->format("Y-m-d");
}

/**
 * Funzione che mostra un errore tenendo conto del fatto che la pagina sia stata caricata attraverso una richiesta AJAX o
 * no
 * @param $codice il codice dell'errore, indice dell'array ERRORI_INSERIMENTO_ATTIVITA definito in funzioni_sicurezza.php
 */
function errore($codice) {
    global $jsAbilitato;

    $jsAbilitato ? erroreJSON(ERRORI_INSERIMENTO_ATTIVITA[$codice]) : header("Location: ../conferma_prenotazione.php?errorePrenotazione=".$codice);
}
?>


