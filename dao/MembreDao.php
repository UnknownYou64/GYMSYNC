<?php

require_once __DIR__ . '/BaseDonneeDao.php';

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
     * Récupère les informations des cours sélectionnés
     */
    private function recupererInfosCours($coursIds) {
        try {
            $placeholders = str_repeat('?,', count($coursIds) - 1) . '?';
            $requete = "SELECT Jour, Heure, Nature FROM cours WHERE IDC IN ($placeholders)";
            $declaration = $this->pdo->prepare($requete);
            $declaration->execute($coursIds);
            return $declaration->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des informations des cours: " . $e->getMessage());
        }
    }

    /**
     * Récupère les cours disponibles
     */
    public function recupererCoursDisponibles() {
        try {
            $requete = "
                SELECT c.IDC, c.Jour, c.Heure, c.Nature, c.Place, c.Professeur,
                    (c.Place - COALESCE(
                        (SELECT COUNT(*) FROM reservation r WHERE r.IDC = c.IDC), 
                        0
                    )) as places_restantes
                FROM cours c
                WHERE c.Place > (
                    SELECT COUNT(*) FROM reservation r WHERE r.IDC = c.IDC
                )
                ORDER BY 
                    CASE 
                        WHEN c.Jour = 'Lundi' THEN 1
                        WHEN c.Jour = 'Mardi' THEN 2
                        WHEN c.Jour = 'Mercredi' THEN 3
                        WHEN c.Jour = 'Jeudi' THEN 4
                        WHEN c.Jour = 'Vendredi' THEN 5
                    END,
                    c.Heure ASC";
            
            $declaration = $this->pdo->query($requete);
            return $declaration->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des cours: " . $e->getMessage());
        }
    }
}