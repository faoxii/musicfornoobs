<?php
/*
 * Fichier : templates/admin_fiches.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : Interface Administration - Liste et gestion des fiches pédagogiques
 */

$categories = getCategoriesAvecCompteurs();
$fiches     = getFichesDetaillees();
?>

<div class="admin-wrapper">

    <!-- EN-TÊTE -->
    <div class="admin-top-bar">
        <h1 class="admin-titre">Gestion des fiches</h1>

        <div class="admin-controls">
            <!-- Recherche -->
            <div class="admin-search-box">
                <svg class="admin-search-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M13 13L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <input type="text" id="adminSearch" placeholder="search..." oninput="filtrerAdmin()">
            </div>

            <!-- Filtres catégorie -->
            <div class="admin-filter-group">
                <button class="admin-filter-btn active" onclick="filtrerCategorie(this, 'Tout')">Tout</button>
                <?php foreach ($categories as $c): ?>
                    <button class="admin-filter-btn" onclick="filtrerCategorie(this, '<?= htmlspecialchars($c['nom']) ?>')">
                        <?= htmlspecialchars($c['nom']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Bouton nouvelle fiche -->
        <a href="index.php?view=admin_fiche_form" class="btn-custom btn-orange admin-btn-new">
            Nouvelle fiche
        </a>
    </div>

    <!-- TABLEAU -->
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="col-fiche">Fiche</th>
                    <th class="col-cat">Catégorie</th>
                    <th class="col-niveau">Niveau</th>
                    <th class="col-audio">Audio</th>
                    <th class="col-questions">Questions</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody id="adminTableBody">
                <?php if (!empty($fiches)): ?>
                    <?php foreach ($fiches as $f): ?>
                        <tr data-categorie="<?= htmlspecialchars($f['categorie']) ?>"
                            data-titre="<?= strtolower(htmlspecialchars($f['titre'])) ?>">

                            <td class="col-fiche">
                                <strong><?= htmlspecialchars($f['titre']) ?></strong>
                            </td>

                            <td class="col-cat">
                                <span class="badge badge--thick" data-cat="<?= htmlspecialchars($f['categorie']) ?>"><?= htmlspecialchars($f['categorie']) ?></span>
                            </td>

                            <td class="col-niveau">
                                <span class="badge badge--thick"><?= htmlspecialchars($f['niveau']) ?></span>
                            </td>

                            <td class="col-audio">
                                <?php if (!empty($f['chemin_audio'])): ?>
                                    <span class="admin-check">&#10003;</span>
                                <?php else: ?>
                                    <span class="admin-dash">-</span>
                                <?php endif; ?>
                            </td>

                            <td class="col-questions">
                                <?= isset($f['nb_questions']) ? $f['nb_questions'] : '0' ?> / 5
                            </td>

                            <td class="col-actions">
                                <a href="index.php?view=admin_fiche_form&id=<?= $f['id'] ?>"
                                   class="admin-btn-action">
                                    Modifier
                                </a>
                                <a href="index.php?view=admin_questions&fiche_id=<?= $f['id'] ?>"
                                   class="admin-btn-action">
                                    Questions
                                </a>
                                <a href="controleur.php?action=supprimer_fiche&id=<?= $f['id'] ?>"
                                   class="admin-btn-action admin-btn-delete"
                                   onclick="return confirm('Supprimer définitivement cette fiche et son quiz ?')">
                                    &#128465;
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="admin-empty">
                            Aucune fiche trouvée. Cliquez sur "Nouvelle fiche" pour commencer.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filtrerAdmin() {
    const search = document.getElementById('adminSearch').value.toLowerCase();
    const rows   = document.querySelectorAll('#adminTableBody tr[data-titre]');
    rows.forEach(row => {
        const titre = row.dataset.titre || '';
        row.style.display = titre.includes(search) ? '' : 'none';
    });
}

function filtrerCategorie(btn, categorie) {
    // mise à jour des boutons actifs
    document.querySelectorAll('.admin-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const rows = document.querySelectorAll('#adminTableBody tr[data-categorie]');
    rows.forEach(row => {
        const cat = row.dataset.categorie || '';
        row.style.display = (categorie === 'Tout' || cat === categorie) ? '' : 'none';
    });
}
</script>