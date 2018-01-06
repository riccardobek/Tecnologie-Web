<?php
function erroreJSON($messaggio) {
    $jsonArray = array();
    $jsonArray["stato"] = 0;
    $jsonArray["messaggio"] = $messaggio;
    echo json_encode($jsonArray);
}

function successoJSON($messaggio) {
    $jsonArray = array();
    $jsonArray["stato"] = 1;
    $jsonArray["messaggio"] = $messaggio;
    echo json_encode($jsonArray);
}