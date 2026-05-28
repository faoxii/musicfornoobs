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

    case "MarquerLue":
        // TODO Mathis
        rediriger("index.php?view=fiches");
        break;

    case "EtapeQuiz":
        $idReponse = valider("reponse", "POST");
        $etape     = $_SESSION["quiz_etape"];

        if ($idReponse) {
            // 1. On stocke la reponse en session
            $_SESSION["quiz_reponses"][$etape] = $idReponse;

            // 2. On avance d'une etape
            $_SESSION["quiz_etape"]++;

            // 3. Si les 5 questions sont repondues, on calcule le resultat ici directement
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

                // Regle metier : meilleur score conserve
                $ancienMax    = getMeilleurScore($idUser, $idFiche);
                if (!$ancienMax) $ancienMax = 0;

                $pointsGagnes = 0;
                if ($score > $ancienMax) {
                    $pointsGagnes = ($score - $ancienMax) * 4;
                }

                // Sauvegarde BDD
                enregistrerResultat($idUser, $idFiche, $score, $pointsGagnes);
                if ($pointsGagnes > 0) {
                    ajouterPoints($idUser, $pointsGagnes);
                }

                // Bilan pour la vue resultats
                $_SESSION['quiz_bilan'] = [
                    'score'        => $score,
                    'points_gagnes'=> $pointsGagnes,
                    'details'      => $details
                ];

                // Nettoyage session quiz
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

    case "CreerFiche":
        // TODO Mathis
        rediriger("index.php?view=admin_fiches");
        break;

    case "ModifierFiche":
        // TODO Mathis
        rediriger("index.php?view=admin_fiches");
        break;

    case "SupprimerFiche":
        // TODO Mathis
        rediriger("index.php?view=admin_fiches");
        break;

    case "CreerQuestion":
        // TODO Mathis
        rediriger("index.php?view=admin_questions");
        break;

    case "ModifierQuestion":
        // TODO Mathis
        rediriger("index.php?view=admin_questions");
        break;

    case "SupprimerQuestion":
        // TODO Mathis
        rediriger("index.php?view=admin_questions");
        break;

    default:
        rediriger("index.php?view=accueil");
        break;
}

?>