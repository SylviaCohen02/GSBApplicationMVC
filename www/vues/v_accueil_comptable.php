<?php

/* 
 * 
 * 
 *  
*/
?>

<div id="accueil">
    <h2>
        Gestion des frais<small> - Comptable :
            <?php 
            echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']// la vue va afficher le nom et le prenom du visiteur
            ?></small>
    </h2>
</div>
<div class="rowComptable">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <spanComptable class="glyphicon glyphicon-bookmark"></spanComptable>
                    Navigation
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <a href="index.php?uc=validerFrais&action=selectionnerVisiteurMois"
                           class="btn btn-success btn-lg" role="button"><!--Qd elle va cliquer sur le bouton, uc aurait la valeur gererFrais et action aurait la valeur saisirfrais -->
                            <span class="glyphicon glyphicon-ok"></span>
                            <br>Valider une fiche de frais</a>
                        <a href="index.php?uc=suivrePaiementFrais&action="
                           class="btn btn-primary  btn-lg" role="button">
                             <span class="glyphicon glyphicon-euro"></span>
                            <br>Suivre le paiement d'une fiche de frais</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

