<?php
/*
 * Fichier : templates/admin_fiche_form.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : Interface Admin - Créer ou modifier une fiche
 */

$idFiche    = valider("id", "GET");
$categories = getCategoriesAvecCompteurs();

$fiche = null;
if ($idFiche) {
    $fiche = getFiche($idFiche);
    if (!$fiche) rediriger("index.php?view=admin_fiches");
}

$modeEdition = ($fiche !== null);
$titrePage   = $modeEdition ? 'Modifier la fiche' : 'Nouvelle fiche';
?>

<div class="admin-form-wrapper">

    <a href="index.php?view=admin_fiches" class="btn-custom btn-white admin-form-retour">
        ← Retour à la liste
    </a>

    <?php if (isset($_GET['erreur'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['erreur']) ?></div>
    <?php endif; ?>

    <form method="POST" action="controleur.php" enctype="multipart/form-data">

        <input type="hidden" name="action" value="<?= $modeEdition ? 'modifier_fiche' : 'creer_fiche' ?>">
        <?php if ($modeEdition): ?>
            <input type="hidden" name="id_fiche" value="<?= $fiche['id'] ?>">
        <?php endif; ?>

        <div class="admin-form-card">

            <!-- SECTION 1 : Informations -->
            <div class="admin-form-section-header">
                <span class="admin-form-section-num">1</span>
                <h2 class="admin-form-section-titre">Informations</h2>
            </div>

            <!-- TITRE -->
            <div class="admin-form-group">
                <label class="admin-form-label" for="titre">
                    Titre <span class="admin-required">*</span>
                    <span class="annotation">// affiché dans le catalogue</span>
                </label>
                <input
                    type="text"
                    id="titre"
                    name="titre"
                    class="admin-form-input"
                    placeholder="Ex. La gamme de Do majeur"
                    value="<?= $modeEdition ? htmlspecialchars($fiche['titre']) : '' ?>"
                    required
                >
            </div>

            <!-- CATÉGORIE + NIVEAU -->
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="id_categorie">
                        Catégorie <span class="admin-required">*</span>
                    </label>
                    <select id="id_categorie" name="id_categorie" class="admin-form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"
                                <?= ($modeEdition && $fiche['id_categorie'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="admin-form-hint">
                        <?= implode(' · ', array_column($categories, 'nom')) ?>
                    </span>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="niveau">
                        Niveau <span class="admin-required">*</span>
                    </label>
                    <select id="niveau" name="niveau" class="admin-form-select" required>
                        <option value="">— Choisir —</option>
                        <option value="Debutant"      <?= ($modeEdition && $fiche['niveau'] === 'Debutant')      ? 'selected' : '' ?>>Débutant</option>
                        <option value="Intermediaire" <?= ($modeEdition && $fiche['niveau'] === 'Intermediaire') ? 'selected' : '' ?>>Intermédiaire</option>
                        <option value="Avance"        <?= ($modeEdition && $fiche['niveau'] === 'Avance')        ? 'selected' : '' ?>>Avancé</option>
                    </select>
                    <span class="admin-form-hint">Débutant · Intermédiaire · Avancé</span>
                </div>
            </div>

            <!-- CONTENU MARKDOWN -->
            <div class="admin-form-group">
                <label class="admin-form-label" for="contenu">
                    Contenu pédagogique <span class="admin-required">*</span>
                    <span class="annotation">// markdown supporté</span>
                </label>

                <!-- Toolbar + Textarea dans un seul bloc sans gap -->
                <div class="md-editor-wrapper">
                    <div class="md-toolbar">
                        <button type="button" class="md-btn" onclick="insererMd('**', '**')" title="Gras"><strong>B</strong></button>
                        <button type="button" class="md-btn" onclick="insererMd('*', '*')"   title="Italique"><em>I</em></button>
                        <button type="button" class="md-btn" onclick="insererMd('## ', '')"  title="Titre H2">H2</button>
                        <button type="button" class="md-btn" onclick="insererListe()"        title="Liste">· liste</button>
                        <button type="button" class="md-btn" onclick="insererMd('> ', '')"   title="Citation">&gt; citation</button>
                        <button type="button" class="md-btn" onclick="insererMd('`', '`')"   title="Code">&lt;/&gt; code</button>
                    </div>

                    <textarea
                        id="contenu"
                        name="contenu"
                        class="admin-form-textarea admin-form-textarea--md"
                        placeholder="Décris la notion : définition, exemples, schéma... Tu peux structurer en sections avec ## titres et insérer du gras avec **texte**."
                        oninput="compterCaracteres(this)"
                        required
                    ><?= $modeEdition ? htmlspecialchars($fiche['contenu']) : '' ?></textarea>
                </div>

                <div class="admin-char-count">
                    <span id="charCount"><?= $modeEdition ? strlen($fiche['contenu']) : 0 ?></span> / 4000 caractères
                </div>
            </div>

            <!-- FICHIER AUDIO -->
            <div class="admin-form-group">
                <label class="admin-form-label">
                    Fichier audio
                    <span class="annotation">// optionnel </span>
                </label>

                <?php if ($modeEdition && !empty($fiche['chemin_audio'])): ?>
                    <div class="admin-audio-actuel">
                        <span class="admin-check">&#10003;</span>
                        Fichier actuel : <code><?= htmlspecialchars($fiche['chemin_audio']) ?></code>
                        <label class="admin-supprimer-audio">
                            <input type="checkbox" name="supprimer_audio" value="1"> Supprimer
                        </label>
                    </div>
                <?php endif; ?>

                <div class="admin-dropzone" id="dropzone" onclick="document.getElementById('fichier_audio').click()">
                    <div class="admin-dropzone-icon-circle">&#8593;</div>
                    <div class="admin-dropzone-body">
                        <strong>Glisse ton fichier ici ou clique pour parcourir</strong>
                        <span class="annotation">// formats acceptés : MP3 — taille max 5 Mo</span>
                        <span id="nomFichierChoisi" class="admin-dropzone-filename"></span>
                    </div>
                    <button type="button" class="btn-custom btn-white admin-dropzone-btn"
                            onclick="event.stopPropagation(); document.getElementById('fichier_audio').click()">
                        Choisir un fichier
                    </button>
                    <input type="file" id="fichier_audio" name="fichier_audio" accept="audio/*"
                           class="hidden" onchange="afficherNomFichier(this)">
                </div>
            </div>

        </div><!-- /.admin-form-card -->

        <!-- ACTIONS -->
        <div class="admin-form-actions">
            <a href="index.php?view=admin_fiches" class="btn-custom btn-white">Annuler</a>
            <button type="submit" class="btn-custom btn-orange">Enregistrer</button>
        </div>

    </form>
</div>

<script>
// Met à jour le compteur de caractères sous le textarea
function compterCaracteres(elt) {
    document.getElementById('charCount').textContent = elt.value.length;
}

// Insère la syntaxe markdown à la position du curseur (ex: ** et ** pour le gras)
// Si du texte est sélectionné, il est enveloppé dans les marqueurs.
// execCommand intègre l'action dans l'historique undo → Ctrl+Z annule tout d'un coup.
function insererMd(avant, apres) {
    const elt            = document.getElementById('contenu');
    const debut          = elt.selectionStart;
    const selection      = elt.value.substring(debut, elt.selectionEnd);
    const avaitSelection = selection.length > 0;

    elt.focus();
    document.execCommand('insertText', false, avant + selection + apres);

    // Si rien n'était sélectionné, on repositionne le curseur entre les marqueurs
    if (!avaitSelection) {
        const nouvellePos = debut + avant.length;
        elt.setSelectionRange(nouvellePos, nouvellePos);
    }

    compterCaracteres(elt);
}

// Insère "- élément" à la position du curseur (avec saut de ligne si nécessaire)
function insererListe() {
    const elt = document.getElementById('contenu');
    const pos = elt.selectionStart;

    // Ajoute un saut de ligne avant si on n'est pas déjà en début de ligne
    const avantCurseur = elt.value.substring(0, pos);
    let prefix = '';
    if (avantCurseur.length > 0 && !avantCurseur.endsWith('\n')) {
        prefix = '\n';
    }

    // Insère l'élément de liste et sélectionne "élément" pour taper directement
    elt.value = elt.value.substring(0, pos) + prefix + '- élément' + elt.value.substring(pos);
    elt.selectionStart = pos + prefix.length + 2;
    elt.selectionEnd   = pos + prefix.length + 9;
    elt.focus();
    compterCaracteres(elt);
}

// Affiche le nom du fichier audio choisi sous la dropzone
function afficherNomFichier(input) {
    const label = document.getElementById('nomFichierChoisi');
    if (input.files[0]) {
        label.textContent = input.files[0].name;
    } else {
        label.textContent = '';
    }
}

// Récupère la dropzone pour gérer le drag & drop
const dropzone = document.getElementById('dropzone');

// Ca empeche que le navigateur ouvre le fichier des qu'on a depose
dropzone.addEventListener('dragover', function(e) {
    e.preventDefault();
    dropzone.classList.add('dragover');
});

// Quitte la zone : retire l'effet visuel
dropzone.addEventListener('dragleave', function() {
    dropzone.classList.remove('dragover');
});

// Dépôt : affecte le fichier déposé à l'input et affiche son nom
dropzone.addEventListener('drop', function(e) {
    e.preventDefault();
    dropzone.classList.remove('dragover');
    const input = document.getElementById('fichier_audio');
    input.files = e.dataTransfer.files;
    afficherNomFichier(input);
});

// Au chargement de la page : lance le compteur (utile en mode édition où le textarea est pré-rempli)
document.addEventListener('DOMContentLoaded', function() {
    const elt = document.getElementById('contenu');
    if (elt) {
        compterCaracteres(elt);
    }
});
</script>