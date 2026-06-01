<?php
/*
 * Fichier : libs/modele/modele_quiz.php
 * Auteur  : LEFEBVRE Lucas
 * Description : fonctions d'accès aux tables quiz_questions et quiz_reponses
 */


/**
 * Renvoie les fiches qui ont un quiz jouable (au moins 5 questions).
 * On utilise HAVING plutôt que WHERE pour filtrer sur nb_questions : WHERE ne peut pas
 * utiliser un alias de colonne calculé par COUNT, et il s'exécute avant le GROUP BY.
 * HAVING s'exécute après, ce qui permet de filtrer sur le résultat de l'agrégation.
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

    if ($idCategorie !== false && $niveau !== false) {
        $sql .= " WHERE f.id_categorie = '$idCategorie' AND f.niveau = '$niveau'";
    } elseif ($idCategorie !== false) {
        $sql .= " WHERE f.id_categorie = '$idCategorie'";
    } elseif ($niveau !== false) {
        $sql .= " WHERE f.niveau = '$niveau'";
    }

    $sql .= " GROUP BY f.id, f.titre, f.slug, f.niveau, c.nom, c.couleur
              HAVING nb_questions >= 5
              ORDER BY f.titre";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie la Nième question d'une fiche avec ses 4 réponses.
 *
 * HISTORIQUE DU BUG :
 * L'ancienne version cherchait avec WHERE q.ordre = $numeroQuestion (ex : ordre = 1).
 * Quand on supprimait des questions et qu'on en recréait, le calcul de l'ordre utilisait
 * count(questions existantes) + 1 au lieu de max(ordre) + 1. Résultat : des trous dans
 * les numéros d'ordre (ex : 3, 4, 4, 4, 5 au lieu de 1, 2, 3, 4, 5). La question
 * avec ordre = 1 n'existait plus → getQuestion retournait vide → le quiz renvoyait
 * silencieusement vers le catalogue sans message d'erreur.
 *
 * SOLUTION :
 * On cherche la Nième question par sa POSITION dans la liste triée, pas par sa valeur
 * d'ordre. Peu importe que les ordres soient 1,2,3,4,5 ou 3,7,9,12,15 — la 1ère
 * question dans l'ordre reste la 1ère question du quiz.
 */
function getQuestion($idFiche, $numeroQuestion) {
    $idF = proteger($idFiche);

    // OFFSET = combien de lignes on saute avant de prendre la suivante.
    // Question 1 → OFFSET 0 (on saute rien, on prend la 1ère).
    // Question 2 → OFFSET 1 (on saute la 1ère, on prend la 2ème). Etc.
    $offset = intval($numeroQuestion) - 1;

    // La sous-requête récupère l'id de la Nième question triée par ordre.
    // Le JOIN avec quiz_reponses retourne 4 lignes (une par réponse A/B/C/D).
    // est_correct est sélectionné ici pour la vérification côté serveur uniquement —
    // il ne doit jamais être affiché dans le HTML envoyé au navigateur.
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
            WHERE q.id = (
                SELECT id FROM quiz_questions
                WHERE id_fiche = '$idF'
                ORDER BY ordre
                LIMIT 1 OFFSET $offset
            )
            ORDER BY r.ordre";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie toutes les questions d'une fiche avec leurs réponses.
 * Retourne plusieurs lignes par question (une par réponse) — le template admin
 * regroupe ensuite par id_question pour construire l'affichage.
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
 * Renvoie une question depuis son id avec ses 4 réponses.
 * Utilisé dans le formulaire d'édition admin pour pré-remplir le formulaire.
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
 * Crée une question et renvoie son id.
 * Les réponses sont créées séparément via creerReponse().
 */
function creerQuestion($idFiche, $type, $enonce, $cheminAudio, $ordre) {
    $valAudio = ($cheminAudio !== null) ? "'$cheminAudio'" : "NULL";

    $sql = "INSERT INTO quiz_questions (id_fiche, type, enonce, chemin_audio, ordre)
            VALUES ('$idFiche', '$type', '$enonce', $valAudio, '$ordre')";

    return SQLInsert($sql);
}


/**
 * Met à jour une question existante.
 * Passer null à $cheminAudio met le champ à NULL en BDD (suppression de l'audio).
 */
function modifierQuestion($idQuestion, $type, $enonce, $cheminAudio) {
    $valAudio = ($cheminAudio !== null) ? "'$cheminAudio'" : "NULL";

    $sql = "UPDATE quiz_questions
            SET type = '$type', enonce = '$enonce', chemin_audio = $valAudio
            WHERE id = '$idQuestion'";

    return SQLUpdate($sql);
}


/**
 * Supprime une question. Ses réponses sont supprimées en cascade par la BDD.
 */
function supprimerQuestion($idQuestion) {
    $sql = "DELETE FROM quiz_questions WHERE id = '$idQuestion'";

    return SQLDelete($sql);
}


/**
 * Crée une réponse pour une question.
 * $ordre va de 1 à 4 (correspond aux choix A, B, C, D).
 * $estCorrect vaut 1 pour la bonne réponse, 0 pour les fausses.
 */
function creerReponse($idQuestion, $contenu, $estCorrect, $ordre) {
    $idQ         = proteger($idQuestion);
    $contenuP    = proteger($contenu);
    $estCorrectP = intval($estCorrect);
    $ordreP      = intval($ordre);

    $sql = "INSERT INTO quiz_reponses (id_question, contenu, est_correct, ordre)
            VALUES ('$idQ', '$contenuP', '$estCorrectP', '$ordreP')";

    return SQLInsert($sql);
}


/**
 * Met à jour le texte et le statut correct/incorrect d'une réponse existante.
 */
function modifierReponse($idReponse, $contenu, $estCorrect) {
    $idR         = proteger($idReponse);
    $contenuP    = proteger($contenu);
    $estCorrectP = intval($estCorrect);

    $sql = "UPDATE quiz_reponses
            SET contenu = '$contenuP', est_correct = '$estCorrectP'
            WHERE id = '$idR'";

    return SQLUpdate($sql);
}

?>
