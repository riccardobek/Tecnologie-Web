<?php
function criptaPassword($password) {
    return hash('sha512',$password);
}

function isUtenteLoggato() {
    return isset($_SESSION["Utente"]) && isset($_SESSION["Utente"]["ID"]);
}

function isAdmin() {
    return isset($_SESSION["Utente"]["Tipo"]) && $_SESSION["Utente"]["Tipo"] == 'Admin';
}
/**
 * Verifica se la data passata come parametro è una data nel futuro o no
 * @param $dataDaValidare stringa contentente la data in formato Y-m-d
 */
function dataFutura($dataDaValidare) {
    return strtotime((new DateTime())->format("Y-m-d")) < strtotime($dataDaValidare);
}

/**
 * @param $stringa
 * @return ritorna la stringa eliminando i ritorni a capo multipli
 */
function eliminaRitorniACapo($stringa) {
    $pattern = "/<br>( )*<br>/";

    $stringa = str_replace(array("\r\n", "\r", "\n"), "<br>", $stringa);

    if(preg_match($pattern, $stringa)) {
        $stringa = preg_replace($pattern, "<br>", $stringa);
        $stringa = eliminaRitorniACapo($stringa);
    }

    return $stringa;
}

/**
 * @param $stringa
 * @return ritorna la stringa eliminado i ritorno a capo e spazi multipli
 */
function eliminaRitorniACapoESpazi($stringa) {
    return  str_replace("<br>","\r ",preg_replace('/\s+/', ' ',eliminaRitorniACapo($stringa)));
}

define("ERRORI_INSERIMENTO_ATTIVITA", array(
    "Data non valida.",
    "Impossibile prenotare un'attività per tale data.",
    "Numero posti inserti maggiore del numero posti disponibili.",
    "Errore nell'inserimento della prenotazione nel database."
));