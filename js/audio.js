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
    var lecteur = document.getElementById(audioId);
    var btnPlay = document.getElementById("btnPlay");
    if (!lecteur) return;

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
    var lecteur = document.getElementById(audioId);
    var btnPlay = document.getElementById("btnPlay");
    if (!lecteur) return;

    lecteur.currentTime = 0;
    lecteur.play();
    if (btnPlay) btnPlay.innerHTML = "⏸";
}


/**
 * Formate un nombre de secondes en MM:SS.
 * Retourne "--:--" si la durée n'est pas encore connue (métadonnées non chargées).
 */
function formatTime(seconds) {
    if (isNaN(seconds)) return "--:--";
    var min = Math.floor(seconds / 60);
    var sec = Math.floor(seconds % 60);
    if (sec < 10) sec = "0" + sec;
    return min + ":" + sec;
}


/**
 * Affiche la durée totale dès que les métadonnées sont disponibles.
 * Déclenché par l'événement "loadedmetadata" du lecteur.
 */
function initialiserChrono(lecteur, tempsLabel) {
    if (tempsLabel) {
        tempsLabel.innerHTML = "0:00 / " + formatTime(lecteur.duration);
    }
}


/**
 * Met à jour la barre de progression et le chrono à chaque tick de lecture.
 * L'événement "timeupdate" se déclenche très fréquemment (~4 fois/sec) ;
 * on garde la logique légère pour éviter tout lag visible.
 */
function actualiserProgression(lecteur, barre, tempsLabel) {
    if (!lecteur.duration) return;

    var pourcentage = (lecteur.currentTime / lecteur.duration) * 100;
    if (barre) barre.style.width = pourcentage + "%";

    if (tempsLabel) {
        tempsLabel.innerHTML = formatTime(lecteur.currentTime) + " / " + formatTime(lecteur.duration);
    }
}


/**
 * Remet l'interface à zéro quand la piste se termine.
 */
function réinitialiserLecteurFin(lecteur, barre, btnPlay, tempsLabel) {
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

    var dimensions  = conteneurBarre.getBoundingClientRect();
    var clicX       = event.clientX - dimensions.left;
    var ratio       = clicX / dimensions.width;

    lecteur.currentTime = ratio * lecteur.duration;
}


document.addEventListener("DOMContentLoaded", function() {
    // On détecte quel lecteur est présent sur la page courante
    var lecteur    = document.getElementById("lecteurQuiz") || document.getElementById("lecteurFiche");
    var barre      = document.getElementById("barreProgression");
    var btnPlay    = document.getElementById("btnPlay");
    var tempsLabel = document.querySelector(".audio-time-label");

    if (!lecteur) return;

    lecteur.addEventListener("loadedmetadata", function() {
        initialiserChrono(lecteur, tempsLabel);
    });

    lecteur.addEventListener("timeupdate", function() {
        actualiserProgression(lecteur, barre, tempsLabel);
    });

    lecteur.addEventListener("ended", function() {
        réinitialiserLecteurFin(lecteur, barre, btnPlay, tempsLabel);
    });

    // La navigation au clic sur la barre n'est activée que pour la fiche pédagogique
    if (lecteur.id === "lecteurFiche" && barre) {
        var conteneurBarre = barre.parentElement;
        conteneurBarre.addEventListener("click", function(event) {
            naviguerDansPiste(event, lecteur, conteneurBarre);
        });
    }
});
