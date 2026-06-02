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
                    <!-- Affiche le nombre de caractères saisis et la limite de 4000 caractères
                    par defaut, le compteur affiche 0 / 4000 caractères, mais en mode édition, il affiche la longueur du contenu déjà présent (ex: 256 / 4000 caractères) -->
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

                    <!-- stopPropagation  permet d'empêcher la propagation de l'événement click, 
                     car ici on veut uniquement déclencher le click sur l'input file 
                     en js, par default les element se propagent vers leurs parents, donc si on clique sur le bouton "Choisir un fichier", 
                     ça déclencherait aussi le click sur la dropzone qui est le parent du bouton, 
                     et ça ouvrirait 2 fois la fenêtre de sélection de fichier, ce qui n'est pas souhaité. 
                     Avec stopPropagation, on empêche que le click sur le bouton remonte jusqu'à la dropzone
                     et ducoup on n'ouvre qu'une seule fois la fenêtre de sélection de fichier.
                     -->
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
    // on met à jour le span #charCount avec la longueur du contenu du textarea
    document.getElementById('charCount').textContent = elt.value.length;
}

// Insère la syntaxe markdown à la position du curseur (ex: ** et ** pour le gras)
// Si du texte est sélectionné, il est enveloppé dans les marqueurs.
// execCommand intègre l'action dans l'historique undo → Ctrl+Z annule tout d'un coup.
function insererMd(avant, apres) {
    const elt            = document.getElementById('contenu');

    //  selectionStart permet identifier l'index du début de la zone sélectionnée 
    // ou la position du curseur si rien n'est sélectionne
    const debut          = elt.selectionStart;

    // extrait le texte present entre les marqueurs s'il y en a, sinon c'est une chaîne vide
    const selection      = elt.value.substring(debut, elt.selectionEnd);
    
    
    // verifie si l'utilisateur a selectionne du texte ou pas
    const avaitSelection = selection.length > 0;

    // execCommand a besoin que l'élément soit focus pour fonctionner, car elle simule une saisie au clavier
    // L'utilisateur était en train d'écrire dans la zone de texte (le <textarea>), donc le <textarea> avait le focus.
    // L'utilisateur clique sur le bouton "Gras". À ce moment précis, le <textarea> perd le focus, et c'est le bouton qui prend le focus
    //Si on ne fait rien, la commande va échouer, car elle va essayer d'insérer du texte dans le bouton sur lequel on vient de cliquer, et non dans la zone de texte. 
    elt.focus();
    
    // c'est comme si on faisais elt.value = ....
    // inserText injecte du texte brut 
    document.execCommand('insertText', false, avant + selection + apres);

    // Si rien n'était sélectionné, on repositionne le curseur entre les marqueurs
    // avant.length c'est la longueur de la chaine markdown qu'on vient d'insérer 
    if (!avaitSelection) {
        const nouvellePos = debut + avant.length;

        // de base selectionRange sert normalement à surligner du texte entre un point A et un point B.
        // mais si on met le même point A et B, ça ne surligne rien, et ça positionne juste le curseur à cet endroit précis.
        elt.setSelectionRange(nouvellePos, nouvellePos);
    }
    // Après l'insertion, on met à jour le compteur de caractères
    compterCaracteres(elt);
}

// Insère "- élément" à la position du curseur (avec saut de ligne si nécessaire)
function insererListe() {
    const elt = document.getElementById('contenu');
    const pos = elt.selectionStart;

    // Ajoute un saut de ligne avant si on n'est pas déjà en début de ligne

    // on extrait tout le texte qui se trouve avant le curseur
    const avantCurseur = elt.value.substring(0, pos);
    let prefix = '';
    // si texte pas vide et que ca finit pas par un saut de ligne, alors on ajoute un saut de ligne avant d'insérer le marqueur de liste
    if (avantCurseur.length > 0 && !avantCurseur.endsWith('\n')) {
        prefix = '\n';
    }

    // Insère l'élément de liste et sélectionne "élément" pour taper directement

    // on reconstuit le texte de la zonne en collant 4 morceaux :
    // - le texte avant le curseur
    // - eventuel saut de ligne
    // - le marqueur de liste "- élément"
    // - le texte après le curseur 
    elt.value = avantCurseur + prefix + '- élément' + elt.value.substring(pos);

    // donc ici le texte est insere, on veut juste surligner le mot "élément" pour que l'utilisateur puisse le remplacer directement
    // on saute le tiret et l'espace
    elt.selectionStart = pos + prefix.length + 2;
    // - élément fait 9 caractères de long
    elt.selectionEnd   = pos + prefix.length + 9;
    //on ramène l'attention (et le clavier) sur la zone de texte, pour que l'utilisateur puisse frapper ses touches immédiatement.
    elt.focus();
    //On met à jour le compteur e
    compterCaracteres(elt);
}

// Affiche le nom du fichier audio choisi sous la dropzone
function afficherNomFichier(input) {
    const label = document.getElementById('nomFichierChoisi');
    // input.files est une liste de fichiers sélectionnés, on prend le premier (input.files[0])
    //  car on n'autorise la sélection que d'un seul fichier
    if (input.files[0]) {
        // on affiche le nom du fichier sélectionné dans la dropzone
        label.textContent = input.files[0].name;
    } else {
        // si aucun fichier n'est sélectionné (ex: si l'utilisateur annule la sélection), on vide le label
        label.textContent = '';
    }
}

// Récupère la dropzone pour gérer le drag & drop
const dropzone = document.getElementById('dropzone');

// Ca empeche que le navigateur ouvre le fichier des qu'on a depose

// dragover est un evenement qui se déclenche en boucle tant que ta souris  survole la zone ciblée.
dropzone.addEventListener('dragover', function(e) {
    // par default quand on depose un fichier , le navigateur essaye de l'ouvrir 
    // donc preventDefault empeche ce comportement
    e.preventDefault();
    // ca ajoute une classe CSS qui change le style de la dropzone pour indiquer visuellement que l'on peut déposer le fichier
    dropzone.classList.add('dragover');
});

// dragleave se déclenche quand la souris quitte la zone ciblée, 
//on suprpime donc  la classe CSS qui indique que l'on peut déposer le fichier
dropzone.addEventListener('dragleave', function() {
    dropzone.classList.remove('dragover');
});


// Dépôt : affecte le fichier déposé à l'input et affiche son nom


// drop c'est l'événement qui se déclenche quand on lâche le fichier sur la dropzone
dropzone.addEventListener('drop', function(e) {
    //on bloque le comportement par défaut du navigateur qui essaierait d'ouvrir ou de télécharger 
    // le fichier dans une autre fenetre
    e.preventDefault();
    // on retire la classe CSS de survol
    dropzone.classList.remove('dragover');
    
    const input = document.getElementById('fichier_audio');

    //dataTransfer.files, qui contient les fichiers que l'on tenais avec la souris
    // c'est comme si on avait cliqué sur le bouton "Choisir un fichier" et sélectionné le même fichier,
    input.files = e.dataTransfer.files;
    // on affiche le nom du fichier déposé dans la dropzone
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