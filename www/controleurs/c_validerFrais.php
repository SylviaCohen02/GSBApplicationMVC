<?php
/**
 * Gestion de la déconnexion
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    beth sefer,Sylvia Cohen
 */
$idComptable = $_SESSION['idUtilisateur']; // on met le contenu de la colonne idcomptable ds la variable $idcomptable
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING); //filtre sur la variable action
$moisAnnee = getMois(date('d/m/Y'));

switch ($action) {


    case 'selectionnerVisiteurMois':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesCles1 = array_keys($lesVisiteurs);
        $visiteurASelectionner = $lesCles1[0];
        $lesMois = getlesDouzeDerniersMois($moisAnnee);
        $lesCles = array_keys($lesMois);
        $moisASelectionner = $lesCles[0];

        include 'vues/v_listeVisiteursMois.php';
        break;
// Afin de sélectionner par défaut le dernier mois dans la zone de liste (le plus recent)
    // on demande toutes les clés, et on prend la première,
    // les mois étant triés décroissants   



    case 'afficherFrais':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $visiteurASelectionner = $idVisiteur;
        $mois = filter_input(INPUT_POST, 'lstMois', FILTER_DEFAULT, FILTER_SANITIZE_STRING);
        $lesMois = getlesDouzeDerniersMois($moisAnnee);
        $moisASelectionner = $mois;
        $infosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        $lesFraisForfait = $pdo->getlesFraisForfait($idVisiteur, $mois);
        $lesFraisHorsForfait = $pdo->getlesFraisHorsForfait($idVisiteur, $mois);
        $nbJustificatifs = $pdo->getnbJustificatifs($idVisiteur, $mois);

        if (!is_array($infosFicheFrais)) {
            ajouterErreur('Pas de fiche de frais pour ce visiteur et ce mois');
            include 'vues/v_erreurs.php';
        } else {
            include 'vues/v_afficherFrais.php';
        }
        break;


    case 'corrigerFraisForfait':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = getlesDouzeDerniersMois($moisAnnee);
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $visiteurASelectionner = $idVisiteur;
        $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $moisASelectionner = $mois;
        $lesFrais = filter_input(INPUT_POST, 'lesFraisF', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (lesQteFraisValides($lesFrais)) {
            $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
            echo "La modification a bien été prise en compte.";
            $nbModif = $pdo->getNbModif($idVisiteur, $mois);

            $nbModif = $nbModif++;
            var_dump($nbModif);
            $pdo->majNbModif($idVisiteur, $mois, $nbModif);
        } else {
            ajouterErreur('Les valeurs des frais doivent être numériques');
            include 'vues/v_erreurs.php';
        }
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        include 'vues/v_afficherFrais.php';

        break;

    case 'corrigerFraisHorsForfaitReporterFHF':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = getLesDouzeDerniersMois($moisAnnee);
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $visiteurASelectionner = $idVisiteur;
        $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $moisASelectionner = $mois;
        $dateHF = filter_input(INPUT_POST, 'dateHF', FILTER_SANITIZE_STRING);
        $libelle = filter_input(INPUT_POST, 'libelleHF', FILTER_SANITIZE_STRING);
        $montantHF = filter_input(INPUT_POST, 'montantHF', FILTER_SANITIZE_STRING);
        $lesFraisHF = filter_input(INPUT_POST, 'lesFraisHF', FILTER_SANITIZE_STRING);
        if (isset($_POST['corriger'])) {
            if (nbErreurs() != 0) {
                ajouterErreur('Les valeurs contiennent une erreur');
                include 'vues/v_erreurs.php';
            } else {
                $pdo->majFraisHorsForfait($idVisiteur, $mois, $libelle, $dateHF, $montantHF, $lesFraisHF);
                echo "La modification a bien été prise en compte.";
                $nbModif = $pdo->getNbModif($idVisiteur, $mois);
                var_dump($nbModifNv);
                $nbModifNv = $nbModif++;

                $pdo->majNbModif($idVisiteur, $mois, $nbModifNv);
            }
            $lesFraisHorsForfait = $pdo->getlesFraisHorsForfait($idVisiteur, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            include 'vues/v_afficherFrais.php';
        }if (isset($_POST['reporter'])) {

            //$idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
            //$mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);

            $lesFraisForfait = $pdo->getlesFraisForfait($idVisiteur, $mois);
            $lesFraisHorsForfait = $pdo->getlesFraisHorsForfait($idVisiteur, $mois);
            $idFraisHF = filter_input(INPUT_POST, 'lesFraisHF', FILTER_SANITIZE_NUMBER_INT);
            $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
            $date = filter_input(INPUT_POST, 'dateHF', FILTER_SANITIZE_STRING);
            $montant = filter_input(INPUT_POST, 'montantHF', FILTER_SANITIZE_STRING);
            $libelle = filter_input(INPUT_POST, 'libelleHF', FILTER_SANITIZE_STRING);

            $pdo->majLibelle($idVisiteur, $mois, $idFraisHF);

            //cloture la fiche de ce mois
            $laDerniereFiche = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);

            //cree une nouvelle fiche de frais
            $mois = getMoisSuivant($mois);
            //nous dit si ce mois est ds la table fiche frais si ya une fiche frais pr ce mois
            if ($pdo->estPremierFraisMois($idVisiteur, $mois) == false) {
                $pdo->creeNouvellesLignesFrais($idVisiteur, $mois);
            }

            $pdo->creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant);
            include 'vues/v_afficherFrais.php';

            break;
        }

    //valide maj etat date et enregistre nbrde justificatif
    case'validerFicheFrais':

        $lesVisiteurs = $pdo->getLesVisiteurs();
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $visiteurASelectionner = $idVisiteur;

        $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $moisASelectionner = $mois;
        $lesMois = getlesDouzeDerniersMois($moisAnnee);
        $nbJustificatifs = filter_input(INPUT_POST, 'nbJustificatifs', FILTER_SANITIZE_STRING);

        $pdo->majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs);
        $pdo->majEtatFicheFrais($idVisiteur, $mois, 'VA');

        $sommeF = $pdo->calculF($idVisiteur, $mois);
        $sommeHF = $pdo->calculHF($idVisiteur, $mois);
        $sommeTotale = $sommeF[0] + $sommeHF;

        $pdo->MontantValide($idVisiteur, $mois, $sommeTotale, $nbJustificatifs);
        ?>
        <div class="alert alert-info" role="alert">
            <p>La fiche a bien été validée ! <a href="index.php">Cliquez ici</a>
                pour revenir à la page d'accueil.</p>
        </div>
        <?php
        break;
}
     