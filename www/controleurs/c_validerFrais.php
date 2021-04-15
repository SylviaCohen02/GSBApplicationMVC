<?php

/* Gestion de la validation des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Lycée Beth Sefer
 * @author    Sylvia COHEN  <tsyviaco@gmail.com>
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

        $lesMois = getlesDouzeDerniersMois($moisAnnee);
        $lesCles2 = array_keys($lesMois); //on met le tableau $lesMois dans 
        //un tableau $lesCles2 car faut pas 2 variables du meme nom
        $moisASelectionner = $lesCles2[0];
        include'vues/v_listeVisiteursMois.php';
        break;

    case 'afficherFrais':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $Visiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $visiteurASelectionner = $Visiteur;
        //$idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);

        $Mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesMois = getlesDouzeDerniersMois($moisAnnee);
        $moisASelectionner = $Mois;

        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($Visiteur, $Mois); //pq cette methode?
        $lesFraisForfait = $pdo->getLesFraisForfait($Visiteur, $Mois);
        $FraisHorsForfait = $pdo->getLesFraisHorsForfait($Visiteur, $Mois);
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        if (!is_array($lesInfosFicheFrais)) {
            ajouterErreur('Pas de fiche de frais pour ce visiteur ce mois');
            include 'vues/v_erreurs.php';
            include 'vues/v_listeVisiteursMois.php';
        } else {
            include 'vues/v_afficherFrais.php';
        }
        break;

    case 'corrigerFraisForfait':

        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $visiteurASelectionner = $idVisiteur;

        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesMois = getlesDouzeDerniersMois($moisAnnee);
        $moisASelectionner = $leMois;

        //on recupere les nouvelles valeurs saisies par le comptable
        $lesFraisF = filter_input(INPUT_POST, 'lesFraisF', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        //(INPUT_POST, 'lesFraisF',  FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (lesQteFraisValides($lesFraisF)) {
            $pdo->majFraisForfait($idVisiteur, $leMois, $lesFraisF);
            echo "La modification a bien été prise en compte.";
        } else {
            ajouterErreur('Les valeurs des frais doivent être numériques');
            include 'vues/v_erreurs.php';
        }

        $FraisAuForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
         $FraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);

        include 'vues/v_afficherFrais.php';
        break;

    case 'corrigerFraisHorsForfaitReporterFHF':

        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = getlesDouzeDerniersMois($moisAnnee);
        
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $visiteurASelectionner= $idVisiteur;
        $moisASelectionner = $leMois;
        //idFHF input de type hidden dans la vue
        
        $idFHF = filter_input(INPUT_POST, 'idFraisHF', FILTER_SANITIZE_NUMBER_INT); //NUMBER_INT)
        $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
        $montant = filter_input(INPUT_POST, 'montant', FILTER_SANITIZE_NUMBER_INT);
        
        var_dump($idVisiteur, $leMois, $idFHF, $libelle, $date,
                $montant);

        //clic btn corriger
        if (isset($_POST['corriger'])) {
            if (nbErreurs() != 0) {
                ajouterErreur('Les valeurs contiennent une erreur');
                include'vues/v_erreurs';
            } else {
                $pdo->majFraisHorsForfait($idVisiteur, $leMois, $idFHF, $libelle, $date,
                        $montant);
                echo "La modification a bien été prise en compte.";
            }
        }
        $FraisAuForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $FraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);

        //si clic btn reporter
        if (isset($_POST['reporter'])) {
            $pdo->majLibelleFHF($idFHF);

            //cloture la fiche de ce mois
            $laDerniereFiche = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
            if ($laDerniereFiche['idEtat'] == 'CR') {
                $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'CL');

                //cree une nouvelle fiche de frais
                $mois = $leMois + 1;
                $pdo->creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date,
                        $montant);
            }
        }
        include 'vues/v_afficherFrais.php';
        break;

    case 'validerFicheFrais':

        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        //le comptable rentre le nb de justificatifs et ca eregistre ds la BDD
        $nbJustificatifs = filter_input(INPUT_POST, 'nbJ', FILTER_SANITIZE_STRING);
        $pdo->majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs);


        if (!$nbJustificatifs) {
            $pdo->majEtatFicheFrais($idVisiteur, $mois, 'VA');
            echo "Cette fiche a été validée";
        } else {
            ajouterErreur('Les valeurs contiennent une erreur'); //ou
            include'vues/v_erreurs.php';
        }
        
        $sommeFHF = $pdo->calculSommeFraisHorsForfait($idVisiteur, $mois);
        $sommeFraisForfait = $pdo->calculSommeFraisForfait($idVisiteur, $mois);       
        $sommeTotale = $sommeFHF + $sommeFraisForfait;
        $pdo ->montantValide($idVisiteur, $mois, $sommeTotale);
        break;
}


