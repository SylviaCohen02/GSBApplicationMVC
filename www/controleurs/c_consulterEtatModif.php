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
}