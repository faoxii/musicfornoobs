<?php
/*
 * Fichier : libs/modele/modele_users.php
 * Auteur  : LEFEBVRE Lucas
 * Description : fonctions d'acces a la table utilisateurs
 */


/**
 * Renvoie la ligne utilisateur (id + mot_de_passe hache) depuis un login
 * La verification du mot de passe se fait dans maLibSecurisation
 */
function verifUserBdd($login) {
    $loginP = proteger($login);
    $sql = "SELECT id, mot_de_passe FROM utilisateurs WHERE login = '$loginP'";
    return parcoursRs(SQLSelect($sql));
}


/**
 * Verifie si un login existe deja
 */
function loginExiste($login) {
    $loginP = proteger($login);
    $sql = "SELECT id FROM utilisateurs WHERE login = '$loginP'";
    $res = SQLGetChamp($sql);
    return ($res !== false && $res !== null);
}


/**
 * Cree un nouvel utilisateur
 * Le hash du mot de passe est fait dans le controleur avant d'appeler cette fonction
 */
function creerUtilisateur($login, $email, $hash) {
    $loginP = proteger($login);
    $emailP = proteger($email);
    $sql = "INSERT INTO utilisateurs (login, email, mot_de_passe)
            VALUES ('$loginP', '$emailP', '$hash')";
    return SQLInsert($sql);
}


/**
 * Renvoie les infos d'un utilisateur depuis son id
 * Retourne un tableau de tableaux (coherent avec les autres modeles)
 */
function getUtilisateur($idUser) {
    $idU = proteger($idUser);
    $sql = "SELECT login, email, role, score_total, date_inscription
            FROM utilisateurs
            WHERE id = '$idU'";
    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie le score total d'un utilisateur
 */
function getScoreTotal($idUser) {
    $idU = proteger($idUser);
    $sql = "SELECT score_total FROM utilisateurs WHERE id = '$idU'";
    return SQLGetChamp($sql);
}


/**
 * Ajoute des points au score total d'un utilisateur (ajout incremental)
 */
function ajouterPoints($idUser, $points) {
    $idU     = proteger($idUser);
    $pointsP = intval($points);
    $sql = "UPDATE utilisateurs SET score_total = score_total + $pointsP WHERE id = '$idU'";
    return SQLUpdate($sql);
}


/**
 * Renvoie le classement (top N utilisateurs)
 */
function getClassement($limit = 20) {
    $limitP = intval($limit);
    $sql = "SELECT
                u.login,
                u.score_total,
                COUNT(p.id_fiche) AS nb_fiches_lues
            FROM utilisateurs u
            LEFT JOIN progression p ON p.id_utilisateur = u.id
            GROUP BY u.id, u.login, u.score_total
            ORDER BY u.score_total DESC
            LIMIT $limitP";
    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie le rang d'un utilisateur dans le classement
 */
function getRang($idUser) {
    $idU = proteger($idUser);
    $sql = "SELECT COUNT(*) + 1 AS rang
            FROM utilisateurs
            WHERE score_total > (
                SELECT score_total FROM utilisateurs WHERE id = '$idU'
            )";
    return SQLGetChamp($sql);
}

?>