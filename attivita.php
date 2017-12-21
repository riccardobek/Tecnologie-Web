<?php
require_once "php/funzioni/funzioni_pagina.php";
require_once "php/database.php";
require_once "php/funzioni/funzioni_attivita.php";
require_once "php/funzioni/pagine/funzioni_pagina_attivita.php";
$activeIndex = 1;

/*Intestazione: indica la pagina attualmente attiva --> attività */
$HTML_INTESTAZIONE = intestazione($activeIndex);

/*Richiamo pagina contatti*/
$HTML = file_get_contents("template/attivita.html");

/*Rimpiazza il segnaposto con il menù*/
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

$HTML = str_replace( "[#ATTIVITA]",stampaAttivita(), $HTML);

/*Footer*/
$HTML = str_replace("[#FOOTER]",footer($activeIndex),$HTML);

echo $HTML;

?>
