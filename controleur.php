<?php
/*
 * Fichier : controleur.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : traitement des actions (POST), redirige vers index.php apres
 */

session_start();



// Includes des modeles
include_once("libs/modele/modele_users.php");
include_once("libs/modele/modele_fiches.php");
include_once("libs/modele/modele_quiz.php");
include_once("libs/modele/modele_progression.php");
include_once("libs/modele/modele_resultats.php");

// Includes des libs
include_once("libs/config.php");
include_once("libs/maLibSQL.pdo.php");
include_once("libs/maLibUtils.php");
include_once("libs/maLibSecurisation.php");

// Action demandee
$action = valider("action", "POST");

switch ($action) {

    case "Connexion":
        // TODO Lucas : verifier identifiants + creer session
        rediriger("index.php?view=fiches");
        break;

    case "Inscription":
        // TODO Lucas : creer le compte + connecter
        rediriger("index.php?view=fiches");
        break;

    case "Logout":
        // TODO Lucas : detruire la session
        session_destroy();
        rediriger("index.php?view=accueil");
        break;

    case "MarquerLue":
        // TODO Mathis : inserer dans progression
        rediriger("index.php?view=fiches");
        break;

    case "SoumettreQuiz":
        // TODO Lucas : calculer score + inserer resultat + maj score_total
        rediriger("index.php?view=resultats");
        break;

    case "CreerFiche":
        // TODO Mathis : insert + upload audio
        rediriger("index.php?view=admin_fiches");
        break;

    case "ModifierFiche":
        // TODO Mathis : update + replacement audio eventuel
        rediriger("index.php?view=admin_fiches");
        break;

    case "SupprimerFiche":
        // TODO Mathis : delete cascade
        rediriger("index.php?view=admin_fiches");
        break;

    case "CreerQuestion":
        // TODO Mathis : insert question + 4 reponses
        rediriger("index.php?view=admin_questions");
        break;

    case "ModifierQuestion":
        // TODO Mathis : update question + 4 reponses
        rediriger("index.php?view=admin_questions");
        break;

    case "SupprimerQuestion":
        // TODO Mathis : delete cascade
        rediriger("index.php?view=admin_questions");
        break;

    default:
        rediriger("index.php?view=accueil");
        break;
}

?>
