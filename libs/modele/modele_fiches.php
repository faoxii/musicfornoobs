<?php
/*
 * Fichier : libs/modele/modele_fiches.php
 * Auteur  : BOURGUIGNON Mathis
 * Description : fonctions d'acces aux tables fiches et categories
 */


/**
 * Renvoie toutes les categories avec leur nombre de fiches
 * Pour la sidebar du catalogue
 */
function getCategoriesAvecCompteurs() {
    // La vraie requête avec les compteurs (COUNT) et la jointure
    $sql = "SELECT c.id, c.nom, c.couleur, COUNT(f.id) AS nb_fiches 
            FROM categories c 
            LEFT JOIN fiches f ON f.id_categorie = c.id 
            GROUP BY c.id, c.nom, c.couleur 
            ORDER BY c.nom";
            
    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie toutes les fiches, eventuellement filtrees
 * $idCategorie = false pour pas de filtre, idem pour $niveau et $recherche
 */
function getFiches($idCategorie = false, $niveau = false, $recherche = false) {
    
    // le "WHERE 1=1" est une condition toujours vraie. 
    // Ca filtre rien du tout, mais ça nous évite de galérer en PHP pour savoir si on 
    // doit écrire "WHERE" ou "AND" pour le premier filtre. Là, on a juste à enchaîner les "AND"
    $sql = "SELECT f.id, f.titre, f.slug, f.niveau, f.chemin_audio, 
                   c.nom AS nom_categorie, c.couleur 
            FROM fiches f 
            JOIN categories c ON f.id_categorie = c.id 
            WHERE 1=1";

    // filtre 1 : si on a cliqué sur une catégorie précise dans la sidebar
    if ($idCategorie !== false && $idCategorie != "") {
        $idCat = proteger($idCategorie); 
        $sql .= " AND f.id_categorie = '$idCat'"; // on ajoute juste la condition à la suite
    }
    
    // filtre 2 :si on a sélectionné un niveau (Débutant, Intermédiaire...)
    if ($niveau !== false && $niveau != "") {
        $niv = proteger($niveau);
        $sql .= " AND f.niveau = '$niv'";
    }
    
    // Filtre 3 : si on a tapé un truc dans la barre de recherche
    if ($recherche !== false && $recherche != "") {
        $rech = proteger($recherche);
        // on utilise LIKE avec les % pour trouver le mot n'importe où dans le titre
        $sql .= " AND f.titre LIKE '%$rech%'"; 
    }

    $sql .= " ORDER BY f.titre ASC";

    return parcoursRs(SQLSelect($sql));
}


/**
 * Renvoie une fiche depuis son slug (avec sa categorie)
 * @return array|false tableau associatif ou false
 */
function getFicheParSlug($slug) {
    // on protege contre les injections    
    $slugProtege = proteger($slug);

    $sql = "SELECT f.id, f.titre, f.slug, f.niveau, f.contenu, f.chemin_audio, 
                   c.nom AS nom_categorie, c.couleur 
            FROM fiches f 
            JOIN categories c ON f.id_categorie = c.id 
            WHERE f.slug = '$slugProtege'";

    $resultat = parcoursRs(SQLSelect($sql));

    // on verifie si on a au moins une ligne 
    if (count($resultat) > 0) {
        return $resultat[0]; 
    } else {
        // si non donc slug introuvable, on renvoie false
        return false;
    }
}


/**
 * Renvoie une fiche depuis son id (principalement pour pré-remplir le formulaire admin)
 */
function getFiche($idFiche) {
    
    $idProtege = proteger($idFiche);
    $sql = "SELECT * FROM fiches WHERE id = '$idProtege'";

    $resultat = parcoursRs(SQLSelect($sql));

    if (count($resultat) > 0) {
        return $resultat[0];
    } else {
        return false; // ID introuvable
    }
}


/**
 *Crée une nouvelle fiche et renvoie son ID
 */
function creerFiche($titre, $slug, $idCategorie, $niveau, $contenu, $cheminAudio) {
    
    // on protège absolument toutes les variables saisies par l'admin
    $titreP = proteger($titre);
    $slugP  = proteger($slug);
    $idCatP = proteger($idCategorie);
    $nivP   = proteger($niveau);
    $contP  = proteger($contenu);
    
    // gestion pour l'audio (qui peut être vide)
    // on vérifie deux choses avant de considérer que le fichier audio est valide :
    // 1) !== false : on verifie que la fonction de récupération n'a pas échoué .
    // 2) != ""     : on verifie que l'utilisateur a bien saisi quelque chose (champ non vide).
    if ($cheminAudio !== false && $cheminAudio != "") {
        $audioSQL = "'" . proteger($cheminAudio) . "'";
    } else {
        $audioSQL = "NULL";
    }

    // requête d'insertion
    $sql = "INSERT INTO fiches (titre, slug, id_categorie, niveau, contenu, chemin_audio) 
            VALUES ('$titreP', '$slugP', '$idCatP', '$nivP', '$contP', $audioSQL)";

    return SQLInsert($sql);
}



/**
 * Modifie une fiche.
 * @param bool $supprimerAudio Si mis à true, on force le champ à NULL.
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
        // l'admin a coché "supprimer l'audio"
        $sqlAudio = ", chemin_audio = NULL";
    } 
    elseif ($cheminAudio !== false && $cheminAudio != "") {
        // lL'admin a uploadé un nouveau fichier
        $sqlAudio = ", chemin_audio = '" . proteger($cheminAudio) . "'";
    }
    // sinon (pas de suppression, pas de nouvel upload), on ne fait rien (l'audio actuel reste)

    $sql = "UPDATE fiches 
            SET titre = '$titreP', 
                slug = '$slugP', 
                id_categorie = '$idCatP', 
                niveau = '$nivP', 
                contenu = '$contP'
                $sqlAudio
            WHERE id = '$idFicheP'";

    return SQLUpdate($sql);
}



/**
 * Supprime une fiche et tout ce qui est lié (questions/réponses)
 * grâce à la règle ON DELETE CASCADE de la base de données.
 */
function supprimerFiche($idFiche) {
    //on protège l'ID avant de le passer à la requête
    $idP = proteger($idFiche);
    
    $sql = "DELETE FROM fiches WHERE id = '$idP'";
    return SQLDelete($sql);
}

/**
 * Genere un slug propre depuis un titre
 * "La gamme de Do majeur" -> "gamme-do-majeur"
 */
function genererSlug($titre) {
    // normalisation en minuscules :
    $slug = strtolower($titre);

    // 'iconv' convertit la chaîne vers un encodage ASCII. 
    // l'option //TRANSLIT tente de trouver l'équivalent le plus proche (ex: "é" devient "e").
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);

    //  nettoyage a l'aide d'une regex :
    // [^a-z0-9]+ signifie : "tout ce qui n'est PAS une lettre ou un chiffre".
    // on remplace tous ces caractères (espaces, ponctuations, symboles) par un seul tiret.
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

    // nettoyage des extrémités :
    // après le remplacement, il peut rester des tirets au début ou à la fin (ex: "-ma-gamme-").
    // 'trim' supprime ces tirets parasites pour un rendu propre.
    $slug = trim($slug, '-');

    return $slug;
}

?>
