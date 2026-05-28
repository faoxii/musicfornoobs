<?php
/*
 * Fichier : libs/maLibSecurisation.php
 * Description : helpers d'authentification et de securisation des pages
 */


/**
 * Verifie login/mot de passe, cree la session si OK
 * @return bool
 */
function verifUser($login, $password) {
    // On recupere la ligne utilisateur depuis le modele
    $rows = verifUserBdd($login);

    if (empty($rows)) return false;

    $user = $rows[0];

    // Verification du mot de passe hache
    if (!password_verify($password, $user['mot_de_passe'])) return false;

    // On recupere les infos completes (role notamment)
    $infos = getUtilisateur($user['id']);
    if (empty($infos)) return false;
    $infos = $infos[0];

    // Creation des variables de session
    $_SESSION["connecte"]       = true;
    $_SESSION["idUser"]         = $user['id'];
    $_SESSION["login"]          = $login;
    $_SESSION["role"]           = $infos['role'];
    $_SESSION["heureConnexion"] = date("H:i:s");

    return true;
}


/**
 * Securise une page privee — redirige si non connecte
 */
function securiser($urlBad, $urlGood = false) {
    if (!valider("connecte", "SESSION")) {
        rediriger($urlBad);
        die("");
    } else {
        if ($urlGood) rediriger($urlGood);
    }
}

?>