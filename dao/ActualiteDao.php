<?php
require_once __DIR__ . '/BaseDonneeDao.php';

class ActualiteDao extends BaseDonneeDao {
    public function __construct() {
        parent::__construct('actualites'); 
    }

    public function getAllActualites() {
        $sql = "SELECT * FROM actualites ORDER BY ordre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateActualite($id, $texte, $couleur, $gras) {
        $sql = "UPDATE actualites SET texte = ?, couleur = ?, gras = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$texte, $couleur, $gras, $id]);
    }

    public function ajouterActualite($texte, $couleur, $gras, $ordre) {
        $sql = "INSERT INTO actualites (texte, couleur, gras, ordre) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$texte, $couleur, $gras, $ordre]);
    }

    public function supprimerActualite($id) {
        $sql = "DELETE FROM actualites WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>