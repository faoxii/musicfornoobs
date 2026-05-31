<?php
/*
 * Fichier : libs/modele/modele_resultats.php
 * Auteur  : LEFEBVRE Lucas
 * Description : fonctions d'accès à la table resultats
 */


/**
 * Enregistre un passage de quiz (score brut + points effectivement gagnés).
 * points_gagnes peut être 0 si l'utilisateur n'a pas battu son meilleur score —
 * on l'enregistre quand même pour garder l'historique d'activité complet.
 */
function enregistrerResultat($idUser, $idFiche, $score, $pointsGagnes) {
    $sql = "INSERT INTO resultats (id_utilisateur, id_fiche, score, points_gagnes)
            VALUES ('$idUser', '$idFiche', '$score', '$pointsGagnes')";

    return SQLInsert($sql);
}


/**
 * Renvoie le meilleur score d'un utilisateur sur une fiche donnée.
 * Retourne NULL (pas 0) si l'utilisateur n'a jamais passé ce quiz :
 * le contrôleur doit donc tester avec !$ancienMax avant de calculer le delta.
 */
function getMeilleurScore($idUser, $idFiche) {
    $sql = "SELECT MAX(score)
            FROM resultats
            WHERE id_utilisateur = '$idUser'
              AND id_fiche = '$idFiche'";

    return SQLGetChamp($sql);
}


/**
 * Renvoie les N derniers quiz passés par un utilisateur, avec le titre de la fiche
 * et la couleur de sa catégorie (nécessaire pour afficher le badge coloré dans le dashboard).
 * Triple jointure : resultats → fiches → categories.
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
 * Renvoie le nombre de quiz distincts passés par un utilisateur.
 * DISTINCT sur id_fiche car un même quiz peut être repassé plusieurs fois.
 */
function getNbQuizPasses($idUser) {
    $sql = "SELECT COUNT(DISTINCT id_fiche)
            FROM resultats
            WHERE id_utilisateur = '$idUser'";

    return SQLGetChamp($sql);
}

?>
