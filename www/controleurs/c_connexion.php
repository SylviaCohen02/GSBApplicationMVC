<?php
/**
 * Gestion de la connexion
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Lycée Beth Sefer
 * @author    Sylvia Cohen
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);//$action change de valeur comme uc, mais on l'utilise dans les controleurs
if (!$action) {//si action est vide
    $action = 'demandeconnexion';
}

switch ($action) {//cas multiple 
case 'demandeConnexion':
    include 'vues/v_connexion.php';
    break;

case 'valideConnexion':
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);//on fait un filter input sur la variable login pr récupérer sa valeur
    $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);
    $visiteur = $pdo->getInfosVisiteur($login, $mdp);//on va dans la classe pdo gsb, la méthode getInfosVisiteur() ac en param le $login et le $mdp
    $comptable = $pdo-> getInfosComptable ($login, $mdp);
    
    if (!is_array($visiteur) && !is_array($comptable)) {//si y a pas dans le tableau =>on n'a pas trouvé le visiteur qui correspond
        ajouterErreur('Login ou mot de passe incorrect'); // cette phrase s'affiche
        include 'vues/v_erreurs.php';
        include 'vues/v_connexion.php';
    } else {
            if(is_array($visiteur))  {
                $idUtilisateur= $visiteur ['id'];
                $nom = $visiteur['nom'];
                $prenom = $visiteur['prenom'];
                $statut = 'visiteur';//on déclare une new variable statut pr que la bdd sache si c un visiteur ou un variable selon le statut
            }
        elseif(is_array($comptable)){//il faut préciser le cas sinon 
            $idUtilisateur= $comptable ['id'];
            $nom = $comptable ['nom'];
            $prenom = $comptable ['prenom'];
            $statut = 'comptable';
        }
        
        connecter($idUtilisateur, $nom, $prenom, $statut);
        header('Location: index.php');//balise pr ns dire qu'on va retourner vers l'index
    }
    
    break;
default:
    include 'vues/v_connexion.php';
    break;
}
