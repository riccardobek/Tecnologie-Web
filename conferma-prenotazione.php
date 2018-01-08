<?php
require_once "php/funzioni/funzioni_pagina.php";
$activeIndex = 2;

if(!isUtenteLoggato()) {
    paginaErrore("Per poter visualizzare questa pagina devi prima effettuare il login","login.php","Vai al login");
    return;
}

if(!isset($_POST["attivita"]) || !isset($_POST["posti"]) || !isset($_POST["data"])) {
    paginaErrore("Parametri non validi", "attivita.php","Torna alla lista di attività");
    return;
}

else {
    /*Intestazione: indica la pagina attualmente attiva --> contattaci */
    $HTML_INTESTAZIONE = intestazione($activeIndex);

    $attivita = filter_var($_POST["attivita"],FILTER_SANITIZE_NUMBER_INT);
    $posti = filter_var($_POST["posti"],FILTER_SANITIZE_NUMBER_INT);
    $data = filter_var($_POST["data"],FILTER_SANITIZE_STRING);

    /*Richiamo pagina contatti*/
    $HTML = file_get_contents("template/conferma-prenotazione.html");

    $HTML = str_replace("[#ATTIVITA-PRENOTAZIONE]",$attivita, $HTML);
    $HTML = str_replace("[#DATA-PRENOTAZIONE]",$data, $HTML);
    $HTML = str_replace("[#POSTI-PRENOTAZIONE]",$posti, $HTML);

}

/*Rimpiazza il segnaposto con il menù*/
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

/*Footer*/
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex),$HTML);
echo $HTML;

?>