<?php
/*
 * Fichier : templates/dashboard.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 9 - Tableau de bord utilisateur
 */

$idUser = valider('idUser',  'SESSION');
$login  = valider('login',   'SESSION');

$infos       = getUtilisateur($idUser);
$infos       = $infos[0];
$scoreTotal  = $infos['score_total'];
$dateInscrit = date('d/m/Y', strtotime($infos['date_inscription']));

$nbFichesLues = getNbFichesLues($idUser);
$nbQuizPasses = getNbQuizPasses($idUser);
$rang         = getRang($idUser);

$activite  = getActiviteRecente($idUser, 3);
$initiales = strtoupper(substr($login, 0, 2));
?>

<div class="dashboard-wrapper">

    <!-- EN-TETE PROFIL -->
    <div class="dashboard-header">
        <div class="dashboard-avatar"><?= $initiales ?></div>
        <div class="dashboard-header-info">
            <h1 class="dashboard-bonjour">
                Bonjour <span class="highlight"><?= htmlspecialchars($login) ?></span>
            </h1>
            <span class="annotation">// membre depuis le <?= $dateInscrit ?></span>
        </div>
    </div>

    <!-- 4 CARTES STATS -->
    <div class="stats-grid">
        <div class="stat-card-custom">
            <div class="stat-card-label-custom">Points</div>
            <div class="stat-card-value-custom stat-value-accent"><?= $scoreTotal ?></div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-card-label-custom">Fiches lues</div>
            <div class="stat-card-value-custom"><?= $nbFichesLues ?></div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-card-label-custom">Quiz passés</div>
            <div class="stat-card-value-custom"><?= $nbQuizPasses ?></div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-card-label-custom">Classement</div>
            <div class="stat-card-value-custom">#<?= $rang ?></div>
        </div>
    </div>

    <!-- ACTIVITE RECENTE -->
    <div class="dashboard-section-header">
        <h2 class="dashboard-section-titre">Activité récente</h2>
        <a href="index.php?view=classement" class="btn-custom btn-white btn-classement">
            Voir le classement →
        </a>
    </div>

    <?php if (empty($activite)): ?>
        <div class="dashboard-empty">
            <p>Tu n'as encore passé aucun quiz.</p>
            <a href="index.php?view=quiz" class="btn-custom btn-orange">
                Commencer un quiz
            </a>
        </div>

    <?php else: ?>
        <div class="activite-table">
            <?php foreach ($activite as $index => $item): ?>
                <div class="activite-ligne <?= $index < count($activite) - 1 ? 'activite-ligne--border' : '' ?>">
                    <span class="badge badge--thick" style="background-color: <?= htmlspecialchars($item['couleur']) ?>;">
                        <?= htmlspecialchars($item['categorie']) ?>
                    </span>
                    <span class="activite-titre"><?= htmlspecialchars($item['titre']) ?></span>
                    <div class="activite-score">
                        <span class="activite-score-num"><?= $item['score'] ?></span>
                        <span class="activite-score-denom">/5</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>