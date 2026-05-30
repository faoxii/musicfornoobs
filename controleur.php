<?php
/*
 * Fichier : controleur.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : traitement des actions (POST), redirige vers index.php apres
 */

session_start();

// Includes des libs
include_once("libs/config.php");
include_once("libs/maLibSQL.pdo.php");
include_once("libs/maLibUtils.php");
include_once("libs/maLibSecurisation.php");

// Includes des modeles
include_once("libs/modele/modele_users.php");
include_once("libs/modele/modele_fiches.php");
include_once("libs/modele/modele_quiz.php");
include_once("libs/modele/modele_progression.php");
include_once("libs/modele/modele_resultats.php");

// Action demandee (POST ou GET selon le cas)
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
                $hash = password_hash($passe, PASSWORD_BCRYPT);
                $idUser = creerUtilisateur($login, $email, $hash);
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

        if ($idReponse) {
            // On stocke la réponse de cette étape en session
            $_SESSION["quiz_reponses"][$etape] = $idReponse;

            // On avance à la question suivante
            $_SESSION["quiz_etape"]++;

            // Si les 5 questions sont répondues, on calcule le résultat
            if ($_SESSION["quiz_etape"] > 5) {

                $slug    = $_SESSION["quiz_slug"];
                $fiche   = getFicheParSlug($slug);
                $idFiche = $fiche["id"];
                $idUser  = $_SESSION["idUser"];

                $score   = 0;
                $details = [];

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

                // Règle métier : on ne donne des points que si on bat son meilleur score
                $ancienMax = getMeilleurScore($idUser, $idFiche);
                if (!$ancienMax) $ancienMax = 0;

                $pointsGagnes = 0;
                if ($score > $ancienMax) {
                    $pointsGagnes = ($score - $ancienMax) * 4;
                }

                // Sauvegarde en BDD
                enregistrerResultat($idUser, $idFiche, $score, $pointsGagnes);
                if ($pointsGagnes > 0) {
                    ajouterPoints($idUser, $pointsGagnes);
                }

                // Bilan stocké en session pour la vue résultats
                $_SESSION['quiz_bilan'] = [
                    'score'         => $score,
                    'points_gagnes' => $pointsGagnes,
                    'details'       => $details
                ];

                // Nettoyage de la session quiz
                unset($_SESSION["quiz_etape"]);
                unset($_SESSION["quiz_reponses"]);
                unset($_SESSION["quiz_slug"]);

                rediriger("index.php?view=resultats");

            } else {
                rediriger("index.php?view=quiz_play&slug=" . $_SESSION["quiz_slug"]);
            }

        } else {
            rediriger("index.php?view=quiz_play&slug=" . $_SESSION["quiz_slug"] . "&msg=Veuillez+choisir+une+reponse");
        }
        break;

    // =========================================
    // ADMIN — FICHES
    // =========================================

    case "creer_fiche":
        $titre       = valider("titre",       "POST");
        $idCategorie = valider("id_categorie","POST");
        $niveau      = valider("niveau",      "POST");
        $contenu     = valider("contenu",     "POST");

        // Vérification des champs obligatoires
        if (!$titre || !$idCategorie || !$niveau || !$contenu) {
            $msg = urlencode("Tous les champs obligatoires doivent être remplis.");
            rediriger("index.php?view=admin_fiche_form&erreur=" . $msg);
            break;
        }

        // Génération du slug depuis le titre
        $slug = genererSlug($titre);

        // Gestion de l'upload audio (optionnel)
        $cheminAudio = null;
        if (!empty($_FILES['fichier_audio']['name'])) {
            $nomFichier  = basename($_FILES['fichier_audio']['name']);
            $destination = "assets/audio/fiches/" . $nomFichier;
            move_uploaded_file($_FILES['fichier_audio']['tmp_name'], $destination);
            $cheminAudio = $destination;
        }

        // Création en BDD
        creerFiche($titre, $slug, $idCategorie, $niveau, $contenu, $cheminAudio);

        rediriger("index.php?view=admin_fiches");
        break;

    case "modifier_fiche":
        $idFiche     = valider("id_fiche",    "POST");
        $titre       = valider("titre",       "POST");
        $idCategorie = valider("id_categorie","POST");
        $niveau      = valider("niveau",      "POST");
        $contenu     = valider("contenu",     "POST");

        // Vérification des champs obligatoires
        if (!$idFiche || !$titre || !$idCategorie || !$niveau || !$contenu) {
            $msg = urlencode("Tous les champs obligatoires doivent être remplis.");
            rediriger("index.php?view=admin_fiche_form&id=" . $idFiche . "&erreur=" . $msg);
            break;
        }

        // Génération du slug depuis le titre
        $slug = genererSlug($titre);

        // Suppression de l'audio si la case est cochée
        $supprimerAudio = (valider("supprimer_audio", "POST") == "1");

        // Gestion de l'upload d'un nouvel audio (optionnel)
        $cheminAudio = null;
        if (!empty($_FILES['fichier_audio']['name'])) {
            $nomFichier  = basename($_FILES['fichier_audio']['name']);
            $destination = "assets/audio/fiches/" . $nomFichier;
            move_uploaded_file($_FILES['fichier_audio']['tmp_name'], $destination);
            $cheminAudio = $destination;
        }

        // Modification en BDD
        modifierFiche($idFiche, $titre, $slug, $idCategorie, $niveau, $contenu, $cheminAudio, $supprimerAudio);

        rediriger("index.php?view=admin_fiches");
        break;

    case "supprimer_fiche":
        $idFiche = valider("id", "GET");

        if ($idFiche) {
            supprimerFiche($idFiche);
        }

        rediriger("index.php?view=admin_fiches");
        break;

    // =========================================
    // ADMIN — QUESTIONS (à compléter)
    // =========================================

    case "CreerQuestion":
        $idFiche  = valider("fiche_id", "POST");
        $type     = valider("type",     "POST");
        $enonce   = valider("enonce",   "POST");
        $ordre    = valider("ordre",    "POST");
 
        if (!$idFiche || !$type || !$enonce || !$ordre) {
            $msg = urlencode("Tous les champs obligatoires doivent être remplis.");
            rediriger("index.php?view=admin_question_form&fiche_id=" . $idFiche . "&erreur=" . $msg);
            break;
        }
 
        // Upload audio de la question (optionnel, seulement si type audio)
        $cheminAudio = null;
        if ($type === 'audio' && !empty($_FILES['fichier_audio_question']['name'])) {
            $nomFichier  = basename($_FILES['fichier_audio_question']['name']);
            $destination = "assets/audio/quiz/" . $nomFichier;
            move_uploaded_file($_FILES['fichier_audio_question']['tmp_name'], $destination);
            $cheminAudio = $destination;
        }
 
        // Création de la question
        $idQuestion = creerQuestion($idFiche, $type, $enonce, $cheminAudio, $ordre);
 
        // Création des 4 réponses via le modèle
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
 
        // Upload audio (optionnel)
        $cheminAudio    = null;
        $supprimerAudio = (valider("supprimer_audio_question", "POST") == "1");
 
        if ($type === 'audio' && !empty($_FILES['fichier_audio_question']['name'])) {
            $nomFichier  = basename($_FILES['fichier_audio_question']['name']);
            $destination = "assets/audio/quiz/" . $nomFichier;
            move_uploaded_file($_FILES['fichier_audio_question']['tmp_name'], $destination);
            $cheminAudio = $destination;
        }
 
        // Modification de la question
        if ($supprimerAudio) {
            modifierQuestion($idQuestion, $type, $enonce, null);
        } else {
            modifierQuestion($idQuestion, $type, $enonce, $cheminAudio);
        }
 
        // Mise à jour des 4 réponses via le modèle
        $contenusReponses = $_POST['reponse_contenu'] ?? [];
        $idsReponses      = $_POST['reponse_id']      ?? [];
        $bonneReponse     = intval(valider("bonne_reponse", "POST"));
 
        for ($i = 0; $i < 4; $i++) {
            $contenuRep = isset($contenusReponses[$i]) ? $contenusReponses[$i] : '';
            $estCorrect = ($i === $bonneReponse) ? 1 : 0;
            $idRep      = isset($idsReponses[$i]) ? $idsReponses[$i] : null;
 
            if ($idRep) {
                // Met à jour la réponse existante
                modifierReponse($idRep, $contenuRep, $estCorrect);
            } else {
                // Crée la réponse si elle n'existe pas encore
                creerReponse($idQuestion, $contenuRep, $estCorrect, $i + 1);
            }
        }
 
        rediriger("index.php?view=admin_questions&fiche_id=" . $idFiche);
        break;
 
    case "SupprimerQuestion":
        $idQuestion = valider("id",       "GET");
        $idFiche    = valider("fiche_id", "GET");
 
        if ($idQuestion) {
            supprimerQuestion($idQuestion);
        }
 
        rediriger("index.php?view=admin_questions&fiche_id=" . $idFiche);
        break;
 
    default:
        rediriger("index.php?view=accueil");
        break;
}
?>