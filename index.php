<?php
/*
 * Fichier : index.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : point d'entree des vues (routeur GET)
 *               recoit ?view=xxx et inclut le template correspondant
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

// Vue demandee, par defaut "accueil"
$view = valider("view", "GET");
if (!$view) $view = "accueil";

// Liste des vues publiques (accessibles sans connexion)
$vues_publiques = ["accueil", "connexion", "inscription"];

// Liste des vues admin
$vues_admin = ["admin_fiches", "admin_fiche_form", "admin_questions", "admin_question_form"];

// Verification de l'acces
$connecte = isset($_SESSION["connecte"]) && $_SESSION["connecte"];
$est_admin = isset($_SESSION["role"]) && $_SESSION["role"] === "admin";

if (!$connecte && !in_array($view, $vues_publiques)) {
    rediriger("index.php?view=connexion");
}

if (in_array($view, $vues_admin) && !$est_admin) {
    rediriger("index.php?view=fiches");
}

// Construction du chemin du template
$template = "templates/" . $view . ".php";

if (!file_exists($template)) {
    // Vue inconnue, on renvoie sur l'accueil
    rediriger("index.php?view=accueil");
}

// Header commun (sauf pour les pages sans navbar)
$pages_sans_header = ["accueil", "connexion", "inscription"];
$avec_header = !in_array($view, $pages_sans_header);

include("templates/header.php");
include($template);
include("templates/footer.php");

?>
