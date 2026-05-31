<?php
/*
 * Fichier : templates/admin_question_form.php
 * Auteurs :  BOURGUIGNON Mathis
 * Description : Interface Admin - Créer ou modifier une question
 *               GET ?fiche_id=X            → création
 *               GET ?fiche_id=X&question_id=Y → édition
 */

$idFiche     = valider("fiche_id",    "GET");
$idQuestion  = valider("question_id", "GET");

if (!$idFiche) {
    rediriger("index.php?view=admin_fiches");
}

$fiche = getFiche($idFiche);
if (!$fiche) {
    rediriger("index.php?view=admin_fiches");
}

// Mode édition : on charge la question existante
$question  = null;
$reponses  = [];
$modeEdition = false;

if ($idQuestion) {
    $lignes = getQuestion_id($idQuestion);
    if (!empty($lignes)) {
        $modeEdition = true;
        $question = [
            'id'           => $lignes[0]['id_question'],
            'enonce'       => $lignes[0]['enonce'],
            'type'         => $lignes[0]['type'],
            'chemin_audio' => $lignes[0]['chemin_audio_question'],
            'ordre'        => $lignes[0]['ordre'],
        ];
        foreach ($lignes as $l) {
            $reponses[] = [
                'id'          => $l['id_reponse'],
                'contenu'     => $l['contenu'],
                'est_correct' => $l['est_correct'],
            ];
        }
    }
}

// Calcule le prochain ordre si mode création
$prochaineOrdre = 1;
if (!$modeEdition) {
    $existantes = getQuestionsFiche($idFiche);
    $ordresExistants = array_unique(array_column($existantes, 'ordre_question'));
    $prochaineOrdre  = count($ordresExistants) + 1;
}

$titrePage = $modeEdition ? 'Modifier la question' : 'Nouvelle question';
?>

