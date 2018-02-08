<?php
define("PERCORSO_RELATIVO","");

require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
require_once  "php/funzioni/funzioni_attivita.php";
$activeIndex = 7;

if(isset($_POST["nuovaScheda"])){
    $output = "";
    $output = file_get_contents("template/admin/scheda_attivita_admin.html");
    $output = str_replace("[#NOME]", $_POST["nome-attivita"], $output );
    $output = str_replace("[#DESCRIZIONE]", $_POST["descrizione"], $output );
    $output = str_replace("[#PREZZO]", $_POST["prezzo"], $output );
    $output = str_replace("[#CLASSE-SCHEDA]", $_POST["Classe"], $output );
    $output = str_replace("[#CODICE-ATTIVITA]", $_POST["Codice"], $output );
    echo $output;
    return;
}
loginRichiesto();

//Intestazione: indica la pagina attualmente attiva
$HTML_INTESTAZIONE = intestazione($activeIndex);

$TEMPLATE_NUOVA_ATTIVITA = file_get_contents("template/admin/nuova_scheda_attivita.html");
$TEMPLATE_NUOVA_MACRO = file_get_contents("template/admin/nuovaMacroattivita.html");
$HTML = file_get_contents("template/admin/pannello_admin.html");

//Rimpiazza il segnaposto con il menù
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

//Rimpiazza segnaposto [#UTENTI]
$HTML = str_replace("[#UTENTI]",listaUtenti(), $HTML);

$HTML = str_replace("[#NUOVA-MACROATTIVITA]",$TEMPLATE_NUOVA_MACRO, $HTML);
$HTML = str_replace("[#NUOVA-ATTIVITA]",$TEMPLATE_NUOVA_ATTIVITA, $HTML);

//Rimpiazza segna posto [#ATTIVITA]
$HTML = str_replace("[#ATTIVITA]",stampaSchedeAttivita(), $HTML);

//tabelle statistiche(rimpiazza i segnaposto [#TABELLA])
$HTML = str_replace("[#ATTIVITA-PIU-PRENOTATE]",getAttivitaPiuPrenotate(),$HTML);

//Rimpiazza segna posto [#PRENOTAZIONI-...]
$HTML = str_replace("[#PRENOTAZIONI-ATTIVE]",prenotazioniAttive(),$HTML);
$HTML = str_replace("[#PRENOTAZIONI-PASSATE]",prenotazioniPassate(),$HTML);


//Footer
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex),$HTML);
echo $HTML;

//Funzione che crea la tabella degli utenti
function listaUtenti(){
    global $db;
    $listaUtenti = $db->prepare("SELECT Utenti.ID AS ID, Utenti.Nome as Nome, Utenti.Cognome as Cognome, Utenti.Indirizzo as Indirizzo, Utenti.Username as Username, Utenti.Email as Email FROM Utenti WHERE Utenti.Tipo='Utente' AND Utenti.Stato=1");
    $listaUtenti->execute();
    $risultato = $listaUtenti->fetchAll();

    $riga="";


    foreach ($risultato as $r){
        $riga .= <<<SCRIVI
<tr id="{$r["ID"]}">    
    <td>{$r["Nome"]}</td>
    <td>{$r["Cognome"]}</td>
    <td>{$r["Indirizzo"]}</td>
    <td class="username">{$r["Username"]}</td>
    <td>{$r["Email"]}</td>
    <td><button data-target="{$r["ID"]}"  class="btn btn-testo btn-reimposta">Reimposta password</button></td>
    <td><button data-target="{$r["ID"]}" class="btn-cancella">&#x1F5D1;</button></td>
 </tr>
SCRIVI;
        
    }

    return $riga;
}

function schedeAttivita($macroattivita) {
    $risultato = getAttivita($macroattivita);

    $output = "";
    $i = false;
    $class = array('pari','dispari');
    foreach ($risultato as $r){
        $output .= file_get_contents("template/admin/scheda_attivita_admin.html");
        $output = str_replace("[#NOME]", $r["Nome"], $output );
        $output = str_replace("[#DESCRIZIONE]", $r["Descrizione"], $output );
        $output = str_replace("[#PREZZO]", $r["Prezzo"], $output );
        $output = str_replace("[#CLASSE-SCHEDA]", $class[intval($i)], $output );
        $output = str_replace("[#CODICE-ATTIVITA]", $r["Codice"], $output );
        $i = !$i;
    }
    return $output;
}

