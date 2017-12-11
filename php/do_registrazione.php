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


function validaCampo($campo) {
    return isset($campo) && strlen(trim("".$campo)) > 0;
}