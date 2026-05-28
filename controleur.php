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
$action = valider("action");

switch ($action) {

    case "Connexion":
        // 1. On récupère les données du formulaire
        $login = valider("login", "POST");
        $passe = valider("passe", "POST");

        if ($login && $passe) {
            // 2. verifUser vérifie le mot de passe et crée la session si c'est bon
            if (verifUser($login, $passe)) {
                rediriger("index.php?view=fiches");
            } else {
                // 3. Si erreur, on renvoie vers la page avec un message
                rediriger("index.php?view=connexion", "msg=Pseudo ou mot de passe incorrect");
            }
        }
        break;

    case "Inscription":
        // 1. On récupère les données
        $login = valider("login", "POST");
        $email = valider("email", "POST");
        $passe = valider("passe", "POST");

        if ($login && $email && $passe) {
            // 2. On vérifie d'abord si le pseudo n'est pas déjà pris
            if (loginExiste($login)) {
                rediriger("index.php?view=inscription", "msg=Ce pseudo est déjà utilisé");
            } else {
                // 3. On crée l'utilisateur dans la BDD
                $idUser = creerUtilisateur($login, $email, $passe);
                
                // 4. Une fois créé, on le connecte automatiquement
                verifUser($login, $passe);
                rediriger("index.php?view=fiches");
            }
        }
        break;

    case "Logout":
        // 1. On vide le tableau de session
        $_SESSION = array();

        // 2. On détruit la session côté serveur
        session_destroy();

        // 3. On redirige vers l'accueil pour les non-connectés
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
