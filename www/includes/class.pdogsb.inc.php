<?php

/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    beth sefer, Sylvia Cohen- tsyviaco@gmail.com
 */
class PdoGsb {

    private static $serveur = 'mysql:host=localhost'; //propriété
    //serveur local qui stocke la BDD 
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'userGsb'; //contient l'utilisateur
    private static $mdp = 'secret'; //contient le mot de passe
    private static $monPdo;
    private static $monPdoGsb = null; //Cette propriété est nulle par défaut

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct() {
        PdoGsb::$monPdo = new PDO(
                // méthode qui crée une instance de la classe Pdo. Chaque méthode est un objet de cette classe,
                //le constructeur sera exécuté donc à chaque fois qu'on déclare une méthode.
                // monPdo est dans la variable PdoGsb
                PdoGsb::$serveur . ';' . PdoGsb::$bdd,
                // ';'= concatenation des 2
                PdoGsb::$user,
                PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct() {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb() {
// fonction qui ne changera pas, si mon pdogsb est egale a null alors je l'inastancie et je cree un objet  newpdogsb
        if (PdoGsb::$monPdoGsb == null) {
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
    public function getInfosVisiteur($login, $mdp) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT visiteur.id AS id, visiteur.nom AS nom, '
                . 'visiteur.prenom AS prenom '
                . 'FROM visiteur '
                . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
// elle nous renvoi nom et prenom qui correspond au mdp et login
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
// facon de preparer la requete et que les parametres c'est ce qu'on a ecrit dans where
//fetch= ça lance et retourne sous forme de tableaux
    }

    /**
     * Retourne les informations d'un comptable
     *
     * @param String $login Login du comptable
     * @param String $mdp   Mot de passe du comptable
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosComptable($login, $mdp) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT comptable.id AS id, comptable.nom AS nom, '
                . 'comptable.prenom AS prenom '
                . 'FROM comptable '
                . 'WHERE comptable.login = :unLogin AND comptable.mdp = :unMdp'
        );
// elle nous renvoi nom et prenom qui correspond au mdp et login
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
// facon de preparer la requete et que les parametres c'est ce qu'on a ecrit dans where
//fetch= ça lance et retourne sous forme de tableaux
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
    public function getLesFraisHorsForfait($idVisiteur, $mois) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
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
    public function getNbjustificatifs($idVisiteur, $mois) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
// on selectionne le nombre de jutificatif  des fiches frais selon idvisiteur et le mois 
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
// retourne dans la variables "la lignes"
//fetch = lancer la requete sql
// cette methode est appele qqpart et donc dans cette varaiable elle va retourner  la ligne a l'indice nb. 
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
    public function getLesFraisForfait($idVisiteur, $mois) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
//a requete prapre on affacte la requet de pdogsb??
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

// fetch lancer un tableau

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais() {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT fraisforfait.id as idfrais '
                . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
// elle selectionne les id de fraisforfait dans l'ordre 
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
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {

        $lesCles = array_keys($lesFrais);

        foreach ($lesCles as $unIdFrais) {
//chaque ligne : les cle comme un idfrais
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                    'UPDATE lignefraisforfait '
                    . 'SET lignefraisforfait.quantite = :uneQte '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.idfraisforfait = :idFrais'
//elle va boucler sur les tablo les cles . chque ligne on va lapelr un idfrais. On va prendre cetet ligne et la rentrée ds la variable quantité. On crée un tablo les cle , avc esfrais. on parle d'un idfrais. Le resultat de chque ligne , on prend le resultat on met la quantité .Les frais c un ablo les cles st a lintereieur .. pr chque ligne la quantité et aprés on fait la requette SQL .Je la met ds le champs de la Qte de la BDD on met la valeur de quantité. Final ds la BDD il yaura le tablo lesfrais.  
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();

        }
        
    }

    public function majFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant, $lesFraisHF) {
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

    /**
     * Modifie le libellé des frais hors forfait, ajoute la mention "refusé" devant le libellé
     * @param char $idVisiteur Id du visiteur
     * @param int $leMois      Mois sous la forme aaaamm
     * @param int $date        Date au format français jj/mm/aaaa
     * @param int $idFHF       Id du FHF
     */
    public function majLibelle($idVisiteur, $mois, $idFraisHF) {

        $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraishorsforfait '
                . 'SET lignefraishorsforfait.libelle = CONCAT("REFUSE ",libelle)'
                . 'WHERE lignefraishorsforfait.idVisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois= :unMois '
                . 'AND  lignefraishorsforfait.id= :unidFraisHF '

//elle va boucler sur les tablo les cles . chque ligne on va lapelr un idfrais. On va prendre cetet ligne et la rentrée ds la variable quantité. On crée un tablo les cle , avc esfrais. on parle d'un idfrais. Le resultat de chque ligne , on prend le resultat on met la quantité .Les frais c un ablo les cles st a lintereieur .. pr chque ligne la quantité et aprés on fait la requette SQL .Je la met ds le champs de la Qte de la BDD on met la valeur de quantité. Final ds la BDD il yaura le tablo lesfrais.  
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unidFraisHF', $idFraisHF, PDO::PARAM_INT);
        /**/
        if ($requetePrepare->execute()) {
            echo 'succès';
        } else {
            echo 'erreur';
        }
    }

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
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE fichefrais'
                . 'SET nbjustificatifs= :unNbJustificatifs '
                . 'WHERE fichefrais.idvisiteur= :unIdVisiteur '
                . 'AND fichefrais.mois= :unMois'
        );
// cette mise a jour va changer le nombre de justificatif qui est ds la bdd par le nom de justifcatif qui a ete donné pas l'utilisateur
        $requetePrepare->bindParam(':unNbJustificatifs', $nbJustificatifs, PDO::PARAM_INT);
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
    public function estPremierFraisMois($idVisiteur, $mois) {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT fichefrais.mois FROM fichefrais '
                . 'WHERE fichefrais.mois= :unMois '
                . 'AND fichefrais.idvisiteur= :unIdVisiteur'
        );
//est ce que pour ce moi la on a une fiche frais 
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT MAX(mois) as dernierMois '
                . 'FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur= :unIdVisiteur'
        );
// elle va chercher le dernier mois pour l'idvisiteur qui a ete donne et elle donne le resultat dans la ligne qu'on met dans la variable derniermois
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois']; // ['dernierMois']= ca fai reference au derniermois du select
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
// this = variable qui s'appele this 
//derniermois= resultat de la requete dernierMoisSaisi selon l'idvisiteur
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
// si dans la derniereFiche a comme idee etat CR alors c'est en cour alors on met a jour en mettant cl=cloturé 
//maj= mise a jour
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO fichefrais (idvisiteur,mois,nbJustificatifs,'
                . 'montantValide,dateModif,idEtat) '
                . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
// cette requete cree une fiche de frais qui est en cours
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {

            $requetePrepare = PdoGsb::$monPdo->prepare(
                    'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                    . 'idFraisForfait,quantite) '
                    . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
// elle met a jour et cree une nouvelle ligne pour le prochain mois
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
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'INSERT INTO lignefraishorsforfait '
                . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
                . ':unMontant) '
        );

//convertit la date francais vers anglais le resultat rentre dans datefr
//on interroge la bdd pdogsb, on rentre des new valeurs dans lignefraishorsforfait
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
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
    public function supprimerFraisHorsForfait($idFrais) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'DELETE FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

//on a supprime la ligne pour l'id qu'on a donné

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
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
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
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE ficheFrais '
                . 'SET idEtat = :unEtat, dateModif = now() '
// now fonction php qui va chercher la date actuelle
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne tous les visiteurs (les id, noms, prenom) de la table visiteur
     *
     * @return un tableau associatif des visiteurs
     */
    public function getLesVisiteurs() {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                'SELECT *'
                . 'FROM visiteur '
                . 'ORDER BY nom'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT fichefrais.mois AS mois FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Calcule la somme des frais hors forfait pour un visiteur et un mois donné
     * (somme de tous les montants des frais hors forfait)
     *
     * @param String $idVisiteur      ID du visiteur
     * @param String $leMois          Mois du frais
     *
     * @return un tableau avec la somme des frais hors forfait
     */
    public function calculHF($idVisiteur, $mois) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT sum(lignefraishorsforfait.montant)'
                . ' From lignefraishorsforfait '
                . ' where lignefraishorsforfait.idvisiteur= :unIdVisiteur'
                . ' and lignefraishorsforfait.mois= :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);

        $requetePrepare->execute();
        $sommeHF = $requetePrepare->fetch()[0];
        return $sommeHF;
    }

    /**
     * Calcule la somme des frais forfait pour un visiteur et un mois donné
     * (produit des quantités par le montant des frais forfait)
     *
     * @param String $idVisiteur      ID du visiteur
     * @param String $leMois          Mois du frais
     *
     * @return un tableau avec le montant des frais forfait
     */
    public function calculF($idVisiteur, $mois) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT sum(lignefraisforfait.quantite*fraisforfait.montant)'
                . ' From lignefraisforfait join fraisforfait on (lignefraisforfait.idfraisforfait=fraisforfait.id)'
                . ' where lignefraisforfait.idvisiteur= :unIdVisiteur'
                . ' and lignefraisforfait.mois= :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $sommeF = $requetePrepare->fetch();
        return $sommeF;
    }

    //on insere le montantvalide dans la fiche frais
    public function MontantValide($idVisiteur, $mois, $sommeTotale, $nbJust) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE fichefrais '
                . ' set montantvalide = :uneSommeTotale, nbjustificatifs = :nbJust'
                . ' where fichefrais.idvisiteur = :unIdVisiteur'
                . ' and fichefrais.mois = :unMois'
        );

        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneSommeTotale', $sommeTotale, PDO::PARAM_STR);
        $requetePrepare->bindParam(':nbJust', $nbJust, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquels il y a au moins une fiche de frais validée (ce sont les fiches deja validees a rembourser)
     *
     * @return un tableau associatif des mois
     */
    function getMoisEtatValide() {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'select mois'
                . ' from fichefrais'
                . ' where idetat= "VA" '
                . 'group by mois'
        );

        $requetePrepare->execute();


        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les visiteurs qui ont une fiche de frais validée (ce sont les fiches deja validees a rembourser)
     *
     * @return un tableau associatif des visiteurs
     */
    function getVisiteurEtatValide() {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'select *'
                . 'from visiteur join fichefrais on(visiteur.id=fichefrais.idvisiteur) '
                . 'where idetat= "VA" '
                . 'group by visiteur.nom'
        );

        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * if($requetePrepare->execute()){
     *      echo 'Succes';
     *  }
     *  else {
     *      echo 'Echec';
     *  }
     *
     */
}
