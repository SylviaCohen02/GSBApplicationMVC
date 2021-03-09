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

$idComptable = $_SESSION['idUtilisateur'];
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

        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);

        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);

        //idFHF input de type hidden dans la vue
        $idFHF = filter_input(INPUT_POST, 'idFraisHF', FILTER_SANITIZE_NUMBER_INT); //NUMBER_INT)

        $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
        $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);

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

        //si clic btn reporter
        if (isset($_POST['reporter'])) {
            $pdo->majLibelleFHF($idVisiteur, $leMois, $idFHF);


            $mois = $leMois + 1;
            //cree une new fiche de frais?

            var_dump($idVisiteur, $leMois, $idFHF);

            $pdo->creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, //cree une new ligne de frais
                    $montant);

            $etat = 'CL';
            $pdo->majEtatFicheFrais($idVisiteur, $mois, $etat);
        }
        $lesFraisF = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $FraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);

        include 'vues/v_afficherFrais.php';
        break;
}
  
    
    





