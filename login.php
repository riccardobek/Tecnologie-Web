<?php
require_once "php/funzioni/funzioni_pagina.php";
$activeIndex=5;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/login.css" >
    <link rel="stylesheet" type="text/css"  href="css/mobile.css" media="handheld, screen and (max-width:768px), only screen and (max-device-width:768px)"/>
    <link rel="icon" href="images/favicon.ico">

</head>
    <body>
        <?php intestazione($activeIndex);?>
        <div class="form">
            <form action="php/do_login.php" method="POST">
                <h1>Accedi</h1>
                <div class="field-container">
                    <label for="username">Nome utente: </label>
                    <input type="text" id="username" name="username" placeholder="Nome utente..">
                </div>
                <div class="field-container">
                    <label for="password">Password: </label>
                    <input type="password" id="password" name="password" placeholder="Password..">
                </div>
                <div class="button-holder"><input type="submit" value="Login" class="primary-btn inline-btn" ></div>

                <p>Non sei ancora registrato? <a href="registrazione.php">Registrati</a></p>
            </form>
        </div>
        <?php footer($activeIndex);?>
    </body>
</html>