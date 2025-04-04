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
}
?>