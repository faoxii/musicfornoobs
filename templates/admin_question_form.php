<?php
/*
 * Fichier : templates/admin_question_form.php
 * Auteur  : BOURGUIGNON Mathis
 * Description : Interface 14 - Formulaire question (creation ou edition)
 */

$id = valider("id", "GET");
$mode_edition = (bool) $id;

// TODO Mathis : formulaire avec fiche associee, type (texte/audio), enonce, upload audio si type=audio, 4 reponses + radio bonne reponse
?>
<h1><?= $mode_edition ? "Modifier la question" : "Nouvelle question" ?></h1>
