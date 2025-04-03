<?php

require_once __DIR__ . '/BaseDonneeDao.php';
require_once __DIR__ . '/CoursDao.php';

class MembreDao extends BaseDonneeDao {
    public function __construct() {
        parent::__construct('membre');
    }

    // Variables simples pour admin
    public $admin_email = "admin@gmail.com";
    public $admin_password = "admin";

    // Connexion basique
    public function verifierConnexion($email, $password) {
        // Si c'est l'admin
        if ($email == $this->admin_email && $password == $this->admin_password) {
            return [
                'role' => 'admin',
                'email' => $email
            ];
        }

        // Si c'est un membre
        $sql = "SELECT * FROM membre WHERE Mail = '$email' AND Code = '$password'";
        $resultat = $this->pdo->query($sql);
        $membre = $resultat->fetch();

        if ($membre) {
            return [
                'role' => 'membre',
                'email' => $membre['Mail'],
                'id' => $membre['Identifiant']
            ];
        }
        return null;
    }

    // Inscription simple
    public function inscrireMembreEtCours($nom, $prenom, $email, $cours) {
        // Vérifier si email existe
        $sql = "SELECT * FROM membre WHERE Mail = '$email'";
        $resultat = $this->pdo->query($sql);
        if ($resultat->fetch()) {
            throw new Exception("Email déjà utilisé");
        }

        // Ajouter le membre
        $sql = "INSERT INTO membre (Nom, Prenom, Mail) VALUES ('$nom', '$prenom', '$email')";
        $this->pdo->query($sql);
        $id_membre = $this->pdo->lastInsertId();

        // Ajouter ses cours
        foreach ($cours as $cours_id) {
            $sql = "INSERT INTO reservation (IDC, Identifiant) VALUES ($cours_id, $id_membre)";
            $this->pdo->query($sql);
        }

        return $id_membre;
    }

    // Générer un code simple
    public function genererCode($nom, $prenom) {
        $sql = "SELECT * FROM membre WHERE Nom = '$nom' AND Prenom = '$prenom'";
        $resultat = $this->pdo->query($sql);
        $membre = $resultat->fetch();

        if (!$membre) {
            throw new Exception("Membre non trouvé");
        }

        // Code aléatoire simple
        $code = rand(10000000, 99999999);
        
        $sql = "UPDATE membre SET Code = '$code' WHERE Identifiant = " . $membre['Identifiant'];
        $this->pdo->query($sql);

        return $code;
    }

    // Ajout membre simple
    public function ajouterMembre($nom, $prenom, $email) {
        // Vérifier si email existe
        $sql = "SELECT * FROM membre WHERE Mail = '$email'";
        $resultat = $this->pdo->query($sql);
        if ($resultat->fetch()) {
            throw new Exception("Email déjà utilisé");
        }

        // Ajouter le membre
        $sql = "INSERT INTO membre (Nom, Prenom, Mail) VALUES ('$nom', '$prenom', '$email')";
        $this->pdo->query($sql);
        return $this->pdo->lastInsertId();
    }

    // Récupérer les informations d'un membre en utilisant une requête préparée
    public function getMembre($id) {
        // Utilisation d'une requête préparée pour éviter les injections SQL
        $sql = "SELECT * FROM membre WHERE Identifiant = :id";
        $stmt = $this->pdo->prepare($sql);
        
        // On associe le paramètre avec une valeur
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // On exécute la requête
        $stmt->execute();
        
        // On retourne le résultat
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les membres triés par nom et prénom
    public function getMembres() {
        // Requête simple car pas de paramètres externes
        $sql = "SELECT * FROM membre ORDER BY Nom, Prenom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les membres inscrits à un cours spécifique
    public function getMembresParCours($cours_id) {
        // Requête préparée pour la jointure
        $sql = "SELECT m.* 
                FROM membre m 
                JOIN reservation r ON m.Identifiant = r.Identifiant 
                WHERE r.IDC = :cours_id 
                ORDER BY m.Nom, m.Prenom";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':cours_id', $cours_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function majTarif($membre_id, $tarif_id) {
        $sql = "UPDATE membre 
                SET tarif_id = :tarif_id 
                WHERE Identifiant = :membre_id";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':tarif_id', $tarif_id, PDO::PARAM_INT);
        $stmt->bindParam(':membre_id', $membre_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function basculerValidation($membre_id) {
        $sql = "UPDATE membre 
                SET A_Regler = CASE WHEN A_Regler = 0 THEN 1 ELSE 0 END 
                WHERE Identifiant = :membre_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['membre_id' => $membre_id]);
    }


    public function getMembresNonPaye() {
        $sql = "SELECT * FROM membre WHERE A_Regler = 0 ORDER BY Nom, Prenom";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}