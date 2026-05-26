<?php
/*
 * Fichier : index.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : point d'entree des vues (routeur GET)
 *               recoit ?view=xxx et inclut le template correspondant
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
