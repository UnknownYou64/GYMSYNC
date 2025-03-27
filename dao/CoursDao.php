<?php

require_once __DIR__ . '/BaseDonneeDao.php';

class CoursDao extends BaseDonneeDao {
    public function __construct() {
        parent::__construct('cours');
    }

    // Récupérer tous les cours disponibles
    public function recupererCoursDisponibles() {
        $sql = "SELECT c.*, 
                (c.Place - (SELECT COUNT(*) FROM reservation r WHERE r.IDC = c.IDC)) as places_restantes 
                FROM cours c 
                WHERE c.Place > (SELECT COUNT(*) FROM reservation r WHERE r.IDC = c.IDC)
                ORDER BY c.Jour, c.Heure";
    
        $resultat = $this->pdo->query($sql);
        return $resultat->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les inscrits d'un cours
    public function recupererInscriptionsParCours($idCours) {
        $sql = "SELECT m.* FROM membre m 
                JOIN reservation r ON m.Identifiant = r.Identifiant 
                WHERE r.IDC = $idCours";
        
        $resultat = $this->pdo->query($sql);
        return $resultat->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifier si un cours est plein
    public function estComplet($idCours) {
        $sql = "SELECT Place, 
                (SELECT COUNT(*) FROM reservation WHERE IDC = cours.IDC) as inscrits
                FROM cours 
                WHERE IDC = $idCours";
        
        $resultat = $this->pdo->query($sql);
        $cours = $resultat->fetch();
        
        return $cours['inscrits'] >= $cours['Place'];
    }

    // Ajouter un nouveau cours
    public function ajouterCours($jour, $heure, $nature, $places, $prof) {
        $sql = "INSERT INTO cours (Jour, Heure, Nature, Place, Professeur) 
                VALUES ('$jour', '$heure', '$nature', $places, '$prof')";
        
        $this->pdo->query($sql);
        return $this->pdo->lastInsertId();
    }

    // Inscrire un membre à un cours
    public function inscrireAuCours($membre_id, $cours_id) {
        // Vérifier si le cours est plein
        if ($this->estComplet($cours_id)) {
            throw new Exception("Ce cours est complet");
        }

        // Ajouter l'inscription
        $sql = "INSERT INTO reservation (IDC, Identifiant) 
                VALUES ($cours_id, $membre_id)";
        
        $this->pdo->query($sql);
        return true;
    }

    // Inscrire un membre à plusieurs cours
    public function inscrireAuxCours($membre_id, $cours_selectionnes) {
        // Vérifier pour chaque cours s'il est complet
        foreach ($cours_selectionnes as $cours_id) {
            if ($this->estComplet($cours_id)) {
                throw new Exception("Le cours " . $cours_id . " est complet");
            }
        }

        // Inscrire aux cours si aucun n'est complet
        foreach ($cours_selectionnes as $cours_id) {
            $sql = "INSERT INTO reservation (IDC, Identifiant) 
                   VALUES ($cours_id, $membre_id)";
            $this->pdo->query($sql);
        }

        return true;
    }

    // Récupérer les informations des cours sélectionnés
    public function getCoursInfo($cours_ids) {
        $cours_ids_str = implode(',', $cours_ids);
        $sql = "SELECT * FROM cours WHERE IDC IN ($cours_ids_str)";
        $resultat = $this->pdo->query($sql);
        return $resultat->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter cette nouvelle méthode dans CoursDao
    public function recupererTousLesCours() {
        $sql = "SELECT c.*, 
                (c.Place - (SELECT COUNT(*) FROM reservation r WHERE r.IDC = c.IDC)) as places_restantes 
                FROM cours c 
                ORDER BY FIELD(Jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), 
                c.Heure";

        $resultat = $this->pdo->query($sql);
        return $resultat->fetchAll(PDO::FETCH_ASSOC);
    }

    public function supprimerMemberDuCours($membre_id, $cours_id) {
        $sql = "DELETE FROM reservation 
                WHERE Identifiant = $membre_id 
                AND IDC = $cours_id";
        
        return $this->pdo->query($sql);
    }

    public function supprimerToutesReservations($cours_id) {
        $sql = "DELETE FROM reservation WHERE IDC = :cours_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':cours_id', $cours_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function supprimerCours($cours_id) {
        $sql = "DELETE FROM cours WHERE IDC = :cours_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':cours_id', $cours_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}