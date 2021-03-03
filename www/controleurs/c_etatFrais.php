<?php
/**
 * Gestion de l'affichage des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author   Beth Sefer, Sylvia Cohen
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$idVisiteur = $_SESSION['idUtilisateur'];
switch ($action) {
case 'selectionnerMois':
    $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
    // Afin de sélectionner par défaut le dernier mois dans la zone de liste
    // on demande toutes les clés, et on prend la première,
    // les mois étant triés décroissants
    $lesCles = array_keys($lesMois);//je la mets en local pr pouvoir la traiter 
    //et l'utiliser cô on veut car c une fonction publique et on peut l'appeler 
    //a priori qd on veut, mais pas possible de modifier resultat d'une req sql
    $moisASelectionner = $lesCles[0];
    include 'vues/v_listeMois.php';
    break;
case 'voirEtatFrais':
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);//on 
    //recupere ce qui a ete selectionné ds la liste deroulante de nummois
    //(qui se trouve dans v_listemois).
    $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
    $moisASelectionner = $leMois;
    include 'vues/v_listeMois.php';//ca affiche la liste deroulante.
    
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
    include 'vues/v_etatFrais.php';
}