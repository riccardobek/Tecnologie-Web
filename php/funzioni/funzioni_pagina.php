<?php
session_start();
require_once "funzioni_sicurezza.php";


$menuElements = array(
    array(
        "Nome" => "Salta intestazione",
        "URL" => "#content",
        "Icona" => PERCORSO_RELATIVO."images/icone/icona_home.png",
        "Pulsante" => false,
        "LoginDipendente" => false,
        "Mobile"=>false,
        "ID"=>"salta-intestazione"
    ),
    array(
        "Nome" => "Home",
        "URL" => "index.php",
        "Icona"=> PERCORSO_RELATIVO."images/icone/icona_home.png",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Attivit&agrave;",
        "URL" => "attivita.php",
        "Icona"=> PERCORSO_RELATIVO."images/icone/icona_attivita.png",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Contattaci",
        "URL" => "contattaci.php",
        "Icona"=> PERCORSO_RELATIVO."images/icone/icona_contattaci.png",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Registrazione",
        "URL" => "registrazione.php",
        "Icona"=> PERCORSO_RELATIVO."images/icone/icona_registrazione.png",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>true
    ),
    array(
        "Nome" => "Login",
        "URL" => "login.php",
        "Icona"=> PERCORSO_RELATIVO."images/icone/icona_login.png",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>true

    ),
    array(
        "Nome" => "Logout",
        "URL" => "php/do_logout.php",
        "Icona"=> PERCORSO_RELATIVO."images/icone/icona_logout.png",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>false
    ),
    array(
        "Nome" => "Pannello utente",
        "URL" => "pannello_utente.php",
        "Icona"=> PERCORSO_RELATIVO."images/icone/icona_logout.png",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>false
    ),
    array(
        "Nome" => "Pannello admin",
        "URL" => "pannello_admin.php",
        "Icona"=> PERCORSO_RELATIVO."images/icone/icona_logout.png",
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
    $activeIndex++;

    $tabIndex = $index+1;
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
        $element = ($index == $activeIndex) ?
            <<<ELEMENTO
<span class="button">{$menuElements[$index]["Nome"]}</span>
ELEMENTO
            :
            <<<ELEMENTO
<a href="{$menuElements[$index]["URL"]}"  tabindex="{$tabIndex}" class="button">{$menuElements[$index]["Nome"]}</a>
ELEMENTO;

    }

    else {
        //L'elemento che devo creare è un "li" che contiene o no un link (in base al fatto che sia o no active)
        $element = ($index == $activeIndex) ?
            <<<ELEMENTO
<li class="active">{$menuElements[$index]["Nome"]}</li>
ELEMENTO
            :
            <<<ELEMENTO
<li><a href="{$menuElements[$index]["URL"]}"  tabindex="{$tabIndex}" [#ID]>{$menuElements[$index]["Nome"]}</a></li>
ELEMENTO;

    }

    (isset($menuElements[$index]["ID"])) ?
        $element = str_replace("[#ID]","id='{$menuElements[$index]["ID"]}'",$element) :
        $element = str_replace("[#ID]","",$element);

    return $element;
}


function intestazione($activeIndex) {
    global $menuElements;
//    print_r($menuElements);

    $INTESTAZIONE = file_get_contents(PERCORSO_RELATIVO."template/pagina/intestazione.html");
    $INTESTAZIONE = str_replace("[#PERCORSO_RELATIVO]", PERCORSO_RELATIVO, $INTESTAZIONE);

    $PULSANTI = "";
    $VOCI_MENU="";

    for($i=0;$i<count($menuElements);$i++) {
        if(isAdmin()){if($i==7) $i++;}
        if ($menuElements[$i]["Pulsante"])
            $PULSANTI .= creaElementoMenu($i, $activeIndex);
        else
            $VOCI_MENU .= creaElementoMenu($i,$activeIndex);
        if(!isAdmin()){if($i==7) $i++;}
    }


    $INTESTAZIONE = str_replace("[#PULSANTI_INTESTAZIONE]",$PULSANTI,$INTESTAZIONE);
    $INTESTAZIONE = str_replace("[#VOCI_MENU]",$VOCI_MENU,$INTESTAZIONE);

    return $INTESTAZIONE;
}



/*Genera in modo dinamico l'elenco delle voci del menu */

function creaElementoMenuMobile($index, $activeIndex){
    $activeIndex++;
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

    else if(isset($menuElements[$index]["Mobile"]) && !$menuElements[$index]["Mobile"]) {
        return $element;
    }


    $element = ($index == $activeIndex) ?
<<<ELEMENTO
    <li class="active"><span><img class="icona-menu" src='{$menuElements[$index]["Icona"]}' alt="{$menuElements[$index]["Nome"]} - icona">{$menuElements[$index]["Nome"]}</span></li>\n
ELEMENTO
            :
<<<ELEMENTO
    <li><a href="{$menuElements[$index]["URL"]}"><img class="icona-menu" src='{$menuElements[$index]["Icona"]}' alt="{$menuElements[$index]["Nome"]} - icona">{$menuElements[$index]["Nome"]}</a></li>\n
ELEMENTO;

    return $element;
}



function menuMobile($activeIndex)
{
    global $menuElements;
    $MENU_MOBILE = file_get_contents(PERCORSO_RELATIVO."template/pagina/menu_mobile.html");
    $MENU_MOBILE = str_replace("[#PERCORSO_RELATIVO]", PERCORSO_RELATIVO, $MENU_MOBILE);

    $VOCI_MENU = "";
    for ($i = 0; $i < count($menuElements); $i++) {
        if(isAdmin()){if($i==7) $i++;}
        $VOCI_MENU.=creaElementoMenuMobile($i, $activeIndex);
        if(!isAdmin()){if($i==7) $i++;}
    }

    $MENU_MOBILE = str_replace("[#VOCI-MENU]", $VOCI_MENU, $MENU_MOBILE);

    return $MENU_MOBILE;
}


function paginaErrore($messaggio="Si è verificato un errore. Riprova più tardi.",$href="index.php",$testoLink="Torna alla home") {
    $HTML_INTESTAZIONE = intestazione(INF);

    /*Richiamo pagina contatti*/
    $HTML = file_get_contents(PERCORSO_RELATIVO."template/pagina/errore.html");

    $HTML = str_replace("[#PERCORSO_RELATIVO]", PERCORSO_RELATIVO, $HTML);

    $HTML = str_replace("[#MESAGGIO-ERRORE]", $messaggio, $HTML);
    $HTML = str_replace("[#HREF-LINK]", $href, $HTML);
    $HTML = str_replace("[#TESTO-LINK]", $testoLink, $HTML);

    /*Rimpiazza il segnaposto con il menù*/
    $HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

    /*Footer*/
    $HTML = str_replace("[#MENU-MOBILE]",menuMobile(INF),$HTML);
    echo $HTML;
}

function paginaSuccesso($messaggio,$href,$testoLink,$targetBlank=false) {
    $HTML_INTESTAZIONE = intestazione(INF);

    /*Richiamo pagina contatti*/
    $HTML = file_get_contents(PERCORSO_RELATIVO."template/pagina/successo.html");

    $HTML = str_replace("[#PERCORSO_RELATIVO]", PERCORSO_RELATIVO, $HTML);

    $HTML = str_replace("[#MESAGGIO-SUCCESSO]", $messaggio, $HTML);
    $HTML = str_replace("[#HREF-LINK]", $href, $HTML);
    $HTML = str_replace("[#TESTO-LINK]", $testoLink, $HTML);

    $HTML = str_replace("[#TARGET]", $targetBlank ? "_blank" : "_self", $HTML);

    /*Rimpiazza il segnaposto con il menù*/
    $HTML = str_replace("[#INTESTAZIONE]",$HTML_INTESTAZIONE, $HTML);

    /*Footer*/
    $HTML = str_replace("[#MENU-MOBILE]",menuMobile(INF),$HTML);
    echo $HTML;
}

function loginRichiesto() {
    if(!isUtenteLoggato()) {
        paginaErrore("Per visualizzare questa pagina effettua prima il login", PERCORSO_RELATIVO."login.php","Vai al login");
        die();
    }
}

function convertiDataToOutput($dataDaConvertire){
    return (new DateTime($dataDaConvertire))->format('d/m/Y');
}

function impostaTestoPagamento(&$listaprenotazioni) {
    foreach($listaprenotazioni as &$prenotazione) {
        if($prenotazione["Pagato"]==0){
            $prenotazione["Pagato"] = 'Non pagato';
        }
        else{
            $prenotazione["Pagato"] = 'Pagato';
        }
    }
}

/**
 * Funzione che, in base al fatto che javascript sia abilitato, stampa un errore o come pagina o come JSON
 * @param $messaggio il messaggio di errore da stampare
 */
function errore($messaggio) {
    global $jsAbilitato;

    $jsAbilitato ? erroreJSON($messaggio) : paginaErrore($messaggio,LINK_PAGINA_ERRORE,TESTO_LINK_PAGINA_ERRORE);
}
