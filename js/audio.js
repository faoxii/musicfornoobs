/*
 * Fichier : js/audio.js
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : Logique modulaire du lecteur audio (boutons play/rejouer, barre de progression, chrono et navigation)
 */

// --- 1. FONCTIONS DE CONTRÔLE PRINCIPALES ---

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

function rejouer(audioId) {
    var lecteur = document.getElementById(audioId);
    var btnPlay = document.getElementById("btnPlay");
    if (!lecteur) return;

    lecteur.currentTime = 0;
    lecteur.play();
    if (btnPlay) btnPlay.innerHTML = "⏸";
}

// --- 2. FONCTIONS OUTILS ET FORMATEURS ---

function formatTime(seconds) {
    if (isNaN(seconds)) return "--:--";
    var min = Math.floor(seconds / 60);
    var sec = Math.floor(seconds % 60);
    if (sec < 10) sec = "0" + sec;
    return min + ":" + sec;
}

// --- 3. SOUS-FONCTIONS GESTIONNAIRES D'ÉVÉNEMENTS (REFACTORING) ---

// Initialise le texte du chrono à la récupération des métadonnées
function initialiserChrono(lecteur, tempsLabel) {
    if (tempsLabel) {
        tempsLabel.innerHTML = "0:00 / " + formatTime(lecteur.duration);
    }
}

// Gère l'avancement de la barre et la mise à jour du timer
function actualiserProgression(lecteur, barre, tempsLabel) {
    if (!lecteur.duration) return;

    var pourcentage = (lecteur.currentTime / lecteur.duration) * 100;
    if (barre) barre.style.width = pourcentage + "%";
    
    if (tempsLabel) {
        tempsLabel.innerHTML = formatTime(lecteur.currentTime) + " / " + formatTime(lecteur.duration);
    }
}

// Remet à zéro l'interface quand la piste audio se termine
function réinitialiserLecteurFin(lecteur, barre, btnPlay, tempsLabel) {
    if (barre) barre.style.width = "0%";
    if (btnPlay) btnPlay.innerHTML = "▶";
    if (tempsLabel) tempsLabel.innerHTML = "0:00 / " + formatTime(lecteur.duration);
}

// Calcule la nouvelle position de lecture lors d'un clic sur la barre (Fiches uniquement)
function naviguerDansPiste(event, lecteur, conteneurBarre) {
    if (!lecteur.duration) return;

    var dimensions = conteneurBarre.getBoundingClientRect();
    var clicX = event.clientX - dimensions.left;
    var largeurTotale = dimensions.width;
    var ratio = clicX / largeurTotale;
    
    lecteur.currentTime = ratio * lecteur.duration;
}

// --- 4. INITIALISATION DES ÉCOUTEURS (DOM READY) ---

document.addEventListener("DOMContentLoaded", function() {
    // Détection automatique du lecteur actif (Quiz ou Fiche)
    var lecteur = document.getElementById("lecteurQuiz") || document.getElementById("lecteurFiche");
    var barre = document.getElementById("barreProgression");
    var btnPlay = document.getElementById("btnPlay");
    var tempsLabel = document.querySelector(".audio-time-label");

    if (!lecteur) return; // Si aucun lecteur sur la page, on s'arrête là

    // Liaison des événements aux fonctions dédiées
    lecteur.addEventListener("loadedmetadata", function() {
        initialiserChrono(lecteur, tempsLabel);
    });

    lecteur.addEventListener("timeupdate", function() {
        actualiserProgression(lecteur, barre, tempsLabel);
    });

    lecteur.addEventListener("ended", function() {
        réinitialiserLecteurFin(lecteur, barre, btnPlay, tempsLabel);
    });

    // Configuration de la navigation au clic si on est sur la fiche pédagogique
    if (lecteur.id === "lecteurFiche" && barre) {
        var conteneurBarre = barre.parentElement;
        
        conteneurBarre.addEventListener("click", function(event) {
            naviguerDansPiste(event, lecteur, conteneurBarre);
        });
    }
});