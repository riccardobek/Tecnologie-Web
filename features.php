<?php
$stringa = "Ciao\n\n sono          \n       \n\n\n\n\n\n      \n mario";

function replacestaminchia($stringa) {
    $pattern = "/<br>( )*<br>/";

    $stringa = str_replace(array("\r\n", "\r", "\n"), "<br>", $stringa);

    if(preg_match($pattern, $stringa)) {
        $stringa = preg_replace($pattern, "<br>", $stringa);
        $stringa = replacestaminchia($stringa);
    }
    return $stringa;
}

$stringa = preg_replace('/\s+/', ' ',replacestaminchia($stringa));

echo $stringa;