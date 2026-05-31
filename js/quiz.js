/*
 * Fichier : js/quiz.js
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : logique du quiz côté client (sélection de réponse)
 *               et filtre live des catalogues fiches/quiz
 */


/**
 * Marque visuellement une réponse comme sélectionnée et coche le radio caché.
 * Les labels .reponse-quiz servent de proxy cliquable pour le radio natif,
 * ce qui permet de styler librement sans perdre l'accessibilité du formulaire.
 */
function selectionnerReponse(element, idReponse) {
    var toutes = document.querySelectorAll(".reponse-quiz");
    for (var i = 0; i < toutes.length; i++) {
        toutes[i].classList.remove("selected");
    }

    element.classList.add("selected");

    var radio = element.querySelector("input[type=radio]");
    if (radio) radio.checked = true;
}


/**
 * Filtre en temps réel les cartes d'un catalogue (fiches ou quiz) selon le titre.
 * La comparaison se fait sur data-titre (lowercase, défini côté PHP) pour éviter
 * de dépendre de la casse ou des accents saisis par l'utilisateur.
 * Le message "aucun résultat" n'est affiché que si la recherche n'est pas vide,
 * pour ne pas perturber le chargement initial de la page.
 */
function filtrerCatalogue(inputId, gridId, msgId) {
    var search = document.getElementById(inputId).value.toLowerCase().trim();
    var cards  = document.querySelectorAll('#' + gridId + ' .card-fiche-custom');
    var visible = 0;

    cards.forEach(function(card) {
        var titre = card.dataset.titre || '';
        var show  = titre.includes(search);
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    var msg = document.getElementById(msgId);
    if (msg) msg.style.display = (visible === 0 && search !== '') ? '' : 'none';
}
