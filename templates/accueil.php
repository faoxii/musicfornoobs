<?php
/*
 * Fichier : templates/accueil.php
 * Auteur  : LEFEBVRE Lucas
 * Date    : 2026-05-26
 * Description : Interface 1 - Page d'accueil (visiteur non connecte)
 */
?>
<div class="accueil-wrapper">

<!-- hero c'est la première section de la page -->
    <section class="hero">
        <h1 class="hero-titre">
            Apprends la théorie<br>
            musicale <span class="highlight">à ton rythme</span>
        </h1>
        <p class="hero-subtitle">Gammes, accords, intervalles — pour débutants.</p>
        <!-- &rarr c'est pour afficher une flèche  -->
        <a href="index.php?view=inscription" class="btn btn-primary hero-cta">Commencer &rarr;</a>
    </section>

    <div class="accueil-categories">
        <div class="accueil-card">
            <div class="accueil-card-icon-wrapper">
                <!-- &&#119070; c'est pour afficher une clef de sol    -->
                <span class="accueil-card-icon">&#119070;</span> </div>
            <h3 class="accueil-card-titre">Gammes</h3>
            <p class="accueil-card-desc">Majeures, mineures, modes — apprends à les reconnaître à l'oreille.</p>
        </div>
        
        <div class="accueil-card">
            <div class="accueil-card-icon-wrapper">
                <!-- &#9835; c'est pour afficher une double croche, le symbole -->
                <span class="accueil-card-icon">&#9835;</span> </div>
            <h3 class="accueil-card-titre">Accords</h3>
            <p class="accueil-card-desc">Triades, septièmes, renversements. Construis-les note par note.</p>
        </div>
        
        <div class="accueil-card">
            <div class="accueil-card-icon-wrapper">
                <span class="accueil-card-icon">&#8645;</span> </div>
            <h3 class="accueil-card-titre">Intervalles</h3>
            <p class="accueil-card-desc">De la seconde à l'octave. La base de toute la théorie musicale.</p>
        </div>
    </div>

</div>