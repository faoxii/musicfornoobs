<?php
/*
 * Fichier : templates/dashboard.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 9 - Tableau de bord utilisateur
 */

$idUser    = $_SESSION['idUser'];
$login     = $_SESSION['login'];

// 4 cartes stats
$infos       = getUtilisateur($idUser);
$infos       = $infos[0];
$scoreTotal  = $infos['score_total'];
$dateInscrit = date('d/m/Y', strtotime($infos['date_inscription']));

$nbFichesLues = getNbFichesLues($idUser);
$nbQuizPasses = getNbQuizPasses($idUser);
$rang         = getRang($idUser);

// Activite recente
$activite = getActiviteRecente($idUser, 3);

// Initiales de l'avatar
$initiales = strtoupper(substr($login, 0, 2));
?>

<div style="max-width: 900px; margin: 0 auto;">

    <!-- EN-TETE PROFIL -->
    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 40px;">
        <div class="avatar avatar-large"><?= $initiales ?></div>
        <div>
            <h1 style="margin: 0;">Bonjour <?= htmlspecialchars($login) ?></h1>
            <span class="annotation">// membre depuis le <?= $dateInscrit ?></span>
        </div>
    </div>

    <!-- 4 CARTES STATS -->
    <div class="stats-grid" style="margin-bottom: 40px;">
        <div class="stat-card">
            <div class="stat-card-value" style="color: var(--accent);"><?= $scoreTotal ?></div>
            <div class="stat-card-label">Points</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value"><?= $nbFichesLues ?></div>
            <div class="stat-card-label">Fiches lues</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value"><?= $nbQuizPasses ?></div>
            <div class="stat-card-label">Quiz passés</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value">#<?= $rang ?></div>
            <div class="stat-card-label">Classement</div>
        </div>
    </div>

    <!-- ACTIVITE RECENTE -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h2 style="margin: 0;">Activité récente</h2>
        <a href="index.php?view=classement" class="btn btn-secondary" style="font-size: 16px;">
            Voir le classement →
        </a>
    </div>

    <?php if (empty($activite)): ?>
        <div style="border: 1px solid var(--border); border-radius: 12px; padding: 32px; text-align: center;">
            <p class="text-muted">Tu n'as encore passé aucun quiz.</p>
            <a href="index.php?view=quiz" class="btn btn-primary" style="margin-top: 16px;">
                Commencer un quiz
            </a>
        </div>

    <?php else: ?>
        <div style="border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">
            <?php foreach ($activite as $index => $item): ?>
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 16px;
                    padding: 16px 24px;
                    <?= $index < count($activite) - 1 ? 'border-bottom: 1px solid var(--border);' : '' ?>
                ">
                    <!-- Badge catégorie -->
                    <span class="badge-categorie" style="background: <?= htmlspecialchars($item['couleur']) ?>20; color: <?= htmlspecialchars($item['couleur']) ?>; border: 1px solid <?= htmlspecialchars($item['couleur']) ?>; flex-shrink: 0;">
                        <?= htmlspecialchars($item['categorie']) ?>
                    </span>

                    <!-- Titre de la fiche -->
                    <span style="flex: 1; font-size: 18px;">
                        <?= htmlspecialchars($item['titre']) ?>
                    </span>

                    <!-- Score -->
                    <div style="text-align: right; flex-shrink: 0;">
                        <span style="font-family: var(--font-title); font-size: 28px;">
                            <?= $item['score'] ?><span style="font-size: 18px; color: var(--text-muted);">/5</span>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>