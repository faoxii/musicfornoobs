/*
 * Fichier : js/audio.js
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : lecteur audio custom — play/pause, barre de progression, chrono, navigation.
 *               Fonctionne à la fois sur la fiche pédagogique (lecteurFiche) et sur le quiz
 *               (lecteurQuiz). Le DOMContentLoaded détecte lequel est présent sur la page.
 */


/**
 * Bascule entre lecture et pause pour un élément <audio> identifié par son id.
 * Appelé depuis les onclick des templates (pas depuis d'autres fichiers JS).
 */
function togglePlay(audioId) {
    // On récupère le lecteur et le bouton de lecture associés à l'id fourni
    const lecteur = document.getElementById(audioId);
    
    const btnPlay = document.getElementById("btnPlay");
    
    // Si le lecteur n'existe pas (id incorrect ou élément manquant), on ne fait rien
    if (!lecteur) return;

    // Si le lecteur est en pause, on lance la lecture et on change l'icône du bouton en "pause"
    if (lecteur.paused) {
        lecteur.play();
        if (btnPlay) btnPlay.innerHTML = "⏸";
    } else {
        lecteur.pause();
        if (btnPlay) btnPlay.innerHTML = "▶";
    }
}

/**
 * Remet la lecture au début et joue immédiatement.
 */
function rejouer(audioId) {
    // On récupère le lecteur et le bouton de lecture associés à l'id fourni
    const lecteur = document.getElementById(audioId);
    const btnPlay = document.getElementById("btnPlay");

    // Si le lecteur n'existe pas (id incorrect ou élément manquant), on ne fait rien
    if (!lecteur) return;

    // On remet la lecture au début 
    lecteur.currentTime = 0;
    // On lance la lecture 
    lecteur.play();
    // On change l'icône du bouton en "pause"
    if (btnPlay) btnPlay.innerHTML = "⏸";
}


/**
 * Formate un nombre de secondes en MM:SS.
 * Retourne "--:--" si la durée n'est pas encore connue (métadonnées non chargées).
 */
function formatTime(seconds) {
    // Si la durée n'est pas encore disponible, on affiche un placeholder
    // isNan ca veut dire 'is Not a Number', donc on vérifie que seconds est bien un nombre avant de faire les calculs
    if (isNaN(seconds)) return "--:--";
    // On calcule les minutes et les secondes restantes
    // Math.floor arrondit à l'entier inférieur
    const min = Math.floor(seconds / 60);
    let sec = Math.floor(seconds % 60);
    // On ajoute un zéro devant les secondes si elles sont inférieures à 10 pour garder un format uniforme
    if (sec < 10) sec = "0" + sec;
    // On retourne la chaîne formatée
    return min + ":" + sec;
}


/**
 * Affiche la durée totale dès que les métadonnées sont disponibles.
 * Déclenché par l'événement "loadedmetadata" du lecteur.
 */
function initialiserChrono(lecteur, tempsLabel) {
    // tempsLabel peut etre null si l'élément n'existe pas sur la page (ex : quiz sans chrono), on vérifie avant de l'utiliser
    if (tempsLabel) {
        tempsLabel.innerHTML = "0:00 / " + formatTime(lecteur.duration);
    }
}


/**
 * Met à jour la barre de progression et le chrono à chaque tick de lecture. ;
 */
function actualiserProgression(lecteur, barre, tempsLabel) {
    
    // Si la durée n'est pas encore connue, on ne peut pas calculer le pourcentage, donc on sort de la fonction
    if (!lecteur.duration) return;

    // On calcule le pourcentage de la piste déjà jouée
    // duration est la durée totale de la piste, currentTime est le temps écoulé depuis le début de la lecture
    const pourcentage = (lecteur.currentTime / lecteur.duration) * 100;

    if (barre) barre.style.width = pourcentage + "%";


    if (tempsLabel) {
        tempsLabel.innerHTML = formatTime(lecteur.currentTime) + " / " + formatTime(lecteur.duration);
    }
}


/**
 * Remet l'interface à zéro quand la piste se termine.
 */
function réinitialiserLecteurFin(lecteur, barre, btnPlay, tempsLabel) {
    // width à 0% pour vider la barre de progression, icône du bouton à "play" et chrono remis à zéro
    if (barre) barre.style.width = "0%";
    if (btnPlay) btnPlay.innerHTML = "▶";
    if (tempsLabel) tempsLabel.innerHTML = "0:00 / " + formatTime(lecteur.duration);
}


/**
 * Positionne la lecture à l'endroit cliqué sur la barre de progression.
 * Uniquement activé sur la fiche pédagogique — le quiz n'a pas de navigation dans la piste.
 */
function naviguerDansPiste(event, lecteur, conteneurBarre) {
    if (!lecteur.duration) return;

    // getBoundingClientREct() retourne les dimensions et la position d'un élément par rapport à la fenêtre.
    // par ecemple, dimensions.width nous donne la largeur de la barre de progression, et dimensions.left sa position horizontale.
    //  On en a besoin pour calculer le ratio du clic par rapport à la largeur de la barre.
    const dimensions  = conteneurBarre.getBoundingClientRect();
    // donc ici on calcule la position du clic par rapport au début de la barre (clicX) et on divise par la largeur totale pour obtenir un ratio entre 0 et 1.
    const clicX       = event.clientX - dimensions.left;
    const ratio       = clicX / dimensions.width;
    // Enfin, on multiplie ce ratio par la durée totale de la piste pour obtenir le temps correspondant au point cliqué, et on positionne la lecture à ce moment-là.
    lecteur.currentTime = ratio * lecteur.duration;
}


document.addEventListener("DOMContentLoaded", function() {
    // On détecte quel lecteur est présent sur la page courante
    const lecteur    = document.getElementById("lecteurQuiz") || document.getElementById("lecteurFiche");
    const barre      = document.getElementById("barreProgression");
    const btnPlay    = document.getElementById("btnPlay");
    const tempsLabel = document.querySelector(".audio-time-label");

    // Si aucun lecteur n'est trouvé, on ne fait rien 
    if (!lecteur) return;

    // Quand les métadonnées sont chargées, on initialise le chrono avec la durée totale de la piste
    // on est oblige de faire ça dans un event listener "loadedmetadata" car la durée n'est pas disponible avant que les métadonnées soient chargée
    lecteur.addEventListener("loadedmetadata", function() {
        initialiserChrono(lecteur, tempsLabel);
    });
    // A chaque tick de lecture, on met à jour la barre de progression et le chrono
    lecteur.addEventListener("timeupdate", function() {
        actualiserProgression(lecteur, barre, tempsLabel);
    });

    // Quand la piste se termine, on remet l'interface à zéro
    // ended est l'événement qui se déclenche quand la lecture arrive à la fin de la piste,
    //  que ce soit parce que l'utilisateur a laissé jouer jusqu'au bout ou parce qu'il a cliqué sur "rejouer"
    lecteur.addEventListener("ended", function() {
        réinitialiserLecteurFin(lecteur, barre, btnPlay, tempsLabel);
    });

    // La navigation au clic sur la barre n'est activée que pour la fiche pédagogique
    if (lecteur.id === "lecteurFiche" && barre) {
        // On ajoute un écouteur de clic sur le conteneur de la barre de progression pour permettre la navigation dans la piste
        // barre.parentElement est le conteneur de la barre de progression, qui correspond à la zone cliquable pour naviguer dans la piste
        const conteneurBarre = barre.parentElement;
        conteneurBarre.addEventListener("click", function(event) {
            naviguerDansPiste(event, lecteur, conteneurBarre);
        });
    }
});
