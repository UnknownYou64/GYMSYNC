<?php

require_once __DIR__ . '/BaseDonneeDao.php';

class HistoriqueDao extends BaseDonneeDao {
    public function __construct() {
        parent::__construct('cours');
    }

    public function insererhistorique($membre_id, $cours_id, $action){
        $sql = "INSERT INTO historique (membre_id, cours_id, Action) VALUES (:membre_id, :cours_id, :action)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'membre_id' => $membre_id,
            'cours_id' => $cours_id,
            'action' => $action
        ]);
        return true;
    }

    // Récupérer l'historique
    public function recupererhistorique(){
        $sql = "SELECT h.Action, h.DateAction
                FROM historique h";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>