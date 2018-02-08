<?php
define("PERCORSO_RELATIVO","");

require_once "php/funzioni/funzioni_pagina.php";
$activeIndex=4;

$HTML_INTESTAZIONE = intestazione($activeIndex);

$HTML = file_get_contents("template/login.html");
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex), $HTML);

$HTML = str_replace("[#HTTP_REFERER]",$_SERVER['HTTP_REFERER'],$HTML);
echo $HTML;

