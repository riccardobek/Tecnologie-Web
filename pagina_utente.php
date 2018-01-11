<?php
ini_set('display_errors', "On");
error_reporting(E_ALL);

require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";

$activeIndex = 0;

//Intestazione: indica la pagina attualmente attiva  contattaci
$HTML_INTESTAZIONE = intestazione($activeIndex);

//Richiamo pagina contatti
$HTML = file_get_contents("template/utente/pagina_utente.html");

//Rimpiazza il segnaposto con il menù
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

$HTML = str_replace("[#PRENOTAZIONI]",prenotazioniUtente(), $HTML);

//Footer
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex),$HTML);
echo $HTML;


function prenotazioniUtente() {
    global $db;

    $infoPrenotazioni = $db->prepare("SELECT  Attivita.Nome AS Nome, Prenotazioni.Giorno AS Giorno, Prenotazioni.PostiPrenotati AS Posti FROM Prenotazioni, Attivita WHERE Prenotazioni.IDUtente = ? AND Prenotazioni.IDAttivita = Attivita.Codice");
    $infoPrenotazioni->execute(array($_SESSION["Utente"]["ID"]));
    $prenotazioni = $infoPrenotazioni->fetchAll();

    $riga2="";

    foreach($prenotazioni as $prenotazione) {

        $riga2 .= <<<RIGA
    <tr><td>{$prenotazione["Nome"]}</td><td>{$prenotazione["Giorno"]}</td><td>{$prenotazione["Posti"]}</td></tr>
RIGA;
    }
    return $riga2;
}

?>
