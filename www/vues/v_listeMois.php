<?php
/**
 * Vue Liste des mois
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Beth Sefer, Sylvia Cohen
 */
?>
<h2>Mes fiches de frais</h2>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un mois : </h3>
    </div>
    <div class="col-md-4">
        <form action="index.php?uc=etatFrais&action=voirEtatFrais" 
              method="post" role="form">
            <div class="form-group">
                <label for="lstMois" accesskey="n">Mois : </label>
                <select id="lstMois" name="lstMois" class="form-control">
                    <?php
                    foreach ($lesMois as $unMois) {//on parcourt ligne par ligne 
                    //le tableau $lesMois, qu'on renomme en $unMois => plus de lisibilité au code
                        $mois = $unMois['mois'];//['mois']=le mois est mis dans 
                        //la colonne mois du tableau $unMOis
                        $numAnnee = $unMois['numAnnee'];
                        $numMois = $unMois['numMois'];
                        if ($mois == $moisASelectionner) {
                            ?>
                            <option selected value="<?php echo $mois //selected 
                            //value =>  celui qui etait par defaut?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois //si on a choisi une 
                            //valeur autre que celle par defaut?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?>    

                </select>
            </div>
            <input id="ok" type="submit" value="Valider" class="btn btn-success" 
                   role="button">
            <input id="annuler" type="reset" value="Effacer" class="btn btn-danger" 
                   role="button">
        </form>
    </div>
</div>