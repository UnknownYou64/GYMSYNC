<?php

require_once __DIR__ . '/BaseDonneeDao.php';

class CoursDao extends BaseDonneeDao {
    public function __construct() {
        parent::__construct('cours');
    }

    public function recupererCoursDisponibles() {
        try {
            $requete = "SELECT c.*, 
                    (c.Place - COALESCE(
                        (SELECT COUNT(*) FROM reservation r WHERE r.IDC = c.IDC), 
                        0
                    )) as places_restantes 
                    FROM cours c 
                    WHERE c.Place > COALESCE(
                        (SELECT COUNT(*) FROM reservation r WHERE r.IDC = c.IDC), 
                        0
                    )
                    ORDER BY 
                        CASE 
                            WHEN c.Jour = 'Lundi' THEN 1 
                            WHEN c.Jour = 'Mardi' THEN 2 
                            WHEN c.Jour = 'Mercredi' THEN 3 
                            WHEN c.Jour = 'Jeudi' THEN 4 
                            WHEN c.Jour = 'Vendredi' THEN 5 
                        END, 
                        c.Heure";
        
        $declaration = $this->pdo->query($requete);
        return $declaration->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des cours: " . $e->getMessage());
        }
    }

    public function recupererInscriptionsParCours($idCours) {
        try {
            $requete = "SELECT m.Identifiant, m.Nom, m.Prenom, m.Mail, r.DateReservation 
                    FROM membre m 
                    INNER JOIN reservation r ON m.Identifiant = r.IDM 
                    WHERE r.IDC = :idCours 
                    ORDER BY m.Nom, m.Prenom";
            
            $declaration = $this->pdo->prepare($requete);
            $declaration->execute([':idCours' => $idCours]);
            return $declaration->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des réservations: " . $e->getMessage());
        }
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
     * Récupère tous les cours avec leurs places restantes
     */
    public function recupererTousLesCours() {
        try {
            $requete = "
                SELECT 
                    c.IDC,
                    c.Jour,
                    c.Heure,
                    c.Place,
                    c.Nature,
                    c.Professeur,
                    (c.Place - IFNULL(COUNT(r.IDC), 0)) AS places_restantes
                FROM cours c
                LEFT JOIN reservation r ON c.IDC = r.IDC
                GROUP BY c.IDC, c.Jour, c.Heure, c.Place, c.Nature, c.Professeur
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

    /**
     * Vérifie si un cours est complet
     */
    public function estComplet($idCours) {
        try {
            $requete = "
                SELECT 
                    (c.Place - COUNT(r.IDC)) as places_restantes
                FROM cours c
                LEFT JOIN reservation r ON c.IDC = r.IDC
                WHERE c.IDC = :idCours
                GROUP BY c.IDC, c.Place";
            
            $declaration = $this->pdo->prepare($requete);
            $declaration->execute([':idCours' => $idCours]);
            $resultat = $declaration->fetch(PDO::FETCH_ASSOC);
            
            return $resultat && $resultat['places_restantes'] <= 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification du cours: " . $e->getMessage());
        }
    }

    /**
     * Récupère les places restantes pour un cours
     */
    public function recupererPlacesRestantes($idCours) {
        try {
            $requete = "
                SELECT 
                    (c.Place - COUNT(r.IDC)) as places_restantes
                FROM cours c
                LEFT JOIN reservation r ON c.IDC = r.IDC
                WHERE c.IDC = :idCours
                GROUP BY c.IDC, c.Place";
            
            $declaration = $this->pdo->prepare($requete);
            $declaration->execute([':idCours' => $idCours]);
            $resultat = $declaration->fetch(PDO::FETCH_ASSOC);
            
            return $resultat ? $resultat['places_restantes'] : 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des places restantes: " . $e->getMessage());
        }
    }

    /**
     * Ajoute un nouveau cours
     */
    public function ajouterCours($jour, $heure, $nature, $places, $professor) {
        try {
            $sql = "INSERT INTO cours (Jour, Heure, Nature, Place, Professeur) 
                   VALUES (:jour, :heure, :nature, :places, :professor)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':jour' => $jour,
                ':heure' => $heure,
                ':nature' => $nature,
                ':places' => $places,
                ':professor' => $professor
            ]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du cours: " . $e->getMessage());
        }
    }

    public function inscrireAuxCours($membre_id, $cours_selectionnes) {
        try {
            $this->pdo->beginTransaction();

            foreach ($cours_selectionnes as $cours_id) {
                $this->inscrireAuCours($membre_id, $cours_id);
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Inscrit un membre à un cours spécifique
     */
    public function inscrireAuCours($membre_id, $cours_id) {
        try {
            // Vérification des places disponibles
            $places_restantes = $this->recupererPlacesRestantes($cours_id);
            if ($places_restantes <= 0) {
                throw new Exception("Plus de places disponibles pour ce cours.");
            }

            // Vérification si déjà inscrit
            $sql = "SELECT COUNT(*) FROM reservation WHERE IDC = :cours_id AND Identifiant = :membre_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':cours_id' => $cours_id,
                ':membre_id' => $membre_id
            ]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Vous êtes déjà inscrit à ce cours.");
            }

            // Inscription au cours
            $sql = "INSERT INTO reservation (IDC, Identifiant) VALUES (:cours_id, :membre_id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':cours_id' => $cours_id,
                ':membre_id' => $membre_id
            ]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'inscription au cours: " . $e->getMessage());
        }
    }

    /**
     * Récupère les informations détaillées des cours sélectionnés
     */
    public function getCoursInfo($coursIds) {
        try {
            $placeholders = str_repeat('?,', count($coursIds) - 1) . '?';
            $sql = "SELECT IDC, Jour, Heure, Nature, Professeur, 
                    (Place - COALESCE((SELECT COUNT(*) FROM reservation r WHERE r.IDC = cours.IDC), 0)) as places_restantes
                    FROM cours 
                    WHERE IDC IN ($placeholders)
                    ORDER BY Jour, Heure";
                    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($coursIds);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des informations des cours: " . $e->getMessage());
        }
    }
}