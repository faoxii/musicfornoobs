<?php
/*
 * Fichier : templates/admin_questions.php
 * Auteur  : BOURGUIGNON Mathis
 * Description : Interface 13 - Liste des questions d'une fiche (admin)
 */

$idFiche = valider("id_fiche", "GET");
if (!$idFiche) rediriger("index.php?view=admin_fiches");

// TODO Mathis : lister les questions de la fiche (utilise getQuestionsFiche, livre par Lucas)
?>
<h1>Questions de la fiche</h1>
