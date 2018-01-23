<?php
session_start();

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";

$db->beginTransaction();

$idUtente = $_SESSION["Utente"]["ID"];

if(isset($_POST["VecchiaPwd"])){
    modificaPassword();

}
else{
    modificaDati();

}




function modificaDati() {
    global $db;

    $nome = $_POST["Nome"];
    $cognome = $_POST["Cognome"];
    $indirizzo = $_POST["Indirizzo"];
    $civico = $_POST["Civico"];
    $citta = $_POST["Citta"];
    $CAP = $_POST["CAP"];
    $email = $_POST["Email"];
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
    $queryModifica = $db->prepare("UPDATE Utenti SET Nome = ?, Cognome = ?, Indirizzo = ?, Civico = ?, Citta = ?, CAP = ?, Email = ? WHERE ID = ?");


    if($queryModifica->execute(array($nome, $cognome, $indirizzo, $civico, $citta, $CAP, $email, $idUtente))) {
        $db->commit();
        successoJSON("Dati account modificati con successo");
        return;
    }
    $db->rollBack();
    erroreJSON("Non è stato possibile modificare i dati dell'account");
    return;
}





function modificaPassword() {
    global $db;


    $vecchiaPwd = $_POST["VecchiaPwd"];
    $nuovaPwd = $_POST["NuovaPwd"];


    $controlloPwdCorrente = $db->prepare("SELECT Password FROM Utenti WHERE ID = ?");
    $controlloPwdCorrente->execute(array($_SESSION["Utente"]["ID"]));

    $risQueryPwd = $controlloPwdCorrente->fetch();

    if(strcmp(criptaPassword($vecchiaPwd),$risQueryPwd["Password"] ) !== 0){
        erroreJSON("Errore: Password inserita non corrisponde alla password corrente");
        return;
    }
    if(strcmp(criptaPassword($vecchiaPwd),criptaPassword($nuovaPwd) ) === 0){
        erroreJSON("Errore: La nuova password corrisponde a quella corrente");
        return;
    }

    //Modifico la password dell'account
    $queryModifica = $db->prepare("UPDATE Utenti SET Password = ? WHERE ID = ?");
    if($queryModifica->execute(array(criptaPassword($nuovaPwd), $_SESSION["Utente"]["ID"]))) {
        $db->commit();
        successoJSON("Password modificata con successo.");
        return;
    }
    $db->rollBack();
    return;
}



