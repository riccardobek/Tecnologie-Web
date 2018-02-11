<?php
define("PERCORSO_RELATIVO","");

require_once "php/funzioni/funzioni_pagina.php";
require_once "php/database.php";


$activeIndex = 1;

/*Intestazione: indica la pagina attualmente attiva --> attività */
$HTML_INTESTAZIONE = intestazione($activeIndex);

/*Richiamo pagina contatti*/
$HTML = file_get_contents("template/attivita/attivita.html");

/*Rimpiazza il segnaposto con il menù*/
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

$HTML = str_replace( "[#ATTIVITA]",stampaAttivita(), $HTML);

/*Footer*/
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex),$HTML);

echo $HTML;


function stampaAttivita() {
    $class = array("odd","even");
    $classIndex = false;
    $listaMacroAttivita = getMacroattivita();

    $output = "";
    $data = (new DateTime())->add(new DateInterval("P1D"))->format('d/m/Y');;

    foreach($listaMacroAttivita as $macro) {
        $output.= file_get_contents("template/attivita/sezione_macroattivita.html");
        $output = str_replace("[#CLASS_MACROATTIVITA]",$class[$classIndex], $output);
        $output = str_replace("[#ID_MACROATTIVITA]",$macro["Ancora"], $output);
        $output = str_replace("[#MACRO_BANNER]",$macro["Banner"], $output);
        $output = str_replace("[#MACRO_NOME]",$macro["Nome"], $output);
        $output = str_replace("[#MACRO_DESCRIZIONE]",$macro["Descrizione"], $output);

        $sottoattivita = "";
        foreach($macro["Attivita"] as $attivita) {
            $sottoattivita  .= file_get_contents("template/attivita/sezione_attivita.html");
            $sottoattivita  = str_replace("[#NOME-SOTTOATTIVITA]", $attivita["Nome"], $sottoattivita );
            $sottoattivita  = str_replace("[#DESCRIZIONE-SOTTOATTIVITA]", $attivita["Descrizione"], $sottoattivita );
            $sottoattivita  = str_replace("[#CODICE-SOTTOATTIVITA]", $attivita["Codice"], $sottoattivita );
            $sottoattivita  = str_replace("[#PREZZO-SOTTOATTIVITA]", $attivita["Prezzo"], $sottoattivita );
            $sottoattivita  = str_replace("[#DATA]", $data, $sottoattivita );

            /**
             * Viene visualizzato il pulsante "Prenota" se si è loggati, altrimenti viene visualizzato uno span con
             * un messaggio. Un utente non registrato e/o non loggato non può effettuare prenotazioni
             */
            if(isUtenteLoggato() && !isAdmin()) {
                $sottoattivita = str_replace("[#ANCORA-GESTISCI-ATTIVITA]",' ',$sottoattivita);
                $sottoattivita = str_replace("[#BTN-LOGGATO]","button",$sottoattivita);
                $sottoattivita = str_replace("[#TESTO-PULSANTE]","Voglio prenotare",$sottoattivita);
                $sottoattivita = str_replace("[#HREF]", "",$sottoattivita);
            }
            elseif(isAdmin()) {
                $sottoattivita = str_replace("[#BTN-LOGGATO]","a",$sottoattivita);
                $sottoattivita = str_replace("[#TESTO-PULSANTE]","Gestisci",$sottoattivita);
                $sottoattivita = str_replace("[#HREF]", "href='pannello_admin.php#attivita-{$attivita["Codice"]}'",$sottoattivita);
            }
            else{
                $sottoattivita = str_replace("[#ANCORA-GESTISCI-ATTIVITA]",' ',$sottoattivita);
                $sottoattivita = str_replace("[#BTN-LOGGATO]","span",$sottoattivita);
                $sottoattivita = str_replace("[#TESTO-PULSANTE]","Effettua il <a href = 'login.php'>login</a> o <a href='registrazione.php'>registrati</a>  per poter prenotare",$sottoattivita);
                $sottoattivita = str_replace("[#HREF]", "",$sottoattivita);
            }
        }

        $output = str_replace("[#SOTTOATTIVITA]", $sottoattivita, $output);
        $classIndex = !$classIndex;
    }

    return $output;
}