<div class="admin-form-wrapper">

    <a href="index.php?view=admin_questions&fiche_id=<?= $idFiche ?>" class="btn-custom btn-white admin-form-retour">
        ← Retour aux questions
    </a>

    <h1 class="admin-form-titre"><?= $titrePage ?></h1>

    <?php if (isset($_GET['erreur'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['erreur']) ?></div>
    <?php endif; ?>

    <form method="POST" action="controleur.php" enctype="multipart/form-data">

        <input type="hidden" name="action"   value="<?= $modeEdition ? 'ModifierQuestion' : 'CreerQuestion' ?>">
        <input type="hidden" name="fiche_id" value="<?= $idFiche ?>">
        <input type="hidden" name="ordre"    value="<?= $modeEdition ? $question['ordre'] : $prochaineOrdre ?>">
        <?php if ($modeEdition): ?>
            <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
        <?php endif; ?>

        <!-- SECTION 1 : Question -->
        <div class="admin-form-card">

            <div class="admin-form-section-header">
                <span class="admin-form-section-num">1</span>
                <h2 class="admin-form-section-titre">Question</h2>
            </div>

            <!-- Fiche associée + Type côte à côte -->
            <div class="admin-form-row">

                <!-- Fiche associée (lecture seule en édition) -->
                <div class="admin-form-group">
                    <label class="admin-form-label">Fiche associée</label>
                    <div class="admin-fiche-associee">
                        <?= htmlspecialchars($fiche['titre']) ?>
                    </div>
                </div>

                <!-- Type de question -->
                <div class="admin-form-group">
                    <label class="admin-form-label">Type de question</label>
                    <div class="admin-type-group">
                        <label class="admin-type-option <?= (!$modeEdition || $question['type'] === 'texte') ? '' : '' ?>">
                            <input type="radio" name="type" value="texte"
                                <?= (!$modeEdition || ($modeEdition && $question['type'] === 'texte')) ? 'checked' : '' ?>
                                onchange="toggleAudioSection(this.value)">
                            QCM texte
                        </label>
                        <label class="admin-type-option">
                            <input type="radio" name="type" value="audio"
                                <?= ($modeEdition && $question['type'] === 'audio') ? 'checked' : '' ?>
                                onchange="toggleAudioSection(this.value)">
                            Audio
                        </label>
                    </div>
                </div>

            </div>

            <!-- Énoncé -->
            <div class="admin-form-group">
                <label class="admin-form-label" for="enonce">Texte de la question</label>
                <input
                    type="text"
                    id="enonce"
                    name="enonce"
                    class="admin-form-input"
                    placeholder="Ex : Quelle gamme entendez-vous ?"
                    value="<?= $modeEdition ? htmlspecialchars($question['enonce']) : '' ?>"
                    required
                >
            </div>

            <!-- Fichier audio (affiché seulement si type = audio) -->
            <div class="admin-form-group" id="section-audio"
                 style="<?= ($modeEdition && $question['type'] === 'texte') ? 'display:none' : '' ?>">
                <label class="admin-form-label">
                    Fichier audio
                </label>

                <?php if ($modeEdition && !empty($question['chemin_audio'])): ?>
                    <div class="admin-audio-actuel">
                        <span class="admin-check">&#10003;</span>
                        Fichier actuel : <code><?= htmlspecialchars(basename($question['chemin_audio'])) ?></code>
                        <label class="admin-supprimer-audio">
                            <input type="checkbox" name="supprimer_audio_question" value="1"> Supprimer
                        </label>
                    </div>
                <?php endif; ?>

                <div class="admin-dropzone" id="dropzone-question"
                     onclick="document.getElementById('fichier_audio_question').click()">
                    <div class="admin-dropzone-icon-circle">&#8593;</div>
                    <div class="admin-dropzone-body">
                        <strong>Glisse ton fichier ici ou clique pour parcourir</strong>
                        <span class="annotation">// formats acceptés : MP3 — taille max 5 Mo</span>
                        <span id="nomFichierQuestion" class="admin-dropzone-filename"></span>
                    </div>
                    <button type="button" class="btn-custom btn-white admin-dropzone-btn"
                            onclick="event.stopPropagation(); document.getElementById('fichier_audio_question').click()">
                        Choisir un fichier
                    </button>
                    <input type="file" id="fichier_audio_question" name="fichier_audio_question"
                           accept="audio/*" class="hidden"
                           onchange="afficherNomFichier(this, 'nomFichierQuestion')">
                </div>
            </div>

        </div><!-- /.admin-form-card -->

        <!-- SECTION 2 : Réponses -->
        <div class="admin-form-card">

            <div class="admin-form-section-header">
                <span class="admin-form-section-num">2</span>
                <h2 class="admin-form-section-titre">Réponses proposées</h2>
            </div>

            <div class="admin-reponses-list">
                <?php
                // Labels A B C D pour les 4 réponses
                $labels = ['A', 'B', 'C', 'D'];
                for ($i = 0; $i < 4; $i++):
                    $valContenu  = $modeEdition && isset($reponses[$i]) ? htmlspecialchars($reponses[$i]['contenu']) : '';
                    $estCorrecte = $modeEdition && isset($reponses[$i]) && $reponses[$i]['est_correct'];
                    if ($modeEdition && isset($reponses[$i])) {
                        echo '<input type="hidden" name="reponse_id[]" value="' . $reponses[$i]['id'] . '">';
                    }
                ?>
                    <div class="admin-reponse-row">
                        <span class="admin-reponse-label"><?= $labels[$i] ?></span>
                        <input
                            type="text"
                            name="reponse_contenu[]"
                            class="admin-form-input admin-reponse-input"
                            placeholder="Réponse <?= $labels[$i] ?>..."
                            value="<?= $valContenu ?>"
                            required
                        >
                        <label class="admin-reponse-correcte-label">
                            <input type="radio" name="bonne_reponse" value="<?= $i ?>"
                                <?= $estCorrecte ? 'checked' : '' ?>>
                            Bonne réponse
                        </label>
                    </div>
                <?php endfor; ?>
            </div>

        </div><!-- /.admin-form-card -->

        <!-- ACTIONS -->
        <div class="admin-form-actions">
            <a href="index.php?view=admin_questions&fiche_id=<?= $idFiche ?>" class="btn-custom btn-white">Annuler</a>
            <button type="submit" class="btn-custom btn-orange">Enregistrer</button>
        </div>

    </form>
</div>

<script>
// Affiche ou masque la section audio selon le type choisi
function toggleAudioSection(type) {
    const section = document.getElementById('section-audio');
    if (type === 'audio') {
        section.style.display = '';
    } else {
        section.style.display = 'none';
    }
}

// Affiche le nom du fichier sélectionné
function afficherNomFichier(input, idLabel) {
    const label = document.getElementById(idLabel);
    if (input.files[0]) {
        label.textContent = input.files[0].name;
    } else {
        label.textContent = '';
    }
}

// Drag & drop sur la dropzone de la question
const dropzoneQ = document.getElementById('dropzone-question');

dropzoneQ.addEventListener('dragover', function(e) {
    e.preventDefault();
    dropzoneQ.classList.add('dragover');
});

dropzoneQ.addEventListener('dragleave', function() {
    dropzoneQ.classList.remove('dragover');
});

dropzoneQ.addEventListener('drop', function(e) {
    e.preventDefault();
    dropzoneQ.classList.remove('dragover');
    const input = document.getElementById('fichier_audio_question');
    input.files = e.dataTransfer.files;
    afficherNomFichier(input, 'nomFichierQuestion');
});
</script>