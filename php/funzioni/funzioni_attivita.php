<?php
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