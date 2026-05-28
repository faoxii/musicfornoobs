<?php
/*
 * Fichier : templates/quiz.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 6 - Catalogue des quiz
 */

// On récupère les filtres de l'URL s'il y en a
$catUrl = valider("cat", "GET");
$nivUrl = valider("niveau", "GET");

// On récupère les données via les modèles
$categories = getCategoriesAvecCompteurs();
$quizzes = getQuizDisponibles($catUrl, $nivUrl);
?>

<div style="display: flex; gap: 32px; align-items: flex-start;">

    <aside class="sidebar-filtres">
        <h3>Catégorie</h3>
        <a href="index.php?view=quiz" class="filter-btn <?= (!$catUrl) ? 'active' : '' ?>">Tout</a>
        <?php foreach ($categories as $c): ?>
            <a href="index.php?view=quiz&cat=<?= $c['id'] ?>" class="filter-btn <?= ($catUrl == $c['id']) ? 'active' : '' ?>">
                <?= htmlspecialchars($c['nom']) ?> (<?= $c['nb_fiches'] ?>)
            </a>
        <?php endforeach; ?>

        <h3 style="margin-top: 24px;">Niveau</h3>
        <a href="index.php?view=quiz" class="filter-btn <?= (!$nivUrl) ? 'active' : '' ?>">Tout</a>
        <a href="index.php?view=quiz&niveau=Debutant" class="filter-btn <?= ($nivUrl == 'Debutant') ? 'active' : '' ?>">Débutant</a>
        <a href="index.php?view=quiz&niveau=Intermediaire" class="filter-btn <?= ($nivUrl == 'Intermediaire') ? 'active' : '' ?>">Intermédiaire</a>
        <a href="index.php?view=quiz&niveau=Avance" class="filter-btn <?= ($nivUrl == 'Avance') ? 'active' : '' ?>">Avancé</a>
    </aside>

    <div style="flex: 1;">
        <h1 style="margin-bottom: 24px;">Tous les quiz</h1>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
            <?php foreach ($quizzes as $q): ?>
                <div class="card-fiche">
                    <span class="badge" style="background: <?= $q['couleur'] ?>; border-color: <?= $q['couleur'] ?>; color: white; align-self: flex-start;">
                        <?= htmlspecialchars($q['categorie']) ?>
                    </span>
                    <h3 class="card-fiche-titre"><?= htmlspecialchars($q['titre']) ?></h3>
                    <p class="annotation" style="margin-bottom: 16px;"><?= $q['nb_questions'] ?> questions</p>
                    
                    <div class="card-fiche-actions">
                        <a href="index.php?view=quiz_play&slug=<?= $q['slug'] ?>" class="btn btn-primary btn-full" style="text-align: center;">Commencer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>