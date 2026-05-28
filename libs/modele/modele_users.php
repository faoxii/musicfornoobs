<?php
/*
 * Fichier : libs/modele/modele_users.php
 * Auteur  : LEFEBVRE Lucas
 * Description : fonctions d'acces a la table utilisateurs
 */

// Note pour moi-même : Toujours appeler proteger() sur les variables dans le controleur avant d'utiliser ces fonctions pour éviter les injections SQL !

/**
 * Verifie un login/mot de passe et renvoie l'id utilisateur ou false
 */
function verifUserBdd($login, $passe) {
    // On récupère d'abord le hash stocké en base, on ne compare jamais en clair
    $sql = "SELECT id, mot_de_passe FROM utilisateurs WHERE login = '$login'";
    $resultats = parcoursRs(SQLSelect($sql));
    
    if (count($resultats) > 0) {
        $hashBdd = $resultats[0]['mot_de_passe'];
        // password_verify gère la comparaison avec le bcrypt automatiquement (vu en cours)
        if (password_verify($passe, $hashBdd)) {
            return $resultats[0]['id'];
        }
    }
    return false;
}


/**
 * Verifie si un login existe deja
 * @return bool
 */
function loginExiste($login) {
    $sql = "SELECT id FROM utilisateurs WHERE login = '$login'";
    $res = SQLGetChamp($sql);
    // Si la requête renvoie quelque chose, c'est que le login est déjà pris
    return ($res !== false && $res !== null);
}


/**
 * Cree un nouvel utilisateur (mot de passe hache en interne avec password_hash)
 * @return int|false id du nouvel utilisateur ou false en cas d'erreur
 */
function creerUtilisateur($login, $email, $motDePasse) {
    // Hachage obligatoire avant insertion (cf Livrable 2, taille VARCHAR 255)
    $hash = password_hash($motDePasse, PASSWORD_BCRYPT);
    $sql = "INSERT INTO utilisateurs (login, email, mot_de_passe) 
            VALUES ('$login', '$email', '$hash')";
    
    // SQLInsert renvoie l'ID généré (lastInsertId), super pratique pour connecter le user direct après
    return SQLInsert($sql);
}


/**
 * Renvoie les infos d'un utilisateur depuis son id
 * @return array|false tableau associatif avec login, email, role, score_total, date_inscription
 */
function getUtilisateur($idUser) {
    $sql = "SELECT login, email, role, score_total, date_inscription 
            FROM utilisateurs 
            WHERE id = '$idUser'";
            
    $resultats = parcoursRs(SQLSelect($sql));
    if (count($resultats) > 0) {
        return $resultats[0]; // On renvoie juste la première ligne (qui est unique via l'id)
    }
    return false;
}


/**
 * Renvoie le score total d'un utilisateur
 */
function getScoreTotal($idUser) {
    $sql = "SELECT score_total FROM utilisateurs WHERE id = '$idUser'";
    return SQLGetChamp($sql);
}


/**
 * Met a jour le score total d'un utilisateur (ajout incremental)
 */
function ajouterPoints($idUser, $points) {
    // Attention: $points c'est le delta calculé dans le contrôleur, on l'additionne direct en base
    $sql = "UPDATE utilisateurs SET score_total = score_total + $points WHERE id = '$idUser'";
    return SQLUpdate($sql);
}


/**
 * Renvoie le classement (top N utilisateurs)
 * @return array tableau de [login, score_total, nb_fiches_lues]
 */
function getClassement($limit = 20) {
    // Requête issue du L2 p.12 : LEFT JOIN indispensable pour inclure les gens avec 0 fiches lues !
    $sql = "SELECT 
                u.login, 
                u.score_total, 
                COUNT(p.id_fiche) AS nb_fiches_lues
            FROM utilisateurs u
            LEFT JOIN progression p ON p.id_utilisateur = u.id
            GROUP BY u.id, u.login, u.score_total
            ORDER BY u.score_total DESC
            LIMIT $limit";
            
    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie le rang d'un utilisateur dans le classement
 */
function getRang($idUser) {
    // Astuce du L2 p.13 : On compte combien de gens ont un score strictement supérieur et on ajoute 1
    $sql = "SELECT COUNT(*) + 1 AS rang 
            FROM utilisateurs 
            WHERE score_total > (
                SELECT score_total FROM utilisateurs WHERE id = '$idUser'
            )";
            
    return SQLGetChamp($sql);
}

?>