<?php
session_start();

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";

$db->beginTransaction();


//Se nel post è stato settatto IDUtente significa che è arrivata una richiesta dal pannello admin di resettare la password dell'account di ID= IDUtente
if(isset($_POST["IDUtente"])){
    //per maggiore sicurezza si controlla che chi ha fatto la richiesta di reset sia effettivamente l'admin altrimenti viene generato un errore JSON
    if(isAdmin()){
        resetPassword(filter_var($_POST["IDUtente"],FILTER_SANITIZE_NUMBER_INT));
    }
    else{
        erroreJSON("Non è stato possibile resettare la password");
    }
    return;
}
//Se non è stato settato IDUtente allora le richieste vengono dal pannello utente. Per cui si controlla se si vuole modificare la password oppure i dati dell'account
if(isset($_POST["VecchiaPwd"])){
    $vecchiaPwd = $_POST["VecchiaPwd"];
    $nuovaPwd = $_POST["NuovaPwd"];
    modificaPassword($vecchiaPwd, $nuovaPwd);

}
else{
    modificaDati();
}


function modificaDati() {
    global $db;

    $idUtente = $_SESSION["Utente"]["ID"];
    $nome = filter_var($_POST["nome"], FILTER_SANITIZE_STRING);
    $cognome = filter_var($_POST["cognome"], FILTER_SANITIZE_STRING);
    $indirizzo = filter_var($_POST["indirizzo"], FILTER_SANITIZE_STRING);
    $civico = filter_var($_POST["civico"],FILTER_SANITIZE_STRING);
    $citta = filter_var($_POST["citta"], FILTER_SANITIZE_STRING);
    $CAP = filter_var($_POST["CAP"], FILTER_SANITIZE_NUMBER_INT);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    if(strlen(filter_var($nome, FILTER_SANITIZE_NUMBER_INT))>0){
        erroreJSON("Nome non valido");
        return;
    }
    if(strlen(filter_var($cognome, FILTER_SANITIZE_NUMBER_INT))>0){
        erroreJSON( "Cognome non valido");
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



function modificaPassword($vecchiaPwd, $nuovaPwd) {
    global $db;

    $controlloPwdCorrente = $db->prepare("SELECT Password FROM Utenti WHERE ID = ?");
    $controlloPwdCorrente->execute(array($_SESSION["Utente"]["ID"]));

    $risQueryPwd = $controlloPwdCorrente->fetch();

    if(strcmp(criptaPassword($vecchiaPwd),$risQueryPwd["Password"] ) !== 0){
        erroreJSON("Password inserita non corrisponde alla password corrente");
        return;
    }
    if(strcmp(criptaPassword($vecchiaPwd),criptaPassword($nuovaPwd) ) === 0){
        erroreJSON("La nuova password corrisponde a quella corrente");
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

function resetPassword($idUtente) {
    global $db;
    $queryModifica = $db->prepare("UPDATE Utenti SET Password = ? WHERE ID = ?");
    if($queryModifica->execute(array(criptaPassword("password"), $idUtente))) {
        $db->commit();
        successoJSON("Password resettata con successo.");
        return;
    }
    $db->rollBack();
    erroreJSON("Errore nel reset della password");

    return;
}


