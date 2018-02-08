<?php
define("PERCORSO_RELATIVO","");

require_once "php/funzioni/funzioni_pagina.php";
$activeIndex = 2;

/*Intestazione: indica la pagina attualmente attiva --> contattaci */
$HTML_INTESTAZIONE = intestazione($activeIndex);

/*Richiamo pagina contatti*/
$HTML = file_get_contents("template/contattaci.html");

/*Rimpiazza il segnaposto con il menù*/
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

/*Footer*/
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex),$HTML);
echo $HTML;



