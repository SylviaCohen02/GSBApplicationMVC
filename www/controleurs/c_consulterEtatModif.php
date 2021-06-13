<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$idComptable = $_SESSION['idUtilisateur']; // on met le contenu de la colonne idcomptable ds la variable $idcomptable
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING); //filtre sur la variable action
$moisAnnee = getMois(date('d/m/Y'));

switch ($action) {


    case 'afficherNbModif':
        $nbTotal = $pdo->getLeNbTotalModif();
        include 'vues/v_etatModif.php';
        break;

    //liste deroulante visiteur
    case 'selectionnerVisiteur':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesCles1 = array_keys($lesVisiteurs);
        $visiteurASelectionner = $lesCles1[0];

        include 'vues/v_listeVisiteurs.php';
        break;

    case 'afficherNbTotalModifVisiteur':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $visiteurASelectionner = $idVisiteur;
        //filter_input sur visiteur selectionne
        include 'vues/v_listeVisiteurs.php';
        
        //appeler fct qui calcule sommme ds une variable
        $nbTotalVisiteur = $pdo->getLeNbTotalModifVisiteur($idVisiteur);
        
        $prenom = $pdo->getPrenomVisiteur($idVisiteur);
        $nom = $pdo->getNomVisiteur($idVisiteur);
    
        //Message de succes?
        include 'vues/v_etatModifVisiteur.php';
        break;
}