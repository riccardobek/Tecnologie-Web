<?php
define("PERCORSO_RELATIVO","../");

require_once "database.php";

require_once "funzioni/funzioni_pagina.php";
require_once "funzioni/funzioni_sicurezza.php";


$jsAbilitato = boolval(filter_var($_POST["JSAbilitato"],FILTER_SANITIZE_NUMBER_INT));

$username = filter_var($_POST["username"],FILTER_SANITIZE_STRING);
$password = $_POST["password"];

$queryLogin = $db->prepare("SELECT * FROM Utenti WHERE Username = ? AND Password = ?");
$queryLogin->execute(array($username,criptaPassword($password)));

$utente = $queryLogin->fetch();

if(!$utente || $utente["Stato"]== 0) {
    ($jsAbilitato) ? print("0") : paginaErrore("Username o password non corretti","../login.php","Torna al login");
    return;
}

$_SESSION["Utente"] = array();
$_SESSION["Utente"] = $utente;

($jsAbilitato) ? print("1") : paginaSuccesso("Login effettuato con successo","../index.php","Prosegui la navigazione");
