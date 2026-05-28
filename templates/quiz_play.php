<?php
/*
 * Fichier : templates/quiz_play.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 7 - Deroulement du quiz (1 question a la fois)
 */

$slug = valider("slug", "GET");
if (!$slug) rediriger("index.php?view=quiz");

// 1. Initialisation session si premiere question
if (!isset($_SESSION["quiz_slug"]) || $_SESSION["quiz_slug"] !== $slug) {
    $_SESSION["quiz_slug"]     = $slug;
    $_SESSION["quiz_etape"]    = 1;
    $_SESSION["quiz_reponses"] = [];
}

$etape = $_SESSION["quiz_etape"];

// 2. Securite : si etape > 5 (ne devrait pas arriver), on redirige
if ($etape > 5) {
    rediriger("index.php?view=quiz");
}

// 3. Recuperation de la fiche et de la question
$fiche = getFicheParSlug($slug);
if (!$fiche) rediriger("index.php?view=quiz");

$lignesQuestion = getQuestion($fiche['id'], $etape);
if (empty($lignesQuestion)) rediriger("index.php?view=quiz");

$question = $lignesQuestion[0];
?>

<div style="max-width: 800px; margin: 0 auto; background: var(--bg-card); padding: 32px; border: 1px solid var(--border); border-radius: 12px;">

    <span class="annotation">QUESTION <?= $etape ?> / 5</span>

    <div class="quiz-progress" style="margin-top: 8px;">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <?php
                $class = "";
                if ($i < $etape)  $class = "done";
                if ($i == $etape) $class = "current";
            ?>
            <div class="quiz-progress-segment <?= $class ?>"></div>
        <?php endfor; ?>
    </div>

    <h2 style="margin: 24px 0;"><?= htmlspecialchars($question['enonce']) ?></h2>

    <?php if ($question['type'] === 'audio' && !empty($question['chemin_audio_question'])): ?>
        <audio id="lecteurQuiz" src="<?= htmlspecialchars($question['chemin_audio_question']) ?>" preload="auto"></audio>

        <div class="audio-player" style="margin-bottom: 24px;">
            <button class="audio-player-play" type="button" onclick="togglePlay('lecteurQuiz')">▶</button>
            <span style="flex:1;">Extrait sonore à identifier</span>
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
                <input type="radio" name="reponse" value="<?= $rep['id_reponse'] ?>" style="transform: scale(1.5); margin-right: 12px;" required>
                <span class="reponse-quiz-letter"><?= $lettres[$index] ?> -</span>
                <span style="font-size: 20px; font-weight: bold;"><?= htmlspecialchars($rep['contenu']) ?></span>
            </label>
        <?php endforeach; ?>

        <div style="text-align: right; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Valider →</button>
        </div>
    </form>

</div>