<?php
require_once "funzioni/funzioni_pagina.php";

setcookie(session_name(), '', 100);
session_unset();
session_destroy();
$_SESSION = array();
$_SESSION["logged"] = 0;
echo "Logout completato. <a href='../index.php'>Torna al sito</a>";