function stampaSchedeAttivita(){
    $elencoMacro = getMacroattivita();
    $output="";

    foreach($elencoMacro as $attivita){
        $listaSchede = schedeAttivita($attivita["Codice"]);
        //Creare template per contenere macroattività che ha pulsanti titolo ecc.
        $output .= <<<SCRIVI
<h2 class="titolo-macro">{$attivita["Nome"]}</h2>
<button class="btn btn-block btn-nuova-attivita" id="macro-{$attivita["Codice"]}" data-info="{$attivita["Nome"]}">Nuova attività</button>
<div id="gruppo-macro-{$attivita["Codice"]}">
    {$listaSchede}
    <div class="clearfix"></div>
</div>

SCRIVI;
    }
    return $output;
}

function getAttivitaPiuPrenotate() {
    global $db;

    $query=$db->prepare("SELECT Attivita.Codice ,Attivita.Nome AS NomeAttivita,COUNT(Prenotazioni.Codice)AS NumeroPrenotazioni FROM Attivita,Prenotazioni WHERE Attivita.Codice=Prenotazioni.IDAttivita GROUP BY Attivita.Codice ORDER BY NumeroPrenotazioni DESC");
    $query->execute();
    $array=$query->fetchAll();

    $row="";
    $counter = 1;
    foreach($array as $item) {
        $row .= <<<RIGA
<tr>
    <td>{$counter}</td>
    <td>{$item["NomeAttivita"]}</td>
    <td class="numero-prenotazioni" data-target="{$item["NomeAttivita"]}">{$item["NumeroPrenotazioni"]}</td>
</tr>
RIGA;
        $counter = $counter + 1;
    }

    return $row;

}

function prenotazioniAttive(){
    global $db;

    $prenotazioni=$db->prepare("SELECT Prenotazioni.Codice AS CodicePrenotazione, Utenti.Nome AS Utente, Attivita.Nome AS Attivita, Prenotazioni.PostiPrenotati AS Posti, Prenotazioni.Giorno AS Giorno, Prenotazioni.Stato AS Stato, Prenotazioni.Pagamento AS Pagato 
FROM Utenti, Attivita,Prenotazioni WHERE Utenti.ID=Prenotazioni.IDUtente AND Prenotazioni.IDAttivita=Attivita.Codice AND Giorno>=(SELECT CURDATE()) AND Prenotazioni.Stato<>'Cancellata' ORDER BY Giorno, Attivita, Utente ASC");
    $prenotazioni->execute();
    $arrayPrenotazioni=$prenotazioni->fetchAll();
    impostaTestoPagamento($arrayPrenotazioni);
    $row="";
    foreach ($arrayPrenotazioni as $riga){
        $row .= <<<RIGA
<tr id="{$riga["CodicePrenotazione"]}">
     <td>{$riga["Utente"]}</td>
     <td>{$riga["Attivita"]}</td>
     <td>{$riga["Posti"]}</td>
     <td>{$riga["Giorno"]}</td>
     <td>{$riga["Stato"]}</td>
     <td>{$riga["Pagato"]}</td>
     <td>&#x270E;</td>
     <td><button data-target="{$riga["CodicePrenotazione"]}" class="btn-cancella">&#x1F5D1;</button></td>
</tr>
RIGA;
    }
    return $row;
}

function prenotazioniPassate(){
    global $db;

    $prenotazioni=$db->prepare("SELECT Prenotazioni.Codice AS CodicePrenotazione, Utenti.Nome  AS Utente, Attivita.Nome AS Attivita, Prenotazioni.PostiPrenotati AS Posti, Prenotazioni.Giorno AS Giorno, Prenotazioni.Stato AS Stato, Prenotazioni.Pagamento AS Pagato 
FROM Utenti, Attivita,Prenotazioni WHERE Utenti.ID=Prenotazioni.IDUtente AND Prenotazioni.IDAttivita=Attivita.Codice AND Giorno<(SELECT CURDATE()) ORDER BY Giorno, Attivita, Utente ASC ");
    $prenotazioni->execute();
    $arrayPrenotazioni=$prenotazioni->fetchAll();
    impostaTestoPagamento($arrayPrenotazioni);
    $row="";
    foreach ($arrayPrenotazioni as $riga){
        $row .= <<<RIGA
<tr>
    <td>{$riga["Utente"]}</td>
    <td>{$riga["Attivita"]}</td>
    <td>{$riga["Posti"]}</td>
    <td>{$riga["Giorno"]}</td>
    <td>{$riga["Stato"]}</td>
    <td>{$riga["Pagato"]}</td>
</tr>
RIGA;
    }
    return $row;
}


