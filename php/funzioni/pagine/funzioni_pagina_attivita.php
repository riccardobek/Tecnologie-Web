<?php
function stampaAttivita() {
    $class = array("odd","even");
    $classIndex = false;
    $listaMacroAttivita = getMacroattivita();

    $output = "";

    foreach($listaMacroAttivita as $macro) {
        $output.= <<<MACROATTIVITA
            
        <div class="macroattivita {$class[$classIndex]}" id="{$macro["Ancora"]}">
            <div class="length-wrapper">
                <div class="responsive-banner" style=" background-image: url('images/attivita/banner/{$macro["Banner"]}')">
                </div>
                <img class="banner" src="images/attivita/banner/{$macro["Banner"]}" alt="{$macro["Nome"]} - immagine promozionale">
                <div class="content-wrapper">
                    <h1>{$macro["Nome"]}</h1>
                    <p>
                        {$macro["Descrizione"]}
                    </p>
                </div>
MACROATTIVITA;
        foreach($macro["Attivita"] as $attivita) {

            $output .= file_get_contents("template/attivita/sezioni_attivita.html");
            $output = str_replace("[#ATTIVITA_NOME]", $attivita["Nome"], $output);
            $output = str_replace("[#ATTIVITA_DESCRIZIONE]", $attivita["Descrizione"], $output);
            $output = str_replace("[#ATTIVITA_PREZZO]", $attivita["Prezzo"], $output);
        }

            /*
            $output.= <<<ATTIVITA
            
                <div class="attivita">
                    <h2>{$attivita["Nome"]}</h2>
                    <p>
                        {$attivita["Descrizione"]}
                        <span class="price-tag">Prezzo: {$attivita["Prezzo"]} euro</span>
                    </p>
                    <a class="primary-btn inline-btn">Prenota</a>
                    </div>
                
ATTIVITA;*/

        $output.= <<<MACROATTIVITA
            
            </div>
        </div>
MACROATTIVITA;
        $classIndex = !$classIndex;
    }

    echo $output;
}