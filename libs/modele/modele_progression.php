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
    $idU = proteger($idUser);
    $idF = proteger($idFiche);
    
    // on utilise INSERT IGNORE pour éviter une erreur si l'utilisateur 
    // a déjà lu la fiche (car il y a une contrainte UNIQUE sur id_utilisateur , id_fiche)
    $sql = "INSERT IGNORE INTO progression (id_utilisateur, id_fiche) 
            VALUES ('$idU', '$idF')";
            
    return SQLInsert($sql);
}


/**
 * Renvoie le nombre de fiches lues par un utilisateur
 */

function getNbFichesLues($idUser) {
    $idU = proteger($idUser);
    $sql = "SELECT COUNT(*) FROM progression WHERE id_utilisateur = '$idU'";
    return SQLGetChamp($sql);
}

/**
 * Verifie si une fiche a ete lue par un utilisateur
 * @return bool
 */
function estFicheLue($idUser, $idFiche) {
    $idU = proteger($idUser);
    $idF = proteger($idFiche);
    
    $sql = "SELECT COUNT(*) FROM progression 
            WHERE id_utilisateur = '$idU' AND id_fiche = '$idF'";
    
    // si le résultat est > 0, c'est que la ligne existe (donc true)
    return (SQLGetChamp($sql) > 0);
}
?>
