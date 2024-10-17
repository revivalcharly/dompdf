<?php
require './common/nombreenlettres.php';

// french_date
// retourne une date au format jour mois annee
// Timestamp $date indique la date à transformer en string 
// si boolen $notime == false, on ajoute l'heure en mn:s au retour
function french_date(string $date , $notime = true)
    {
    $mois = array("Janvier", "Fevrier", "Mars",
                  "Avril","Mai", "Juin", 
                  "Juillet", "Août","Septembre",
                  "Octobre", "Novembre", "Decembre");
    $jours= array("Dimanche", "Lundi", "Mardi",
                  "Mercredi", "Jeudi", "Vendredi",
                  "Samedi");
    $retour = $jours[date("w",$date)]." ".date("j",$date).(date("j",$date)==1 ? "er":" ").
    $mois[date("n",$date)-1]." ".date("Y",$date);
    
    if (!$notime)
        $retour .= " à ".date("H",$date)."H".date("i",$date);

    return $retour;
    // return $jours[date("w",$date)]." ".date("j",$date).(date("j",$date)==1 ? "er":" ").
    //        $mois[date("n",$date)-1]." ".date("Y",$date)." à ".date("H",$date)."H".date("i",$date);
    }
    
// comme son nom l'indique
// en entrée : $montant de type decimal 10,2
// unité monétaire (par défaut l'euro)
// en sortie, un tableau de deux valeurs donnant  
// - le montant en chiffre sous forme d'un nombre formatté avec l'unité monaitaire 
// - le montant en lettres 
function montantenlettres($montant, $currency = "€")    
{
    $price = new nuts($montant, $currency );
    $text = $price->convert("fr-FR");
    $nb = $price->getFormated(" ", ",");   
    return [$text,$nb];
}
// retourne une image en base64
// (utile pour générer un PDF)
function ImageToDataUrl(String $filename) : String {
    if(!file_exists($filename))
        throw new Exception('File not found.');
    
    $mime = mime_content_type($filename);
    if($mime === false) 
        throw new Exception('Illegal MIME type.');

    $raw_data = file_get_contents($filename);
    if(empty($raw_data))
        throw new Exception('File not readable or empty.');
    
    return "data:{$mime};base64," . base64_encode($raw_data);
}

// répupère l'image en base64 (sinon n'apparait pas en PDF)
// variante de la fonction ci-dessus
// path est le chemin de l'image
function getBase64(String $path){
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
return $base64;
}

?>