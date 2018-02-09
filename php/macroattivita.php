<?php
session_start();

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";

$db->beginTransaction();

if(isAdmin()){
    $nomeMacroattivita = $_POST["nome-macro"];
    $descrizione = $_POST["descrizione-macro"];
    $img = $_POST["immagine"];
	$banner = $_POST["immagine-banner"];

    if(isset($_POST["nuovaMacro"])) {
        $queryControllo = $db->prepare("SELECT Nome FROM Macroattivita WHERE Nome = ?");
        $queryControllo->execute(array($nomeMacroattivita));
        if ($queryControllo->fetch()) {
            erroreJSON("Nome macroattività già in uso", array("Tipo" => "0"));
            return;
        }

        $queryInserimento = $db->prepare("INSERT INTO Macroattivita VALUES (NULL,?,?,?,?)");

        if ($queryInserimento->execute(array($nomeMacroattivita, $descrizione, $img, $banner))) {
            $db->commit();
            successoJSON("Nuova macroattività inserita con successo.");
        } else {
            $db->rollBack();
            erroreJSON("Errore nell'inserimetno  della nuova macroattività.");
        }
    }
    /*
    else{
        $idMacro = $_POST["idMacro"];
        $idMacro = str_replace("macroattivita-",'',$idMacro);
        $queryModifica = $db->prepare("UPDATE Macroattivita SET Nome = ?, Descrizione = ?, Immagine = ? Banner = ? WHERE Codice = ?");
        if($queryModifica->execute(array($nomeMacroattivita,$descrizione,$img,$banner,$idMacro))) {
            $db->commit();
            successoJSON("Macroattività modificata con successo.");
        }
        else {
            $db->rollBack();
            erroreJSON("Errore nella modifica dellla macroattività.");
        }
    }
*/
}
else{
    erroreJSON("Non è stato possibile modificare l'attività.");
}
