<?php
session_start();
require_once "funzioni_sicurezza.php";

$menuElements = array(
    array(
        "Nome" => "Home",
        "URL" => "index.php",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Attivit&agrave;",
        "URL" => "attivita.php",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Chi siamo",
        "URL" => "chi_siamo.php",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Contattaci",
        "URL" => "contattaci.php",
        "Pulsante"=>false,
        "LoginDipendente"=>false
    ),
    array(
        "Nome" => "Registrazione",
        "URL" => "registrazione.php",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>true
    ),
    array(
        "Nome" => "Login",
        "URL" => "login.php",
        "Pulsante"=>true,
        "LoginDipendente"=>true,
        "VisibileGuest"=>true

    ),
    array(
        "Nome" => "Logout",
        "URL" => "php/do_logout.php",
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


function creaElementoMenuFooter($index, $activeIndex, $odd){
    global $menuElements;
    $class = ($odd) ? "odd" : "even";
    $element = ($index == $activeIndex) ?
<<<ELEMENTO
    <li class="active {$class}">{$menuElements[$index]["Nome"]}</li>\n
ELEMENTO
            :
<<<ELEMENTO
    <li class="{$class}"><a href="{$menuElements[$index]["URL"]}">{$menuElements[$index]["Nome"]}</a></li>\n
ELEMENTO;

    return $element;
}



function footer($activeIndex)
{
    global $menuElements;
    $FOOTER = "";
    $FOOTER.= <<<FOOTER
<div id="menu-mobile" class="even">
    <a href="#header" id="icona-wrapper"><img src="images/icone/icona_chiudi_menu.png" alt="chiudi menu"></a>
    <ul>\n
FOOTER;

    for ($i = 0, $odd = true; $i < count($menuElements); $i++,$odd=!$odd) {
        $FOOTER.=creaElementoMenuFooter($i, $activeIndex, $odd);
    }
$FOOTER.= <<<FOOTER
</ul>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<script type="text/javascript" src="js/global.js"></script>
FOOTER;

    return $FOOTER;

}