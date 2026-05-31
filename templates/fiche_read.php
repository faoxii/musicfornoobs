<?php
/*
 * Fichier : templates/fiche_read.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : Interface 5 - Affichage détaillé d'une fiche pédagogique
 */

$slug = valider("slug", "GET");
if (!$slug) rediriger("index.php?view=fiches");

$fiche = getFicheParSlug($slug);
if (!$fiche) rediriger("index.php?view=fiches");

// La lecture est enregistrée automatiquement à l'ouverture de la page, sans bouton :
// on fait confiance au fait que l'utilisateur a vu le contenu s'il a navigué jusqu'ici.
// marquerFicheLue utilise INSERT IGNORE, donc ouvrir la fiche plusieurs fois ne duplique pas.
if (valider('idUser', 'SESSION')) {
    marquerFicheLue(valider('idUser', 'SESSION'), $fiche['id']);
}
?>

<div class="container">

    <div class="fiche-action-retour">
        <a href="index.php?view=fiches" class="btn-custom btn-white">
            ← Retour au catalogue
        </a>
    </div>

    <div class="fiche-badges-container">
        <span class="badge badge--thick" style="background-color: <?= htmlspecialchars($fiche['couleur']) ?>;">
            <?= htmlspecialchars($fiche['categorie']) ?>
        </span>
        <span class="badge badge--thick">
            <?= htmlspecialchars($fiche['niveau']) ?>
        </span>
    </div>

    <h1 class="fiche-titre-principal">
        <?= htmlspecialchars($fiche['titre']) ?>
    </h1>

    <div class="fiche-texte-contenu">
        <?= fichesMarkdownToHtml($fiche['contenu']) ?>
    </div>

    <?php if (!empty($fiche['chemin_audio'])): ?>
        <div class="fiche-section-audio">
            <h2>Écoute</h2>

            <audio id="lecteurFiche" src="<?= htmlspecialchars($fiche['chemin_audio']) ?>" preload="auto"></audio>

            <div class="audio-player fiche-audio-custom">
                <button id="btnPlay" class="audio-player-play" type="button" onclick="togglePlay('lecteurFiche')">▶</button>

                <div class="audio-progress-bg">
                    <div id="barreProgression" class="audio-progress-fill"></div>
                </div>

                <div class="audio-time-label">0:00 / --:--</div>

                <button class="btn btn-outline btn-rejouer-custom" type="button" onclick="rejouer('lecteurFiche')">
                    Rejouer
                </button>
            </div>
        </div>
    <?php endif; ?>

    <div class="fiche-action-quiz">
        <a href="index.php?view=quiz_play&slug=<?= htmlspecialchars($fiche['slug']) ?>" class="btn-custom btn-orange">
            Passer le quiz →
        </a>
    </div>

</div>
