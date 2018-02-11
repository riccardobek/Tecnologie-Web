<?php
define("PERCORSO_RELATIVO","../");

require_once "database.php";
require_once "funzioni/funzioni_sicurezza.php";
require_once "funzioni/funzioni_pagina.php";
require_once "funzioni/funzioni_json.php";

define("LINK_PAGINA_ERRORE","../registrazione.php");
define("TESTO_LINK_PAGINA_ERRORE", "Torna alla registrazione");

$campiRichiesti = array("nome","cognome","username","password","password2");

$nome = filter_var($_POST["nome"],FILTER_SANITIZE_STRING);
$cognome = filter_var($_POST["cognome"],FILTER_SANITIZE_STRING);

$username = filter_var($_POST["username"],FILTER_SANITIZE_STRING);

$email = $_POST["email"];
$password = $_POST["password"];
$password2 = $_POST["password2"];

$indirizzo = filter_var($_POST["indirizzo"],FILTER_SANITIZE_STRING);
$civico = filter_var($_POST["civico"],FILTER_SANITIZE_STRING);
$citta = filter_var($_POST["citta"],FILTER_SANITIZE_STRING);
$CAP = filter_var($_POST["CAP"],FILTER_SANITIZE_NUMBER_INT);

//Variabile (passata dalla pagina) che mi dice se la richiesta arriva da ajax o no
$jsAbilitato = boolval(filter_var($_POST["JSAbilitato"],FILTER_SANITIZE_NUMBER_INT));


foreach($campiRichiesti as $campo) {
    if(!(validaCampo($$campo))) {
        $messaggio = "Campo richiesto non compilato";
        errore($messaggio);
        return;
    }
}

if(($errore = esisteUtente($username,$email)) > 0) {
    $messaggio = $errore === 1 ? "Username già in uso. Riprova." : "Email già in uso. Riprova.";
    errore($messaggio);
    return;
}

if($password != $password2) {
    $messaggio = "Le due password non combaciano";
    errore($messaggio);
    return;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $messaggio = "Email inserita non valida";
    errore($messaggio);
    return;
}

$db->beginTransaction();
$insertStatement = $db->prepare("INSERT INTO Utenti VALUES (NULL,?,?,?,?,?,?,?,?,?,'Utente',1)");
if($insertStatement->execute(array(
    $nome,
    $cognome,
    $username,
    $email,
    criptaPassword($password),
    $indirizzo,
    $civico,
    $citta,
    $CAP
))) {
    $db->commit();
}

else {
    $db->rollBack();
    $messaggio = "Errore nell'inserimento dell'utente.";
    errore($messaggio);
    return;
}
$messaggio = "Utente inserito con successo";
$jsAbilitato ? successoJSON($messaggio) : paginaSuccesso($messaggio,"../login.php","Vai al login");


/**
 * Funzione che controlla se il parametro passato è settato e non vuoto
 * @param $campo il campo da validare
 * @return bool
 */
function validaCampo($campo) {
    return isset($campo) && strlen(trim("".$campo)) > 0;
}

/**
 * Funzione che controlla l'esistenza di un utente nel database
 * @param $username
 * @param $email
 *
 * @return 0 se non esiste nessun utente con tale username oppure con tale email, 1 se esiste un utente con tale username,
 * 2 se esiste un utente con tale email
 */
function esisteUtente($username,$email) {
    global $db;

    $queryUsername= $db->prepare("SELECT ID From Utenti WHERE Username = ?");
    $queryUsername->execute(array($username));

    if($queryUsername->fetch()) return 1;

    $queryEmail= $db->prepare("SELECT ID From Utenti WHERE Email = ?");
    $queryEmail->execute(array($email));

    if($queryEmail->fetch()) return 2;

    return 0;
}