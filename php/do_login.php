<?php
require_once "funzioni/funzioni_pagina.php";
require_once "funzioni/funzioni_sicurezza.php";

require_once "database.php";

$username = filter_var($_POST["username"],FILTER_SANITIZE_STRING);
$password = $_POST["password"];

$queryLogin = $db->prepare("SELECT * FROM Utenti WHERE Username = ? AND Password = ?");
$queryLogin->execute(array($username,criptaPassword($password)));

$utente = $queryLogin->fetch();

if(!$utente) {
    die("0");
}

$_SESSION["Utente"] = array();
$_SESSION["Utente"] = $utente;
$_SESSION["logged"] = 1;

echo "1";