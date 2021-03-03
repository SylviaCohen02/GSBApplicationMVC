<?php
/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost';//propriété
						     //serveur local qui stocke la BDD 
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'userGsb';//contient l'utilisateur
    private static $mdp = 'secret';//contient le mot de passe
    private static $monPdo;
    private static $monPdoGsb = null;//Cette propriété est nulle par défaut

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(// méthode qui crée une instance de la classe Pdo. Chaque méthode est un obj de cette classe, 
				  //le constructeur sera exécuté donc à chaque fois qu'on appelle une méthode.
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,//3 paramètres : celui-ci est un param regroupé ;=> concaténation de la propriété $serveur et de la propriété $bdd
            PdoGsb::$user,//2ème param
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');//requete sql entre parenthèses, elle modifie le champ "CHARACTER" avec la valeur utf8
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()//le destructeur fait un peu d'ordre, il détruit la méthode dès qu'on en a plus besoin
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();// on affecte à la variable $instancePdoGsb le résultat de la méthode getPdoGsb().
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {//si la  classe PdoGsb (=la classe même) est nulle, on l'instancie

            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)//qd l'utilisateur rentre le login et le mot de passe, il va rechercher ds la BDD l'id, le nom et le prenom
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);//on dit que la variable unLogin avec la variable $login
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();//exécuter la req
        return $requetePrepare->fetch();//fetch()=>rentrer le résultat de la req
    }

    /**
     * Retourne les informations d'un comptable
     *
     * @param String $login Login du comptable
     * @param String $mdp   Mot de passe du comptable
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
     public function getInfosComptable($login, $mdp){
         $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT comptable.id AS id, comptable.nom AS nom, '
            . 'comptable.prenom AS prenom '
            . 'FROM comptable '
            . 'WHERE comptable.login = :unLogin AND comptable.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);//on dit que la variable unLogin avec la variable $login
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();//exécuter la req
        return $requetePrepare->fetch();
     }
    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $leMois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(//verifie qu'on est bien connecté à la BDD
            'SELECT * FROM lignefraishorsforfait '//Cette requete sql retourne ttes les lignes 
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '//à condition que idVisiteur correspond à :unIdVisiteur 
            . 'AND lignefraishorsforfait.mois = :unMois'//idem que ligne précédente
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $leMois, PDO::PARAM_STR);
        $requetePrepare->execute();//exécute la req
        $lesLignes = $requetePrepare->fetchAll();//tt le résultat de la requête sql sera mis dans la variable lesLignes (=tableau)
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];//c comme une regle de 3, on utilise la variable temporelle $date pr convertir de l'anglais vers le français
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);//convertir la date d'anglais vers francais
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();//fetch()=> retourne le résultat de la requête
        return $laLigne['nb'];//retourne la ligne à l'indice nb
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }
 
    
    
    

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(//Cette requête classe les idFrais par id
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)//il va boucler sur le tableau les cles qui contient les cles. Chacune des lignes=> idFrais
    {
        $lesCles = array_keys($lesFrais);//array ca veut dire tableau
        foreach ($lesCles as $unIdFrais) {//boucle foreach=>parcourir le tableau : pr chaque ligne du tableau lesCles
            $qte = $lesFrais[$unIdFrais];//pr chaque un idFrais, (=une ligne du tableau "lesFrais) je mets le resultat de la ligne de "idFrais" ds la variable $qte
            $requetePrepare = PdoGSB::$monPdo->prepare(//mettre à jour la table lignfraisforfait en mettant la valeur de la variable $qte (pr chaque idFrais)
                'UPDATE lignefraisforfait '
                . 'SET lignefraisforfait.quantite = :uneQte '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }//En gros, après exécution de la requete,  le champ quantité (de la table lignefraisforfait) de la BDD sera rempli de la valeur de la variable $qte (pr chaque idFrais).
	 //Au final ttes les valeurs du tableau lesFrais seront mises dans le champ quantite de la table lignefraisforfait.
    }

    public function majFraisHorsForfait($idVisiteur, $mois, $idFHF, $libelle, 
            $date, $montant)//il va boucler sur le tableau les cles qui contient les cles. Chacune des lignes=> idFrais
    {
       //array ca veut dire tableau
       //boucle foreach=>parcourir le tableau : pr chaque ligne du tableau lesCles
            
            $requetePrepare = PdoGSB::$monPdo->prepare(//mettre à jour la table lignfraisforfait en mettant la valeur de la variable $qte (pr chaque idFrais)
                'UPDATE lignefraishorsforfait '
                . 'SET lignefraishorsforfait.libelle = :unLibelle '
                 .'lignefraishorsforfait.date = :uneDate '
                 .'lignefraishorsforfait.montant = :unMontant '
                . 'WHERE lignefraishorsforfait.idVisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois '
               
                 . 'AND lignefraishorsforfait.id ='
                    . '  :idFraisHorsForfait'
            );
            $requetePrepare->bindParam(':unIdVisiteur ', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unIdFrais', $idFHF, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
            $requetePrepare->bindParam(':uneDate ', $date, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);//INT? car n'existe pas float           
            $requetePrepare->execute();
        }
        
        public function majLibelleFHF($idVisiteur, $leMois, $idFHF)//il va boucler sur le tableau les cles qui contient les cles. Chacune des lignes=> idFrais
    {
    
            $requetePrepare = PdoGSB::$monPdo->prepare(//mettre à jour la table lignfraisforfait en mettant la valeur de la variable $qte (pr chaque idFrais)
            'UPDATE lignefraishorsforfait '
           . 'SET lignefraishorsforfait.libelle =  CONCAT("REFUSE: ", libelle)'
           ."WHERE lignefraishorsforfait.idvisiteur = '$idVisiteur'"
           . "AND lignefraishorsforfait.mois = '$leMois'"
           ."AND lignefraishorsforfait.id= '$idFHF'"
                 
            );
            //$requetePrepare->bindParam(':unIdVisiteur ', $idVisiteur, PDO::PARAM_STR);
            //$requetePrepare->bindParam(':unMois', $leMois, PDO::PARAM_STR);
            //$requetePrepare->bindParam(':unIdFraisHF', $idFHF, PDO::PARAM_INT);          
            $requetePrepare->execute();
        }
        
        
 public function majFrais($idVisiteur,$mois,$libelle,$date,$montant,$lesFraisHF)
{
  $dateFr = dateFrancaisVersAnglais($date);
   $requetePrepare = PdoGSB::$monPdo->prepare(      
            'UPDATE lignefraishorsforfait '
           . 'SET lignefraishorsforfait.date = :uneDateFr, '
           . 'lignefraishorsforfait.montant = :unMontant, '  
           . 'lignefraishorsforfait.libelle = :unLibelle '
           . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
           . 'AND lignefraishorsforfait.mois = :unMois '
           . 'AND lignefraishorsforfait.id = :unIdFrais'      
   );
   $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
   $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
   $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
   $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
   $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
   $requetePrepare->bindParam(':unIdFrais', $lesFraisHF, PDO::PARAM_INT);
   $requetePrepare->execute();
 
}
        
        
        ////En gros, après exécution de la requete,  le champ quantité (de la table lignefraisforfait) de la BDD sera rempli de la valeur de la variable $qte (pr chaque idFrais).
	 //Au final ttes les valeurs du tableau lesFrais seront mises dans le champ quantite de la table lignefraisforfait.
    
    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '//:unIdVisiteur est le correspondant sql de idvisiteur (=car idvisiteur ne passe pas en sql,
            						    // et cela "correspond" aussi au champ idvisiteur de la BDD)
	    . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)// elle verifie si y a ce mois actuel dans le BDD pr un visiteur donné
    {
        $boolReturn = false;//par défaut c false => pas de fiche de frais
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '// dans mois y a "2020 10"
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;//true => y a une fiche de frais
        }
        return $boolReturn;//retourne la valeur de la variable
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();//elle retourne le résultat dans la ligne
        $dernierMois = $laLigne['dernierMois'];//on met le résultat de la requête (qui est ds $la ligne) dans la variable $dernier mois
					       //'dernier mois' entre crochets correspond à l'alias du champ "mois" de la req sql (= cf SELECT)
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champ
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);//la variable $dernierMois est égale au résultat de la méthode dernierMoisSaisi()
        // (ds les autres fichiers c pdo, et la this car on est ds la classe meme
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);//elle récupère le résultat de la méthode getLesInfosFicheFrais()
        if ($laDerniereFiche['idEtat'] == 'CR') { //si la dernière fiche de frais est à l'état CR = en cours
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');//alors il faudra appeler la méthode EtatFicheFrais() la mettre à jr => à l'état cloturé et ainsi le comptable pourrait continuer
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(//il insère une new fiche de frais
            'INSERT INTO fichefrais (idvisiteur,mois,nbJustificatifs,'
            . 'montantValide,dateModif,idEtat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();

        $lesIdFrais = $this->getLesIdFrais();//Cette req régénère l'association ligneFraisForfait : elle met une new page pr le prochain mois
        				     //(lien ac slam 3: association car 2 clés primaires)
	foreach ($lesIdFrais as $unIdFrais) {//boucle foreach => pr chaque ligne
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'// on la remplit avec l'idvisiteur, le mois et la fiche de frais
                . 'idFraisForfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }
     
    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    ) {
        $dateFr = dateFrancaisVersAnglais($date);//convertit la date du fr à l'anglais, et le résultat rentre dans $dateFr
        $requetePrepare = PdoGSB::$monPdo->prepare(//interroger la BDD PdoGSB
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant) '//ca va rentrer ces infos dans la table lignefraishorsforfait
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);//c pr faire la correspondance entre la "variable" en PHP et celle en sql
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(//elle va  récupérer le mois pr un visiteur donné
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'//trie les mois ds l'ordre décroissant (du plus recent au moins recent)
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();//ca exécute la req

        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {//exécuter
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);//substr() c pr extraire l'année (en anglais c aaaa mm)
            $numMois = substr($mois, 4, 2);//on extrait le mois
            $lesMois['$mois'] = array(//tableau ac 3 colonnes
                'mois' => $mois,//tte la date (aaaa mm)
                'numAnnee' => $numAnnee,//que l'année
                'numMois' => $numMois//que le mois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT ficheFrais.idEtat as idEtat, '
            . 'ficheFrais.dateModif as dateModif,'
            . 'ficheFrais.nbJustificatifs as nbJustificatifs, '
            . 'ficheFrais.montantValide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN Etat ON ficheFrais.idEtat = Etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idEtat = :unEtat, dateModif = now() '//fonction php qui va chercher la date actuelle sur l'ordi
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
   /*
    * récupère l'id, le nom, le prenom de tous les visiteurs et les met dans 
    * une liste déroulante
    */  
  public function getLesVisiteurs()
   {
       $requetePrepare = PdoGsb::$monPdo->prepare(
           'SELECT *'
           . 'FROM visiteur '
           . 'ORDER BY nom'
       );
       $requetePrepare->execute();
       return $requetePrepare->fetchAll();
   }
  
  public function majEtatVAFicheFrais($idVisiteur, $mois, $etat){
       $requetePrepare = PdoGSB::$monPdo->prepare(
        'UPDATE ficheFrais '
      . 'SET etat.id = VA '//fonction php qui va chercher la date actuelle sur l'ordi
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'AND fichefrais.mois = :unMois'
      . 'AND fichefrais.etat = :unEtat'
      . 'AND etat.id= CL'
              );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne2 = $requetePrepare->fetch();
        return $laLigne2;
      }
      
      public function getNomPrenomVisiteur($idVisiteur){
          $requetePrepare = PdoGSB::$monPdo->prepare(
          'SELECT  visiteur.id AS id,'
          . 'visiteur.nom as nom,'
          . 'visiteur.prenom as prenom'
          . 'FROM visiteur'  
          .'WHERE visiteur.idvisiteur = $idVisiteur '
                  
          );
          $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
          $requetePrepare->execute();
          $laLigne3 = $requetePrepare->fetch();
        return $laLigne3;
      }
    


}
