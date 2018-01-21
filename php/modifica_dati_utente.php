<?php
session_start();

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";

$db->beginTransaction();

$nome = $_POST["Nome"];
$cognome = $_POST["Cognome"];
$indirizzo = $_POST["Indirizzo"];
$civico = $_POST["Civico"];
$citta = $_POST["Citta"];
$CAP = $_POST["CAP"];
$email = $_POST["Email"];

$idUtente = $_SESSION["Utente"]["ID"];

//Controllo, forse inutile
$queryControllo = $db->prepare("SELECT ID FROM Utenti WHERE ID = ?");
$queryControllo->execute(array($idUtente));

if(!$queryControllo->fetch()) {
    erroreJSON("Utente non trovato",$queryControllo->errorInfo());
    return;
}

$queryUtente = $db->prepare("SELECT * FROM Utenti WHERE ID = ?");
$queryUtente->execute(array($idUtente));

$risQueryUtente = $queryUtente->fetchAll();

$queryEmail= $db->prepare("SELECT ID From Utenti WHERE Email = ?");
$queryEmail->execute(array($email));

$risQueryEmail = $queryEmail->fetch();

if($risQueryEmail && $risQueryEmail["ID"] != $idUtente){
    erroreJSON("Email già in uso.");
    return;
}

//Se è stata inserta la vecchia password allora si vuole modificare la password
if(isset($_POST["VecchiaPWd"])) {
    $vecchiaPwd = $_POST["VecchiaPWd"];
    $nuovaPwd = $_POST["NuovaPwd"];
    if(criptaPassword($vecchiaPwd) != $risQueryUtente["Password"]) {
        erroreJSON("Vecchia password non corretta.");
        return;
    }
    //Modifico la password dell'account
    $queryModifica = $db->prepare(UPDATE Utenti SET Password = ? WHERE ID = ?);
    if($queryModifica->execute(array($nuovaPwd, $idUtente))) {
        $db->commit();
    }
    else{
        $db->rollBack();
        erroreJSON("Non è stato possibile modificare i dati dell'account");
    }
}

//A questo punto i controlli sono stati effettuati e la password è stata modificata o meno ed il form della modifica è valido
$queryModifica = $db->prepare("UPDATE Utenti SET Nome = ?, Cognome = ?, Indirizzo = ?, Civico = ?, Citta = ?, CAP = ?, Email = ? WHERE ID = ?");


if($queryModifica->execute(array($nome, $cognome, $indirizzo, $civico, $citta, $CAP, $email, $idUtente))) {
    $db->commit();
    successoJSON("Dati account modificati con successo");
    return;
}
$db->rollBack();
erroreJSON("Non è stato possibile modificare i dati dell'account");