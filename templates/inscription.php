<?php
/*
 * Fichier : templates/inscription.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 3 - Formulaire d'inscription
 */
?>
<!-- TODO Lucas : formulaire d'inscription (login, email, mot de passe 8 caracteres mini) -->
<h1>Inscription</h1>
<form method="POST" action="controleur.php">
    <input type="hidden" name="action" value="Inscription">
    <label>Login : <input type="text" name="login" required></label>
    <label>Email : <input type="email" name="email" required></label>
    <label>Mot de passe : <input type="password" name="passe" minlength="8" required></label>
    <button type="submit">Creer mon compte</button>
</form>
