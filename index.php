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
                      Situata nella valle del Brenta, il Centro Onda Selvaggia® offre la possibilità di praticare sport fluviali
                      rimanendo a stretto contatto con la natura e in un contesto denso di richiami storici e di tradizioni culturali.
                      Questa attività, nata nel 1996 dalla passione per gli sport fluviali dei suoi fondatori (Chicco e Sonia),
                       è una miscela di energia e competenza, di simpatia e professionalità.
                  </p>
                  <p>
                      Vieni a scoprire le meraviglie del Brenta! I nostri istruttori sono pronti ad accoglierti
                      e a farti appasionare ai nostri sport. </br>
                      Disponiamo di una vasta gamma di attività aperte a tutti: Canoa, Rafting, HydroSpeed con la possibilità di prenotare
                      per un periodo di tempo prolungato grazie alle offerte Week-end e Settimane Multisport.
                 </p>
                  <p>
                      Per nessuna di queste attività è richiesta una particolare preparazione fisica o tecnica, complice anche la natura
                      placida del fiume Brenta.Si avranno a disposizione le migliori attrezzature ed equipaggiamenti che il Centro
                      Sportivo ha da offrire.
                  </p>

             </div>
         </div>

         <div class="even">
             <div class="length-wrapper">
                 <h1>Attivit&agrave; offerte</h1>
                 <?php
                 $listaMacroAttivita = getMacroattivita();
                 foreach($listaMacroAttivita as $macroAttivita) {
                     $macroAttivita["Descrizione"]= substr($macroAttivita["Descrizione"], 0, 202)."...";
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
        <div class="odd">
            <div class="length-wrapper">
            <div id="box">
                <img src="images/icona_professionalita.png" class="box_icon" alt=" "/>
                <div class="box_title">
                    <h2>Professionalità</h2>
                </div>
                <p>I nostri collaboratori sono dotati di titoli e brevetti riconosciuti a livello Nazionale nelle discipline fluviali.
                    Ogni anno si prestano ad aggiornare le loro conoscenze per assicurare ai nostri clienti il massimo divertimento
                    con altrettanta preparazione.
                </p>
            </div>

            <div id="box">
                <img src="images/icona_sicurezza.png" class="box_icon" alt=" "/>
                <h2 class="box_title">Sicurezza</h2>

                    <p>Utilizziamo equipaggiamento e attrezzatura tecnica omologata secondo le direttive EN UNI ISO 12402-5 oppure
                        CE EN 1385 per le attività sia Fluviali e secondo le normative vigenti per le attività Montane. Aggiornamenti
                        continui dei nostri Collaboratori per essere sempre informati sulle nuove procedure di sicurezza.
                    </p>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php footer($activeIndex);?>
</body>
</html>