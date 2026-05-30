<?php
/*
 * Fichier : templates/header.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : en-tête commune (HTML head + navbar adaptative)
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicForNoobs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/caveat/v18/Wnz6HAc5bAfYB2Q7Yj82cw.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/kalam/v16/ztzj4dQxBltKwwh25A.woff2" as="font" type="font/woff2" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&family=Kalam:wght@300;400;700&family=JetBrains+Mono:wght@400&display=block" rel="stylesheet">

    <!-- Styles de l'application -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php?view=accueil" class="navbar-logo">
        <span class="logo-icon">&#9835;</span>
        <span class="logo-text">MusicForNoobs</span>
    </a>

    <?php if (isset($_SESSION["connecte"]) && $_SESSION["connecte"]): ?>
        <div class="navbar-links">
            <a href="index.php?view=fiches"
               class="nav-link <?= ($view === 'fiches' || $view === 'fiche_read') ? 'active' : '' ?>">
                Fiches
            </a>
            <a href="index.php?view=quiz"
               class="nav-link <?= ($view === 'quiz' || $view === 'quiz_play' || $view === 'resultats') ? 'active' : '' ?>">
                Quiz
            </a>
            <a href="index.php?view=classement"
               class="nav-link <?= $view === 'classement' ? 'active' : '' ?>">
                Classement
            </a>
            <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                <a href="index.php?view=admin_fiches"
                   class="nav-link <?= strpos($view, 'admin_') === 0 ? 'active' : '' ?>">
                    Administration
                </a>
            <?php endif; ?>
        </div>

        <div class="navbar-user">
            <?php
                $initiales = strtoupper(substr($_SESSION["login"], 0, 2));
            ?>
            <div class="avatar-menu" id="avatarMenu">
                <button class="avatar" onclick="toggleAvatarMenu()" type="button">
                    <?= htmlspecialchars($initiales) ?>
                </button>
                <div class="avatar-dropdown" id="avatarDropdown">
                    <div class="avatar-dropdown-header">
                        <span class="annotation">Connecté en tant que</span>
                        <strong><?= htmlspecialchars($_SESSION["login"]) ?></strong>
                    </div>
                    <a href="index.php?view=dashboard" class="avatar-dropdown-item">
                        Dashboard
                    </a>
                    <a href="controleur.php?action=Logout" class="avatar-dropdown-item avatar-dropdown-logout">
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="navbar-actions">
            <?php if (isset($view) && ($view === 'inscription' || $view === 'connexion')): ?>
                <a href="index.php?view=accueil" class="btn btn-outline">&larr; Retour à l'accueil</a>
            <?php else: ?>
                <a href="index.php?view=connexion" class="btn btn-outline">Se connecter</a>
                <a href="index.php?view=inscription" class="btn btn-primary">S'inscrire</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</nav>

<script>
function toggleAvatarMenu() {
    var dropdown = document.getElementById("avatarDropdown");
    dropdown.classList.toggle("open");
}

document.addEventListener("click", function(event) {
    var menu = document.getElementById("avatarMenu");
    var dropdown = document.getElementById("avatarDropdown");
    if (menu && !menu.contains(event.target)) {
        dropdown.classList.remove("open");
    }
});
</script>

<main class="<?= (isset($view) && in_array($view, ['fiches', 'quiz', 'fiche_read'])) ? 'container container--full' : 'container' ?>">