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
    intestazione(1);
    ?>

    <div id="content">
        <div class="length-wrapper">
            <h1>Attivit&agrave; offerte</h1>
            <p>
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
                ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident."
            </p>
        </div>
        <?php
        $class = array("odd","even");
        $classIndex = false;
        $listaMacroAttivita = getMacroattivita();

        foreach($listaMacroAttivita as $macro) {
            echo <<<MACROATTIVITA
            
        <div class="macroattivita {$class[$classIndex]}" id="{$macro["Ancora"]}">
            <div class="length-wrapper">
                <img class="banner" src="images/attivita/banner/{$macro["Banner"]}" alt="{$macro["Nome"]} - immagine promozionale">
                <div class="content-wrapper">
                    <h1>{$macro["Nome"]}</h1>
                    <p>
                        {$macro["Descrizione"]}
                    </p>
                </div>
MACROATTIVITA;
            foreach($macro["Attivita"] as $attivita)
            echo <<<ATTIVITA
            
                <div class="attivita">
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
        </div>
MACROATTIVITA;
            $classIndex = !$classIndex;
        }
        ?>
        
    </div>

    <?php footer();?>

</body>
</html>
