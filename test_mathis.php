<?php
// 1. Inclusions indispensables
include_once("libs/config.php");
include_once("libs/maLibSQL.pdo.php");
include_once("libs/maLibUtils.php");

// 2. Inclusion de TON modèle
include_once("libs/modele/modele_fiches.php");
include_once("libs/modele/modele_progression.php");


echo "<h1>🛠️ Zone de test de Mathis</h1>";


echo "<h2>Test du modèle Progression</h2>";

// Données de test
$idUser = 1;
$idFiche = 1;

// 1. Test : Marquer comme lue
echo "<h3>1. Test de marquerFicheLue()</h3>";
if (marquerFicheLue($idUser, $idFiche)) {
    echo "✅ Fiche $idFiche marquée comme lue.";
} else {
    echo "ℹ️ La fiche était peut-être déjà marquée comme lue (ce qui est normal).";
}

// 2. Test : Vérifier si lue
echo "<h3>2. Test de estFicheLue()</h3>";
if (estFicheLue($idUser, $idFiche)) {
    echo "✅ La fiche $idFiche est bien marquée comme lue pour l'utilisateur $idUser.";
} else {
    echo "❌ Erreur : la fiche devrait être marquée comme lue.";
}

// 3. Test : Compteur
echo "<h3>3. Test de getNbFichesLues()</h3>";
$nb = getNbFichesLues($idUser);
echo "Nombre total de fiches lues : <strong>$nb</strong>";

?>