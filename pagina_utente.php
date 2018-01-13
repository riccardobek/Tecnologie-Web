<?php

ini_set('display_errors', "On");
error_reporting(E_ALL);


define("PERCORSO_RELATIVO","");

require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";

loginRichiesto();

$activeIndex = 0;

//Intestazione: indica la pagina attualmente attiva  contattaci
$HTML_INTESTAZIONE = intestazione($activeIndex);



//Richiamo pagina contatti
$HTML = file_get_contents("template/utente/pagina_utente.html");

//Rimpiazza il segnaposto con il menù
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

$HTML = str_replace("[#SCHEDE]",stampaSchedePrenotazioniAttive(), $HTML);

//$HTML = str_replace("[#PRENOTAZIONI]",prenotazioniAttive(), $HTML);

$HTML = str_replace("[#STORICO-PRENOTAZIONI]",storicoPrenotazioni(), $HTML);

//Footer
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex),$HTML);
echo $HTML;


function prenotazioniAttive() {
    global $db;

    $infoPrenotazioni = $db->prepare("SELECT  Attivita.Nome AS Nome, Prenotazioni.Giorno AS Giorno, Prenotazioni.PostiPrenotati AS Posti, Pagamento FROM Prenotazioni, Attivita WHERE Prenotazioni.IDUtente = ? AND Prenotazioni.IDAttivita = Attivita.Codice AND Prenotazioni.Stato = 'Sospesa' AND Prenotazioni.Giorno >= (SELECT CURDATE() ) ");
    $infoPrenotazioni->execute(array($_SESSION["Utente"]["ID"]));
    $prenotazioni = $infoPrenotazioni->fetchAll();

    foreach($prenotazioni as &$prenotazione) {
        if($prenotazione["Pagamento"]==0){
            $prenotazione["Pagamento"] = 'Non pagato';
        }
        else{
            $prenotazione["Pagamento"] = 'Pagato';
        }
    }
    return $prenotazioni;
}

function stampaSchedePrenotazioniAttive(){
    $listaPrenotazioniAttive = prenotazioniAttive();

    $output = "";

    foreach ($listaPrenotazioniAttive as $prenotazione){
        $data = convertiDataToOutput($prenotazione["Giorno"]);
        $output .= file_get_contents("template/utente/schede_prenotazioni.html");
        $output = str_replace("[#NOME-ATTIVITA]", $prenotazione["Nome"], $output );
        $output = str_replace("[#GIORNO]", $data, $output );
        $output = str_replace("[#POSTI]", $prenotazione["Posti"], $output );
        $output = str_replace("[#PAGAMENTO]", $prenotazione["Pagamento"], $output );
    }
    return $output;
}



function storicoPrenotazioni() {
    global $db;

    $infoPrenotazioni = $db->prepare("SELECT  Attivita.Nome AS Nome, Prenotazioni.Giorno AS Giorno, Prenotazioni.PostiPrenotati AS Posti, Prenotazioni.Stato AS Stato  FROM Prenotazioni, Attivita WHERE Prenotazioni.IDUtente = ? AND Prenotazioni.IDAttivita = Attivita.Codice AND (Prenotazioni.Stato='Confermata' OR Prenotazioni.Stato='Cancellata' OR (Prenotazioni.Stato='Sospesa' AND Prenotazioni.Giorno < (SELECT CURDATE()) ) )");
    $infoPrenotazioni->execute(array($_SESSION["Utente"]["ID"]));
    $prenotazioni = $infoPrenotazioni->fetchAll();

    $riga2="";

    foreach($prenotazioni as $prenotazione) {
        $data = convertiDataToOutput($prenotazione["Giorno"]);
        $riga2 .= <<<RIGA
<tr><td>{$prenotazione["Nome"]}</td><td>{$data}</td><td>{$prenotazione["Posti"]}</td><td>{$prenotazione["Stato"]}</td></tr>
RIGA;
    }
    return $riga2;
}

?>
