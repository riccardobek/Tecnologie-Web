<?php
session_start();

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";

$db->beginTransaction();

if(isAdmin()){
    $nomeMacroattivita = filter_var($_POST["nome-macro"], FILTER_SANITIZE_STRING);
    $descrizione = filter_var($_POST["descrizione-macro"], FILTER_SANITIZE_STRING);
    $img = isset($_POST["immagine"]) ? $_POST["immagine"] : NULL;
	$banner = isset($_POST["immagine-banner"]) ? $_POST["immagine-banner"] : NULL;

	//creazione di una nuova macro attività
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
            //Macroattività inserita, ora serve una select per ottenere il codice
            $queryCodiceMacro = $db->prepare("SELECT Codice FROM Macroattivita WHERE Nome = ?");
            $queryCodiceMacro->execute(array($nomeMacroattivita));
            $codice = $queryCodiceMacro->fetch();

            successoJSON("Nuova macroattività inserita con successo.",array("idMacro"=>$codice["Codice"]));
        }
        else {
            $db->rollBack();
            erroreJSON("Errore nell'inserimetno della nuova macroattività.");
        }
    }
    //modifica di una macro attività
    else {
        $idMacro = abs(filter_var($_POST["idMacro"],FILTER_SANITIZE_NUMBER_INT));

        $queryModifica = $db->prepare("UPDATE Macroattivita SET Nome = ?, Descrizione = ?, Immagine = ?, Banner = ? WHERE Codice = ?");
        if($queryModifica->execute(array($nomeMacroattivita,$descrizione,$img,$banner,$idMacro))) {
            $db->commit();
            successoJSON("Macroattività modificata con successo.");
        }
        else {
            $db->rollBack();
            erroreJSON("Errore nella modifica della macroattività.");
        }
    }
}
else {
    erroreJSON("Permesso negato.");
}
