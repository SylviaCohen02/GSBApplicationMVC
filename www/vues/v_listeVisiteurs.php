

<?php

/* Vue liste des visiteurs EXAM ORAL
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Lycée Beth Sefer
 * @author    Sylvia COHEN
 */
?>

<h2 style="color:orange">Voir l'état des modifications des fiches d'un visiteur</h2>
<div class="row">
    <div class="col-md-4">
        <form action="index.php?uc=consulterEtatModif&action=afficherNbTotalModifVisiteur" 
              method="post" role="form">

            <?php//liste déroulante des visiteurs?>
            
            <div class="form-group" style="display: inline-block"> 
                <label for="lstVisiteurs" accesskey="n">Choisir le visiteur : </label>
                <select id="lstVisiteurs" name="lstVisiteurs" class="form-control">
                    <?php
                    foreach ($lesVisiteurs as $unVisiteur) {
                        $id = $unVisiteur['id'];
                        $nom = $unVisiteur['nom'];
                        $prenom = $unVisiteur['prenom'];
                        if ($unVisiteur == $visiteurASelectionner) {
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

            <br><input id="ok" type="submit" value="Valider" class="btn btn-success" 
                   role="button">
        </form>
    </div>
</div>