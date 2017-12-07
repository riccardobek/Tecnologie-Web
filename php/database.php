<?php
require_once "impostazioni.inc.php";

try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    print "ERRORE DI CONNESSIONE AL DATABASE:" . $e->getMessage() . "<br/>";
    die();
}
?>