<?php
/*
 * Fichier : libs/modele/modele_users.php
 * Auteur  : LEFEBVRE Lucas
 * Description : fonctions d'acces a la table utilisateurs
 */


/**
 * Verifie un login/mot de passe et renvoie l'id utilisateur ou false
 */
function verifUserBdd($login, $passe) {
    // TODO
}


/**
 * Verifie si un login existe deja
 * @return bool
 */
function loginExiste($login) {
    // TODO
}


/**
 * Cree un nouvel utilisateur (mot de passe hache en interne avec password_hash)
 * @return int|false id du nouvel utilisateur ou false en cas d'erreur
 */
function creerUtilisateur($login, $email, $motDePasse) {
    // TODO
}


/**
 * Renvoie les infos d'un utilisateur depuis son id
 * @return array|false tableau associatif avec login, email, role, score_total, date_inscription
 */
function getUtilisateur($idUser) {
    // TODO
}


/**
 * Renvoie le score total d'un utilisateur
 */
function getScoreTotal($idUser) {
    // TODO
}


/**
 * Met a jour le score total d'un utilisateur (ajout incremental)
 */
function ajouterPoints($idUser, $points) {
    // TODO
}


/**
 * Renvoie le classement (top N utilisateurs)
 * @return array tableau de [login, score_total, nb_fiches_lues]
 */
function getClassement($limit = 20) {
    // TODO
}


/**
 * Renvoie le rang d'un utilisateur dans le classement
 */
function getRang($idUser) {
    // TODO
}

?>
