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
    $sql = "INSERT INTO resultats (id_utilisateur, id_fiche, score, points_gagnes)
            VALUES ('$idUser', '$idFiche', '$score', '$pointsGagnes')";

    return SQLInsert($sql);
}


/**
 * Renvoie le meilleur score d'un utilisateur sur une fiche
 * 0 si jamais passe
 */
function getMeilleurScore($idUser, $idFiche) {
    $sql = "SELECT MAX(score)
            FROM resultats
            WHERE id_utilisateur = '$idUser'
              AND id_fiche = '$idFiche'";

    return SQLGetChamp($sql);
}


/**
 * Renvoie les N derniers quiz passes par un utilisateur
 * Tableau de [score, points_gagnes, date_passage, titre_fiche, categorie, couleur]
 */
function getActiviteRecente($idUser, $limit = 3) {
    $sql = "SELECT
                r.score,
                r.points_gagnes,
                r.date_passage,
                f.titre,
                c.nom     AS categorie,
                c.couleur
            FROM resultats r
            JOIN fiches f     ON r.id_fiche     = f.id
            JOIN categories c ON f.id_categorie = c.id
            WHERE r.id_utilisateur = '$idUser'
            ORDER BY r.date_passage DESC
            LIMIT $limit";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie le nombre de quiz differents passes par un utilisateur
 */
function getNbQuizPasses($idUser) {
    $sql = "SELECT COUNT(DISTINCT id_fiche)
            FROM resultats
            WHERE id_utilisateur = '$idUser'";

    return SQLGetChamp($sql);
}

?>
