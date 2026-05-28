<?php
/*
 * Fichier : templates/resultats.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 8 - Bilan du quiz
 */

// Si la session bilan n'existe pas, c'est qu'on essaie d'accéder à la page illégalement
if (!isset($_SESSION['quiz_bilan'])) {
    rediriger("index.php?view=quiz");
}

$bilan = $_SESSION['quiz_bilan'];
?>

<div style="max-width: 800px; margin: 0 auto; text-align: center;">
    
    <h1 style="font-size: 80px; margin-bottom: 0;"><?= $bilan['score'] ?> <span style="font-size: 40px; color: var(--text-muted);">/ 5</span></h1>
    <p class="annotation" style="margin-bottom: 32px;">
        Points gagnés : <span class="highlight">+<?= $bilan['points_gagnes'] ?> pts</span>
    </p>

    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; text-align: left; padding: 24px;">
        <h3 style="margin-bottom: 16px;">Détail des réponses</h3>

        <?php foreach ($bilan['details'] as $detail): ?>
            <div class="resultat-question <?= !$detail['est_bonne'] ? 'wrong' : '' ?>">
                
                <div class="resultat-icon <?= $detail['est_bonne'] ? 'correct' : 'incorrect' ?>">
                    <?= $detail['est_bonne'] ? '✓' : '✗' ?>
                </div>
                
                <div style="flex: 1;">
                    <strong style="display: block; margin-bottom: 4px;">Q<?= $detail['num'] ?> — <?= htmlspecialchars($detail['enonce']) ?></strong>
                    
                    <?php if ($detail['est_bonne']): ?>
                        <span class="badge" style="background: var(--success); color: white; border-color: var(--success);">
                            <?= htmlspecialchars($detail['user_txt']) ?>
                        </span>
                    <?php else: ?>
                        <span class="badge reponse-barree" style="background: white; border-color: var(--error);">
                            <?= htmlspecialchars($detail['user_txt']) ?>
                        </span>
                        <span style="margin: 0 8px;">→</span>
                        <span class="badge" style="background: var(--success); color: white; border-color: var(--success);">
                            <?= htmlspecialchars($detail['correct_txt']) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top: 32px; display: flex; gap: 16px; justify-content: center;">
        <a href="index.php?view=quiz" class="btn btn-outline">← Retour au catalogue</a>
        <a href="index.php?view=classement" class="btn btn-primary">Voir le classement →</a>
    </div>

</div>