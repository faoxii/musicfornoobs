<?php
/*
 * Fichier : controleur.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : point d'entrée unique pour toutes les actions POST/GET.
 *               Traite, appelle les modèles, puis redirige — ne génère jamais de HTML.
 */

session_start();

include_once("libs/config.php");
include_once("libs/maLibSQL.pdo.php");
include_once("libs/maLibUtils.php");
include_once("libs/maLibSecurisation.php");

include_once("libs/modele/modele_users.php");
include_once("libs/modele/modele_fiches.php");
include_once("libs/modele/modele_quiz.php");
include_once("libs/modele/modele_progression.php");
include_once("libs/modele/modele_resultats.php");

// L'action peut venir en POST (formulaires) ou en GET (liens de suppression)
$action = valider("action", "POST");
if (!$action) $action = valider("action", "GET");

switch ($action) {

    // =========================================
    // AUTHENTIFICATION
    // =========================================

    case "Connexion":
        $login = valider("login", "POST");
        $passe = valider("passe", "POST");

        if ($login && $passe) {
            if (verifUser($login, $passe)) {
                rediriger("index.php?view=fiches");
            } else {
                $message = urlencode("Pseudo ou mot de passe incorrect");
                rediriger("index.php?view=connexion&msg=" . $message);
            }
        } else {
            $message = urlencode("Veuillez remplir tous les champs");
            rediriger("index.php?view=connexion&msg=" . $message);
        }
        break;

    case "Inscription":
        $login = valider("login", "POST");
        $email = valider("email", "POST");
        $passe = valider("passe", "POST");

        if ($login && $email && $passe) {
            if (loginExiste($login)) {
                rediriger("index.php?view=inscription", "msg=Ce pseudo est deja utilise");
            } else {
                // On hash le mot de passe ici, avant de le passer au modèle,
                // pour que la couche BDD ne manipule jamais le mot de passe en clair
                $hash   = password_hash($passe, PASSWORD_BCRYPT);
                $idUser = creerUtilisateur($login, $email, $hash);

                // Connexion automatique après inscription
                verifUser($login, $passe);
                rediriger("index.php?view=fiches");
            }
        }
        break;

    case "Logout":
        $_SESSION = array();
        session_destroy();
        rediriger("index.php?view=accueil");
        break;


    // =========================================
    // QUIZ
    // =========================================

    case "EtapeQuiz":
        $idReponse = valider("reponse", "POST");
        $etape     = $_SESSION["quiz_etape"];

        if (!$idReponse) {
            // L'utilisateur a soumis sans cocher de réponse (contournement du required JS)
            rediriger("index.php?view=quiz_play&slug=" . $_SESSION["quiz_slug"] . "&msg=Veuillez+choisir+une+reponse");
            break;
        }

        // On stocke la réponse en session plutôt qu'en GET pour empêcher l'utilisateur
        // de manipuler l'URL et sauter des questions ou rejouer une étape déjà répondue
        $_SESSION["quiz_reponses"][$etape] = $idReponse;
        $_SESSION["quiz_etape"]++;

        if ($_SESSION["quiz_etape"] <= 5) {
            // Il reste des questions, on passe à la suivante
            rediriger("index.php?view=quiz_play&slug=" . $_SESSION["quiz_slug"]);
            break;
        }

        // Toutes les 5 questions ont été répondues — on calcule le bilan

        $slug    = $_SESSION["quiz_slug"];
        $fiche   = getFicheParSlug($slug);
        $idFiche = $fiche["id"];
        $idUser  = $_SESSION["idUser"];

        $score   = 0;
        $details = [];

        // Pour chaque question, on relit les 4 réponses depuis la BDD pour comparer
        // (on ne fait pas confiance aux données POST pour déterminer si c'est correct)
        for ($i = 1; $i <= 5; $i++) {
            $idRepUser = $_SESSION["quiz_reponses"][$i];
            $lignes    = getQuestion($idFiche, $i);
            $qText     = $lignes[0]['enonce'];

            $repCorrecteTxt = "";
            $repUserTxt     = "";
            $estBonne       = false;

            foreach ($lignes as $l) {
                if ($l['id_reponse'] == $idRepUser) {
                    $repUserTxt = $l['contenu'];
                    if ($l['est_correct'] == 1) {
                        $score++;
                        $estBonne = true;
                    }
                }
                if ($l['est_correct'] == 1) {
                    $repCorrecteTxt = $l['contenu'];
                }
            }

            $details[] = [
                'num'         => $i,
                'enonce'      => $qText,
                'user_txt'    => $repUserTxt,
                'correct_txt' => $repCorrecteTxt,
                'est_bonne'   => $estBonne
            ];
        }

        // Règle "meilleur score conservé" : on n'ajoute des points que sur la progression
        // par rapport au meilleur score précédent, pas sur le score brut.
        // Chaque bonne réponse vaut 4 pts (5 questions × 4 pts = 20 pts max par quiz).
        $ancienMax    = getMeilleurScore($idUser, $idFiche);
        $pointsGagnes = 0;

        if ($score > $ancienMax) {
            $pointsGagnes = ($score - $ancienMax) * 4;
        }

        enregistrerResultat($idUser, $idFiche, $score, $pointsGagnes);

        if ($pointsGagnes > 0) {
            ajouterPoints($idUser, $pointsGagnes);
        }

        // Le bilan est passé via session plutôt qu'en GET pour ne pas exposer les
        // détails des réponses dans l'URL (et pour éviter le rechargement de page)
        $_SESSION['quiz_bilan'] = [
            'score'         => $score,
            'points_gagnes' => $pointsGagnes,
            'details'       => $details
        ];

        // Nettoyage de la session quiz — on libère l'état pour éviter qu'un rechargement
        // de la page résultats resoumette le bilan

        // unset permet de supprimer une variable de session sans détruire toute la session (contrairement à session_destroy)
        unset($_SESSION["quiz_etape"]);
        unset($_SESSION["quiz_reponses"]);
        unset($_SESSION["quiz_slug"]);

        rediriger("index.php?view=resultats");
        break;


    // =========================================
    // ADMIN — FICHES
    // =========================================

    case "creer_fiche":
        $titre       = valider("titre",        "POST");
        $idCategorie = valider("id_categorie", "POST");
        $niveau      = valider("niveau",       "POST");
        $contenu     = valider("contenu",      "POST");

        if (!$titre || !$idCategorie || !$niveau || !$contenu) {
            $msg = urlencode("Tous les champs obligatoires doivent être remplis.");
            rediriger("index.php?view=admin_fiche_form&erreur=" . $msg);
            break;
        }

        $slug = genererSlug($titre);

        // L'audio est facultatif — une fiche peut exister sans exemple sonore.
        // traiterUploadAudio valide (taille, type réel) et déplace le fichier ;
        // on interprète son retour ici car l'URL de redirection dépend du contexte.
        $audio = traiterUploadAudio('fichier_audio', 'assets/audio/fiches/', $slug);
        if (!$audio['ok']) {
            rediriger("index.php?view=admin_fiche_form&erreur=" . urlencode($audio['erreur']));
            break;
        }
        $cheminAudio = $audio['chemin'];

        creerFiche($titre, $slug, $idCategorie, $niveau, $contenu, $cheminAudio);

        rediriger("index.php?view=admin_fiches");
        break;

    case "modifier_fiche":
        $idFiche     = valider("id_fiche",     "POST");
        $titre       = valider("titre",        "POST");
        $idCategorie = valider("id_categorie", "POST");
        $niveau      = valider("niveau",       "POST");
        $contenu     = valider("contenu",      "POST");

        if (!$idFiche || !$titre || !$idCategorie || !$niveau || !$contenu) {
            $msg = urlencode("Tous les champs obligatoires doivent être remplis.");
            rediriger("index.php?view=admin_fiche_form&id=" . $idFiche . "&erreur=" . $msg);
            break;
        }

        $slug           = genererSlug($titre);
        $supprimerAudio = (valider("supprimer_audio", "POST") == "1");

        // Validation + déplacement de l'audio déléguée à traiterUploadAudio.
        // Si aucun fichier n'est envoyé, $audio['chemin'] vaut null (cas normal).
        $audio = traiterUploadAudio('fichier_audio', 'assets/audio/fiches/', $slug);
        if (!$audio['ok']) {
            rediriger("index.php?view=admin_fiche_form&id=" . $idFiche . "&erreur=" . urlencode($audio['erreur']));
            break;
        }
        $cheminAudio = $audio['chemin'];

        modifierFiche($idFiche, $titre, $slug, $idCategorie, $niveau, $contenu, $cheminAudio, $supprimerAudio);

        rediriger("index.php?view=admin_fiches");
        break;

    case "supprimer_fiche":
        $idFiche = valider("id", "GET");

        if ($idFiche) {
            // La suppression cascade en BDD : questions et réponses associées sont supprimées automatiquement
            supprimerFiche($idFiche);
        }

        rediriger("index.php?view=admin_fiches");
        break;


    // =========================================
    // ADMIN — QUESTIONS
    // =========================================

    case "CreerQuestion":
        $idFiche = valider("fiche_id", "POST");
        $type    = valider("type",     "POST");
        $enonce  = valider("enonce",   "POST");
        $ordre   = valider("ordre",    "POST");

        if (!$idFiche || !$type || !$enonce || !$ordre) {
            $msg = urlencode("Tous les champs obligatoires doivent être remplis.");
            rediriger("index.php?view=admin_question_form&fiche_id=" . $idFiche . "&erreur=" . $msg);
            break;
        }

        // L'audio n'est uploadé que pour les questions de type "audio"
        $cheminAudio = null;
        if ($type === 'audio') {
            $audio = traiterUploadAudio('fichier_audio_question', 'assets/audio/quiz/', 'question-' . $idFiche);
            if (!$audio['ok']) {
                rediriger("index.php?view=admin_question_form&fiche_id=" . $idFiche . "&erreur=" . urlencode($audio['erreur']));
                break;
            }
            $cheminAudio = $audio['chemin'];
        }

        $idQuestion = creerQuestion($idFiche, $type, $enonce, $cheminAudio, $ordre);

        // Les 4 réponses arrivent dans des tableaux parallèles $_POST['reponse_contenu']
        // et l'index de la bonne réponse dans $_POST['bonne_reponse']
        $contenusReponses = $_POST['reponse_contenu'] ?? [];
        $bonneReponse     = intval(valider("bonne_reponse", "POST"));

        for ($i = 0; $i < 4; $i++) {
            $contenuRep = isset($contenusReponses[$i]) ? $contenusReponses[$i] : '';
            $estCorrect = ($i === $bonneReponse) ? 1 : 0;
            creerReponse($idQuestion, $contenuRep, $estCorrect, $i + 1);
        }

        rediriger("index.php?view=admin_questions&fiche_id=" . $idFiche);
        break;

    case "ModifierQuestion":
        $idQuestion = valider("question_id", "POST");
        $idFiche    = valider("fiche_id",    "POST");
        $type       = valider("type",        "POST");
        $enonce     = valider("enonce",      "POST");

        if (!$idQuestion || !$idFiche || !$type || !$enonce) {
            $msg = urlencode("Tous les champs obligatoires doivent être remplis.");
            rediriger("index.php?view=admin_question_form&fiche_id=" . $idFiche . "&question_id=" . $idQuestion . "&erreur=" . $msg);
            break;
        }

        $cheminAudio    = null;
        $supprimerAudio = (valider("supprimer_audio_question", "POST") == "1");

        if ($type === 'audio') {
            $audio = traiterUploadAudio('fichier_audio_question', 'assets/audio/quiz/', 'question-' . $idFiche);
            if (!$audio['ok']) {
                rediriger("index.php?view=admin_question_form&fiche_id=" . $idFiche . "&question_id=" . $idQuestion . "&erreur=" . urlencode($audio['erreur']));
                break;
            }
            $cheminAudio = $audio['chemin'];
        }

        if ($supprimerAudio) {
            modifierQuestion($idQuestion, $type, $enonce, null);
        } else {
            modifierQuestion($idQuestion, $type, $enonce, $cheminAudio);
        }

        $contenusReponses = $_POST['reponse_contenu'] ?? [];
        $idsReponses      = $_POST['reponse_id']      ?? [];
        $bonneReponse     = intval(valider("bonne_reponse", "POST"));

        for ($i = 0; $i < 4; $i++) {
            $contenuRep = isset($contenusReponses[$i]) ? $contenusReponses[$i] : '';
            $estCorrect = ($i === $bonneReponse) ? 1 : 0;
            $idRep      = isset($idsReponses[$i]) ? $idsReponses[$i] : null;

            if ($idRep) {
                modifierReponse($idRep, $contenuRep, $estCorrect);
            } else {
                // Cas rare : réponse manquante en BDD, on la recrée plutôt que de planter
                creerReponse($idQuestion, $contenuRep, $estCorrect, $i + 1);
            }
        }

        rediriger("index.php?view=admin_questions&fiche_id=" . $idFiche);
        break;

    case "SupprimerQuestion":
        $idQuestion = valider("id",       "GET");
        $idFiche    = valider("fiche_id", "GET");

        if ($idQuestion) {
            // Les réponses liées sont supprimées en cascade par la contrainte FK en BDD
            supprimerQuestion($idQuestion);
        }

        rediriger("index.php?view=admin_questions&fiche_id=" . $idFiche);
        break;

    default:
        rediriger("index.php?view=accueil");
        break;
}
?>