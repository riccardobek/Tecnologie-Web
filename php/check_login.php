<?php
session_start();

if(isset($_SESSION["logged"])&&$_SESSION["logged"]==1){
    echo json_encode(array("logged"=>true));
    return;
}
else {
    echo json_encode(array("logged"=>false));
    return;
}
