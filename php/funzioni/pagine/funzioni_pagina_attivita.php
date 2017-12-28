<?php
function stampaAttivita() {
    $class = array("odd","even");
    $classIndex = false;
    $listaMacroAttivita = getMacroattivita();

    $output = "";

    foreach($listaMacroAttivita as $macro) {
        $output.= file_get_contents("template/attivita/sezione_macroattivita.html");
        $output = str_replace("[#CLASS_MACROATTIVITA]",$class[$classIndex], $output);
        $output = str_replace("[#ID_MACROATTIVITA]",$macro["Ancora"], $output);
        $output = str_replace("[#MACRO_BANNER]",$macro["Banner"], $output);
        $output = str_replace("[#MACRO_NOME]",$macro["Nome"], $output);
        $output = str_replace("[#MACRO_DESCRIZIONE]",$macro["Descrizione"], $output);

        $sottoattivita = "";
        foreach($macro["Attivita"] as $attivita) {
            $sottoattivita  .= file_get_contents("template/attivita/sezione_sottoattivita.html");
            $sottoattivita  = str_replace("[#NOME-SOTTOATTIVITA]", $attivita["Nome"], $sottoattivita );
            $sottoattivita  = str_replace("[#DESCRIZIONE-SOTTOATTIVITA]", $attivita["Descrizione"], $sottoattivita );
            $sottoattivita  = str_replace("[#CODICE-SOTTOATTIVITA]", $attivita["Codice"], $sottoattivita );
            $sottoattivita  = str_replace("[#PREZZO-SOTTOATTIVITA]", $attivita["Prezzo"], $sottoattivita );
        }
        $output = str_replace("[#SOTTOATTIVITA]", $sottoattivita, $output);
        $classIndex = !$classIndex;
    }

    return $output;
}
