<?php
/**
 * Vue Déconnexion
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @link  * @version   GIT: <0>
     http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

deconnecter();
?>
<div class="alert alert-info" role="alert">
    <p>Vous avez bien été déconnecté ! <a href="index.php">Cliquez ici</a> <!--système d'ancre => adresse vers un autre fichier
        pour revenir à la page de connexion.</p>
</div>
<?php
header("Refresh: 3;URL=index.php");//url = redirection vers un autre fichier La on est redirigé vers la page index
