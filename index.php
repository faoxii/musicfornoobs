<?php
/*
 * Fichier : index.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : point d'entrée des vues (routeur GET)
 * reçoit ?view=xxx et inclut le template correspondant
 */

session_start();

// Includes des libs
include("libs/config.php");
include("libs/maLibSQL.pdo.php");
include("libs/maLibUtils.php");
include("libs/maLibSecurisation.php");

// Includes des modeles
include("libs/modele/modele_users.php");
include("libs/modele/modele_fiches.php");
include("libs/modele/modele_quiz.php");
include("libs/modele/modele_progression.php");
include("libs/modele/modele_resultats.php");


// ============================================================
// TODO : DÉCOMMENTER CE BLOC POUR FORCER LA CONNEXION (DEV)
// Simule une connexion admin pour pouvoir developper les vues
// sans attendre que l'auth soit prete
// ============================================================
/* if (!isset($_SESSION["connecte"])) {
    $_SESSION["connecte"] = true;
    $_SESSION["idUser"] = 1;
    $_SESSION["login"] = "Lucas_L";
    $_SESSION["role"] = "admin";
}
*/
// ============================================================



$view = valider("view", "GET");
if (!$view) {
    $view = "accueil";
}

// si l'utilisateur est connecte et qu'il arrive sur l'accueil, 
// on l'envoie direct sur son catalogue de fiches
if ($view === "accueil" && valider("connecte", "SESSION")) {
    $view = "fiches";
}

// liste des vues publiques (accessibles sans connexion)
$vues_publiques = ["accueil", "connexion", "inscription"];

// liste des vues admin
$vues_admin = ["admin_fiches", "admin_fiche_form", "admin_questions", "admin_question_form"];

$connecte = valider("connecte", "SESSION");
$est_admin = valider("role", "SESSION") === "admin";

// si l'user est pas connecte on renvoie vers connexion
if (!$connecte && !in_array($view, $vues_publiques)) {
    rediriger("index.php?view=connexion");
}
// si l'user connecté tente d'aller sur une page admin on renvoie vers la page fiches qui est la page par default quand on est connecte
if (in_array($view, $vues_admin) && !$est_admin) {
    rediriger("index.php?view=fiches");
}

// construction du chemin du template
$template = "templates/" . $view . ".php";

if (!file_exists($template)) {
    // vue inconnue donc on renvoie sur l'accueil
    rediriger("index.php?view=accueil");
}

include("templates/header.php");
include($template);
include("templates/footer.php");

?>