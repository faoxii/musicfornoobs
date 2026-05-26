<?php
/*
 * Fichier : libs/modele/modele_resultats.php
 * Auteur  : LEFEBVRE Lucas
 * Description : fonctions d'acces a la table resultats
 */


/**
 * Enregistre le resultat d'un quiz
 */
function enregistrerResultat($idUser, $idFiche, $score, $pointsGagnes) {
    // TODO
}


/**
 * Renvoie le meilleur score d'un utilisateur sur une fiche
 * 0 si jamais passe
 */
function getMeilleurScore($idUser, $idFiche) {
    // TODO
}


/**
 * Renvoie les N derniers quiz passes par un utilisateur
 * Tableau de [score, points_gagnes, date_passage, titre_fiche, categorie, couleur]
 */
function getActiviteRecente($idUser, $limit = 3) {
    // TODO
}


/**
 * Renvoie le nombre de quiz differents passes par un utilisateur
 */
function getNbQuizPasses($idUser) {
    // TODO
}

?>
