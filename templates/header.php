<?php
/*
 * Fichier : templates/header.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : entete commune (HTML head + navbar pour les pages connectees)
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicForNoobs</title>

    <!-- Importation des polices  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&family=Kalam:wght@300;400;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages.css">
</head>
<body>






<?php if (isset($avec_header) && $avec_header): ?>
<nav class="navbar">
    <!-- Logo = icone note + nom du site -->
    <a href="index.php?view=fiches" class="navbar-logo">
        <span class="logo-icon">&#9835;</span>
        <span class="logo-text">MusicForNoobs</span>
    </a>

    <!-- Liens de navigation uniquement si on est connecte -->
    <?php if (valider("connecte",$type="SESSION")): ?>
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
            <?php if ($_SESSION["role"] === "admin"): ?>
                <a href="index.php?view=admin_fiches"
                   class="nav-link <?= strpos($view, 'admin_') === 0 ? 'active' : '' ?>">
                    Administration
                </a>
            <?php endif; ?>
        </div>

        <!-- Avatar : initiales du login dans un cercle + menu deroulant -->
        <div class="navbar-user">
            <?php
                // On prend les 2 premieres lettres du login en majuscules
                // Ex: "Lucas_L" -> "LU", "thomas.b" -> "TH"
                $initiales = strtoupper(substr($_SESSION["login"], 0, 2));
            ?>
            <div class="avatar-menu" id="avatarMenu">
                <button class="avatar" onclick="toggleAvatarMenu()" type="button">
                    <?= htmlspecialchars($initiales) ?>
                </button>
                <div class="avatar-dropdown" id="avatarDropdown">
                    <div class="avatar-dropdown-header">
                        <span class="annotation">Connecte en tant que</span>
                        <strong><?= htmlspecialchars($_SESSION["login"]) ?></strong>
                    </div>
                    <a href="index.php?view=dashboard" class="avatar-dropdown-item">
                        Dashboard
                    </a>
                    <a href="controleur.php?action=Logout" class="avatar-dropdown-item avatar-dropdown-logout">
                        Deconnexion
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</nav>

<!-- Script pour le menu deroulant de l'avatar -->
<script>
function toggleAvatarMenu() {
    var dropdown = document.getElementById("avatarDropdown");
    dropdown.classList.toggle("open");
}

// Fermer le menu si on clique en dehors
document.addEventListener("click", function(event) {
    var menu = document.getElementById("avatarMenu");
    var dropdown = document.getElementById("avatarDropdown");
    if (menu && !menu.contains(event.target)) {
        dropdown.classList.remove("open");
    }
});
</script>
<?php endif; ?>

<main class="container">