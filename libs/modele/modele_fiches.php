<?php
/*
 * Fichier : libs/modele/modele_fiches.php
 * Auteur  : BOURGUIGNON Mathis
 * Description : fonctions d'acces aux tables fiches et categories
 */


/**
 * Renvoie toutes les categories avec leur nombre de fiches
 * Pour la sidebar du catalogue
 */
function getCategoriesAvecCompteurs() {
    // TODO
}


/**
 * Renvoie toutes les fiches, eventuellement filtrees
 * $idCategorie = false pour pas de filtre, idem pour $niveau et $recherche
 */
function getFiches($idCategorie = false, $niveau = false, $recherche = false) {
    // TODO
}


/**
 * Renvoie une fiche depuis son slug (avec sa categorie)
 * @return array|false tableau associatif ou false
 */
function getFicheParSlug($slug) {
    // TODO
}


/**
 * Renvoie une fiche depuis son id (pour l'admin)
 */
function getFiche($idFiche) {
    // TODO
}


/**
 * Cree une nouvelle fiche
 */
function creerFiche($titre, $slug, $idCategorie, $niveau, $contenu, $cheminAudio) {
    // TODO
}


/**
 * Modifie une fiche existante
 */
function modifierFiche($idFiche, $titre, $slug, $idCategorie, $niveau, $contenu, $cheminAudio) {
    // TODO
}


/**
 * Supprime une fiche (cascade en BDD)
 */
function supprimerFiche($idFiche) {
    // TODO
}


/**
 * Genere un slug propre depuis un titre
 * "La gamme de Do majeur" -> "gamme-do-majeur"
 */
function genererSlug($titre) {
    // TODO
}

?>
