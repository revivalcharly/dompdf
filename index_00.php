<?php 
$i = 0;
$fh = fopen('source.html', 'r');
// tant que je ne suis pas à la fin du fichier
while (!feof($fh)) {
    // je récupère la ligne courante
    $ligne = fgets($fh);
    // j'affiche le contenu de la ligne
    echo $ligne;
}
// je ferme mon fichier
fclose($fh);
?>