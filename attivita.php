<?php
require_once "php/database.php";
require_once "php/funzioni/funzioni_attivita.php";
require_once "php/funzioni/funzioni_pagina.php";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta charset="UTF-8">
    <link rel="icon" href="images/favicon.ico"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900" rel="stylesheet">
    <title>Onda Selvaggia - Attività</title>
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
</head>
<body>
    <?php
    intestazione("Attività");
    ?>

    <div class="length-wrapper" id="content">
        <h1>Attivit&agrave; offerte</h1>
        <p>
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
            ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident."
        </p>

        <?php
        $listaMacroAttivita = getMacroattivita($db);
        foreach($listaMacroAttivita as $macro) {
            echo <<<MACROATTIVITA
            
        <div class="attivita">
            <img class="banner-attivita" src="images/rafting-banner.png">
            <div class="content-wrapper">
                <h1>{$macro["Nome"]}</h1>
                <p>
                    {$macro["Descrizione"]}
                </p>
            </div>
MACROATTIVITA;
            foreach($macro["Attivita"] as $attivita)
            echo <<<ATTIVITA
            
            <div class="subattivita">
                <h2>{$attivita["Nome"]}</h2>
                <p>
                    {$attivita["Descrizione"]}
                    <span>Prezzo: {$attivita["Prezzo"]} euro</span>
                </p>
                <a class="button">Prenota</a>
            </div>
ATTIVITA;
            echo <<<MACROATTIVITA
            
        </div>
MACROATTIVITA;
        }
        ?>
        
    </div>

    <?php footer();?>

</body>
</html>
