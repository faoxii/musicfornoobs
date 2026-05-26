/*
 * Fichier : js/quiz.js
 * Auteur  : LEFEBVRE Lucas
 * Description : logique du quiz cote client (selection de la reponse)
 */


/**
 * Marque une reponse comme selectionnee (effet visuel)
 * Appele depuis le onclick des .reponse-quiz
 */
function selectionnerReponse(element, idReponse) {
    // Retirer la classe selected sur toutes les reponses
    var toutes = document.querySelectorAll(".reponse-quiz");
    for (var i = 0; i < toutes.length; i++) {
        toutes[i].classList.remove("selected");
    }

    // Ajouter sur celle cliquee
    element.classList.add("selected");

    // Cocher le radio cache associe
    var radio = element.querySelector("input[type=radio]");
    if (radio) radio.checked = true;
}
