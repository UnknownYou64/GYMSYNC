<?php
require_once __DIR__ . '/BaseDonneeDao.php';

class TarifDao extends BaseDonneeDao {
    public function __construct() {
        parent::__construct('tarifs');
    }

    public function getTarif($type_adherent, $nombre_cours) {
        $sql = "SELECT * FROM tarifs 
                WHERE categorie = :type 
                AND nbcours = :nombre";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':type', $type_adherent, PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $nombre_cours, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllTarifs() {
        return $this->findAll();
    }
}