<?php
$numero = "34.50";
echo filter_var($numero,FILTER_SANITIZE_NUMBER_FLOAT, array(
  'flags'=>FILTER_FLAG_ALLOW_FRACTION));
?>

<!--
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