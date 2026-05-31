<?php
/*
 * Fichier : templates/admin_questions.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : Interface Admin - Liste des questions d'une fiche (max 5)
 */

$idFiche = valider("fiche_id", "GET");

if (!$idFiche) {
    rediriger("index.php?view=admin_fiches");
}

$fiche = getFiche($idFiche);
if (!$fiche) {
    rediriger("index.php?view=admin_fiches");
}

// Récupère toutes les questions avec leurs réponses
$lignes = getQuestionsFiche($idFiche);

// getQuestionsFiche retourne plusieurs lignes par question (une par réponse).
// On regroupe ici en tableau indexé par id_question pour simplifier l'affichage.
$questions = [];
foreach ($lignes as $l) {
    $idQ = $l['id_question'];

    if (!isset($questions[$idQ])) {
        $questions[$idQ] = [
            'id'           => $idQ,
            'enonce'       => $l['enonce'],
            'type'         => $l['type'],
            'chemin_audio' => $l['chemin_audio_question'],
            'ordre'        => $l['ordre_question'],
            'reponses'     => [],
        ];
    }

    $questions[$idQ]['reponses'][] = [
        'id'          => $l['id_reponse'],
        'contenu'     => $l['contenu'],
        'est_correct' => $l['est_correct'],
    ];
}

$nbQuestions = count($questions);
?>

<div class="admin-wrapper">

    <!-- EN-TÊTE -->
    <div class="admin-q-header">
        <a href="index.php?view=admin_fiches" class="btn-custom btn-white admin-form-retour">
            ← Retour à la liste
        </a>

        <div class="admin-q-header-center">
            <h1 class="admin-q-titre">
                Questions &mdash; <span class="highlight"><?= htmlspecialchars($fiche['titre']) ?></span>
            </h1>
            <span class="annotation">// <?= $nbQuestions ?> / 5 questions</span>
        </div>

        <div class="admin-q-header-btn">
            <?php if ($nbQuestions < 5): ?>
                <a href="index.php?view=admin_question_form&fiche_id=<?= $idFiche ?>"
                   class="btn-custom btn-orange">
                    + Nouvelle question
                </a>
            <?php else: ?>
                <span class="admin-q-max">5 / 5 — maximum atteint</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- LISTE DES QUESTIONS -->
    <?php if (empty($questions)): ?>
        <div class="admin-empty">
            Aucune question pour cette fiche. Cliquez sur "Nouvelle question" pour commencer.
        </div>

    <?php else: ?>
        <div class="admin-q-list">
            <?php foreach ($questions as $q): ?>
                <div class="admin-q-card">

                    <!-- En-tête de la question -->
                    <div class="admin-q-card-header">
                        <h3 class="admin-q-enonce"><?= htmlspecialchars($q['enonce']) ?></h3>
                        <div class="admin-q-actions">
                            <a href="index.php?view=admin_question_form&fiche_id=<?= $idFiche ?>&question_id=<?= $q['id'] ?>"
                               class="admin-btn-action">
                                &#9998; Éditer
                            </a>
                            <a href="controleur.php?action=SupprimerQuestion&id=<?= $q['id'] ?>&fiche_id=<?= $idFiche ?>"
                               class="admin-btn-action admin-btn-delete"
                               onclick="return confirm('Supprimer cette question et ses réponses ?')">
                                &#128465;
                            </a>
                        </div>
                    </div>

                    <!-- Lecteur audio si question de type audio -->
                    <?php if ($q['type'] === 'audio' && !empty($q['chemin_audio'])): ?>
                        <div class="admin-q-audio">
                            <button class="admin-q-play-btn" type="button"
                                    onclick="toggleAudio('audio-<?= $q['id'] ?>', this)">&#9654;</button>
                            <span><?= htmlspecialchars(basename($q['chemin_audio'])) ?></span>
                            <audio id="audio-<?= $q['id'] ?>" src="<?= htmlspecialchars($q['chemin_audio']) ?>"></audio>
                        </div>
                    <?php endif; ?>

                    <!-- Grille des 4 réponses -->
                    <div class="admin-q-reponses">
                        <?php foreach ($q['reponses'] as $r): ?>
                            <div class="admin-q-reponse <?= $r['est_correct'] ? 'correcte' : '' ?>">
                                <span><?= htmlspecialchars($r['contenu']) ?></span>
                                <?php if ($r['est_correct']): ?>
                                    <span class="admin-q-bonne">&#10003; bonne réponse</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
// Lecture audio des questions de type audio
function toggleAudio(id, btn) {
    const audio = document.getElementById(id);
    if (audio.paused) {
        audio.play();
        btn.textContent = '⏸';
    } else {
        audio.pause();
        btn.textContent = '▶';
    }
    // Remet le bouton à l'état initial quand l'audio se termine
    audio.addEventListener('ended', function() {
        btn.textContent = '▶';
    });
}
</script>