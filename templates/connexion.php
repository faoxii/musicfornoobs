<?php
/*
 * Fichier : templates/connexion.php
 * Auteur  : LEFEBVRE Lucas
 */

// On vérifie si on a reçu un message d'erreur via l'URL (ex: login incorrect)
$msg = valider("msg", "GET");
?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Bon retour !</h1>
        <p class="auth-subtitle">Connecte-toi pour retrouver ta progression.</p>

        <?php if ($msg): ?>
            <div class="alert alert-error"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="POST" action="controleur.php">
            <input type="hidden" name="action" value="Connexion">

            <div class="form-group">
                <label for="login">Pseudo</label>
                <input type="text" name="login" id="login" placeholder="Ton pseudo..." required>
            </div>

            <div class="form-group">
                <label for="passe">Mot de passe</label>
                <input type="password" name="passe" id="passe" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
        </form>

        <p class="auth-footer">
            Pas encore de compte ? <a href="index.php?view=inscription">Inscris-toi ici</a>
        </p>
    </div>
</div>