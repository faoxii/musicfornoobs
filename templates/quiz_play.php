<?php
/*
 * Fichier : templates/quiz_play.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 7 - Déroulement du quiz, une question à la fois
 */

$slug = valider("slug", "GET");
if (!$slug) rediriger("index.php?view=quiz");

// On initialise la session quiz uniquement si l'utilisateur commence un nouveau quiz
// (slug différent du quiz en cours) — relancer la même URL en cours de quiz ne remet pas à zéro
if (!isset($_SESSION["quiz_slug"]) || $_SESSION["quiz_slug"] !== $slug) {
    $_SESSION["quiz_slug"]     = $slug;
    $_SESSION["quiz_etape"]    = 1;
    $_SESSION["quiz_reponses"] = [];
}

$etape = $_SESSION["quiz_etape"];

// Guard : etape > 5 ne devrait jamais arriver en navigation normale,
// mais protège contre un rechargement de page après la dernière question
if ($etape > 5) {
    rediriger("index.php?view=quiz");
}

$fiche = getFicheParSlug($slug);
if (!$fiche) rediriger("index.php?view=quiz");

// getQuestion retourne 4 lignes (une par réponse), pas un objet structuré
$lignesQuestion = getQuestion($fiche['id'], $etape);
if (empty($lignesQuestion)) rediriger("index.php?view=quiz");

$question = $lignesQuestion[0];
?>

<div class="quiz-play-wrapper">

    <span class="annotation">QUESTION <?= $etape ?> / 5</span>

    <div class="quiz-progress">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <?php
                $class = "";
                if ($i < $etape)  $class = "done";
                if ($i == $etape) $class = "current";
            ?>
            <div class="quiz-progress-segment <?= $class ?>"></div>
        <?php endfor; ?>
    </div>

    <h2 class="quiz-question-titre"><?= htmlspecialchars($question['enonce']) ?></h2>

    <?php if ($question['type'] === 'audio' && !empty($question['chemin_audio_question'])): ?>
        <audio id="lecteurQuiz" src="<?= htmlspecialchars($question['chemin_audio_question']) ?>" preload="auto"></audio>

        <div class="audio-player-container">
            <button id="btnPlay" class="audio-player-play" type="button" onclick="togglePlay('lecteurQuiz')">▶</button>

            <div class="audio-progress-bg">
                <div id="barreProgression" class="audio-progress-fill"></div>
            </div>

            <button class="btn btn-outline" type="button" onclick="rejouer('lecteurQuiz')">Rejouer</button>
        </div>
    <?php endif; ?>

    <?php if (valider("msg", "GET")): ?>
        <div class="alert alert-error"><?= valider("msg", "GET") ?></div>
    <?php endif; ?>

    <form method="POST" action="controleur.php">
        <input type="hidden" name="action" value="EtapeQuiz">

        <?php
        $lettres = ['A', 'B', 'C', 'D'];
        foreach ($lignesQuestion as $index => $rep):
        ?>
            <label class="reponse-quiz">
                <input type="radio" name="reponse" value="<?= $rep['id_reponse'] ?>" required>
                <span class="reponse-quiz-letter"><?= $lettres[$index] ?> -</span>
                <span class="reponse-quiz-text"><?= htmlspecialchars($rep['contenu']) ?></span>
            </label>
        <?php endforeach; ?>

        <div class="quiz-form-actions">
            <button type="submit" class="btn btn-primary">Valider →</button>
        </div>
    </form>

</div>
