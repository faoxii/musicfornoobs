<?php
/*
 * Fichier : templates/header.php
 * Auteurs : LEFEBVRE Lucas / BOURGUIGNON Mathis
 * Description : entete commune a toutes les pages (navbar + CSS)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicForNoobs</title>

    <!-- Google Fonts : Caveat (titres), Kalam (texte), JetBrains Mono (annotations) -->
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
    <a href="index.php?view=accueil" class="navbar-logo">
        <span class="logo-icon">♪</span>
        MusicForNoobs
    </a>

    <?php if (isset($_SESSION["connecte"]) && $_SESSION["connecte"]): ?>
        <div class="navbar-links">
            <a href="index.php?view=fiches" class="<?= $view === 'fiches' || $view === 'fiche_read' ? 'active' : '' ?>">Fiches</a>
            <a href="index.php?view=quiz" class="<?= $view === 'quiz' || $view === 'quiz_play' ? 'active' : '' ?>">Quiz</a>
            <a href="index.php?view=dashboard" class="<?= $view === 'dashboard' ? 'active' : '' ?>">Progression</a>
            <a href="index.php?view=classement" class="<?= $view === 'classement' ? 'active' : '' ?>">Classement</a>
            <?php if ($_SESSION["role"] === "admin"): ?>
                <a href="index.php?view=admin_fiches" class="<?= strpos($view, 'admin_') === 0 ? 'active' : '' ?>">Administration</a>
            <?php endif; ?>
        </div>
        <div class="navbar-user">
            <?php
                // Avatar avec les initiales du login
                $initiales = strtoupper(substr($_SESSION["login"], 0, 2));
            ?>
            <span class="avatar"><?= $initiales ?></span>
        </div>
    <?php endif; ?>
</nav>
<?php endif; ?>

<main class="container">
