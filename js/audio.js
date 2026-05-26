/*
 * Fichier : js/audio.js
 * Auteur  : LEFEBVRE Lucas
 * Description : logique du lecteur audio (boutons play/rejouer)
 */


/**
 * Bascule lecture/pause du lecteur audio
 */
function togglePlay(audioId) {
    var lecteur = document.getElementById(audioId);
    if (!lecteur) return;

    if (lecteur.paused) {
        lecteur.play();
    } else {
        lecteur.pause();
    }
}


/**
 * Remet la lecture au debut et joue
 */
function rejouer(audioId) {
    var lecteur = document.getElementById(audioId);
    if (!lecteur) return;

    lecteur.currentTime = 0;
    lecteur.play();
}
