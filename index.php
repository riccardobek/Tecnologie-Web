<?php
require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
require_once "php/funzioni/funzioni_attivita.php";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/favicon.ico"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900" rel="stylesheet">
    <title>Onda Selvaggia - Home</title>
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css"  href="css/mobile.css" media="handheld, screen and (max-width:480px), only screen and (max-device-width:480px)"/>
</head>
<body ontouchstart> <!-- ontouchstart fixa il comportamento degli eventi touch su Safari per iOS -->
    <?php intestazione(0);?>
    <div id="content">
        <img  src="images/fiume_montagne.png" class="banner">
         <div class="odd">
              <div class="length-wrapper">
                 <h1>Titolo</h1>

                 Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                 sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
                 ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                 cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                 sunt in culpa qui officia deserunt mollit anim id est laborum." "Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                 sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
                 ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                 cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                 sunt in culpa qui officia deserunt mollit anim id est laborum." "Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                 sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
                 ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                 cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                 sunt in culpa qui officia deserunt mollit anim id est laborum." "Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                 sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
                 ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                 cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                 sunt in culpa qui officia deserunt mollit anim id est laborum."
                 "Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                 sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
                 ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                 cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                 sunt in culpa qui officia m ipsum dolor sit amet, consectetur adipiscing elit,

             </div>
         </div>

         <div class="even">
             <div class="length-wrapper">
                 <h1>Attivit&agrave; offerte</h1>
                 <?php
                 $listaMacroAttivita = getMacroattivita();
                 foreach($listaMacroAttivita as $macroAttivita) {
                     echo <<<MACROATTIVITA
                 <div class="column">
                     <div class="wrapper" id="{$macroAttivita["Ancora"]}">
                         <div class="title-wrapper">
                             <h1>{$macroAttivita["Nome"]}</h1>
                             <img src="images/attivita/index/{$macroAttivita["Immagine"]}">
                         </div>
                         <p>
                             {$macroAttivita["Descrizione"]}
                         </p>
                         <div class="button-holder">
                            <a  class="primary-btn index-btn"  href="attivita.php#{$macroAttivita["Ancora"]}">Dettagli</a>
                         </div>
                         
                    </div>
                 </div>

MACROATTIVITA;

                 }
                 ?>
             </div>
             <div class="clearfix"></div>
         </div>
    </div>
    <?php footer(0);?>
</body>
</html>