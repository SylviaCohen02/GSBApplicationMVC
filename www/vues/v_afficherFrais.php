<?php
/**
 * Vue affichage des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    beth sefer, Sylvia Cohen
 */
?>

<div class="row">  
    <div class="col-md-4">
        <form action="index.php?uc=validerFrais&action=corrigerFraisForfait" 
              method="post" role="form">


            <?php //liste déroulante des visiteurs?>

            <div class="form-group" style="display: inline-block"> 
                <label for="lstVisiteurs" accesskey="n">Choisir le visiteur : </label>
                <select id="lstVisiteurs" name="lstVisiteurs" class="form-control">
                    <?php
                    foreach ($lesVisiteurs as $unVisiteur) {
                        $id = $unVisiteur['id'];
                        $nom = $unVisiteur['nom'];
                        $prenom = $unVisiteur['prenom'];
                        if ($id == $visiteurASelectionner) {
                            ?>
                            <option selected value="<?php echo $id ?>">
                                <?php echo $nom . ' ' . $prenom ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $id ?>">
                                <?php echo $nom . ' ' . $prenom ?> </option>
                            <?php
                        }
                    }
                    ?>    

                </select>
            </div>

            <?php //liste déroulante des mois?>

            &nbsp;<div class="form-group" style="display: inline-block">
                <label for="lstMois" accesskey="n">Mois : </label>
                <select id="lstMois" name="lstMois" class="form-control">
                    <?php
                    foreach ($lesMois as $unMois) {
                        $mois = $unMois['mois'];
                        $numAnnee = $unMois['numAnnee'];
                        $numMois = $unMois['numMois'];
                        if ($mois == $moisASelectionner) {
                            ?>
                            <option selected value="<?php echo $mois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?>    

                </select>
            </div>  

    </div><br><br><br><br> 

    <div class="row">    
        <h2 style="color:orange">&nbsp;Valider la fiche de frais</h2>
        <h3>&nbsp;&nbsp;Eléments forfaitisés</h3>
        <div class="col-md-4">  

            <fieldset>
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite'];
                    ?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <input type="text" id="idFrais" 
                               name="lesFraisF[<?php echo $idFrais ?>]"
                               size="10" maxlength="5" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">
                    </div>
                    <?php
                }
                ?> 
                <button class="btn btn-success" type="submit">Corriger</button>       
                <button class="btn btn-danger" type="reset">Réinitialiser</button>
            </fieldset>

        </div>
    </div>
</form>

<hr>


&nbsp; 
<br><br>
<form action="index.php?uc=validerFrais&action=corrigerFraisHorsForfaitReporterFHF" 
      method="post" role="form">


    <div class="panel panel-info">
        <div class="panel-heading">Descriptif des éléments hors forfait</div>


        <input id="lstVisiteurs" name = "lstVisiteurs" type="hidden" class="form-control" value="<?php echo $visiteurASelectionner ?>">
        <input id="lstMois" name="lstMois" type="hidden" class="form-control" value="<?php echo $moisASelectionner ?>">
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>  
                    <th class="montant">Montant</th>  
                    <th class="action">&nbsp;</th> 
                </tr>
            </thead>  
            <tbody>

                <?php
                foreach ($FraisHorsForfait as $unFraisHorsForfait) {
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                    $date = $unFraisHorsForfait['date'];
                    $montant = $unFraisHorsForfait['montant'];
                    $idFHF = $unFraisHorsForfait['id'];
                    ?>    
                    <tr id="fraisHorsForfait" name="idFraisHorsForfait">
                <input name= idFraisHF type="hidden" value="<?php echo $idFHF ?>">
                <td> <input name="date" value="<?php echo $date ?>"></td>
                <td> <input name="libelle" value="<?php echo $libelle ?>"></td>
                <td><input name="montant" value="<?php echo $montant ?>"></td>


                <td>
                    <button class="btn btn-success" type="submit" 
                            name="corriger" value="corriger" >
                        Corriger</button>
                    <button class="btn btn-danger" type="reset">
                        Réinitialiser</button></td> 
                <td>
                    <button class="btn btn-success" type="submit"
                            name="reporter" value="reporter" 
                            onclick="return confirm('Voulez-vous\n\
                                       vraiment reporter ce frais?');">Reporter</button>    
                </td>  
                <?php
            }
            ?>
            </tr>

            </tbody>  
        </table>

    </div>


</form>  



<br><br><br><br><br>

<form action="index.php?uc=validerFrais&action=validerFicheFrais" 
      method="post" role="form">


    <div class="col-md-4">  
        
        <input id="lstVisiteurs" name = "lstVisiteurs" type="hidden" class="form-control" value="<?php echo $visiteurASelectionner ?>">
        <input id="lstMois" name="lstMois" type="hidden" class="form-control" value="<?php echo $moisASelectionner ?>">


        <label for="nbJ">Nombre de justificatifs:</label>
        <input name="nbJ" id="nbJ" type="text" size="3">
        <br><br>
        <button class="btn btn-success" type="submit">Valider</button>
        <button class="btn btn-danger" type="reset">Réinitialiser</button>

    </div>


</form>
















