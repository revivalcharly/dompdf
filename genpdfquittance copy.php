<?php 

use Dompdf\Dompdf;  // précise une namespace
use Dompdf\Options; // précise une namespace

require './common/utilities.php';
require_once './vendor/autoload.php';

//--------------------------------éléments liés à chaque période de quittancement------------------------------
// Se connecter et sélectioner une base de données MySQL nommé glodj
// Hostname: 127.0.0.1, username: votre_utilisateur, password: votre_mdp, db: sakila
$mysqli = new mysqli('127.0.0.1', 'root', 'arcturus', 'glodj');
// si erreur de connection
if ($mysqli->connect_errno) {
    // La connexion a échoué. Que voulez-vous faire ? 
    // Vous pourriez vous contacter (email ?), enregistrer l'erreur, afficher une jolie page, etc.
    // Vous ne voulez pas révéler des informations sensibles

    // Essayons ceci :
    echo "Désolé, le site web subit des problèmes.";

    // Quelque chose que vous ne devriez pas faire sur un site public,
    // mais cette exemple vous montrera quand même comment afficher des
    // informations lié à l'erreur MySQL -- vous voulez peut être enregistrer ceci
    echo "Error: Échec d'établir une connexion MySQL, voici pourquoi : \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";
    
    // Vous voulez peut être leurs afficher quelque chose de jolie, nous ferons simplement un exit
    exit;
}
$query = "SELECT * FROM testdate";
if ($mysqli->multi_query($query)) {
    do {
        /* Stockage du premier jeu de résultats */
        if ($result = $mysqli->use_result()) {
            while ($row = $result->fetch_row()) {
                // printf("%s\n", $row[0]);
            }
            $result->close();
        }
        /* Affichage d'une démarcation */
        // if ($mysqli->more_results()) {
        //     printf("-----------------\n");
        // }
    } while ($mysqli->next_result());
}

//--------------------------------éléments fixes liés au bail --------------------------------------------------


$signeBase64 = getBase64("signe.jpg"); // image de la signature/tampon
$bandeauBase64 = getBase64("Bandeau_LODJ.jpeg"); // image du bandeau d'entête

$nq = 5; // numéro de quittance
$FrequenceLoyer = "mensuel";
$TypeCharges = "forfaitaires";
$TypeTaxe = "contribution représentative du droit de bail";
$AutreSomme = "Dépôt de garantie";
$CivilitePrenomNom = "Mr Pierre-Yves ROYER";
$sisA1 = "425 cours émile Zola";
$sisA2 = "69100 VILLEURBANNE";
$FaitA = "LA MULATIÈRE";

// les deux lignes ci-dessous vraiment utiles ? Ne semblent pas avoir d'effet
setlocale(LC_TIME, 'fr_FR');
date_default_timezone_set('Europe/Paris');

$loyer = 410;
$montant = montantenlettres($loyer);
$loyerEnLettres = $montant[0];
$loyerEnChiffres = $montant[1];

$depotDeGarantie = 410;
$montant = montantenlettres($depotDeGarantie);
$autreSommeEnLettres = $montant[0];
$autreSommeEnChiffres = $montant[1];

$charges = 65;
$montant = montantenlettres($charges);
$chargesEnLettres = $montant[0];
$chargesEnChiffres = $montant[1];

$montantTotal = $loyer + $depotDeGarantie + $charges;
$montant = montantenlettres($montantTotal);
$montant = montantenlettres($montantTotal);
$laSommeDeEnLettres = $montant[0];
$laSommeDeEnChiffres = $montant[1];

// localhost/dompdf/genpdf.php/?XDEBUG_SESSION=1

// $DateDuJour = (new \DateTime())->format('d-m-Y H:i:s'); // par défaut crée la date du jour
// $DateDuJour = date("l", mktime(0, 0, 0, 7, 1, 2000));
// $DateDuJour = (new \DateTime())->setDate(2023,7,1)->format('d-m-Y H:i:s');
// $DateDuJour = (new \DateTime())->setDate(2023,7,1)->getTimestamp();

