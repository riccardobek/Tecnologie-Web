<?php
define("PERCORSO_RELATIVO","");
require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
require_once "php/tcpdf/tcpdf_import.php";

loginRichiesto();

define("PDF_PATH",getcwd().DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR);

if(!isset($_GET["codice"])) {
    paginaErrore("Parametri non validi");
    return;
}

$codicePrenotazione = filter_var($_GET["codice"],FILTER_SANITIZE_NUMBER_INT);

//Controllo che il codicePrenotazione corrisponda ad una prenotazione che ho effettuato io e non qualcun altro
$prenotazione = getDettagliPrenotazione($codicePrenotazione);

if(!$prenotazione) {
    paginaErrore("Prenotazione non trovata");
    return;
}

if($prenotazione["IDUtente"] != $_SESSION["Utente"]["ID"]) {
    paginaErrore("Non sei autorizzato a visualizzare questa prenotazione");
    return;
}


$nomePDF = "prenotazione_".$codicePrenotazione.".pdf";
$nomePDFCompleto = PDF_PATH.$nomePDF;

if(file_exists($nomePDFCompleto)) {
    header('Content-Type: application/pdf');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Content-Length: ' . filesize($nomePDFCompleto));
    header('Content-Disposition: inline; filename="'.$nomePDF.'";');
    echo file_get_contents($nomePDFCompleto);
    die();
}

$HTML = file_get_contents("template/attivita/conferma_prenotazione_pdf.html");
$HTML = str_replace("[#NOME]",$prenotazione["Nome"]." ".$prenotazione["Cognome"],$HTML);
$HTML = str_replace("[#DATA]",(new DateTime($prenotazione["Data"]))->format("d/m/Y"),$HTML);
$HTML = str_replace("[#ATTIVITA]",$prenotazione["NomeAttivita"],$HTML);
$HTML = str_replace("[#POSTI]",$prenotazione["Posti"],$HTML);
$HTML = str_replace("[#IMPORTO]",number_format(intval($prenotazione["Posti"])*doubleval($prenotazione["Prezzo"]),2),$HTML);

$contenutoQR = "pr-".$codicePrenotazione."-".$prenotazione["IDUtente"];

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setPrintHeader(false);
$pdf->SetFont('helvetica', '', 9);

// add a page
$pdf->AddPage();

// output the HTML content
$pdf->writeHTML($HTML);

$QRStyle = array(
    'border' => false,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);

$pdf->write2DBarcode($contenutoQR, 'QRCODE,H', 160, 10, 50, 50, $QRStyle, 'N');


// reset pointer to the last page
$pdf->lastPage();

$pdf->Output(PDF_PATH."prenotazione_{$codicePrenotazione}.pdf", 'FI');