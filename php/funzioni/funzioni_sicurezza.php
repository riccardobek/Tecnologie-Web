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

define("ERRORI_INSERIMENTO_ATTIVITA", array(
    "Data non valida.",
    "Impossibile prenotare un'attività per tale data.",
    "Numero posti inserti maggiore del numero posti disponibili.",
    "Errore nell'inserimento della prenotazione nel database."
));