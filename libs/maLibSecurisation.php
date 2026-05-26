<?php


/**
 * @file login.php
 * Fichier contenant des fonctions de vérification de logins
 */

/**
 * Cette fonction vérifie si le login/passe passés en paramètre sont légaux
 * Elle stocke les informations sur la personne dans des variables de session : session_start doit avoir été appelé...
 * Infos à enregistrer : pseudo, idUser, heureConnexion, isAdmin
 * Elle enregistre l'état de la connexion dans une variable de session "connecte" = true
 * @pre login et passe ne doivent pas être vides
 * @param string $login
 * @param string $password
 * @return false ou true ; un effet de bord est la création de variables de session
 */
function verifUser($login, $password)
{
    // On appelle le modèle pour vérifier en BDD
    $id = verifUserBdd($login, $password);

    if (!$id) return false; 

    // On récupère les infos de l'utilisateur pour avoir son rôle
    $userInfos = getUtilisateur($id);

    // On crée les variables de session définies dans le L3
    $_SESSION["login"] = $login;
    $_SESSION["idUser"] = $id;
    $_SESSION["role"] = $userInfos["role"]; // Important pour l'accès admin !
    $_SESSION["connecte"] = true;
    $_SESSION["heureConnexion"] = date("H:i:s");
    
    return true;
}




/**
 * Fonction à placer au début de chaque page privée
 * Cette fonction redirige vers la page $urlBad en envoyant un message d'erreur 
	et arrête l'interprétation si l'utilisateur n'est pas connecté
 * Elle ne fait rien si l'utilisateur est connecté, et si $urlGood est faux
 * Elle redirige vers urlGood sinon
 */
function securiser($urlBad,$urlGood=false)
{
	if (! valider("connecte","SESSION")) {
		rediriger($urlBad);
		die("");
	}
	else {
		if ($urlGood)
			rediriger($urlGood);
	}
}

?>
