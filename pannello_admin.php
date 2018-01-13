<?php
define("PERCORSO_RELATIVO","");

require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
$activeIndex = INF;

//Intestazione: indica la pagina attualmente attiva
$HTML_INTESTAZIONE = intestazione($activeIndex);

//Richiamo pagina contatti
$HTML = file_get_contents("template/pannello_admin.html");

//Rimpiazza il segnaposto con il menù
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

//Funzione che crea la tabella degli utenti
function listaUtenti(){
    global $db;
    $listaUtenti = $db->prepare("SELECT Utenti.Nome as Nome, Utenti.Cognome as Cognome, Utenti.Indirizzo as Indirizzo, Utenti.Username as Username, Utenti.Email as Email FROM Utenti WHERE Utenti.Tipo='Utente'");
    $listaUtenti->execute();
    $risultato = $listaUtenti->fetchAll();

    $riga="";
    foreach ($risultato as $r){
        $riga .= <<<SCRIVI
<tr><td>{$r["Nome"]}</td><td>{$r["Cognome"]}</td><td>{$r["Indirizzo"]}</td><td>{$r["Username"]}</td><td>{$r["Email"]}></td><td><span id="reimpostaPswd">Reimposta password</span></td></tr>
SCRIVI;

    }
    return $riga;
}
//Rimpiazza segnaposto [#UTENTI]
$HTML = str_replace("[#UTENTI]",listaUtenti(), $HTML);


//Footer
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex),$HTML);
echo $HTML;



?>
