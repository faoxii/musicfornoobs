<?php
/*
 * Fichier : test.php
 * Auteur  : LEFEBVRE Lucas
 * Date    : 2026-05-26
 * Description : fichier de test des fonctions modele_quiz.php et modele_resultats.php
 * ATTENTION : a supprimer avant la soutenance
 */

require_once 'libs/config.php';
require_once 'libs/maLibSQL.pdo.php';
require_once 'libs/maLibUtils.php';
require_once 'libs/modele/modele_quiz.php';
require_once 'libs/modele/modele_resultats.php';

// IDs issus des donnees de test du .sql
$ID_USER  = 1;  // Lucas_L
$ID_FICHE = 1;  // La gamme de Do majeur (a 5 questions)
$ID_FICHE_SANS_QUIZ = 3; // Les accords parfaits majeurs (0 questions)

// ============================================================
// Helpers d'affichage
// ============================================================

function ok($label) {
    echo "<p style='color:green'>✔ $label</p>";
}

function ko($label, $detail = '') {
    echo "<p style='color:red'>✘ $label" . ($detail ? " — <em>$detail</em>" : '') . "</p>";
}

function section($titre) {
    echo "<h2 style='margin-top:2em; border-bottom:1px solid #ccc'>$titre</h2>";
}

function dump($val) {
    echo "<pre style='background:#f4f4f4;padding:8px;font-size:12px'>";
    print_r($val);
    echo "</pre>";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tests — MusicForNoobs</title>
    <style>
        body { font-family: monospace; max-width: 900px; margin: 40px auto; }
        h1   { background: #1F2937; color: #fff; padding: 12px 16px; border-radius: 6px; }
        h2   { color: #1F2937; }
    </style>
</head>
<body>
<h1>Tests Lucas — modele_quiz & modele_resultats</h1>
<p><em>BDD : <?= $BDD_base ?> @ <?= $BDD_host ?></em></p>

<?php

// ============================================================
// getQuizDisponibles
// ============================================================
section('getQuizDisponibles()');

$tous = getQuizDisponibles();
if (!empty($tous))
    ok("Sans filtre : " . count($tous) . " quiz dispo(s) trouve(s)");
else
    ko("Sans filtre : aucun quiz retourne (verifie que la fiche 1 a bien 5 questions)");
dump($tous);

$filtreCategorie = getQuizDisponibles(1); // Gammes
if (!empty($filtreCategorie))
    ok("Filtre categorie id=1 (Gammes) : " . count($filtreCategorie) . " quiz");
else
    ko("Filtre categorie id=1 : aucun resultat");

$filtreNiveau = getQuizDisponibles(false, 'Debutant');
if (!empty($filtreNiveau))
    ok("Filtre niveau=Debutant : " . count($filtreNiveau) . " quiz");
else
    ko("Filtre niveau=Debutant : aucun resultat");

$filtreCumulé = getQuizDisponibles(1, 'Debutant');
ok("Filtre categorie + niveau : " . count($filtreCumulé) . " quiz");

$sansDispo = getQuizDisponibles(99); // categorie inexistante
if (empty($sansDispo))
    ok("Categorie inexistante : tableau vide (correct)");
else
    ko("Categorie inexistante : devrait renvoyer vide");


// ============================================================
// getQuestion
// ============================================================
section('getQuestion()');

$q1 = getQuestion($ID_FICHE, 1);
if (!empty($q1))
    ok("Question 1 de la fiche $ID_FICHE recuperee (" . count($q1) . " lignes = 4 reponses attendues)");
else
    ko("Question 1 introuvable");
dump($q1);

$qInexistante = getQuestion($ID_FICHE, 99);
if (empty($qInexistante))
    ok("Question ordre=99 : tableau vide (correct)");
else
    ko("Question ordre=99 : devrait renvoyer vide");


// ============================================================
// getQuestionsFiche
// ============================================================
section('getQuestionsFiche()');

$questions = getQuestionsFiche($ID_FICHE);
if (!empty($questions))
    ok("Fiche $ID_FICHE : " . count($questions) . " lignes retournees");
else
    ko("getQuestionsFiche fiche $ID_FICHE : rien retourne");
dump($questions);

$questionsFicheVide = getQuestionsFiche($ID_FICHE_SANS_QUIZ);
if (empty($questionsFicheVide))
    ok("Fiche $ID_FICHE_SANS_QUIZ sans questions : tableau vide (correct)");
else
    ko("Fiche $ID_FICHE_SANS_QUIZ : devrait renvoyer vide");


// ============================================================
// getQuestion_id
// ============================================================
section('getQuestion_id()');

// On recupere l'id de la premiere question depuis getQuestionsFiche
if (!empty($questions)) {
    $idQ = $questions[0]['id_question'];
    $qById = getQuestion_id($idQ);
    if (!empty($qById))
        ok("getQuestion_id($idQ) : " . count($qById) . " lignes");
    else
        ko("getQuestion_id($idQ) : rien retourne");
    dump($qById);
}

$qByIdInexistant = getQuestion_id(9999);
if (empty($qByIdInexistant))
    ok("getQuestion_id(9999) : tableau vide (correct)");
else
    ko("getQuestion_id(9999) : devrait renvoyer vide");


// ============================================================
// creerQuestion + creerReponse
// ============================================================
section('creerQuestion() + creerReponse()');

$idNouvelleQuestion = creerQuestion($ID_FICHE_SANS_QUIZ, 'texte', 'Question de test', null, 1);
if ($idNouvelleQuestion)
    ok("creerQuestion() : question creee avec id=$idNouvelleQuestion");
else
    ko("creerQuestion() : echec de l'insertion");

// 4 reponses pour cette question
$reponses = [
    ['contenu' => 'Bonne reponse', 'est_correct' => 1],
    ['contenu' => 'Mauvaise A',    'est_correct' => 0],
    ['contenu' => 'Mauvaise B',    'est_correct' => 0],
    ['contenu' => 'Mauvaise C',    'est_correct' => 0],
];
foreach ($reponses as $ordre => $rep) {
    $idRep = creerReponse($idNouvelleQuestion, $rep['contenu'], $rep['est_correct'], $ordre + 1);
    if (!$idRep)
        ko("creerReponse ordre=" . ($ordre+1) . " : echec");
}
ok("creerReponse() : 4 reponses inserees");


// ============================================================
// estBonneReponse
// ============================================================
section('estBonneReponse()');

// On recupere les reponses de la question qu'on vient de creer
$repsCrees = getQuestion_id($idNouvelleQuestion);
if (!empty($repsCrees)) {
    $idBonne   = null;
    $idMauvaise = null;
    foreach ($repsCrees as $row) {
        if ($row['est_correct'] == 1 && $idBonne === null)   $idBonne    = $row['id_reponse'];
        if ($row['est_correct'] == 0 && $idMauvaise === null) $idMauvaise = $row['id_reponse'];
    }

    $res = estBonneReponse($idNouvelleQuestion, $idBonne);
    if ($res == 1)
        ok("Bonne reponse correctement identifiee (est_correct=1)");
    else
        ko("La bonne reponse n'est pas reconnue");

    $res2 = estBonneReponse($idNouvelleQuestion, $idMauvaise);
    if ($res2 == 0)
        ok("Mauvaise reponse correctement identifiee (est_correct=0)");
    else
        ko("La mauvaise reponse n'est pas reconnue");
}


// ============================================================
// modifierQuestion
// ============================================================
section('modifierQuestion()');

$nbModif = modifierQuestion($idNouvelleQuestion, 'texte', 'Question modifiee', null);
if ($nbModif)
    ok("modifierQuestion() : mise a jour OK");
else
    ko("modifierQuestion() : aucune ligne modifiee");

$qModifiee = getQuestion_id($idNouvelleQuestion);
if (!empty($qModifiee) && $qModifiee[0]['enonce'] === 'Question modifiee')
    ok("Enonce bien mis a jour en BDD");
else
    ko("Enonce pas mis a jour");


// ============================================================
// supprimerReponsesQuestion + supprimerQuestion
// ============================================================
section('supprimerReponsesQuestion() + supprimerQuestion()');

supprimerReponsesQuestion($idNouvelleQuestion);
$repsApres = getQuestion_id($idNouvelleQuestion);
if (empty($repsApres))
    ok("supprimerReponsesQuestion() : reponses supprimees");
else
    ko("supprimerReponsesQuestion() : des reponses restent en BDD");

supprimerQuestion($idNouvelleQuestion);
$qApres = getQuestionsFiche($ID_FICHE_SANS_QUIZ);
$trouve = false;
foreach ($qApres as $row) {
    if ($row['id_question'] == $idNouvelleQuestion) $trouve = true;
}
if (!$trouve)
    ok("supprimerQuestion() : question supprimee");
else
    ko("supprimerQuestion() : question toujours presente en BDD");


// ============================================================
// getMeilleurScore
// ============================================================
section('getMeilleurScore()');

$meilleur = getMeilleurScore($ID_USER, $ID_FICHE);
if ($meilleur !== false)
    ok("getMeilleurScore(user=$ID_USER, fiche=$ID_FICHE) = $meilleur");
else
    ko("getMeilleurScore : retourne false (aucun resultat en BDD ?)");

$jamaisPasse = getMeilleurScore($ID_USER, 9999);
if ($jamaisPasse === false || $jamaisPasse === null)
    ok("getMeilleurScore fiche inexistante : false/null (correct)");
else
    ok("getMeilleurScore fiche inexistante : $jamaisPasse (aucun passage)");


// ============================================================
// enregistrerResultat
// ============================================================
section('enregistrerResultat()');

$idResultat = enregistrerResultat($ID_USER, $ID_FICHE, 3, 0);
if ($idResultat)
    ok("enregistrerResultat() : insere avec id=$idResultat");
else
    ko("enregistrerResultat() : echec insertion");


// ============================================================
// getActiviteRecente
// ============================================================
section('getActiviteRecente()');

$activite = getActiviteRecente($ID_USER);
if (!empty($activite))
    ok("getActiviteRecente(limit=3) : " . count($activite) . " entree(s)");
else
    ko("getActiviteRecente : aucun resultat");
dump($activite);

$activite5 = getActiviteRecente($ID_USER, 5);
ok("getActiviteRecente(limit=5) : " . count($activite5) . " entree(s)");


// ============================================================
// getNbQuizPasses
// ============================================================
section('getNbQuizPasses()');

$nb = getNbQuizPasses($ID_USER);
if ($nb !== false)
    ok("getNbQuizPasses(user=$ID_USER) = $nb quiz differents");
else
    ko("getNbQuizPasses : retourne false");

$nbNouvel = getNbQuizPasses(9999);
ok("getNbQuizPasses(user inexistant) = $nbNouvel (attendu 0)");

?>

<hr>
<p><em>Tests termines. Pense a supprimer ce fichier avant la soutenance.</em></p>
</body>
</html>
