<?php 

use Dompdf\Dompdf;
use Dompdf\Options;

require_once './vendor/autoload.php';

// instantiate and use the dompdf class
$options = new Options();
$options->set('defaultFont', 'Arial');
// $Options->setPdfBackend('GD');
$dompdf = new Dompdf($options);

// $nq = 5; // numéro de quittance
// $keywords = [
//     ["toReplace"=>"N°","replaceBy"=>"N°.$nq"],
//     ["toReplace"=>"mensuel","replaceBy"=>"trimestriel"],
// ];


// $typecharge = "mensuel";
// $fh = fopen('source01.html', 'r');
// // tant que je ne suis pas à la fin du fichier
// $htmlTab = array();

// while (!feof($fh)) {
//     // je récupère la ligne courante
//     $ligne = fgets($fh);
//     foreach($keywords as $kw) {
// //        $ligne = str_replace($kw["toReplace"],$kw["replaceBy"],$ligne,$count);
//         $ligne = str_replace($kw["toReplace"],$kw["replaceBy"],$ligne,$count);
//     }
//     // on l'ajoute au tableau courant
//     $htmlTab[] = $ligne;
//     // j'affiche le contenu de la ligne
//     echo end($htmlTab);
// }

// // je ferme mon fichier
// fclose($fh);
// $typecharge = "mensuel";

$html = 'source.txt';
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
// $dompdf->setPaper('A4', 'landscape');
// $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

$fichier = "MonPremierPDF_dompdf.pdf";
// Output the generated PDF to Browser
$dompdf->stream($fichier)
?>