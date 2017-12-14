<?php
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

    )
);
/**
 * Questa funzione crea un elemento del menu tenendo conto di numerosi paramentri
 *
 * @param $index indica l'elemento del menù che si vuole creare nell'array menuElements
 * @param $activeIndex indica l'elemento del menù che è attivo. Se activeIndex = index, allora non viene creato un
 * collegamento ma solo un elemento statico
 * @param $utenteLoggato indica se l'utente è loggato o no
 */
function creaElementoMenu($index, $activeIndex, $utenteLoggato) {
    global $menuElements;
    $element = "";

    if($menuElements[$index]["LoginDipendente"] && !$menuElements[$index]["VisibileGuest"] && !$utenteLoggato) {
        //Se l'elemento del menu può essere visualizzato solo dagli utenti loggati, e l'utente non è loggato allora non
        //lo visualizzo
        return $element;
    }

    else if($menuElements[$index]["LoginDipendente"] && $menuElements[$index]["VisibileGuest"] && $utenteLoggato) {
        //Se l'elemento del menu può essere visualizzato solo dagli utenti non loggati (ad esempio link alla pagina
        //"login") e l'utente corrente è loggato, allora non lo visualizzo
        return $element;
    }

    if($menuElements[$index]["Pulsante"]) {
        //L'elemento che devo creare è un pulsante, quindi un "a" o uno "span" (in base al fatto che sia active o no
        $element = ($index == $activeIndex) ? <<<ELEMENTO
        <span class="button">{$menuElements[$index]["Nome"]}</span>

ELEMENTO
            : <<<ELEMENTO
        <a href="{$menuElements[$index]["URL"]}" class="button">{$menuElements[$index]["Nome"]}</a>

ELEMENTO;
    }

    else {
        //L'elemento che devo creare è un "li" che contiene o no un link (in base al fatto che sia o no active)
        $element = ($index == $activeIndex) ? <<<ELEMENTO
        <li class="active">{$menuElements[$index]["Nome"]}</li>

ELEMENTO
            : <<<ELEMENTO
        <li><a href="{$menuElements[$index]["URL"]}">{$menuElements[$index]["Nome"]}</a></li>

ELEMENTO;
    }

    return $element;
}


function intestazione($activeIndex) {
    global $menuElements;
    echo <<<HEADER
    
    <div id="menu-wrapper">
        <div id="menu" class="length-wrapper">
HEADER;
    for($i=0;$i<count($menuElements);$i++)
        if($menuElements[$i]["Pulsante"])
            echo creaElementoMenu($i,$activeIndex, false);
    echo <<<HEADER
            <ul>
HEADER;
    for($i=0;$i<count($menuElements);$i++)
        if(!$menuElements[$i]["Pulsante"])
            echo creaElementoMenu($i,$activeIndex, false);
    echo <<<HEADER
            </ul>
        </div>
    </div>
    <div id="header">
        <div class="length-wrapper">
            <img src="images/new_logo.png" alt="Onda Selvaggia - Logo">
            <a href="#footer" class="hamburger"><img src="images/icone/icona_menu.png" alt="Mostra menu"></a>
            <div id="contatti-header"><span><img src="images/icone/icona_telefono.png" alt="telefono fisso"> 0424 99581 |
                <img src="images/icone/icona_cellulare.png" alt="cellulare"> 3473767729</span>
                <span><img src="images/icone/icona_email.png" alt="email"> info@ondaselvaggia.com</span>
            </div>
        </div>
    </div>
HEADER;
}

function footer($activeIndex)
{
    global $menuElements;

    echo <<<FOOTER
    <div id="footer" class="even">
FOOTER;
    for ($i = 0; $i < count($menuElements); $i++) {
        if ($menuElements[$i]["Pulsante"])
            echo creaElementoMenu($i, $activeIndex, false);
    }
    echo <<<FOOTER
            <ul>

FOOTER;
    for ($i = 0; $i < count($menuElements); $i++) {
        if (!$menuElements[$i]["Pulsante"])
            echo creaElementoMenu($i, $activeIndex, false);
    }
    echo <<<FOOTER
            
            </ul>
    </div>
FOOTER;

}