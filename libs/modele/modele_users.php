<?php
/*
 * Fichier : libs/modele/modele_users.php
 * Auteur  : LEFEBVRE Lucas
 * Description : fonctions d'accès à la table utilisateurs
 */


/**
 * Renvoie l'id et le mot de passe haché d'un utilisateur par son login.
 * La vérification du hash se fait dans maLibSecurisation (verifUser), pas ici.
 */
function verifUserBdd($login) {
    $loginP = proteger($login);
    $sql    = "SELECT id, mot_de_passe FROM utilisateurs WHERE login = '$loginP'";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Vérifie si un login est déjà pris avant la création d'un compte.
 */
function loginExiste($login) {
    $loginP = proteger($login);
    $sql    = "SELECT id FROM utilisateurs WHERE login = '$loginP'";
    $res    = SQLGetChamp($sql);

    return ($res !== false && $res !== null);
}


/**
 * Insère un nouvel utilisateur.
 * Le hash PASSWORD_BCRYPT est calculé dans le contrôleur avant d'appeler cette fonction,
 * pour que la couche modèle ne manipule jamais de mot de passe en clair.
 */
function creerUtilisateur($login, $email, $hash) {
    $loginP = proteger($login);
    $emailP = proteger($email);

    $sql = "INSERT INTO utilisateurs (login, email, mot_de_passe)
            VALUES ('$loginP', '$emailP', '$hash')";

    return SQLInsert($sql);
}


/**
 * Renvoie les informations de profil d'un utilisateur (tout sauf le mot de passe).
 */
function getUtilisateur($idUser) {
    $idU = proteger($idUser);
    $sql = "SELECT login, email, role, score_total, date_inscription
            FROM utilisateurs
            WHERE id = '$idU'";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Incrémente le score total d'un utilisateur.
 * C'est une addition, pas une mise à jour absolue : on ajoute la progression
 * par rapport à l'ancien meilleur score, conformément à la règle "meilleur score conservé".
 */
function ajouterPoints($idUser, $points) {
    $idU     = proteger($idUser);
    $pointsP = intval($points);

    $sql = "UPDATE utilisateurs SET score_total = score_total + $pointsP WHERE id = '$idU'";

    return SQLUpdate($sql);
}


/**
 * Renvoie le classement général par score décroissant.
 * LEFT JOIN parce qu'un utilisateur avec 0 fiches lues doit quand même apparaître.
 */
function getClassement($limit = 20) {
    $limitP = intval($limit);
    $sql    = "SELECT
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
 * Calcule le rang d'un utilisateur sans stocker ce rang en BDD.
 * Le trick : compter combien de gens ont un score strictement supérieur, puis ajouter 1.
 * Si 3 personnes ont un score plus haut, l'utilisateur est 4e.
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
