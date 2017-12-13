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
    
    </body>
</html>
