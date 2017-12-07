<?php

function intestazione($titolo,$stylesheet="") {
    echo <<<HEADER
    
    <div id="menu-wrapper">
        <div id="menu" class="length-wrapper">
            <a href="registrazione.html" class="button">Registrati</a>
            <a href="login.html" class="button">Login</a>

            <ul>
                <li><a href="index.html"> Home</a></li>
                <li class="active">Attivit&agrave;</li>
                <li><a href="chi_siamo.html">Chi siamo</a></li>
                <li><a href="contattaci.html">Contattaci</a></li>
            </ul>
        </div>
    </div>
    <div id="header">
        <div class="length-wrapper">
            <img src="images/new_logo.png" alt="Onda Selvaggia - Logo">
            <div id="contatti-header"><span><img src="images/icona_telefono.png" alt="telefono fisso"> 0424 99581 |
                <img src="images/icona_cellulare.png" alt="cellulare"> 3473767729</span>
                <span><img src="images/icona_email.png" alt="email"> info@ondaselvaggia.com</span>
            </div>
        </div>
    </div>
HEADER;
}

function footer() {
    echo <<<FOOTER
FOOTER;
}