<?php
/*
 * Fichier : templates/resultats.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 8 - Bilan du quiz
 */

// Le bilan est passé via session (pas en GET) pour éviter d'exposer les détails dans l'URL
// et pour empêcher un rechargement de page de re-déclencher la soumission du quiz.
// Si la session bilan est absente, c'est un accès direct non autorisé.
if (!isset($_SESSION['quiz_bilan'])) {
    rediriger("index.php?view=quiz");
}

$bilan = $_SESSION['quiz_bilan'];
?>

<div class="resultats-wrapper">

    <h1 class="resultats-score"><?= $bilan['score'] ?> <span class="resultats-score-denom">/ 5</span></h1>

    <p class="annotation resultats-annotation">
        Points gagnés : <span class="highlight">+<?= $bilan['points_gagnes'] ?> pts</span>
    </p>

    <div class="resultats-detail-card">
        <h3>Détail des réponses</h3>

        <?php foreach ($bilan['details'] as $detail): ?>
            <div class="resultat-question <?= !$detail['est_bonne'] ? 'wrong' : '' ?>">

                <div class="resultat-icon <?= $detail['est_bonne'] ? 'correct' : 'incorrect' ?>">
                    <?= $detail['est_bonne'] ? '✓' : '✗' ?>
                </div>

                <div class="resultat-detail-body">
                    <strong class="resultat-question-enonce">Q<?= $detail['num'] ?> — <?= htmlspecialchars($detail['enonce']) ?></strong>

                    <?php if ($detail['est_bonne']): ?>
                        <span class="badge badge-correct">
                            <?= htmlspecialchars($detail['user_txt']) ?>
                        </span>
                    <?php else: ?>
                        <span class="badge reponse-barree badge-wrong">
                            <?= htmlspecialchars($detail['user_txt']) ?>
                        </span>
                        <span class="resultat-arrow">→</span>
                        <span class="badge badge-correct">
                            <?= htmlspecialchars($detail['correct_txt']) ?>
                        </span>
                    <?php endif; ?>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <div class="resultats-actions">
        <a href="index.php?view=quiz" class="btn btn-outline">← Retour au catalogue</a>
        <a href="index.php?view=classement" class="btn btn-primary">Voir le classement →</a>
    </div>

</div>
