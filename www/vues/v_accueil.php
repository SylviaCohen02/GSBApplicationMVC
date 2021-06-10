<?php
/**
 * Vue Accueil
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    beth sefer,Sylvia Cohen
 */
?>
<div id="accueil">
    <h2>
        
       
        <small> <h2 style="color:black">Gestion des frais  </h2>Visiteur : 
            <?php 
            echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']// la vue va afficher le nom et le prenom du visiteur
            ?></small>
    </h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Navigation
                </h3>
            </div>
            <div class="panel-body">
                 
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <a href="index.php?uc=gererFrais&action=saisirFrais"
                           class="btn btn-success btn-lg" role="button"><!--Qd elle va cliquer sur le bouton, uc aurait la valeur gererFrais et action aurait la valeur saisirfrais -->
                            <span class="glyphicon glyphicon-pencil"></span>
                            <br>Renseigner la fiche de frais</a>
                        <a href="index.php?uc=etatFrais&action=selectionnerMois"
                           class="btn btn-primary btn-lg" role="button">
                            <span class="glyphicon glyphicon-list-alt"></span>
                            <br>Afficher mes fiches de frais</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>