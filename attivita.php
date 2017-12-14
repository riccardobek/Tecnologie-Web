<?php
require_once "php/database.php";
require_once "php/funzioni/funzioni_attivita.php";
require_once "php/funzioni/funzioni_pagina.php";

require_once "php/funzioni/pagine/funzioni_pagina_attivita.php";
$activeIndex = 1;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/favicon.ico"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900" rel="stylesheet">
    <title>Onda Selvaggia - Attività</title>
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css"  href="css/mobile.css" media="handheld, screen and (max-width:768px), only screen and (max-device-width:768px)"/>
</head>
<body>
    <?php
    intestazione($activeIndex);
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
        stampaAttivita();
        ?>
        
    </div>

    <?php footer($activeIndex);?>

</body>
</html>
