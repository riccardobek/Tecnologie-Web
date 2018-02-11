<?php
define("PERCORSO_RELATIVO","");

require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
require_once  "php/funzioni/funzioni_attivita.php";
$activeIndex = 7;

if(!isAdmin()) {
    paginaErrore("Non hai l'autorizzazione per accedere a questa pagina");
    return;
}

if(isset($_POST["confermaPagamento"])) {
    settaPagato($_POST["codicePrenotazione"]);
    return;
}
if(isset($_POST["nuovaMacro"])) {

    return;
}
if(isset($_POST["nuovaScheda"])) {
    $codiceAttivita = filter_var($_POST["Codice"], FILTER_SANITIZE_NUMBER_INT);
    $classe = filter_var($_POST["Classe"], FILTER_SANITIZE_STRING);
    $output = "";

    $querySelect = $db->prepare("SELECT * FROM Attivita WHERE Codice = ?");
    $querySelect->execute(array($codiceAttivita));

    if(!($attivita = $querySelect->fetch())) {
        return $output;
    }

    $output = file_get_contents("template/admin/scheda_attivita_admin.html");
    $output = str_replace("[#NOME]", $attivita["Nome"], $output );
    $output = str_replace("[#DESCRIZIONE]", $attivita["Descrizione"], $output );
    $output = str_replace("[#PREZZO]", $attivita["Prezzo"], $output );
    $output = str_replace("[#CLASSE-SCHEDA]", $classe, $output );
    $output = str_replace("[#CODICE-ATTIVITA]",$codiceAttivita , $output );
    if($_POST["Classe"]=='dispari') {
        $output = str_replace("[#SEPARATORE]", "<div class=separatore></div>", $output );
    }
    else {
        $output = str_replace("[#SEPARATORE]", '', $output );
    }
    echo $output;
    return;
}

if(isset($_POST["RichiestaMacro"])){
    $idMacro = abs(filter_var($_POST["RichiestaMacro"],FILTER_SANITIZE_NUMBER_INT));
    //$idMacro = str_replace("macro-",'',$idMacro);
    $ris = getMacroattivitaByCodice($idMacro);
    echo json_encode($ris);
    return;
}



loginRichiesto();

//Intestazione: indica la pagina attualmente attiva
$HTML_INTESTAZIONE = intestazione($activeIndex);

$TEMPLATE_NUOVA_ATTIVITA = file_get_contents("template/admin/nuova_scheda_attivita.html");
$TEMPLATE_NUOVA_MACRO = file_get_contents("template/admin/scheda_macroattivita.html");

$HTML = file_get_contents("template/admin/pannello_admin.html");

//Rimpiazza il segnaposto con il menù
$HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

//Rimpiazza segnaposto [#UTENTI]
$HTML = str_replace("[#UTENTI]",listaUtenti(), $HTML);

$HTML = str_replace("[#NUOVA-ATTIVITA]",$TEMPLATE_NUOVA_ATTIVITA, $HTML);
$HTML = str_replace("[#SCHEDA-MACROATTIVITA]",$TEMPLATE_NUOVA_MACRO, $HTML);



//Rimpiazza segna posto [#ATTIVITA]
$HTML = str_replace("[#ATTIVITA]",stampaSchedeAttivita(), $HTML);

$HTML = str_replace("[#ENTRATE-DEL-MESE]",entrateDelMese(),$HTML);
$HTML = str_replace("[#ENTRATE-PREVISTE]",entratePreviste(),$HTML);
//tabelle statistiche(rimpiazza i segnaposto [#ATTIVITA-PIU-PRENOTATE])
$HTML = str_replace("[#ATTIVITA-PIU-PRENOTATE]",getAttivitaPiuPrenotate(),$HTML);
//rimpiazza [#UTENTI-PIU-ATTIVI]
$HTML = str_replace("[#UTENTI-PIU-ATTIVI]",utentiPiuAttivi(),$HTML);


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
        if($i == true) {
            $output = str_replace("[#SEPARATORE]", "<div class=separatore></div>", $output );
        }
        else {
            $output = str_replace("[#SEPARATORE]", '', $output );
        }

        $i = !$i;
    }
    return $output;
}


