<?php
/*
 * Fichier : templates/connexion.php
 * Auteur  : LEFEBVRE Lucas
 * Description : Interface 2 - Formulaire de connexion
 */
?>
<!-- TODO Lucas : formulaire de connexion avec onglet Inscription -->
<h1>Connexion</h1>
<form method="POST" action="controleur.php">
    <input type="hidden" name="action" value="Connexion">
    <label>Login : <input type="text" name="login" required></label>
    <label>Mot de passe : <input type="password" name="passe" required></label>
    <button type="submit">Se connecter</button>
</form>