$DateDuJour = french_date(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y')),false);

/*
remplacement des éléments permanents, c'est-à dire ceux qui ne sont pas affectés par
le parcours du compte du client (essentiellement, les images, le nom du locataire, l'adresse de sa colocation,
la date et le lieu de a signature, etc ...)
Ceci permet de ne pas avoir à les recharger à chaque fois depuis le disque.
*/
$permanentkeywords = [
    ["toReplace"=>"src=Bandeau_LODJ.jpeg","replaceBy"=>"src=$bandeauBase64"], // bandeau d'entête
    ["toReplace"=>"src=signe.jpg","replaceBy"=>"src=$signeBase64"], // signature
    ["toReplace"=>"<!--FrequenceLoyer-->","replaceBy"=>&$FrequenceLoyer], // fréquence d'appel de loyer
    ["toReplace"=>"<!--TypeCharges-->","replaceBy"=>&$TypeCharges], // type de charge
    ["toReplace"=>"<!--TypeTaxe-->","replaceBy"=>&$TypeTaxe], // Taxes
    ["toReplace"=>"<!--CivilitePrenomNom-->","replaceBy"=>&$CivilitePrenomNom], 
    ["toReplace"=>"<!--FaitA-->","replaceBy"=>&$FaitA], 
    ["toReplace"=>"<!--DateDuJour-->","replaceBy"=>&$DateDuJour],     
    ["toReplace"=>"<!--sisA1-->","replaceBy"=>&$sisA1],     
    ["toReplace"=>"<!--sisA2-->","replaceBy"=>&$sisA2],     
];

/*
remplacement des éléments sensibles au parcours du compte du client 
par exemple, numéro de quittance, mois, montants
*/
$recordkeywords = [
    ["toReplace"=>"<!--NumQuittance-->","replaceBy"=>&$nq], // numéro de quittance   
    ["toReplace"=>"<!--AutreSomme-->","replaceBy"=>&$AutreSomme], // autres sommes (dépôt de garantie)
    ["toReplace"=>"<!--autreSommeEnChiffres-->","replaceBy"=>&$autreSommeEnChiffres], // autres sommes (dépôt de garantie)
    ["toReplace"=>"<!--autreSommeEnLettres-->","replaceBy"=>&$autreSommeEnLettres], // autres sommes (dépôt de garantie)
    ["toReplace"=>"<!--loyerEnLettres-->","replaceBy"=>&$loyerEnLettres], //Loyer en lettres
    ["toReplace"=>"<!--loyerEnChiffres-->","replaceBy"=>&$loyerEnChiffres], //Loyer en lettres
    ["toReplace"=>"<!--chargesEnLettres-->","replaceBy"=>&$chargesEnLettres], //charges en lettres
    ["toReplace"=>"<!--chargesEnChiffres-->","replaceBy"=>&$chargesEnChiffres], //charges en chiffres
    ["toReplace"=>"<!--laSommeDeEnLettres-->","replaceBy"=>&$laSommeDeEnLettres], //somme totale en lettres
    ["toReplace"=>"<!--laSommeDeEnChiffres-->","replaceBy"=>&$laSommeDeEnChiffres], //somme totale en chiffres    
];

//--------------------------------Préparation du dompdf---------------------------------------------------------
// instantiate and use the Option class
$options = new Options();
$options->set('defaultFont', 'Courier');

// $Options->setPdfBackend('GD');

// instantiate and use the dompdf class
$dompdf = new Dompdf($options);

// initialisation d'un fichier html que l'on va incrémenter par le parcours de tous les éléments
$html = ""; 
// Lecture du fichier template d'entête
     $entetetemplate = file_get_contents('./entetequittance.html');
// mise à jour des variables indépendantes des enregistrements
      foreach($permanentkeywords as $kw) {
         $entetetemplate = str_replace($kw["toReplace"],$kw["replaceBy"],$entetetemplate,$count);
     }
// Lecture du fichier template de corp auquel on applique les paramètres permanents
     $corptemplate = file_get_contents('./corpquittance.html');
      foreach($permanentkeywords as $kw) {
         $corptemplate = str_replace($kw["toReplace"],$kw["replaceBy"],$corptemplate,$count);
     }

    for ($i = 1; $i<5 ; $i++) {
    $corp = $corptemplate;
    // mise à jour des variables propres à chaque enregistrement
    $nq++;
    foreach($recordkeywords as $kw) {
        $corp = str_replace($kw["toReplace"],$kw["replaceBy"],$corp,$count);
        }
    $current = $entetetemplate.$corp;

    $corp = $corptemplate;
    // mise à jour des variables propres à chaque enregistrement
    $nq++;
    foreach($recordkeywords as $kw) {
        $corp = str_replace($kw["toReplace"],$kw["replaceBy"],$corp,$count);
        }
    $current .= $corp."  </body></html>";
    $html .= $current;
    }
    
$dompdf->loadHtml($html);

$dompdf->setPaper('A4','portrait');
// Render the HTML as PDF
$dompdf->render();

$fichier = "Quittances.pdf";
// Output the generated PDF to Browser
$dompdf->stream($fichier)
?>