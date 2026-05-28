<?php
/*
 * Fichier : templates/inscription.php
 * Auteur  : LEFEBVRE Lucas
 */

$msg = valider("msg", "GET");
?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Rejoins l'aventure</h1>
        <p class="auth-subtitle">Crée ton compte pour commencer à apprendre.</p>

        <?php if ($msg): ?>
            <div class="alert alert-error"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="POST" action="controleur.php">
            <input type="hidden" name="action" value="Inscription">

            <div class="form-group">
                <label for="login">Pseudo souhaité</label>
                <input type="text" name="login" id="login" placeholder="Ex: Melomane59" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="ton@email.com" required>
            </div>

            <div class="form-group">
                <label for="passe">Mot de passe (8 caractères min.)</label>
                <input type="password" name="passe" id="passe" minlength="8" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Créer mon compte</button>
        </form>

        <p class="auth-footer">
            Déjà inscrit ? <a href="index.php?view=connexion">Connecte-toi</a>
        </p>
    </div>
</div>