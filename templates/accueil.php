<?php
/*
 * Fichier : templates/accueil.php
 * Auteur  : LEFEBVRE Lucas
 * Date    : 2026-05-26
 * Description : Interface 1 - Page d'accueil (visiteur non connecte)
 */
?>

<div class="accueil-wrapper">

    <!-- NAVBAR -->
    <nav class="accueil-navbar">
        <a href="index.php?view=accueil" class="navbar-logo">
            <span class="logo-icon">♪</span>
            MusicForNoobs
        </a>
        <div class="accueil-navbar-actions">
            <a href="index.php?view=connexion" class="btn btn-secondary">Se connecter</a>
            <a href="index.php?view=inscription" class="btn btn-primary">S'inscrire</a>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <h1 class="hero-titre">
            Apprends la <span class="highlight">théorie musicale</span><br>
            à ton rythme
        </h1>
        <a href="index.php?view=inscription" class="btn btn-primary hero-cta">Commencer</a>
    </section>

    <!-- CARTES CATEGORIES -->
    <div class="accueil-categories">
        <div class="accueil-card">
            <span class="accueil-card-icon">🎵</span>
            <h3 class="accueil-card-titre">Gammes</h3>
            <p class="accueil-card-desc">Majeures, mineures, modes — apprends à les reconnaître à l'oreille.</p>
        </div>
        <div class="accueil-card">
            <span class="accueil-card-icon">🎸</span>
            <h3 class="accueil-card-titre">Accords</h3>
            <p class="accueil-card-desc">Triades, septièmes, renversements. Construis-les note par note.</p>
        </div>
        <div class="accueil-card">
            <span class="accueil-card-icon">🎹</span>
            <h3 class="accueil-card-titre">Intervalles</h3>
            <p class="accueil-card-desc">De la seconde à l'octave. La base de toute la théorie musicale.</p>
        </div>
    </div>

</div>