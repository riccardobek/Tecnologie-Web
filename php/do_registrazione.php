<?php
require_once "database.php";
require_once "funzioni/funzioni_sicurezza.php";
require_once "funzioni/funzioni_json.php";


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

foreach($campiRichiesti as $campo) {
    if(!(validaCampo($$campo))) {
        erroreJSON("Campo richiesto non compilato");
        return;
    }
}

if(($errore = esisteUtente($username,$email)) > 0) {
    $errore === 1 ? erroreJSON("Username già in uso. Riprova.") : erroreJSON("Email già in uso. Riprova.");
    return;
}

if($password != $password2) {
    erroreJSON("Le due password non combaciano");
    return;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    erroreJSON("Email inserita non valida");
    return;
}

$db->beginTransaction();
$insertStatement = $db->prepare("INSERT INTO Utenti VALUES (NULL,?,?,?,?,?,?,?,?,?,'Utente')");
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
    erroreJSON("Errore nell'inserimento dell'utente.");
    return;
}
successoJSON("Utente inserito con successo");

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