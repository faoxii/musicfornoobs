<?php
/*
 * Fichier : libs/modele/modele_progression.php
 * Auteur  : BOURGUIGNON Mathis
 * Description : fonctions d'accès à la table progression (fiches lues par utilisateur)
 */


/**
 * Enregistre la première lecture d'une fiche par un utilisateur.
 * INSERT IGNORE tire parti de la contrainte UNIQUE(id_utilisateur, id_fiche) en BDD :
 * si la ligne existe déjà, la requête ne fait rien silencieusement au lieu de planter.
 * Cela permet d'appeler cette fonction à chaque ouverture de fiche sans vérification préalable.
 */
function marquerFicheLue($idUser, $idFiche) {
    $idU = proteger($idUser);
    $idF = proteger($idFiche);

    $sql = "INSERT IGNORE INTO progression (id_utilisateur, id_fiche)
            VALUES ('$idU', '$idF')";

    return SQLInsert($sql);
}


/**
 * Renvoie le nombre total de fiches lues par un utilisateur (pour le dashboard).
 */
function getNbFichesLues($idUser) {
    $idU = proteger($idUser);
    $sql = "SELECT COUNT(*) FROM progression WHERE id_utilisateur = '$idU'";

    return SQLGetChamp($sql);
}


/**
 * Vérifie si une fiche spécifique a été lue par un utilisateur.
 * Utilisé pour afficher le badge "✓ Lu" dans le catalogue.
 */
function estFicheLue($idUser, $idFiche) {
    $idU = proteger($idUser);
    $idF = proteger($idFiche);

    $sql = "SELECT COUNT(*) FROM progression
            WHERE id_utilisateur = '$idU' AND id_fiche = '$idF'";

    return (SQLGetChamp($sql) > 0);
}

?>
