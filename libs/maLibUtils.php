<?php
require_once("libs/Parsedown.php"); // pour le markdown


/**
 * @file maLibUtils.php
 * Ce fichier définit des fonctions d'accès ou d'affichage pour les tableaux superglobaux
 */

/**
 * Vérifie l'existence (isset) et la taille (non vide) d'un paramètre dans un des tableaux GET, POST, COOKIES, SESSION
 * Renvoie false si le paramètre est vide ou absent
 * @note l'utilisation de empty est critique : 0 est empty !!
 * Lorsque l'on teste, il faut tester avec un ===
 * @param string $nom
 * @param string $type
 * @return string|boolean
 */
function valider($nom, $type = "REQUEST")
{
    switch ($type) {
        case 'REQUEST':
            if (isset($_REQUEST[$nom]) && !($_REQUEST[$nom] == ""))
                return $_REQUEST[$nom];
            break;
        case 'GET':
            if (isset($_GET[$nom]) && !($_GET[$nom] == ""))
                return $_GET[$nom];
            break;
        case 'POST':
            if (isset($_POST[$nom]) && !($_POST[$nom] == ""))
                return $_POST[$nom];
            break;
        case 'COOKIE':
            if (isset($_COOKIE[$nom]) && !($_COOKIE[$nom] == ""))
                return $_COOKIE[$nom];
            break;
        case 'SESSION':
            if (isset($_SESSION[$nom]) && !($_SESSION[$nom] == ""))
                return $_SESSION[$nom];
            break;
        case 'SERVER':
            if (isset($_SERVER[$nom]) && !($_SERVER[$nom] == ""))
                return $_SERVER[$nom];
            break;
    }
    return false;
}

/**
 * Vérifie l'existence (isset) et la taille (non vide) d'un paramètre dans un des tableaux GET, POST, COOKIE, SESSION
 * Prend un argument définissant la valeur renvoyée en cas d'absence de l'argument dans le tableau considéré

 * @param string $nom
 * @param string $defaut
 * @param string $type
 * @return string
*/
function getValue($nom,$defaut=false,$type="REQUEST")
{
	// NB : cette commande affecte la variable resultat une ou deux fois
	if (($resultat = valider($nom,$type)) === false)
		$resultat = $defaut;

	return $resultat;
}

/**
*
* Evite les injections SQL en protegeant les apostrophes par des '\'
* Attention : SQL server utilise des doubles apostrophes au lieu de \'
* ATTENTION : LA PROTECTION N'EST EFFECTIVE QUE SI ON ENCADRE TOUS LES ARGUMENTS PAR DES APOSTROPHES
* Y COMPRIS LES ARGUMENTS ENTIERS !!
* @param string $str
*/
function proteger($str)
{
	// attention au cas des select multiples !
	// On pourrait passer le tableau par référence et éviter la création d'un tableau auxiliaire
	if (is_array($str))
	{
		$nextTab = array();
		foreach($str as $cle => $val)
		{
			$nextTab[$cle] = addslashes($val);
		}
		return $nextTab;
	}
	else 	
		return addslashes ($str);
	//return str_replace("'","''",$str); 	//utile pour les serveurs de bdd Crosoft
}



function tprint($tab)
{
	echo "<pre>\n";
	print_r($tab);
	echo "</pre>\n";	
}


function rediriger($url,$qs="")
{
	// if ($qs != "")	 $qs = urlencode($qs);	
	// Il faut respecter l'encodage des caractères dans les chaînes de requêtes
	// NB : Pose des problèmes en cas de valeurs multiples
	// TODO: Passer un tabAsso en paramètres

	if ($qs != "") $qs = "?$qs";
 
	header("Location:$url$qs"); // envoi par la méthode GET
	die(""); // interrompt l'interprétation du code 

	// TODO: on pourrait passer en parametre le message servant au die...
}

// TODO: intégrer les redirections vers la page index dans une fonction :

/*
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php");
	die("");
}
*/





/**
 * Traduit le Markdown de l'admin en composants "Style Carnet"
 * @param string $texte Brut de la BDD
 * @return string Code HTML exécutable
 */
function fichesMarkdownToHtml($texte) {
    
    $parsedown = new Parsedown();
    
    // Active le SafeMode pour bloquer les injections XSS de scripts malveillants (<script>)
    // tout en laissant passer le Markdown et le HTML classique de confiance
    $parsedown->setSafeMode(true); 
    
    // Génère le HTML officiel
    $html = $parsedown->text($texte);

    // Transformation magique pour appliquer tes classes CSS fluides (.pages.css)
    $html = str_replace('<ul>', '<ul class="liste-mots-flex">', $html);
    $html = str_replace('<li>', '<li class="mot-box">', $html);
    
    // Détection des éléments mis en gras pour les passer en Orange actif
    $html = str_replace('<li class="mot-box"><strong>', '<li class="mot-box active">', $html);
    $html = str_replace('</strong></li>', '</li>', $html);

    return $html;
}


/**
 * Valide et déplace un fichier audio uploadé.
 * Ne redirige pas elle-même : retourne un tableau que l'appelant interprète,
 * car l'URL de redirection en cas d'erreur dépend du contexte (fiche, question...).
 *
 * @param string $champ    Nom du champ dans $_FILES (ex: 'fichier_audio')
 * @param string $dossier  Dossier de destination (ex: 'assets/audio/fiches/')
 * @param string $prefixe  Préfixe du nom de fichier généré (ex: un slug ou 'question-12')
 * @return array  ['ok' => true, 'chemin' => '...']  si succès
 *                ['ok' => false, 'erreur' => '...']  si refus
 *                ['ok' => true, 'chemin' => null]    si aucun fichier fourni (cas normal, audio facultatif)
 */
function traiterUploadAudio($champ, $dossier, $prefixe)
{
    // Aucun fichier envoyé : ce n'est pas une erreur, l'audio est facultatif
    if (empty($_FILES[$champ]['name'])) {
        return ['ok' => true, 'chemin' => null];
    }

    $f = $_FILES[$champ];

    // Erreur d'upload signalée par PHP (taille ini dépassée, upload partiel...)
    if ($f['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'erreur' => "Erreur lors de l'upload du fichier audio."];
    }

    // Taille : 5 Mo max (5 * 1024 * 1024 octets)
    if ($f['size'] > 5 * 1024 * 1024) {
        return ['ok' => false, 'erreur' => "Fichier audio trop volumineux (5 Mo max)."];
    }

    // Type réel : on lit les octets du fichier avec finfo, pas l'extension ni le type
    // annoncé par le navigateur, qui sont tous les deux falsifiables par le client
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $f['tmp_name']);
    finfo_close($finfo);

    $extensionsAutorisees = ['audio/mpeg' => 'mp3']; // MP3 uniquement
    if (!isset($extensionsAutorisees[$mime])) {
        return ['ok' => false, 'erreur' => "Format non autorisé (MP3 uniquement)."];
    }

    // On génère nous-mêmes le nom du fichier : on ne fait jamais confiance
    // au nom d'origine (évite les noms piégés et les collisions)
    $extension   = $extensionsAutorisees[$mime];
    $nomFichier  = $prefixe . '-' . time() . '.' . $extension;
    $destination = $dossier . $nomFichier;
    move_uploaded_file($f['tmp_name'], $destination);

    return ['ok' => true, 'chemin' => $destination];
}
?>

