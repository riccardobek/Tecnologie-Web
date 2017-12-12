<?php
require_once "database.php";

$campiRichiesti = array("nome","cognome","username","password","password2");

if(!isset($_POST["registrazione"])) {
    die("Parametri non validi");
}

$nome = $_POST["nome"];
$cognome = $_POST["cognome"];

$username = $_POST["username"];

$email = $_POST["email"];
$password = $_POST["password"];
$password2 = $_POST["password2"];

$indirizzo = $_POST["indirizzo"];
$civico = $_POST["civico"];
$citta = $_POST["citta"];

foreach($campiRichiesti as $campo) {
    if(!(validaCampo($$campo))) {
        die("Campo richiesto non compilato");
    }
}

if(($errore = esisteUtente($username,$email)) > 0) {
    $errore === 1 ? die("Username gi&agrave; in uso. Riprova.") : die("Email gi&agrave; in uso. Riprova.");
}

if($password != $password2) {
    die("Le due password non combaciano");
}

$db->beginTransaction();
$insertStatement = $db->prepare("INSERT INTO Utenti VALUES (NULL,?,?,?,?,?,?)");
if($insertStatement->execute(array(
    $nome,
    $cognome,
    $username,
    $email,
    $password,
    $indirizzo
))) {
    $db->commit();
}

else {
    $db->rollBack();
    die("Errore nell'inserimento dell'utente.");
}

echo "Utente inserito con successo!";

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