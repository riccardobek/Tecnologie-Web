<?php
define("PERCORSO_RELATIVO","");

require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
require_once "php/funzioni/funzioni_json.php";

$activeIndex = 7;

if(!isAdmin()) {
    paginaErrore("Non hai l'autorizzazione per accedere a questa pagina");
    return;
}
//gestisci diponibilità
if(isset($_POST["Disponibilita"])) {
    $data = convertiData(filter_var($_POST["data"],FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $db->beginTransaction();

    $queryControllo = $db->prepare("SELECT Giorno FROM Disponibilita WHERE Giorno = ?");
    $queryControllo->execute(array($data));

    $postiPrenotati = getNumeroPostiPrenotati($data);

    if(isset($_POST["Elimina"])){
        if(!($queryControllo->fetch())) {
            erroreJSON("Disponibilità non trovata");
            return;
        }
        if($postiPrenotati > POSTI_DISPONIBILI_DEFAULT) {
            //NON posso eliminare la disponibilità perché ci sono già delle prenotazioni il cui totale dei posti supera
            //il numero di posti disponibili di default (che è quello che andrei a ripristinare)
            erroreJSON("Non è stato possibile ripristinare la disponibilità in quanto esistono già delle prenotazioni per un totale di ".$postiPrenotati." posti.");
            return;
        }

        $queryDelete = $db->prepare("DELETE FROM Disponibilita WHERE Giorno = ?");
        if($queryDelete->execute(array($data))) {
            $db->commit();
            successoJSON("Disponibilità predefinita ripristinata");
            return;
        }
        $db->rollBack();
        erroreJSON("Non è stato possibile ripristinare la disponibilità.");
        return;
    }

    if(!$data){
        erroreJSON("Data non valida");
        return;
    }
    $posti = abs(filter_var($_POST["posti"], FILTER_SANITIZE_NUMBER_INT));
    if($posti == POSTI_DISPONIBILI_DEFAULT) {
        erroreJSON("Non puoi selezionare un numero posti uguale al valore predefinito");
        return;
    }


    if($queryControllo->fetch()) {
        erroreJSON("Disponibilità per la data selezionata già modificata");
        return;
    }
    //controllo se la data selezionata è del passato esclusa la data corrente
    if(!(dataFutura($data))) {
        erroreJSON("Data selezionata non valida");
        return;
    }

    if($postiPrenotati > $posti) {
        //NON posso modificare la disponibilità perché ci sono già delle prenotazioni il cui totale dei posti supera
        //il numero di posti disponibili che vorrei inserire
        erroreJSON("Non è stato possibile modificare la disponibilità in quanto esistono già delle prenotazioni per un totale di ".$postiPrenotati." posti.");
        return;
    }

    $queryInserimento = $db->prepare("INSERT INTO Disponibilita VALUES (?,?)");
    if($queryInserimento->execute(array($data,$posti))) {
        $db->commit();
        successoJSON("Disponibilità modificata con successo");
        return;
    }
    $db->rollBack();
    erroreJSON("Errore durante l'inserimeto.");
    return;
}

if(isset($_POST["confermaPagamento"])) {
    settaPagato($_POST["codicePrenotazione"]);
    return;
}
if(isset($_POST["nuovaMacro"])) {
    $nomeMacroattivita = filter_var($_POST["nome-macro"], FILTER_SANITIZE_STRING);
    $idMacro = filter_var($_POST["idMacro"], FILTER_SANITIZE_NUMBER_INT);

    $output = "";
    $output .= file_get_contents("template/admin/settore_macroattivita.html");
    $output = str_replace("[#NOME-MACRO]", $nomeMacroattivita, $output);
    $output = str_replace("[#CODICE-MACRO]", $idMacro, $output);
    $output = str_replace("[#SCHEDE]", '', $output);

    echo $output;
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
    $ris = getMacroattivitaByCodice($idMacro);
    echo json_encode($ris);
    return;
}

if(isset($_POST["convalidaPrenotazione"])) {
    convalidaPrenotazione(filter_var($_POST["prenotazione"],FILTER_SANITIZE_STRING));
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

//tabelle statistiche(rimpiazza i segnaposto [#ATTIVITA-PIU-PRENOTATE])
$HTML = str_replace("[#ATTIVITA-PIU-PRENOTATE]",getAttivitaPiuPrenotate(),$HTML);
//rimpiazza [#UTENTI-PIU-ATTIVI]
$HTML = str_replace("[#UTENTI-PIU-ATTIVI]",utentiPiuAttivi(),$HTML);


//Rimpiazza segna posto [#PRENOTAZIONI-...]
$HTML = str_replace("[#PRENOTAZIONI-ATTIVE]",prenotazioniAttive(),$HTML);
$HTML = str_replace("[#PRENOTAZIONI-PASSATE]",prenotazioniPassate(),$HTML);

$HTML = str_replace("[#TABELLA-GIORNI]",impostaGiorni(),$HTML);


//Scheda impostazioni
$HTML = str_replace("[#POSTI-DISPONIBILI-DEFAULT]",POSTI_DISPONIBILI_DEFAULT,$HTML);

//Footer
$HTML = str_replace("[#MENU-MOBILE]",menuMobile($activeIndex),$HTML);
echo $HTML;

//Funzione che crea la tabella degli utenti
function listaUtenti(){
    global $db;
    $listaUtenti = $db->prepare("SELECT Utenti.ID AS ID, Utenti.Nome as Nome, Utenti.Cognome as Cognome, Utenti.Indirizzo as Indirizzo, Utenti.Username as Username, Utenti.Email as Email FROM Utenti WHERE Utenti.Tipo='Utente' ");
    $listaUtenti->execute();
    $risultato = $listaUtenti->fetchAll();

    $riga="";


    foreach ($risultato as $r){
        $riga .= <<<SCRIVI
<tr id="utente-{$r["ID"]}">    
    <td>{$r["Nome"]}</td>
    <td>{$r["Cognome"]}</td>
    <td>{$r["Indirizzo"]}</td>
    <td class="username">{$r["Username"]}</td>
    <td>{$r["Email"]}</td>
    <td><button data-target="utente-{$r["ID"]}"  class="btn btn-testo btn-reimposta">Reimposta password</button></td>
    <td><button data-target="utente-{$r["ID"]}" class="btn-cancella" title="Elimina">X</button></td>
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
        $output .= file_get_contents("template/admin/settore_macroattivita.html");
        $output = str_replace("[#NOME-MACRO]", $macroAttivita["Nome"], $output);
        $output = str_replace("[#CODICE-MACRO]", $macroAttivita["Codice"], $output);
        $output = str_replace("[#SCHEDE]", $listaSchede, $output);
    }
    return $output;
}


function getAttivitaPiuPrenotate() {
    global $db;

    $query=$db->prepare("SELECT Attivita.Codice, Attivita.Nome AS NomeAttivita,COUNT(Prenotazioni.Codice)AS NumeroPrenotazioni FROM Attivita,Prenotazioni,Utenti WHERE Attivita.Codice=Prenotazioni.IDAttivita AND Prenotazioni.IDUtente = Utenti.ID AND Utenti.Tipo!='Admin' GROUP BY Attivita.Codice ORDER BY NumeroPrenotazioni DESC LIMIT 5");
    $query->execute();
    $array=$query->fetchAll();

    $row="";
    foreach($array as $item) {
        $row .= <<<RIGA
<tr data-attivita="{$item["Codice"]}">
    <td>{$item["NomeAttivita"]}</td>
    <td class="numero-prenotazioni" data-target="{$item["NomeAttivita"]}">{$item["NumeroPrenotazioni"]}</td>
</tr>
RIGA;
    }

    return $row;

}

function utentiPiuAttivi(){

    global $db;

    $query=$db->prepare("SELECT Utenti.ID AS ID, Utenti.Nome AS Nome, Utenti.Cognome AS Cognome, COUNT(Prenotazioni.Codice) AS NumeroPrenotazioni FROM Prenotazioni, Utenti WHERE Prenotazioni.IDUtente = Utenti.ID AND Utenti.Tipo!='Admin' GROUP BY Nome, Cognome, ID ORDER BY NumeroPrenotazioni DESC LIMIT 5");
    $query->execute();
    $array=$query->fetchAll();

    $row="";
    $counter=1;
    foreach($array as $item) {
        $row .= <<<RIGA
<tr data-user="{$item["ID"]}">
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

    $prenotazioni=$db->prepare("SELECT Prenotazioni.Codice AS CodicePrenotazione,Utenti.ID AS ID, Utenti.Username AS Username, Attivita.Codice AS IDAttivita, Attivita.Nome AS Attivita, Prenotazioni.PostiPrenotati AS Posti, Prenotazioni.Giorno AS Giorno, Prenotazioni.Stato AS Stato, Prenotazioni.Pagamento AS Pagato FROM Utenti, Attivita,Prenotazioni WHERE Utenti.ID=Prenotazioni.IDUtente AND Prenotazioni.IDAttivita=Attivita.Codice AND Giorno>=(SELECT CURDATE()) AND Utenti.Tipo<>'Admin' AND Prenotazioni.Stato<>'Cancellata' ORDER BY Giorno, Attivita, Username ASC");
    $prenotazioni->execute();
    $arrayPrenotazioni=$prenotazioni->fetchAll();
    impostaTestoPagamento($arrayPrenotazioni);
    $row="";
    foreach ($arrayPrenotazioni as $riga) {
        $riga["Giorno"] = convertiDataToOutput($riga["Giorno"]);

        if($riga["Pagato"]=="Non pagato") {
            $row .= <<<RIGA
            <tr id="{$riga["CodicePrenotazione"]}" data-attivita="{$riga["IDAttivita"]}" data-user="{$riga["ID"]}">
                 <td>{$riga["Username"]}</td>
                 <td>{$riga["Attivita"]}</td>
                 <td>{$riga["Posti"]}</td>
                 <td>{$riga["Giorno"]}</td>
                 <td class="stato">{$riga["Stato"]}</td>
                 <td>{$riga["Pagato"]}</td>
                 <td><button data-target="{$riga["CodicePrenotazione"]}" class="btn btn-primary pay">Conferma Pagamento</button></td>
                 <td><button data-target="{$riga["CodicePrenotazione"]}" class="btn-cancella" title="Elimina">X</button></td>
            </tr>
RIGA;
         }
         else {
            $row .= <<<RIGA
            <tr id="{$riga["CodicePrenotazione"]}" data-attivita="{$riga["IDAttivita"]}" data-user="{$riga["ID"]}">
                 <td>{$riga["Username"]}</td>
                 <td>{$riga["Attivita"]}</td>
                 <td>{$riga["Posti"]}</td>
                 <td>{$riga["Giorno"]}</td>
                 <td class="stato">{$riga["Stato"]}</td>
                 <td>{$riga["Pagato"]}</td>
                 <td>Pagamento effettuato</td>
                 <td><button data-target="{$riga["CodicePrenotazione"]}" class="btn-cancella" title="Elimina">X</button></td>
            </tr>
RIGA;
        }
    }
    return $row;
}

function prenotazioniPassate(){
    global $db;

    $prenotazioni=$db->prepare("SELECT Prenotazioni.IDAttivita, Prenotazioni.Codice AS CodicePrenotazione, Utenti.Nome  AS Utente, Utenti.ID AS ID , Attivita.Nome AS Attivita, Prenotazioni.PostiPrenotati AS Posti, Prenotazioni.Giorno AS Giorno, Prenotazioni.Stato AS Stato, Prenotazioni.Pagamento AS Pagato 
FROM Utenti, Attivita,Prenotazioni WHERE Utenti.ID=Prenotazioni.IDUtente AND Prenotazioni.IDAttivita=Attivita.Codice AND Giorno<(SELECT CURDATE()) ORDER BY Giorno, Attivita, Utente ASC ");
    $prenotazioni->execute();
    $arrayPrenotazioni=$prenotazioni->fetchAll();
    impostaTestoPagamento($arrayPrenotazioni);
    $row="";
    foreach ($arrayPrenotazioni as $riga){
        $riga["Giorno"] = convertiDataToOutput($riga["Giorno"]);
        $row .= <<<RIGA
<tr data-attivita="{$riga["IDAttivita"]}" data-user="{$riga["ID"]}">
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
        successoJSON("Pagamento confermato con successo");
        return;
        }

    $db->rollBack();
    erroreJSON("Non è stato possibile confermare il pagamento");
    return;

}

function impostaGiorni(){
    global $db;

    $giorni=$db->prepare("SELECT * FROM Disponibilita");
    $giorni->execute();
    $arrayGiorni=$giorni->fetchAll();

    $riga="";

    foreach ($arrayGiorni as $g){
        $g["Giorno"] = convertiDataToOutput($g["Giorno"]);
        $giornoID = filter_var($g["Giorno"], FILTER_SANITIZE_NUMBER_INT);
        $riga .= <<<DAY
<tr id="{$giornoID}">
    <td>{$g["Giorno"]}</td>
    <td>{$g["PostiDisponibili"]}</td>
    <td><button data-target="{$giornoID}" class="btn-cancella" title="Elimina">X</button></td>
</tr>
DAY;
    }
    return $riga;
}