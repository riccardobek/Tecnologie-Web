<?php
require_once "php/funzioni/funzioni_pagina.php";
$activeIndex = 2;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/favicon.ico"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900" rel="stylesheet">
    <title>Onda Selvaggia - Chi Siamo</title>
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css"  href="css/mobile.css" media="handheld, screen and (max-width:768px), only screen and (max-device-width:768px)"/>
</head>
<body>
<?php intestazione($activeIndex);?>
    <div id="content">
        <div class="even">
            <div class="length-wrapper">
                <h1>Chi siamo</h1>
                <p>
                Questa attività, nata nel 1996 dalla passione per gli sport fluviali dei suoi fondatori (Chicco e Sonia), Onda Selvaggia® è una miscela di energia e competenza, di simpatia e professionalità.
                Situata nella valle del Brenta, il Centro Onda Selvaggia® offre la possibilità di praticare sport fluviali rimanendo a stretto contatto con la natura e in un contesto
                denso di richiami storici e di tradizioni culturali. Offriamo una vasta gamma di attività aperte a tutti: Canoa, Rafting, HydroSpeed con la possibilità di prenotare
                per un periodo di tempo prolungato grazie alle offerte Week-end e Settimane Multisport.
                </p>

                <p>Per nessuna di queste attività è richiesta una particolare preparazione fisica o tecnica, complice anche la natura placida del fiume Brenta.
                    Si avranno a disposizione le migliori attrezzature ed equipaggiamenti che il Centro Sportivo ha da offrire.
                </p>
            </div>
        </div>
    </div>
    <?php footer($activeIndex);?>
</body>
</html>