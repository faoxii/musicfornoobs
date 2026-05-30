<?php
/*
 * Fichier : templates/fiches.php
 * Auteur  : Mathis BOURGUIGNON
 * Description : Interface 4 - Catalogue des fiches
 */

$catUrl    = valider("cat", "GET");
$nivUrl    = valider("niveau", "GET");
$searchUrl = valider("search", "GET");

$categories = getCategoriesAvecCompteurs();
$fiches     = getFiches($catUrl, $nivUrl, $searchUrl);
$toutesfiches = getFiches(); // pour le total sans filtre

// On cherche le nom de la catégorie sélectionnée pour le titre
$nomCatActive = 'Toutes';
if ($catUrl) {
    foreach ($categories as $c) {
        if ($c['id'] == $catUrl) {
            $nomCatActive = $c['nom'];
            break;
        }
    }
}

$nbFichesTotal = count($toutesfiches);


?>

<div class="catalogue-layout">

    <aside class="sidebar-filtres-custom">
        <h3 class="sidebar-titre">Catégorie</h3>
        
        <a href="index.php?view=fiches<?= $nivUrl ? '&niveau='.$nivUrl : '' ?>" class="filter-btn-custom <?= (!$catUrl) ? 'active' : '' ?>">
            <span>Tout</span> <span><?= $nbFichesTotal ?></span>
        </a>
        
        <?php foreach ($categories as $c): ?>
            <a href="index.php?view=fiches&cat=<?= $c['id'] ?><?= $nivUrl ? '&niveau='.$nivUrl : '' ?>" class="filter-btn-custom <?= ($catUrl == $c['id']) ? 'active' : '' ?>">
                <span><?= htmlspecialchars($c['nom']) ?></span> 
                <span><?= $c['nb_fiches'] ?></span>
            </a>
        <?php endforeach; ?>

        <h3 class="sidebar-titre mt-space">Niveau</h3>
        <a href="index.php?view=fiches<?= $catUrl ? '&cat='.$catUrl : '' ?>" class="filter-btn-custom <?= (!$nivUrl) ? 'active' : '' ?>">Tout</a>
        <a href="index.php?view=fiches&niveau=Debutant<?= $catUrl ? '&cat='.$catUrl : '' ?>" class="filter-btn-custom <?= ($nivUrl == 'Debutant') ? 'active' : '' ?>">Debutant</a>
        <a href="index.php?view=fiches&niveau=Intermediaire<?= $catUrl ? '&cat='.$catUrl : '' ?>" class="filter-btn-custom <?= ($nivUrl == 'Intermediaire') ? 'active' : '' ?>">Intermediaire</a>
        
        
    </aside>

    <div class="catalogue-main">
        
        <div class="catalogue-header">
            <h1 class="page-titre-custom">
                Fiches &mdash; 
                <span class="highlight"><?= htmlspecialchars($nomCatActive) ?></span>
            </h1>
            <form method="GET" action="index.php" class="search-form">
                <input type="hidden" name="view" value="fiches">
                <input type="text" name="search" placeholder="🔍 Rechercher..." value="<?= htmlspecialchars($searchUrl) ?>" class="search-input-custom">
            </form>
        </div>
        
        <div class="fiches-grid-2">
            <?php if (empty($fiches)): ?>
                <p>Aucune fiche trouvée pour ces critères.</p>
            <?php else: ?>
                <?php foreach ($fiches as $f): ?>
                    <div class="card-fiche-custom">
                        
                        <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                            <span class="badge-cat-custom" style="background-color: <?= htmlspecialchars($f['couleur']) ?>;">
                                <?= htmlspecialchars($f['categorie']) ?>
                            </span>
                            <?php if (isset($_SESSION['idUser']) && estFicheLue($_SESSION['idUser'], $f['id'])): ?>
                                <span class="badge-lu">✓ Lu</span>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="card-titre-custom"><?= htmlspecialchars($f['titre']) ?></h3>
                        
                        <div class="card-niveau-container">
                            <span class="badge-niveau-custom">
                                <?= htmlspecialchars($f['niveau']) ?>
                            </span>
                        </div>
                        
                        <div class="card-actions-custom">
                            <a href="index.php?view=fiche_read&slug=<?= htmlspecialchars($f['slug']) ?>" class="btn-custom btn-orange">Lire</a>
                            <a href="index.php?view=quiz_play&slug=<?= htmlspecialchars($f['slug']) ?>" class="btn-custom btn-white">Quiz</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>