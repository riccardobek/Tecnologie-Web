<?php
require_once "impostazioni.inc.php";

try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    print "ERRORE DI CONNESSIONE AL DATABASE:" . $e->getMessage() . "<br/>";
    die();
}

function getMacroattivita() {
    global $db;
    $query = $db->prepare("SELECT Codice, Nome, Descrizione, Immagine, Banner, REPLACE(LOWER(`Nome`), ' ', '-') AS Ancora FROM Macroattivita");
    $query->execute();

    $listaMacro = $query->fetchAll();
    for($i=0; $i<count($listaMacro);$i++) {
        $listaMacro[$i]["Attivita"] = getAttivita($listaMacro[$i]["Codice"]);
    }

    return $listaMacro;
}

function getAttivita($codiceMacro) {
    global $db;
    $query = $db->prepare("SELECT * FROM Attivita WHERE Macro = ?");
    $query->execute(array($codiceMacro));

    $listaAttivita = $query->fetchAll();
    return $listaAttivita;
}

function getAttivitaByCodice($codiceAttivita) {
    global $db;
    $query = $db->prepare("SELECT * FROM Attivita WHERE Codice = ?");
    $query->execute(array($codiceAttivita));

    $attivita = $query->fetch();
    return $attivita;
}

function getMacroattivitaByCodice($codiceMacro) {
    global $db;
    $query = $db->prepare("SELECT Nome, Descrizione, Immagine, Banner, REPLACE(LOWER(`Nome`), ' ', '-') AS Ancora FROM Macroattivita WHERE Codice = ?");
    $query->execute(array($codiceMacro));

    $infoMacro = $query->fetch();
    return $infoMacro;
}
?>