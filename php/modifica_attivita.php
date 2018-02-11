<?php
session_start();

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";



if(isAdmin()){
    //richiesta di eliminazione di un'attivita
    if(isset($_POST["eliminaAttivita"])) {
        $idAttivita = abs(filter_var($_POST["idAttivita"],FILTER_SANITIZE_NUMBER_INT));
        eliminaAttivita($idAttivita);
    }

    $nomeAttivita = filter_var($_POST["nome"],FILTER_SANITIZE_STRING);
    $descrizione = filter_var($_POST["descrizione"], FILTER_SANITIZE_STRING);
    $prezzo = filter_var(str_replace(',','.',$_POST["prezzo"]),FILTER_SANITIZE_NUMBER_FLOAT, array(
        'flags'=>FILTER_FLAG_ALLOW_FRACTION));



    if(isset($_POST["nuovaAttivita"])) {
        $db->beginTransaction();
        $queryControllo = $db->prepare("SELECT Nome FROM Attivita WHERE Nome = ?");
        $queryControllo->execute(array($nomeAttivita));
        if($queryControllo->fetch()) {
           erroreJSON("Nome attività già in uso", array("Tipo"=>"0"));
           return;
        }

        $idMacro = $_POST["idMacro"];
        $idMacro = str_replace("macro-",'',$idMacro);

        $queryInserimento = $db->prepare("INSERT INTO Attivita VALUES (NULL,?,?,?,?)");


        if($queryInserimento->execute(array($idMacro,$nomeAttivita,$descrizione,$prezzo))) {
            $db->commit();
            $queryCodiceAttivita = $db->prepare("SELECT Codice FROM Attivita WHERE Nome = ?");
            $queryCodiceAttivita->execute(array($nomeAttivita));
            $codice = $queryCodiceAttivita->fetch();

            successoJSON("Nuova attività inserita con successo.", array("CodiceAtt"=>$codice["Codice"],"idMacro"=>$idMacro));
        }
        else {
            $db->rollBack();
            erroreJSON("Errore nell'inserimento della nuova attività.");
        }
    }
    else {
        $db->beginTransaction();
        $idAttivita = abs(filter_var($_POST["idAttivita"], FILTER_SANITIZE_NUMBER_INT));
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
else {
    erroreJSON("Non è stato possibile modificare l'attività.");
}