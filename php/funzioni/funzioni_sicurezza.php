<?php
function criptaPassword($password) {
    return hash('sha512',$password);
}

function isUtenteLoggato() {
    return isset($_SESSION["Utente"]) && isset($_SESSION["Utente"]["ID"]);
}