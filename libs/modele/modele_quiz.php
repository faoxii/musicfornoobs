<?php
/*
 * Fichier : libs/modele/modele_quiz.php
 * Auteur  : LEFEBVRE Lucas
 * Description : fonctions d'acces aux tables quiz_questions et quiz_reponses
 */


/**
 * Renvoie les fiches qui ont un quiz pret (5+ questions)
 * Pour le catalogue des quiz
 */
function getQuizDisponibles($idCategorie = false, $niveau = false) {
    // TODO
}


/**
 * Renvoie une question + ses 4 reponses (pour le deroulement du quiz)
 * $numeroQuestion entre 1 et 5
 */
function getQuestion($idFiche, $numeroQuestion) {
    // TODO
}


/**
 * Renvoie toutes les questions d'une fiche (pour l'admin)
 * IMPORTANT : a livrer tot, Mathis en a besoin pour l'interface 13
 */
function getQuestionsFiche($idFiche) {
    // TODO
}


/**
 * Renvoie une question depuis son id (avec ses 4 reponses)
 */
function getQuestion_id($idQuestion) {
    // TODO
}


/**
 * Cree une nouvelle question avec ses 4 reponses
 * $reponses = tableau de 4 elements [['contenu' => ..., 'est_correct' => 0/1], ...]
 */
function creerQuestion($idFiche, $type, $enonce, $cheminAudio, $ordre, $reponses) {
    // TODO
}


/**
 * Modifie une question existante (supprime puis recree les reponses)
 */
function modifierQuestion($idQuestion, $type, $enonce, $cheminAudio, $reponses) {
    // TODO
}


/**
 * Supprime une question (cascade les reponses)
 */
function supprimerQuestion($idQuestion) {
    // TODO
}


/**
 * Verifie si une reponse est correcte (utilise a la soumission du quiz)
 */
function estBonneReponse($idQuestion, $idReponse) {
    // TODO
}

?>
