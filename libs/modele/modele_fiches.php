<?php
/*
 * Fichier : libs/modele/modele_fiches.php
 * Auteur  : BOURGUIGNON Mathis
 * Description : fonctions d'accès aux tables fiches et categories
 */


/**
 * Renvoie toutes les catégories avec leur nombre de fiches associées.
 * LEFT JOIN pour que les catégories sans fiche apparaissent quand même (compteur à 0).
 */
function getCategoriesAvecCompteurs() {
    $sql = "SELECT c.id, c.nom, c.couleur, COUNT(f.id) AS nb_fiches
            FROM categories c
            LEFT JOIN fiches f ON f.id_categorie = c.id
            GROUP BY c.id, c.nom, c.couleur
            ORDER BY c.nom";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie les fiches, optionnellement filtrées par catégorie et/ou niveau.
 * Les deux paramètres sont facultatifs — false signifie "pas de filtre".
 * Le filtre par titre est géré côté client en JS (filtrerCatalogue dans quiz.js).
 */
function getFiches($idCategorie = false, $niveau = false) {
    // WHERE 1=1 : condition toujours vraie qui permet d'enchaîner les AND dynamiques
    // sans avoir à gérer si c'est le premier filtre ou non
    $sql = "SELECT f.id, f.titre, f.slug, f.niveau, f.chemin_audio,
                   c.nom AS categorie, c.couleur
            FROM fiches f
            JOIN categories c ON f.id_categorie = c.id
            WHERE 1=1";

    if ($idCategorie !== false && $idCategorie != "") {
        $idCat = proteger($idCategorie);
        $sql  .= " AND f.id_categorie = '$idCat'";
    }

    if ($niveau !== false && $niveau != "") {
        $niv  = proteger($niveau);
        $sql .= " AND f.niveau = '$niv'";
    }

    $sql .= " ORDER BY f.titre ASC";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie une fiche depuis son slug, avec sa catégorie.
 * Retourne false si le slug n'existe pas (la page appellera rediriger()).
 */
function getFicheParSlug($slug) {
    $slugProtege = proteger($slug);
    $sql         = "SELECT f.id, f.titre, f.slug, f.niveau, f.contenu, f.chemin_audio,
                           c.nom AS categorie, c.couleur
                    FROM fiches f
                    JOIN categories c ON f.id_categorie = c.id
                    WHERE f.slug = '$slugProtege'";

    $resultat = parcoursRs(SQLSelect($sql));

    if (count($resultat) > 0) {
        return $resultat[0];
    } else {
        return false;
    }
}


/**
 * Renvoie une fiche depuis son id, principalement pour pré-remplir le formulaire admin.
 * Retourne false si l'id n'existe pas.
 */
function getFiche($idFiche) {
    $idProtege = proteger($idFiche);
    $sql       = "SELECT * FROM fiches WHERE id = '$idProtege'";

    $resultat = parcoursRs(SQLSelect($sql));

    if (count($resultat) > 0) {
        return $resultat[0];
    } else {
        return false;
    }
}


/**
 * Crée une nouvelle fiche et renvoie son ID (via lastInsertId).
 * $cheminAudio peut être null si l'admin n'a pas fourni de fichier.
 */
function creerFiche($titre, $slug, $idCategorie, $niveau, $contenu, $cheminAudio) {
    $titreP = proteger($titre);
    $slugP  = proteger($slug);
    $idCatP = proteger($idCategorie);
    $nivP   = proteger($niveau);
    $contP  = proteger($contenu);

    // En SQL, NULL ne s'insère pas avec des guillemets, d'où la distinction
    if ($cheminAudio !== false && $cheminAudio != "") {
        $audioSQL = "'" . proteger($cheminAudio) . "'";
    } else {
        $audioSQL = "NULL";
    }

    $sql = "INSERT INTO fiches (titre, slug, id_categorie, niveau, contenu, chemin_audio)
            VALUES ('$titreP', '$slugP', '$idCatP', '$nivP', '$contP', $audioSQL)";

    return SQLInsert($sql);
}


/**
 * Modifie une fiche existante.
 * L'audio suit trois cas : suppression explicite (NULL), nouveau fichier uploadé, ou inchangé.
 * On construit la partie audio du SET dynamiquement pour ne pas écraser accidentellement
 * un fichier existant quand l'admin ne touche pas à l'audio.
 */
function modifierFiche($idFiche, $titre, $slug, $idCategorie, $niveau, $contenu, $cheminAudio, $supprimerAudio = false) {
    $idFicheP = proteger($idFiche);
    $titreP   = proteger($titre);
    $slugP    = proteger($slug);
    $idCatP   = proteger($idCategorie);
    $nivP     = proteger($niveau);
    $contP    = proteger($contenu);

    $sqlAudio = "";

    if ($supprimerAudio === true) {
        $sqlAudio = ", chemin_audio = NULL";
    } elseif ($cheminAudio !== false && $cheminAudio != "") {
        $sqlAudio = ", chemin_audio = '" . proteger($cheminAudio) . "'";
    }
    // Si ni suppression ni nouvel upload, on ne modifie pas le champ audio

    $sql = "UPDATE fiches
            SET titre        = '$titreP',
                slug         = '$slugP',
                id_categorie = '$idCatP',
                niveau       = '$nivP',
                contenu      = '$contP'
                $sqlAudio
            WHERE id = '$idFicheP'";

    return SQLUpdate($sql);
}


/**
 * Supprime une fiche. Les questions et réponses associées sont supprimées en cascade
 * grâce à la contrainte ON DELETE CASCADE définie en BDD.
 */
function supprimerFiche($idFiche) {
    $idP = proteger($idFiche);
    $sql = "DELETE FROM fiches WHERE id = '$idP'";

    return SQLDelete($sql);
}


/**
 * Génère un slug URL-safe depuis un titre.
 * Exemple : "La gamme de Do majeur" → "la-gamme-de-do-majeur"
 * iconv //TRANSLIT convertit les accents en leur équivalent ASCII (é→e, à→a, etc.)
 * avant que preg_replace remplace tout ce qui n'est pas alphanumérique par un tiret.
 */
function genererSlug($titre) {
    $slug = strtolower($titre);
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    return $slug;
}


/**
 * Renvoie toutes les fiches avec le nombre de questions associées.
 * Utilisé uniquement dans l'interface admin pour afficher "X / 5 questions".
 * LEFT JOIN pour que les fiches sans question apparaissent (nb_questions = 0).
 */
function getFichesDetaillees() {
    $sql = "SELECT f.id, f.titre, f.slug, f.niveau, f.chemin_audio,
                   c.nom AS categorie, c.couleur,
                   COUNT(q.id) AS nb_questions
            FROM fiches f
            JOIN categories c ON f.id_categorie = c.id
            LEFT JOIN quiz_questions q ON q.id_fiche = f.id
            GROUP BY f.id, f.titre, f.slug, f.niveau, f.chemin_audio, c.nom, c.couleur
            ORDER BY f.titre ASC";

    return parcoursRs(SQLSelect($sql));
}

?>
