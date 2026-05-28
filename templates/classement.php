<?php
/*
 * Fichier : templates/classement.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 10 - Classement general
 */

$classement = getClassement(20);
$rangUser   = getRang($_SESSION['idUser']);
?>

<div style="max-width: 800px; margin: 0 auto;">

    <h1 style="margin-bottom: 8px;">Classement général</h1>
    <p class="annotation" style="margin-bottom: 32px;">
        // top 20 joueurs — votre rang : #<?= $rangUser ?>
    </p>

    <?php if (empty($classement)): ?>
        <p class="text-muted">Aucun joueur au classement pour l'instant.</p>

    <?php else: ?>
        <div style="border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">

            <?php foreach ($classement as $index => $joueur): ?>
                <?php
                    $rang      = $index + 1;
                    $estVous   = ($joueur['login'] === $_SESSION['login']);
                ?>

                <div class="classement-ligne <?= $estVous ? 'vous' : '' ?>">

                    <!-- Rang / Médaille -->
                    <div style="width: 56px; display: flex; justify-content: center; flex-shrink: 0;">
                        <?php if ($rang === 1): ?>
                            <div class="medaille medaille-1">🥇</div>
                        <?php elseif ($rang === 2): ?>
                            <div class="medaille medaille-2">🥈</div>
                        <?php elseif ($rang === 3): ?>
                            <div class="medaille medaille-3">🥉</div>
                        <?php else: ?>
                            <span style="font-family: var(--font-mono); font-size: 14px; color: var(--text-muted);">
                                #<?= $rang ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Login -->
                    <div style="flex: 1; font-size: 20px;">
                        <?= htmlspecialchars($joueur['login']) ?>
                        <?php if ($estVous): ?>
                            <span class="annotation" style="margin-left: 8px;">← vous</span>
                        <?php endif; ?>
                    </div>

                    <!-- Fiches lues -->
                    <div style="text-align: center; min-width: 80px;">
                        <div style="font-family: var(--font-title); font-size: 22px;">
                            <?= $joueur['nb_fiches_lues'] ?>
                        </div>
                        <div class="annotation">fiches</div>
                    </div>

                    <!-- Score -->
                    <div style="text-align: right; min-width: 100px;">
                        <div style="font-family: var(--font-title); font-size: 28px; color: var(--accent);">
                            <?= $joueur['score_total'] ?>
                        </div>
                        <div class="annotation">pts</div>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>