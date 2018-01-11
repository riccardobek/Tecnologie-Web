<?php
session_start();
require_once "funzioni_sicurezza.php";


$menuElements = array(
    array(
        "Nome" => "Home",
        "URL" => "index.php",
        "Icona"=> "images/icone/icona_home.png",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Attivit&agrave;",
        "URL" => "attivita.php",
        "Icona"=> "images/icone/icona_attivita.png",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Contattaci",
        "URL" => "contattaci.php",
        "Icona"=> "images/icone/icona_contattaci.png",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Registrazione",
        "URL" => "registrazione.php",
        "Icona"=> "images/icone/icona_registrazione.png",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>true
    ),
    array(
        "Nome" => "Login",
        "URL" => "login.php",
        "Icona"=> "images/icone/icona_login.png",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>true

    ),
    array(
        "Nome" => "Logout",
        "URL" => "php/do_logout.php",
        "Icona"=> "images/icone/icona_logout.png",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>false
    ),
    array(
        "Nome" => "Pannello utente",
        "URL" => "pagina_utente.php",
        "Icona"=> "images/icone/icona_logout.png",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>false
    ),
    array(
        "Nome" => "Pannello amministrazione",
        "URL" => "pannello_admin.php",
        "Icona"=> "images/icone/icona_logout.png",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>false
    )
);
/**
 * Questa funzione crea un elemento del menu tenendo conto di numerosi paramentri
 *
 * @param $index indica l'elemento del menù che si vuole creare nell'array menuElements
 * @param $activeIndex indica l'elemento del menù che è attivo. Se activeIndex == index, allora non viene creato un
 * collegamento ma solo un elemento statico
 */
function creaElementoMenu($index, $activeIndex) {
    global $menuElements;
    $element = "";

    if($menuElements[$index]["LoginDipendente"] && !$menuElements[$index]["VisibileGuest"] && !isUtenteLoggato()) {
        //Se l'elemento del menu può essere visualizzato solo dagli utenti loggati, e l'utente non è loggato allora non
        //lo visualizzo
        return $element;
    }

    else if($menuElements[$index]["LoginDipendente"] && $menuElements[$index]["VisibileGuest"] && isUtenteLoggato()) {
        //Se l'elemento del menu può essere visualizzato solo dagli utenti non loggati (ad esempio link alla pagina
        //"login") e l'utente corrente è loggato, allora non lo visualizzo
        return $element;
    }

    if($menuElements[$index]["Pulsante"]) {
        //L'elemento che devo creare è un pulsante, quindi un "a" o uno "span" (in base al fatto che sia active o no)
        $element = ($index == $activeIndex) ? file_get_contents("template/menu/pulsante_attivo.html")
            : file_get_contents("template/menu/pulsante.html");
    }

    else {
        //L'elemento che devo creare è un "li" che contiene o no un link (in base al fatto che sia o no active)
        $element = ($index == $activeIndex) ? file_get_contents("template/menu/voce_attiva.html")
            : file_get_contents("template/menu/voce.html");
    }

    $element = str_replace("[#NOME_ELEMENTO]",$menuElements[$index]["Nome"],$element);
    $element = str_replace("[#LINK_ELEMENTO]",$menuElements[$index]["URL"],$element);

    return $element;
}


function intestazione($activeIndex) {
    global $menuElements;
//    print_r($menuElements);

    $INTESTAZIONE = file_get_contents("template/intestazione.html");

    $PULSANTI = "";
    $VOCI_MENU="";

    for($i=0;$i<count($menuElements);$i++) {
        if ($menuElements[$i]["Pulsante"])
            $PULSANTI .= creaElementoMenu($i, $activeIndex);
        else
            $VOCI_MENU .= creaElementoMenu($i,$activeIndex);
    }

    $INTESTAZIONE = str_replace("[#PULSANTI_INTESTAZIONE]",$PULSANTI,$INTESTAZIONE);
    $INTESTAZIONE = str_replace("[#VOCI_MENU]",$VOCI_MENU,$INTESTAZIONE);

    return $INTESTAZIONE;
}



/*Genera in modo dinamico l'elenco delle voci del menu */

function creaElementoMenuMobile($index, $activeIndex){
    global $menuElements;

    $element ="";
    if($menuElements[$index]["LoginDipendente"] && !$menuElements[$index]["VisibileGuest"] && !isUtenteLoggato()) {
        //Se l'elemento del menu può essere visualizzato solo dagli utenti loggati, e l'utente non è loggato allora non
        //lo visualizzo
        return $element;
    }

    else if($menuElements[$index]["LoginDipendente"] && $menuElements[$index]["VisibileGuest"] && isUtenteLoggato()) {
        //Se l'elemento del menu può essere visualizzato solo dagli utenti non loggati (ad esempio link alla pagina
        //"login") e l'utente corrente è loggato, allora non lo visualizzo
        return $element;
    }
    $element = ($index == $activeIndex) ?
<<<ELEMENTO
    <li class="active"><span><img class="icona-menu" src='{$menuElements[$index]["Icona"]}'>{$menuElements[$index]["Nome"]}</span></li>\n
ELEMENTO
            :
<<<ELEMENTO
    <li><a href="{$menuElements[$index]["URL"]}"><img class="icona-menu" src='{$menuElements[$index]["Icona"]}'>{$menuElements[$index]["Nome"]}</a></li>\n
ELEMENTO;

    return $element;
}



function menuMobile($activeIndex)
{
    global $menuElements;
    $MENU_MOBILE = file_get_contents("template/menu/menu_mobile.html");
    $VOCI_MENU = "";
    for ($i = 0; $i < count($menuElements); $i++) {
        $VOCI_MENU.=creaElementoMenuMobile($i, $activeIndex);
    }

    $MENU_MOBILE = str_replace("[#VOCI-MENU]", $VOCI_MENU, $MENU_MOBILE);

    return $MENU_MOBILE;
}


function paginaErrore($messaggio="Si è verificato un errore. Riprova più tardi.",$href="index.php",$testoLink="Torna alla home") {
    $HTML_INTESTAZIONE = intestazione(INF);

    /*Richiamo pagina contatti*/
    $HTML = file_get_contents("template/errore.html");

    $HTML = str_replace("[#MESAGGIO-ERRORE]", $messaggio, $HTML);
    $HTML = str_replace("[#HREF-LINK]", $href, $HTML);
    $HTML = str_replace("[#TESTO-LINK]", $testoLink, $HTML);

    /*Rimpiazza il segnaposto con il menù*/
    $HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

    /*Footer*/
    $HTML = str_replace("[#MENU-MOBILE]",menuMobile(INF),$HTML);
    echo $HTML;
}

function prenotazioniUtente() {
    global $db;
    $infoPrenotazioni = $db->prepare("SELECT  Nome Giorno PostiPrenotati FROM Prenotazioni, Attivita WHERE IDUtente = ? AND IDAttivita = Prenotazioni.Codice");
    $infoPrenotazioni->execute(array($_SESSION["Utente"]["ID"]));
    $riga2 = "";
    while($riga = $infoPrenotazioni->fetch()){

    $riga2 .= <<<RIGA
<tr><td>{$infoPrenotazioni["Nome"]}</td><td>{$infoPrenotazioni["Giorno"]}</td><td>{$infoPrenotazioni["PostiPrenotati"]}</td></tr>
RIGA;
    }
    return $riga2;
}