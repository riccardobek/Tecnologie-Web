<?php
session_start();

require_once "database.php";
require_once "funzioni/funzioni_json.php";
require_once "funzioni/funzioni_sicurezza.php";

$imageBaseDir = str_replace("macroattivita.php","",__FILE__)."../images/attivita/";


$db->beginTransaction();

if(isAdmin()) {
    if(isset($_POST["eliminaMacro"])) {
        $idMacro = abs(filter_var($_POST["idMacro"],FILTER_SANITIZE_NUMBER_INT));

        $queryDettaglioMacro = $db->prepare("SELECT * FROM Macroattivita WHERE Codice = ?");
        $queryDettaglioMacro->execute(array($idMacro));

        $macroAttivita = $queryDettaglioMacro->fetch();
        if(!$macroAttivita) {
            $db->rollBack();
            erroreJSON("Macroattivita non trovata");
            return;
        }


        $listaAttivita = getAttivita($idMacro);
        $errore = false;
        foreach ($listaAttivita as $attivita) {
            if(!(eliminaAttivita($attivita["Codice"],false))) {
                $errore = true;
                break;
            }
        }
        if($errore){
            $db->rollBack();
            erroreJSON("Errore durante l'eliminazione della macroattività.");
            return;
        }
        $queryDeleteMacro = $db->prepare("DELETE FROM Macroattivita WHERE Codice = ?");
        if($queryDeleteMacro->execute(array($idMacro))) {

            if(strlen($macroAttivita["Immagine"]) > 0)
                eliminaImage($macroAttivita["Immagine"],"index");
            if(strlen($macroAttivita["Banner"]) > 0)
                eliminaImage($macroAttivita["Banner"],"banner");

            $db->commit();

            successoJSON("Macroattività eliminata con successo.");
            return;
        }
        else{
            $db->rollBack();
            erroreJSON("Errore durante l'eliminazione della macroattività.");
            return;
        }
    }

    $nomeMacroattivita = filter_var($_POST["nome-macro"], FILTER_SANITIZE_STRING);
    $descrizione = filter_var($_POST["descrizione-macro"], FILTER_SANITIZE_STRING);
    $img = isset($_FILES["immagine"]) ? uploadImage("immagine","index") : NULL;
	$banner = isset($_FILES["immagine-banner"]) ? uploadImage("immagine-banner","banner") : NULL;

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

        $queryDettaglioMacro = $db->prepare("SELECT * FROM Macroattivita WHERE Codice = ?");
        $queryDettaglioMacro->execute(array($idMacro));

        $macroAttivita = $queryDettaglioMacro->fetch();
        if(!$macroAttivita) {
            $db->rollBack();
            erroreJSON("Macroattivita non trovata");
            return;
        }

        $imgDaEliminare = false;

        if($img == null || !$img) {
            $img = $macroAttivita["Immagine"];
        }
        else {
            //ho caricato una nuova immagine quindi rimuovo quella vecchia
            $imgDaEliminare = $macroAttivita["Immagine"];
        }

        $bannerDaEliminare = false;
        if($banner == null || !$banner) {
            $banner = $macroAttivita["Banner"];
        }
        else {
            //ho caricato una nuova immagine quindi rimuovo quella vecchia
            $bannerDaEliminare = $macroAttivita["Banner"];
        }


        $queryModifica = $db->prepare("UPDATE Macroattivita SET Nome = ?, Descrizione = ?, Immagine = ?, Banner = ? WHERE Codice = ?");
        if($queryModifica->execute(array($nomeMacroattivita,$descrizione,$img,$banner,$idMacro))) {
            $db->commit();
            if($imgDaEliminare != false) eliminaImage($imgDaEliminare,"index");
            if($bannerDaEliminare != false) eliminaImage($bannerDaEliminare,"banner");

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


/**
 * Funzione che carica un immagine o nella cartella images/attivita/banner o nella cartella images/attivita/index
 * @param $image l'immagine che si vuole caricare
 * @param $cartella la cartella nella quale si vuole caricare l'immagine
 */
function uploadImage($image,$cartella) {
    global $imageBaseDir;

    $imageFileType = strtolower(pathinfo($_FILES[$image]["name"], PATHINFO_EXTENSION));

    $nomeNuovoFile = time().rand(0,9999).".".$imageFileType;

    if ($cartella == "index" || $cartella = "banner") {
        $target_file = $imageBaseDir.$cartella."/".$nomeNuovoFile;
    }
    else {
        return false; //parametri non corretti
    }

    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES[$image]["tmp_name"]);
        if ($check === false) {
            //erroreJSON("il file non è una immagine");
            return false;
        }
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        erroreJSON("formato immagine non riconosciuto ");
        return false;
    }

    if (move_uploaded_file($_FILES[$image]["tmp_name"], $target_file)) {
        //successoJSON("Immagine caricata con successo");
        return $nomeNuovoFile;
    } else {
        //erroreJSON("Errore nel caricamento del file");
        return false;
    }
}

function eliminaImage($image,$cartella) {
    global $imageBaseDir;

    unlink($imageBaseDir.$cartella."/".$image);
}