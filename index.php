<?php 
use Dompdf\Dompdf;
use Dompdf\Options;

require_once './vendor/autoload.php';

// instantiate and use the dompdf class
$options = new Options();
$options->set('defaultFont', 'Arial');
// $Options->setPdfBackend('GD');
$dompdf = new Dompdf($options);

$html = file_get_contents("./source01.html");
// $dompdf = new Dompdf();
// $dompdf->loadHtml('hello world');
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
// $dompdf->setPaper('A4', 'landscape');
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

$fichier = "MonPremierPDF_dompdf.pdf";
// Output the generated PDF to Browser
$dompdf->stream($fichier);