function stampaSchedeAttivita(){
    $elencoMacro = getMacroattivita();
    $output="";

    foreach($elencoMacro as $macroAttivita){
        $listaSchede = schedeAttivita($macroAttivita["Codice"]);
        //Creare template per contenere macroattività che ha pulsanti titolo ecc.
        $output .= <<<SCRIVI
<h1 class="titolo-macro">{$macroAttivita["Nome"]} &nbsp;&nbsp;<span class="dim-mod-canc" data-target="macro-{$macroAttivita["Codice"]}">( <a class="mod-macro" >modifica</a> | <a class="canc-macro">cancella</a> )</span></h1>
<button class="btn btn-block btn-nuova-attivita" data-target="macro-{$macroAttivita["Codice"]}" data-info="{$macroAttivita["Nome"]}">Nuova attività</button>
<div id="gruppo-macro-{$macroAttivita["Codice"]}">
    {$listaSchede}
    <div class="clearfix inserimento-scheda"></div>
</div>

SCRIVI;
    }
    return $output;
}

function entrateDelMese(){
   global $db;

   $today=date("t-m-Y");


    $query=$db->prepare("SELECT Attivita.Prezzo AS Prezzo,Prenotazioni.PostiPrenotati AS Posti,Prenotazioni.Giorno AS Giorno FROM Attivita, Prenotazioni WHERE Attivita.Codice=Prenotazioni.IDAttivita AND month(CURRENT_DATE)=month(Prenotazioni.Giorno) AND Prenotazioni.Giorno<CURDATE() AND Prenotazioni.Stato='Confermata' AND Prenotazioni.Pagamento=1");
    $query->execute();
    $array=$query->fetchAll();
    $result=0;

    foreach($array as $item) {
        $result=$result+($item["Prezzo"]*$item["Posti"]);
    }
    return <<<SCRIVI
 <div class="results">
    {$result} €
 </div>
SCRIVI;


}

function entratePreviste(){
    global $db;


    $query=$db->prepare("SELECT Attivita.Prezzo AS Prezzo,Prenotazioni.PostiPrenotati AS Posti,Prenotazioni.Giorno AS Giorno FROM Attivita, Prenotazioni WHERE Attivita.Codice=Prenotazioni.IDAttivita AND month(CURRENT_DATE)=month(Prenotazioni.Giorno) AND Prenotazioni.Giorno>CURDATE() AND Prenotazioni.Stato='Confermata' ");
    $query->execute();
    $array=$query->fetchAll();
    $result=0;
    foreach($array as $item) {
        $result=$result+($item["Prezzo"]*$item["Posti"]);

    }
    return <<<SCRIVI
<div class="results">
{$result} €
</div>
SCRIVI;
}



