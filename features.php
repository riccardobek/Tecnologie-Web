<?php
/*
 *
 * TEST DELLA FUNZIONE DI CONVALIDA DELLA DATA
 */
$dataDaConvertire = "31/11/2017";

if(!preg_match("/^(\d{2})\/(\d{2})\/(\d{4})$/",$dataDaConvertire))
    return false;

$matches = explode("/",$dataDaConvertire);
$dataCalcolata = new DateTime(intval($matches[2])."-".intval($matches[1])."-".intval($matches[0]));

//Se la data è nel formato corretto ma non è valida (ad esempio 31/02/2018) ritorno false
if($dataCalcolata->format("d/m/Y") != $dataDaConvertire) {
    echo "Data calcolata diversa: ";
    print_r($dataCalcolata->format("d/m/Y"));
    print_r($dataDaConvertire);
    return false;
}

echo "TUTTAPOOOOST";