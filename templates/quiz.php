<?php
/*
 * Fichier : templates/quiz.php
 * Auteur  : LEFEBVRE Lucas / Design par Mathis
 * Description : Interface 6 - Catalogue des quiz
 */

$catUrl = valider("cat", "GET");
$nivUrl = valider("niveau", "GET");

// On récupère les données via les modèles
$categories = getCategoriesAvecCompteurs();
$quizzes    = getQuizDisponibles($catUrl, $nivUrl);
$tousQuizzes = getQuizDisponibles(); // pour compter le total sans filtre

// Nom de la catégorie active pour le titre
$nomCatActive = 'Tous';
if ($catUrl) {
    foreach ($categories as $c) {
        if ($c['id'] == $catUrl) {
            $nomCatActive = $c['nom'];
            break;
        }
    }
}

// Nombre de quiz dispo par catégorie (ceux avec >= 5 questions)
$nbQuizParCat = [];
foreach (getQuizDisponibles() as $q) {
    // On récupère l'id catégorie depuis les categories
    foreach ($categories as $c) {
        if ($c['nom'] === $q['categorie']) {
            $nbQuizParCat[$c['id']] = ($nbQuizParCat[$c['id']] ?? 0) + 1;
            break;
        }
    }
}
$nbQuizTotal = count($tousQuizzes);
?>

<div class="catalogue-layout">

    <aside class="sidebar-filtres-custom">
        <h3 class="sidebar-titre">Catégorie</h3>
        
        <a href="index.php?view=quiz<?= $nivUrl ? '&niveau='.$nivUrl : '' ?>" class="filter-btn-custom <?= (!$catUrl) ? 'active' : '' ?>">
            <span>Tout</span> <span><?= $nbQuizTotal ?></span>
        </a>
        
        <?php foreach ($categories as $c): ?>
            <a href="index.php?view=quiz&cat=<?= $c['id'] ?><?= $nivUrl ? '&niveau='.$nivUrl : '' ?>" class="filter-btn-custom <?= ($catUrl == $c['id']) ? 'active' : '' ?>">
                <span><?= htmlspecialchars($c['nom']) ?></span>
                <span><?= $nbQuizParCat[$c['id']] ?? 0 ?></span>
            </a>
        <?php endforeach; ?>

        <h3 class="sidebar-titre mt-space">Niveau</h3>
        <a href="index.php?view=quiz<?= $catUrl ? '&cat='.$catUrl : '' ?>" class="filter-btn-custom <?= (!$nivUrl) ? 'active' : '' ?>">Tout</a>
        <a href="index.php?view=quiz&niveau=Debutant<?= $catUrl ? '&cat='.$catUrl : '' ?>" class="filter-btn-custom <?= ($nivUrl == 'Debutant') ? 'active' : '' ?>">Debutant</a>
        <a href="index.php?view=quiz&niveau=Intermediaire<?= $catUrl ? '&cat='.$catUrl : '' ?>" class="filter-btn-custom <?= ($nivUrl == 'Intermediaire') ? 'active' : '' ?>">Intermediaire</a>
    </aside>

    <div class="catalogue-main">

        <div class="catalogue-header">
            <h1 class="page-titre-custom">Quiz &mdash; <span class="highlight"><?= htmlspecialchars($nomCatActive) ?></span></h1>
            <div style="width: 200px;"></div>
        </div>

        <div class="fiches-grid-2">
            <?php if (empty($quizzes)): ?>
                <p>Aucun quiz trouvé pour ces critères.</p>
            <?php else: ?>
                <?php foreach ($quizzes as $q): ?>
                    <div class="card-fiche-custom">

                        <div style="margin-bottom: 16px;">
                            <span class="badge-cat-custom" style="background-color: <?= htmlspecialchars($q['couleur']) ?>;">
                                <?= htmlspecialchars($q['categorie']) ?>
                            </span>
                        </div>

                        <h3 class="card-titre-custom"><?= htmlspecialchars($q['titre']) ?></h3>

                        <div class="card-niveau-container">
                            <span class="annotation">// <?= $q['nb_questions'] ?> questions disponibles</span>
                        </div>

                        <div class="card-actions-custom">
                            <a href="index.php?view=quiz_play&slug=<?= htmlspecialchars($q['slug']) ?>" class="btn-custom btn-orange" style="width: 100%;">Commencer →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>