function getAttivitaPiuPrenotate() {
    global $db;

    $query=$db->prepare("SELECT Attivita.Codice, Attivita.Nome AS NomeAttivita,COUNT(Prenotazioni.Codice)AS NumeroPrenotazioni FROM Attivita,Prenotazioni,Utenti WHERE Attivita.Codice=Prenotazioni.IDAttivita AND Prenotazioni.IDUtente = Utenti.ID AND Utenti.Tipo!='Admin' GROUP BY Attivita.Codice ORDER BY NumeroPrenotazioni DESC LIMIT 5");
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

function utentiPiuAttivi(){

    global $db;

    $query=$db->prepare("SELECT Utenti.Nome AS Nome, Utenti.Cognome AS Cognome, COUNT(Prenotazioni.Codice) AS NumeroPrenotazioni FROM Prenotazioni, Utenti WHERE Prenotazioni.IDUtente = Utenti.ID AND Utenti.Tipo!='Admin' GROUP BY Nome, Cognome ORDER BY NumeroPrenotazioni DESC LIMIT 5");
    $query->execute();
    $array=$query->fetchAll();

    $row="";
    $counter=1;
    foreach($array as $item) {
        $row .= <<<RIGA
<tr>
    <td>{$counter}</td>
    <td>{$item["Nome"]}</td>
    <td>{$item["Cognome"]}</td>
    <td class="numero-prenotazioni" data-target="{$item["Nome"]}">{$item["NumeroPrenotazioni"]}</td>
</tr>
RIGA;
        $counter = $counter + 1;
    }

    return $row;
}


function prenotazioniAttive(){
    global $db;

    $prenotazioni=$db->prepare("SELECT Prenotazioni.Codice AS CodicePrenotazione, Utenti.Username AS Username, Attivita.Nome AS Attivita, Prenotazioni.PostiPrenotati AS Posti, Prenotazioni.Giorno AS Giorno, Prenotazioni.Stato AS Stato, Prenotazioni.Pagamento AS Pagato FROM Utenti, Attivita,Prenotazioni WHERE Utenti.ID=Prenotazioni.IDUtente AND Prenotazioni.IDAttivita=Attivita.Codice AND Giorno>=(SELECT CURDATE()) AND Utenti.Tipo<>'Admin' AND Prenotazioni.Stato<>'Cancellata' ORDER BY Giorno, Attivita, Username ASC");
    $prenotazioni->execute();
    $arrayPrenotazioni=$prenotazioni->fetchAll();
    impostaTestoPagamento($arrayPrenotazioni);
    $row="";
    foreach ($arrayPrenotazioni as $riga){
    if($riga["Pagato"]=="Non pagato") {
                $row .= <<<RIGA
        <tr id="{$riga["CodicePrenotazione"]}">
             <td>{$riga["Username"]}</td>
             <td>{$riga["Attivita"]}</td>
             <td>{$riga["Posti"]}</td>
             <td>{$riga["Giorno"]}</td>
             <td>{$riga["Stato"]}</td>
             <td>{$riga["Pagato"]}</td>
             <td><button data-target="{$riga["CodicePrenotazione"]}" class="btn btn-primary pay">Conferma Pagamento</button></td>
             <td><button data-target="{$riga["CodicePrenotazione"]}" class="btn-cancella">&#x1F5D1;</button></td>
        </tr>
RIGA;
     }
     else{
         $row .= <<<RIGA
        <tr id="{$riga["CodicePrenotazione"]}">
             <td>{$riga["Username"]}</td>
             <td>{$riga["Attivita"]}</td>
             <td>{$riga["Posti"]}</td>
             <td>{$riga["Giorno"]}</td>
             <td>{$riga["Stato"]}</td>
             <td>{$riga["Pagato"]}</td>
             <td>Pagamento effettuato</td>
             <td><button data-target="{$riga["CodicePrenotazione"]}" class="btn-cancella">&#x1F5D1;</button></td>
        </tr>
RIGA;
        }
    }
    return $row;
}

function prenotazioniPassate(){
    global $db;

    $prenotazioni=$db->prepare("SELECT Prenotazioni.Codice AS CodicePrenotazione, Utenti.Nome  AS Utente, Attivita.Nome AS Attivita, Prenotazioni.PostiPrenotati AS Posti, Prenotazioni.Giorno AS Giorno, Prenotazioni.Stato AS Stato, Prenotazioni.Pagamento AS Pagato 
FROM Utenti, Attivita,Prenotazioni WHERE Utenti.ID=Prenotazioni.IDUtente AND Prenotazioni.IDAttivita=Attivita.Codice AND Giorno<(SELECT CURDATE()) AND Utente.Tipo<>'Admin' ORDER BY Giorno, Attivita, Utente ASC ");
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

function settaPagato($codice){
    global $db;
    $db->beginTransaction();
    $queryControllo = $db->prepare("SELECT Codice FROM Prenotazioni WHERE Codice = ?");
    $queryControllo->execute(array($codice));

    if(!($queryControllo->fetch())) {
        erroreJSON("Non è stato possibile effettuare il pagamento");
        return;
    }
    $query = $db->prepare("UPDATE Prenotazioni SET Pagamento = '1' WHERE Codice = ?");

    if($query->execute(array($codice))) {
        $db->commit();
        successoJSON("Pagamento effettuato con successo");
        return;
        }

    $db->rollBack();
    erroreJSON("Non è stato possibile effettuare il pagamento");
    return;

}
