<?php
    /**
     * Class pour la gestion de la donnée Utilisateur
     * permet les fonctions de création / modification / suppression en base
     * 
     * renvoi la liste des utilisateurs
     * renvoi l'existence ou non d'un utilisateur par son identifiant
     * 
     * Enregistre dans la session le fait qu'un utilisateur se connecte
     * 
     * Permet le controle de la demande de connexion
     * 
     * L'utilisation de l'algorithme SHA1 permet de sécuriser le mot de passe
     * @author
     *
     */
    class Utilisateur {
        // On enregistre les information de connexion du driver pdo
        private $user;
        
        /**
         * __construct est relatif au constructeur de class
         * quand on 
         * @param unknown $pdo 
         */
        public function __construct() {
        }
        
        /**
         * Liste des utilisateurs
         *
         * @param unknown $pdo
         * @return unknown
         */
        public static function getListe($pdo){
            $sql = "select * from utilisateur ";
            return DbAccess::getRequeteSql($pdo, $sql);
        }
        
        
        /**
         * Fonction pour l'ajout d'utilisateur
         * @param unknown $identifiant
         * @param unknown $mot_de_passe
         * @param unknown $nom
         * @param unknown $prenom
         * @param unknown $type_utilisateur
         */
        public static function ajoute($pdo,$identifiant,$mot_de_passe,$nom,$prenom,$type_utilisateur){
            $data = [
                'tidentifiant' => $identifiant,
                'tmdp' => Utilisateur::pw_encode($mot_de_passe),
                'tnom' => $nom,
                'tprenom' => $prenom,
                'ttype_utilisateur' => $type_utilisateur,
            ];
            $sql = "INSERT INTO utilisateur (identifiant,mdp,nom,prenom,type_utilisateur) VALUES (:tidentifiant, :tmdp, :tnom, :tprenom, :ttype_utilisateur)";
            
            $stmt= $pdo->prepare($sql);
            return $stmt->execute($data);
        }

        /**
         * Méthode de recherche de l'existence d'un utilisateur 
         * 
         * @param unknown $pdo              Connecteur 
         * @param unknown $identifiant      Identifiant de saisie
         * @return boolean true / false
         */
        public static function isExiste($pdo, $identifiant){
            $sql = "select * from utilisateur where identifiant = " . $pdo->quote($identifiant);
            
            return is_array(DbAccess::canFind($pdo, $sql));
        }

        public static function modifie($pdo,$identifiant,$mot_de_passe,$nom,$prenom,$type_utilisateur){
            $data = [
                'tidentifiant' => $identifiant,
                'tmdp' => $mot_de_passe,
                'tnom' => $nom,
                'tprenom' => $prenom,
                'ttype_utilisateur' => $type_utilisateur,
            ];
            $sql = "UPDATE utilisateur SET mdp=:tmdp, nom=:tnom, prenom=:tprenom, type_utilisateur=:ttype_utilisateur WHERE identifiant=:tidentifiant";
            
            $stmt= $pdo->prepare($sql);
            return $stmt->execute($data);
        }
        
        public static function supprime($pdo,$identifiant) {
            $data = [
                'id' => $identifiant,
            ];
            $sql = "DELETE FROM utilisateur WHERE identifiant = :id";
            $stmt= $pdo->prepare($sql);
            return $stmt->execute($data);
        }

        /**
         * Procesus de connex
         *
         * @param unknown $pdo
         * @param unknown $identifiant
         * @param unknown $motDePasse
         * @return boolean
         */
        public function verifieConnection($pdo,$identifiant,$motDePasse) {
            $sql = 'select * from utilisateur '.
                'where utilisateur.identifiant = '.$pdo->quote($identifiant).' ';
            
            if ($reqUser = DbAccess::canFind($pdo,$sql) ) {
                if (
                    Utilisateur::pw_check($motDePasse, $reqUser['mdp'])
                    || $reqUser['mdp'] == $motDePasse // pour le momet on laisse car on a mis des mots de passe non chiffré
                ){
                    $this->user = $reqUser;
                    return true;
                }
                else
                    return false;
            }
            else
                return false;
        }
        
        /**
         * Renvoi tout les colonnes de la requetes de connexion utilisateur
         * @return array retourne un array de toutes les colonnes relatif à l'utilisateur connecté
         */
        public function getUser() {
            return $this->user;
        }

        /**
         * Renvoi le code type de l'utilisateur
         * @return unknown
         */
        public function getTypeUser(){
            return  $this->user['type_utilisateur'];
        }

        /**
         * Renvoi true false pour savoir si l'utilisateur est admin
         * @return boolean
         */
        public function isAdmin(){
            return  $this->user['type_utilisateur'] == 'A';
        }        
        
        
        /**
         * sécurisation mdp BCRYPT 
         */
        private static function pw_encode($password){

            return password_hash($password, PASSWORD_DEFAULT);
        }

        private static function pw_check($password, $stored_value){

            return password_verify($password, $stored_value);
        }
    }
?>
