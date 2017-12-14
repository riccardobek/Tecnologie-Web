<?php
require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
require_once "php/funzioni/funzioni_attivita.php";
$activeIndex = 0;
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
    <link rel="stylesheet" type="text/css"  href="css/mobile.css" media="handheld, screen and (max-width:768px), only screen and (max-device-width:768px)"/>
</head>
<body ontouchstart> <!-- ontouchstart fixa il comportamento degli eventi touch su Safari per iOS -->
    <?php intestazione($activeIndex);?>
    <div id="content">
        <img  src="images/fiume_montagne.png" class="banner">
         <div class="odd">
              <div class="length-wrapper">
                 <h1>Onda Selvaggia</h1>

                 <p>
                     Vieni ad apprendere le meraviglie del Brenta! I nostri istruttori sono pronti ad accoglierti
                     e a farti appasionare ai nostri sport. Le attività che hanno dato inizio alla nostra associazione sono
                     canottaggio, 
                 </p>

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
                             <div class="img-wrapper">
                                <img src="images/attivita/index/{$macroAttivita["Immagine"]}">
                             </div>
                             
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
    <?php footer($activeIndex);?>
</body>
</html>