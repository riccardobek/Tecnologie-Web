<?php
define("PERCORSO_RELATIVO","");
require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";


loginRichiesto();

$activeIndex = INF;

if(isset($_GET["successoPrenotazione"])) {
    //Visualizzo messaggio di successo (avvenuto inserimento della prenotazione).

    $codicePrenotazione = filter_var($_GET["successoPrenotazione"],FILTER_SANITIZE_NUMBER_INT);
    
    //Controllo che il codicePrenotazione passato come parametro corrisponda ad una prenotazione che ho effettuato io
    $queryControlloPrenotazione = $db->prepare("SELECT IDUtente FROM Prenotazioni WHERE Codice = ?");
    $queryControlloPrenotazione->execute(array($codicePrenotazione));
    $dettaglioPrenotazione = $queryControlloPrenotazione->fetch();

    if(!$dettaglioPrenotazione){
        paginaErrore("Prenotazione non trovata");
        return;
    }
    if($dettaglioPrenotazione["IDUtente"] != $_SESSION["Utente"]["ID"]) {
        paginaErrore("Non sei autorizzato a visualizzare i dettagli di questa prenotazione");
        return;
    }

    $linkAlternativo = array("HREF"=>"attivita.php","Messaggio"=>"Torna all'attivita");
    paginaSuccesso("Prenotazione inserita con successo!","pdf_prenotazione.php?codice=".$codicePrenotazione,"Clicca qui per scaricare la conferma prenotazione",true,$linkAlternativo);
    return;
}

elseif(isset($_GET["errorePrenotazione"])) {
    $codiceErrore = filter_var($_GET["errorePrenotazione"],FILTER_SANITIZE_NUMBER_INT);
    /*ERRORI_INSERIMENTO_ATTIVITA è un'array che, per ogni errore che si potrebbe verificare in do_prenotazione.php, associa
    il codice al relativo messaggio*/
    paginaErrore(ERRORI_INSERIMENTO_ATTIVITA[$codiceErrore],"attivita.php","Torna alla lista delle attivita");
    return;
}

if(!isset($_GET["attivita"]) || !isset($_GET["posti"]) || !isset($_GET["data"])) {
    paginaErrore("Parametri non validi", "attivita.php","Torna alla lista di attività");
    return;
}

/*Intestazione: indica la pagina attualmente attiva --> non definita */
$HTML_INTESTAZIONE = intestazione($activeIndex);

$codiceAttivita = filter_var($_GET["attivita"],FILTER_SANITIZE_NUMBER_INT);
$posti = intval(filter_var($_GET["posti"],FILTER_SANITIZE_NUMBER_INT));

$data = convertiData(filter_var($_GET["data"],FILTER_SANITIZE_STRING)); //Ritorna false se la data non è nel formato corretto
if(!$data) {
    paginaErrore("La data non è nel formato corretto. Riprova.","attivita.php","Torna indietro");
    return;
}

if(!dataFutura(implode("-",array_reverse(explode("/",$data))))) {
    paginaErrore("Le prenotazioni per tale data sono chiuse. Selezionare una data futura.","attivita.php","Torna indietro");
    return;
}

$postiDisponibili = getNumeroPostiDisponibili($data);
if($posti > $postiDisponibili) {
    paginaErrore("I posti che si vogliono prenotare sono maggiori della disponibilità (che è di {$postiDisponibili} posti)","attivita.php","Torna indietro");
    return;
}

$attivita = getAttivitaByCodice($codiceAttivita);

$totale = doubleval($attivita["Prezzo"]) * intval($posti);

/*Richiamo pagina contatti*/
$HTML = file_get_contents("template/attivita/conferma_prenotazione.html");

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

