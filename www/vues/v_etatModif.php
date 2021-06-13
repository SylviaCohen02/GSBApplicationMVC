<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<hr>

<h2 style="color:orange">Voir l'etat du nombre de modifications des fiches</h2>
<div class="panel panel-primary">
    <div class="panel-heading"><h4> Etat statistique du nombre de modifications </h4>
         </div>
    <div class="panel-body">
         
        <p> Les comptables ont effectué <?php echo $nbTotal ?> corrections sur l'ensemble des fiches de frais </p>
         <br>
         
    </div>
    
</div>

<form action="index.php?uc=consulterEtatModif&action=selectionnerVisiteur" role="form" method="post">
    <p>Voulez vous connaitre le nombre de corrections effectuées sur les fiches de frais d'un visiteur donné?</p>
    
    <button class="btn btn-success" type="submit">Oui</button>
                <button class="btn btn-danger" type="reset">Non</button>
            
</form>
