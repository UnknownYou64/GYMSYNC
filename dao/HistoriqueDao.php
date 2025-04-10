<?php

require_once 'dao/BaseDonneeDao.php';

class HistoriqueDao extends BaseDonneeDao {
    public function __construct() {
        parent::__construct('cours');
    }

    public function insererhistorique($action){
        $sql = "INSERT INTO historique (Action) VALUES (:action)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'action' => $action
        ]);
        return true;
    }

    // Récupérer l'historique
    public function recupererhistorique(){
        $sql = "SELECT  Action, DateAction
                FROM historique 
                ORDER BY DateAction DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function recupHistMembreCours($idmembre, $idcours) {
        $sql = "SELECT m.Nom, m.Prenom, c.Nature, c.Jour, c.Heure
                FROM membre m
                JOIN reservation r ON m.Identifiant = r.Identifiant
                JOIN cours c ON c.IDC = r.IDC
                WHERE m.Identifiant = :idmembre AND c.IDC = :idcours";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
        $stmt->bindParam(':idcours', $idcours, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        
        public function recupHistoCours($idcours) {
        $sql = "SELECT Nature
                FROM cours
                WHERE IDC = :idcours";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idcours', $idcours, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
}



?>