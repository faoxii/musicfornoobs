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
    // Protection du login avant insertion dans la requête
    $loginP = proteger($login); 
    
    $sql = "SELECT id, mot_de_passe FROM utilisateurs WHERE login = '$loginP'";
    $resultats = parcoursRs(SQLSelect($sql));
    
    if (count($resultats) > 0) {
        $hashBdd = $resultats[0]['mot_de_passe'];
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
    $loginP = proteger($login);
    $sql = "SELECT id FROM utilisateurs WHERE login = '$loginP'";
    $res = SQLGetChamp($sql);
    // Si la requête renvoie quelque chose, c'est que le login est déjà pris
    return ($res !== false && $res !== null);
}


/**
 * Cree un nouvel utilisateur (mot de passe hache en interne avec password_hash)
 * @return int|false id du nouvel utilisateur ou false en cas d'erreur
 */
function creerUtilisateur($login, $email, $motDePasse) {
    $loginP = proteger($login);
    $emailP = proteger($email);
    //on hash le mot de passe
    $hash = password_hash($motDePasse, PASSWORD_BCRYPT);
    $sql = "INSERT INTO utilisateurs (login, email, mot_de_passe) 
            VALUES ('$loginP', '$emailP', '$hash')";
    
    return SQLInsert($sql);
}


/**
 * Renvoie les infos d'un utilisateur depuis son id
 * @return array|false tableau associatif avec login, email, role, score_total, date_inscription
 */
function getUtilisateur($idUser) {
    $idU = proteger($idUser);
    $sql = "SELECT login, email, role, score_total, date_inscription 
            FROM utilisateurs 
            WHERE id = '$idU'";
            
    $resultats = parcoursRs(SQLSelect($sql));
    if (count($resultats) > 0) {
        return $resultats[0];
    }
    return false;
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
 * Met a jour le score total d'un utilisateur (ajout incremental)
 */
function ajouterPoints($idUser, $points) {
    $idU = proteger($idUser);
    // intval transforme n'importe quelle donnée en un nombre entier (integer) , permet d'eviter injection si on met par exemple 10; DROP TABLE utilisateurs;
    $pointsP = intval($points); 
    $sql = "UPDATE utilisateurs SET score_total = score_total + $pointsP WHERE id = '$idU'";
    return SQLUpdate($sql);
}


/**
 * Renvoie le classement (top N utilisateurs)
 * @return array tableau de [login, score_total, nb_fiches_lues]
 */
function getClassement($limit = 20) {
    $limitP = intval($limit); // on convertit en int par securité
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
    // Astuce du L2 p.13 : On compte combien de gens ont un score strictement supérieur et on ajoute 1
    $idU = proteger($idUser);
    $sql = "SELECT COUNT(*) + 1 AS rang 
            FROM utilisateurs 
            WHERE score_total > (
                SELECT score_total FROM utilisateurs WHERE id = '$idU'
            )";
            
    return SQLGetChamp($sql);
}

?>