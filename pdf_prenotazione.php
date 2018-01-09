<?php
require_once "php/database.php";
require_once "php/funzioni/funzioni_pagina.php";
require_once "php/tcpdf/tcpdf_import.php";

if(!isset($_GET["codice"])) {
    paginaErrore("Parametri non validi");
    return;
}

$codicePrenotazione = $_GET["codice"];

$prenotazione = getDettagliPrenotazione($codicePrenotazione);

$HTML = file_get_contents("template/conferma_prenotazione_pdf.html");
$HTML = str_replace("[#NOME]",$prenotazione["Nome"]." ".$prenotazione["Cognome"],$HTML);
$HTML = str_replace("[#DATA]",(new DateTime($prenotazione["Data"]))->format("d/m/Y"),$HTML);
$HTML = str_replace("[#ATTIVITA]",$prenotazione["NomeAttivita"],$HTML);
$HTML = str_replace("[#POSTI]",$prenotazione["Posti"],$HTML);
$HTML = str_replace("[#IMPORTO]",number_format(intval($prenotazione["Posti"])*doubleval($prenotazione["Prezzo"]),2),$HTML);

$contenutoQR = $codicePrenotazione."-".substr(sha1($prenotazione["IDUtente"]),10);

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

$pdf->Output('example_021.pdf', 'I');

function getDettagliPrenotazione($codicePrenotazione) {
    global $db;

    $query = $db->prepare("SELECT Prenotazioni.Giorno AS Data, Prenotazioni.PostiPrenotati as Posti, 
          Attivita.Nome AS NomeAttivita, Attivita.Prezzo AS Prezzo, Utenti.Nome AS Nome, Utenti.Cognome AS Cognome,
          Utenti.ID AS IDUtente
          FROM Attivita JOIN (Utenti JOIN Prenotazioni ON Utenti.ID = Prenotazioni.IDUtente)
            ON Attivita.Codice = Prenotazioni.IDAttivita WHERE Prenotazioni.Codice = ?");

    $query->execute(array($codicePrenotazione));
    return $query->fetch();
}