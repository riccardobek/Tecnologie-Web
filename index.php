<?php
require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
require_once "php/funzioni/funzioni_attivita.php";
$activeIndex = 0;

$HTML_INTESTAZIONE = intestazione($activeIndex);

$HTML = file_get_contents("template/index/main.html");
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

/****** COLONNE ATTIVITA *****/
$listaMacroAttivita = getMacroattivita();

$HTML_MACROATTIVITA="";
foreach($listaMacroAttivita as $macroAttivita) {
    $HTML_MACROATTIVITA .= file_get_contents("template/index/colonna_attivita.html");

    $macroAttivita["Descrizione"] = substr($macroAttivita["Descrizione"], 0, 202) . "...";

    $HTML_MACROATTIVITA = str_replace("[#IMMAGINE_MACROATTIVITA]",$macroAttivita["Immagine"],$HTML_MACROATTIVITA);
    $HTML_MACROATTIVITA = str_replace("[#NOME_MACROATTIVITA]",$macroAttivita["Nome"],$HTML_MACROATTIVITA);
    $HTML_MACROATTIVITA = str_replace("[#DESCRIZIONE_MACROATTIVITA]",$macroAttivita["Descrizione"],$HTML_MACROATTIVITA);
    $HTML_MACROATTIVITA = str_replace("[#ANCORA_MACROATTIVITA]",$macroAttivita["Ancora"],$HTML_MACROATTIVITA);
}
$HTML = str_replace("[#COLONNE_ATTIVITA]",$HTML_MACROATTIVITA,$HTML);

/********** FOOTER **************/
$HTML = str_replace("[#FOOTER]",footer($activeIndex),$HTML);

echo $HTML;
?>