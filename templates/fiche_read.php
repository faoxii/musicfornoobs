<?php
/*
 * Fichier : templates/fiche_read.php
 * Auteur  : BOURGUIGNON Mathis
 * Description : Interface 5 - Detail d'une fiche
 */

$slug = valider("slug", "GET");
if (!$slug) rediriger("index.php?view=fiches");

// TODO Mathis : recuperer la fiche avec getFicheParSlug, afficher contenu + lecteur audio
?>
<h1>Fiche : <?= htmlspecialchars($slug) ?></h1>
