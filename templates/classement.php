<?php
/*
 * Fichier : templates/classement.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 10 - Classement general
 */

$classement = getClassement(20);
$rangUser   = getRang($_SESSION['idUser']);
?>

<div class="classement-wrapper">

    <h1 class="classement-titre">Classement général</h1>
    <p class="annotation classement-sous-titre">
        // top 20 joueurs — votre rang : #<?= $rangUser ?>
    </p>

    <?php if (empty($classement)): ?>
        <p class="text-muted">Aucun joueur au classement pour l'instant.</p>

    <?php else: ?>
        <div class="classement-table">

            <?php foreach ($classement as $index => $joueur): ?>
                <?php
                    $rang    = $index + 1;
                    $estVous = ($joueur['login'] === $_SESSION['login']);
                ?>

                <div class="classement-ligne <?= $estVous ? 'vous' : '' ?>">

                    <!-- Rang / Médaille -->
                    <div class="classement-rang">
                        <?php if ($rang === 1): ?>
                            <div class="medaille medaille-1">1</div>
                        <?php elseif ($rang === 2): ?>
                            <div class="medaille medaille-2">2</div>
                        <?php elseif ($rang === 3): ?>
                            <div class="medaille medaille-3">3</div>
                        <?php else: ?>
                            <span class="classement-rang-num">#<?= $rang ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Login -->
                    <div class="classement-login">
                        <?= htmlspecialchars($joueur['login']) ?>
                        <?php if ($estVous): ?>
                            <span class="annotation classement-vous">← vous</span>
                        <?php endif; ?>
                    </div>

                    <!-- Fiches lues -->
                    <div class="classement-stat">
                        <div class="classement-stat-valeur"><?= $joueur['nb_fiches_lues'] ?></div>
                        <div class="annotation">fiches</div>
                    </div>

                    <!-- Score -->
                    <div class="classement-stat classement-stat--score">
                        <div class="classement-stat-valeur classement-score"><?= $joueur['score_total'] ?></div>
                        <div class="annotation">pts</div>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>