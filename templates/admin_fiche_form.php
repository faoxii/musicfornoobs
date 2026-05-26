<?php
/*
 * Fichier : templates/admin_fiche_form.php
 * Auteur  : BOURGUIGNON Mathis
 * Description : Interface 12 - Formulaire fiche (creation ou edition)
 */

$idFiche = valider("id", "GET");
$mode_edition = (bool) $idFiche;

// TODO Mathis : formulaire avec titre, categorie, niveau, contenu, upload audio
?>
<h1><?= $mode_edition ? "Modifier la fiche" : "Nouvelle fiche" ?></h1>
