<?php
require_once "php/funzioni/funzioni_pagina.php";
$activeIndex=3;

$HTML_INTESTAZIONE = intestazione($activeIndex);

$HTML = file_get_contents("template/registrazione.html");
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex), $HTML);

echo $HTML;
?>