<?php
require_once "php/funzioni/funzioni_json.php";

function uploadImage($cartella)
{

    if ($cartella == "index" || $cartella = "banner") {
        $target_dir = "images/attivita/" . $cartella . "/";
    }


    $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["tmp_name"], PATHINFO_EXTENSION));

    $target_file = $target_dir . time() . rand() . $imageFileType;


    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check === false) {
            erroreJSON("il file non è una immagine");
            return;
        }
    }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            erroreJSON("formato immagine non riconosciuto");
            return;
        }

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            successoJSON("Immagine caricata con successo");
            return;
        } else {
            erroreJSON("Errore nel caricamento del file");
            return;
        }
}


