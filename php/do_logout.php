<?php
require_once "funzioni/funzioni_pagina.php";

setcookie(session_name(), '', 100);
session_unset();
session_destroy();
$_SESSION = array();

echo "Logout completato. <a href='../index.php'>Torna al sito</a>";