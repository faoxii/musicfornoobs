/*
 * Fichier : js/quiz.js
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : logique du quiz côté client (sélection de réponse)
 *               et filtre live des catalogues fiches/quiz
 */



/**
 * Filtre en temps réel les cartes d'un catalogue (fiches ou quiz) selon le titre..
 * Le message "aucun résultat" n'est affiché que si la recherche n'est pas vide,
 * pour ne pas perturber le chargement initial de la page.
 */
function filtrerCatalogue(inputId, gridId, msgId) {
    // trim() supprime les espaces au début et à la fin de la chaîne
    // toLowerCase() met tout en minuscules pour une recherche insensible à la casse
    const search = document.getElementById(inputId).value.toLowerCase().trim();
    // On sélectionne toutes les cartes du catalogue ciblé par gridId
    const cards  = document.querySelectorAll('#' + gridId + ' .card-fiche-custom');
    // on compte pour le moment 0 carte affichée
    let visible = 0;

    // On parcourt toutes les cartes pour vérifier si leur titre contient la chaîne de recherche
    cards.forEach(function(card) {
        //si le dataset de la carte ne contient pas de titre, on utilise une chaîne vide par défaut pour éviter les erreurs
        const titre = card.dataset.titre || '';
        // includes() vérifie si le titre contient la chaîne de recherche (search)
        const show  = titre.includes(search);
        // On affiche la carte si elle correspond à la recherche, sinon on la masque
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    // On récupère l'élément HTML prévu pour afficher le message "Aucun résultat"
    const msg = document.getElementById(msgId);

    if (msg) {
        // Si aucune carte n'est visible et que l'utilisateur a tapé une recherche
        if (visible === 0 && search !== '') {
            // On affiche le message
            msg.style.display = '';
        } else {
            // Sinon si des cartes sont trouvées ou si la recherche est vide, on masque le message
            msg.style.display = 'none';
        }
    }
}
