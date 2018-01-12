<?php
define("PERCORSO_RELATIVO","");
require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
require_once "php/funzioni/funzioni_sicurezza.php";
require_once "php/funzioni/funzioni_attivita.php";

$activeIndex = INF;

if(!isUtenteLoggato()) {
    paginaErrore("Per poter visualizzare questa pagina devi prima effettuare il login","login.php","Vai al login");
    return;
}

if(!isset($_POST["attivita"]) || !isset($_POST["posti"]) || !isset($_POST["data"])) {
    paginaErrore("Parametri non validi", "attivita.php","Torna alla lista di attività");
    return;
}

/*Intestazione: indica la pagina attualmente attiva --> contattaci */
$HTML_INTESTAZIONE = intestazione($activeIndex);

$codiceAttivita = filter_var($_POST["attivita"],FILTER_SANITIZE_NUMBER_INT);
$posti = intval(filter_var($_POST["posti"],FILTER_SANITIZE_NUMBER_INT));
$data = filter_var($_POST["data"],FILTER_SANITIZE_STRING);

if(!dataFutura(implode("-",array_reverse(explode("/",$data))))) {
    paginaErrore("Le prenotazioni per tale data sono chiuse. Selezionare una data futura.","attivita.php","Torna indietro");
    return;
}

$attivita = getAttivitaByCodice($codiceAttivita);

$totale = doubleval($attivita["Prezzo"]) * intval($posti);

/*Richiamo pagina contatti*/
$HTML = file_get_contents("template/conferma_prenotazione.html");

$HTML = str_replace("[#NOME-ATTIVITA]",$attivita["Nome"], $HTML);
$HTML = str_replace("[#DATA-PRENOTAZIONE]",$data, $HTML);

$HTML = str_replace("[#PREZZO-ATTIVITA]",$attivita["Prezzo"], $HTML);
$HTML = str_replace("[#POSTI-PRENOTAZIONE]",$posti, $HTML);
$HTML = str_replace("[#TOTALE-PRENOTAZIONE]",number_format($totale,2), $HTML);

$HTML = str_replace("[#ATTIVITA-PRENOTAZIONE]",$attivita["Codice"], $HTML);


/*Rimpiazza il segnaposto con il menù*/
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

/*Footer*/
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex),$HTML);
echo $HTML;

?>