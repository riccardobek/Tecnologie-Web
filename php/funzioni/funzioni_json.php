<?php
function erroreJSON($messaggio, $altriDati = array()) {
    $jsonArray = array();
    $jsonArray["stato"] = 0;
    $jsonArray["messaggio"] = $messaggio;

    foreach($altriDati as $key=>$value)
        $jsonArray[$key] = $value;

    echo json_encode($jsonArray);

}

function successoJSON($messaggio, $altriDati = array()) {
    $jsonArray = array();
    $jsonArray["stato"] = 1;
    $jsonArray["messaggio"] = $messaggio;

    foreach ($altriDati as $key => $value)
        $jsonArray[$key] = $value;

    echo json_encode($jsonArray);
}