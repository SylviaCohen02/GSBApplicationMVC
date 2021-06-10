<?php

/* Suivi du paiement des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Lycée Beth Sefer
 * @author    Sylvia COHEN
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
//pr liste deroulante lstMois 
$moisAnnee = getMois(date('d/m/Y'));

switch ($action) {

    case 'selectionnerVisiteurMois':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesCles = array_keys($lesVisiteurs); //on met la variable $lesVisiteurs
        // dans un tableau en local : dans un tableau $lesCles    
        $visiteurASelectionner = $lesCles[0]; //on a l'id des visiteurs
//fct selectionnerVisiteurValide
        
        $lesMois = getlesDouzeDerniersMois($moisAnnee);
        $lesCles2 = array_keys($lesMois); //on met le tableau $lesMois dans 
        //un tableau $lesCles2 car faut pas 2 variables du meme nom
        $moisASelectionner = $lesCles2[0];
        //vue suivrePaiement
         include 'vues/v_suivrePaiementFrais.php';
         
        break;
    case 'afficherFrais':
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $lesVisiteurs = $pdo->getVisiteurEtatValide();//fct visiteurValide
        $visiteurASelectionner = $idVisiteur;
        
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $moisASelectionner = $leMois;
        $lesMois = $pdo-> getMoisEtatValide();
  
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);

        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
         include 'vues/v_miseEnPaiement.php';
       //vue misePaiement
        break;
       
    case 'mettreEnPaiement':
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $lesVisiteurs = $pdo->getVisiteurEtatValide();
        $visiteurASelectionner = $idVisiteur;
         
         
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesMois = $pdo-> getMoisEtatValide();
        $moisASelectionner = $leMois;
        $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'RB');
        echo "Cette fiche a été payée.";  
        break;
}

