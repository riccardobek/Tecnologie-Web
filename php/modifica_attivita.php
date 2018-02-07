<?php
session_start();

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";

$db->beginTransaction();

if(isAdmin()){
    $nomeAttivita = $_POST["nome-attivita"];
    $descrizione = $_POST["descrizione"];
    $prezzo = $_POST["prezzo"];

    //Modifico la possibile virgola nel prezzo in un punto
    $prezzo = str_replace(',','.',$prezzo);

    if(isset($_POST["nuovaAttivita"])) {
        $queryControllo = $db->prepare("SELECT Nome FROM Attivita WHERE Nome = ?");
        $queryControllo->execute(array($nomeAttivita));
        if($queryControllo->fetch()) {
           erroreJSON("Nome attività già in uso",array("Tipo"=>"0"));
           return;
        }

        $idMacro = $_POST["idMacro"];
        $idMacro = str_replace("macro-",'',$idMacro);

        $queryInserimento = $db->prepare("INSERT INTO Attivita VALUES (NULL,?,?,?,?)");

        if($queryInserimento->execute(array($idMacro,$nomeAttivita,$descrizione,$prezzo))) {
            $db->commit();
            successoJSON("Nuova attività inserita con successo.");
        }
        else {
            $db->rollBack();
            erroreJSON("Errore nell'inserimetno  della nuova attività.");
        }
    }
    else{
        $idAttivita = $_POST["idAttivita"];
        $idAttivita = str_replace("attivita-",'',$idAttivita);
        $queryModifica = $db->prepare("UPDATE Attivita SET Nome = ?, Descrizione = ?, Prezzo = ? WHERE Codice = ?");
        if($queryModifica->execute(array($nomeAttivita,$descrizione,$prezzo,$idAttivita))) {
            $db->commit();
            successoJSON("Attività modificata con successo.");
        }
        else {
            $db->rollBack();
            erroreJSON("Errore nella modifica dell'attività.");
        }
    }


}
else{
    erroreJSON("Non è stato possibile modificare l'attività.");
}