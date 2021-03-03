<?php
/**
 * Gestion de l'accueil
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    beth sefer,Sylvia Cohen
 */


if (estComptableConnecte()) {
    include 'vues/v_accueil_comptable.php';// on est redirigé vers la vue accueil
} else if(estVisiteurConnecte()){// si elle est vide
        include 'vues/v_accueil.php';// on va vers vue connexion
        }else{ 
                include 'vues/v_connexion.php'; 
}

