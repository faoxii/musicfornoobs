<?php
/*
 * Fichier : templates/quiz_play.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 7 - Deroulement du quiz (1 question a la fois)
 */

$slug = valider("slug", "GET");
if (!$slug) rediriger("index.php?view=quiz");

// Initialisation de la session quiz si premiere question
if (!isset($_SESSION["quiz_slug"]) || $_SESSION["quiz_slug"] !== $slug) {
    $_SESSION["quiz_slug"] = $slug;
    $_SESSION["quiz_etape"] = 1;
    $_SESSION["quiz_reponses"] = [];
}

$etape = $_SESSION["quiz_etape"];

// TODO Lucas : afficher question + 4 reponses + barre de progression
?>
<h1>Question <?= $etape ?> / 5</h1>
