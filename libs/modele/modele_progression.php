<?php
/*
 * Fichier : libs/modele/modele_progression.php
 * Auteur  : BOURGUIGNON Mathis
 * Description : fonctions d'acces a la table progression
 */


/**
 * Enregistre la lecture d'une fiche par un utilisateur
 * Ne fait rien si deja enregistre (UNIQUE en BDD)
 */
function marquerFicheLue($idUser, $idFiche) {
    // TODO
}


/**
 * Renvoie le nombre de fiches lues par un utilisateur
 */
function getNbFichesLues($idUser) {
    // TODO
}


/**
 * Verifie si une fiche a ete lue par un utilisateur
 * @return bool
 */
function ficheLue($idUser, $idFiche) {
    // TODO
}

?>
