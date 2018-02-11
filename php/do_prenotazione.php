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

$nPosti = abs(filter_var($_POST["posti"],FILTER_SANITIZE_NUMBER_INT));
$utente = $_SESSION["Utente"]["ID"];
$attivita = filter_var($_POST["attivita"],FILTER_SANITIZE_NUMBER_INT);

if ($nPosti > getNumeroPostiDisponibili($nPosti)) {
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


