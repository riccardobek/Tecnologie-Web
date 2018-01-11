<?php
require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
if(false)
    echo "O";
else
    paginaSuccesso("Login effettuato con successo","index.php","Prosegui la navigazione");
die();
?><!--
<!DOCTYPE html>
<html>
<head>
    <title>Instascan</title>
    <script type="text/javascript" src="js/instascan.min.js"></script>
</head>
<body>
<video id="preview"></video>
<script type="text/javascript">
    var scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    scanner.addListener('scan', function (content) {
        alert(content);
    });
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            console.error('No cameras found.');
        }
    }).catch(function (e) {
        console.error(e);
    });
</script>
</body>
</html>

-->