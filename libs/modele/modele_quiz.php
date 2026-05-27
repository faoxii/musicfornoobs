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
    $sql = "SELECT
                f.id,
                f.titre,
                f.slug,
                f.niveau,
                c.nom       AS categorie,
                c.couleur,
                COUNT(q.id) AS nb_questions
            FROM fiches f
            JOIN categories c ON f.id_categorie = c.id
            LEFT JOIN quiz_questions q ON q.id_fiche = f.id";

    if ($idCategorie !== false && $niveau !== false)
        $sql .= " WHERE f.id_categorie = '$idCategorie' AND f.niveau = '$niveau'";
    else if ($idCategorie !== false)
        $sql .= " WHERE f.id_categorie = '$idCategorie'";
    else if ($niveau !== false)
        $sql .= " WHERE f.niveau = '$niveau'";

    $sql .= " GROUP BY f.id, f.titre, f.slug, f.niveau, c.nom, c.couleur
              HAVING nb_questions >= 5
              ORDER BY f.titre";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie une question + ses 4 reponses (pour le deroulement du quiz)
 * $numeroQuestion entre 1 et 5
 */
function getQuestion($idFiche, $numeroQuestion) {
    $sql = "SELECT
                q.id           AS id_question,
                q.enonce,
                q.type,
                q.chemin_audio AS chemin_audio_question,
                r.id           AS id_reponse,
                r.contenu,
                r.est_correct,
                r.ordre
            FROM quiz_questions q
            JOIN quiz_reponses r ON r.id_question = q.id
            WHERE q.id_fiche = '$idFiche'
              AND q.ordre    = '$numeroQuestion'
            ORDER BY r.ordre";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie toutes les questions d'une fiche (pour l'admin)
 * IMPORTANT : a livrer tot, Mathis en a besoin pour l'interface 13
 */
function getQuestionsFiche($idFiche) {
    $sql = "SELECT
                q.id           AS id_question,
                q.enonce,
                q.type,
                q.chemin_audio AS chemin_audio_question,
                q.ordre        AS ordre_question,
                r.id           AS id_reponse,
                r.contenu,
                r.est_correct,
                r.ordre        AS ordre_reponse
            FROM quiz_questions q
            JOIN quiz_reponses r ON r.id_question = q.id
            WHERE q.id_fiche = '$idFiche'
            ORDER BY q.ordre, r.ordre";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie une question depuis son id (avec ses 4 reponses)
 */
function getQuestion_id($idQuestion) {
    $sql = "SELECT
                q.id           AS id_question,
                q.enonce,
                q.type,
                q.chemin_audio AS chemin_audio_question,
                q.ordre,
                r.id           AS id_reponse,
                r.contenu,
                r.est_correct,
                r.ordre        AS ordre_reponse
            FROM quiz_questions q
            JOIN quiz_reponses r ON r.id_question = q.id
            WHERE q.id = '$idQuestion'
            ORDER BY r.ordre";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Cree une nouvelle question avec ses 4 reponses
 * $reponses = tableau de 4 elements [['contenu' => ..., 'est_correct' => 0/1], ...]
 */
function creerQuestion($idFiche, $type, $enonce, $cheminAudio, $ordre) {
    $valAudio = ($cheminAudio !== null) ? "'$cheminAudio'" : "NULL";

    $sql = "INSERT INTO quiz_questions (id_fiche, type, enonce, chemin_audio, ordre)
            VALUES ('$idFiche', '$type', '$enonce', $valAudio, '$ordre')";

    return SQLInsert($sql);
}


/**
 * Modifie une question existante (supprime puis recree les reponses)
 */
function modifierQuestion($idQuestion, $type, $enonce, $cheminAudio) {
    $valAudio = ($cheminAudio !== null) ? "'$cheminAudio'" : "NULL";

    $sql = "UPDATE quiz_questions
            SET type = '$type', enonce = '$enonce', chemin_audio = $valAudio
            WHERE id = '$idQuestion'";

    return SQLUpdate($sql);
}


/**
 * Supprime une question (cascade les reponses)
 */
function supprimerQuestion($idQuestion) {
    $sql = "DELETE FROM quiz_questions WHERE id = '$idQuestion'";
    return SQLDelete($sql);
}


/**
 * Verifie si une reponse est correcte (utilise a la soumission du quiz)
 */
function estBonneReponse($idQuestion, $idReponse) {
    $sql = "SELECT est_correct
            FROM quiz_reponses
            WHERE id = '$idReponse'
              AND id_question = '$idQuestion'";

    return SQLGetChamp($sql);
}

?>
