<?php

require_once __DIR__ . '/BaseDonneeDao.php';
require_once __DIR__ . '/CoursDao.php';

class MembreDao extends BaseDonneeDao {
    private $admin_email = "admin@gmail.com";
    private $admin_password = "admin";

    public function __construct() {
        parent::__construct('membre');
    }

    /**
     * Vérifie les identifiants de connexion
     */
    public function verifierConnexion($email, $password) {
        if ($email === $this->admin_email && $password === $this->admin_password) {
            return [
                'role' => 'admin',
                'email' => $email
            ];
        }

        try {
            $requete = "SELECT * FROM membre WHERE Mail = :email AND Code = :code";
            $declaration = $this->pdo->prepare($requete);
            $declaration->execute([
                ':email' => $email,
                ':code' => $password
            ]);
            $membre = $declaration->fetch(PDO::FETCH_ASSOC);

            if ($membre) {
                return [
                    'role' => 'membre',
                    'email' => $membre['Mail'],
                    'id' => $membre['Identifiant']
                ];
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification des identifiants: " . $e->getMessage());
        }
    }

    /**
     * Inscrit un nouveau membre et ses cours
     */
    public function inscrireMembreEtCours($nom, $prenom, $email, $coursSelectionnes) {
        try {
            $this->pdo->beginTransaction();

            // Vérification si l'email existe déjà
            if ($this->emailExiste($email)) {
                throw new Exception("Cette adresse email est déjà utilisée.");
            }

            
            $requeteMembre = "INSERT INTO membre (Nom, Prenom, Mail) VALUES (:nom, :prenom, :email)";
            $declarationMembre = $this->pdo->prepare($requeteMembre);
            $declarationMembre->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email
            ]);
            
            $membreId = $this->pdo->lastInsertId();

            
            $requeteReservation = "INSERT INTO reservation (IDC, Identifiant) VALUES (:idCours, :idMembre)";
            $declarationReservation = $this->pdo->prepare($requeteReservation);

            foreach ($coursSelectionnes as $coursId) {
                $declarationReservation->execute([
                    ':idCours' => $coursId,
                    ':idMembre' => $membreId
                ]);
            }

            $this->pdo->commit();
            return [
                'membre_id' => $membreId,
                'cours_info' => $this->recupererInfosCours($coursSelectionnes)
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception("Erreur lors de l'inscription: " . $e->getMessage());
        }
    }

    /**
     * Vérifie si un email existe déjà
     */
    private function emailExiste($email) {
        $requete = "SELECT COUNT(*) FROM membre WHERE Mail = :email";
        $declaration = $this->pdo->prepare($requete);
        $declaration->execute([':email' => $email]);
        return $declaration->fetchColumn() > 0;
    }

    

    

    /**
     * Génère un code pour un membre existant
     */
    public function genererCode($nom, $prenom) {
        try {
            $sql = "SELECT * FROM membre WHERE Nom = :nom AND Prenom = :prenom";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':nom' => $nom, ':prenom' => $prenom]);
            $membre = $stmt->fetch();

            if (!$membre) {
                throw new Exception("Membre introuvable.");
            }

            $code = substr(bin2hex(random_bytes(4)), 0, 8);
            
            $updateSql = "UPDATE membre SET Code = :code WHERE Identifiant = :id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute([
                ':code' => $code,
                ':id' => $membre['Identifiant']
            ]);

            return $code;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la génération du code: " . $e->getMessage());
        }
    }

    /**
     * Ajoute un nouveau membre
     */
    public function ajouterMembre($nom, $prenom, $email) {
        try {
            // Vérification si l'email existe déjà
            if ($this->emailExiste($email)) {
                throw new Exception("Cette adresse email est déjà utilisée.");
            }

            $sql = "INSERT INTO membre (Nom, Prenom, Mail) VALUES (:nom, :prenom, :mail)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':mail' => $email
            ]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du membre: " . $e->getMessage());
        }
    }

    
}