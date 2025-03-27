<?php

require_once __DIR__ . '/../config/GestionConnexion.php';

abstract class BaseDonneeDao {
    protected $pdo;
    protected $table;

    public function __construct($table) {
        $this->pdo = Connexiondb::getInstance()->getConnection();
        $this->table = $table;
    }

    
    public function findAll() {
        try {
            $sql = "SELECT * FROM {$this->table}";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des données: " . $e->getMessage());
        }
    }

    // Récupère un enregistrement par ID
    
    public function findById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE Identifiant = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération: " . $e->getMessage());
        }
    }

    
     // Supprime un enregistrement par ID
     
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE Identifiant = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression: " . $e->getMessage());
        }
    }

    //nombre total d'enregistrements
     
    public function count() {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            return $this->pdo->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage: " . $e->getMessage());
        }
    }

    
     // Vérifie si un enregistrement existe
     
    public function exists($id) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE Identifiant = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification: " . $e->getMessage());
        }
    }

    private function emailExiste($email) {
        $requete = "SELECT COUNT(*) FROM membre WHERE Mail = :email";
        $declaration = $this->pdo->prepare($requete);
        $declaration->execute([':email' => $email]);
        return $declaration->fetchColumn() > 0;
    }

    
    
}