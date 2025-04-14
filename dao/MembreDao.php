<?php

require_once __DIR__ . '/BaseDonneeDao.php';
require_once __DIR__ . '/CoursDao.php';

class MembreDao extends BaseDonneeDao {
    public function __construct() {
        parent::__construct('membre');
    }

    public $admin_email = "admin@gmail.com";
    public $admin_password = "admin";

    public function verifierConnexion($email, $password) {
        if ($email == $this->admin_email && $password == $this->admin_password) {
            return [
                'role' => 'admin',
                'email' => $email
            ];
        }

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

    public function inscrireMembreEtCours($nom, $prenom, $email, $cours) {
        $sql = "SELECT * FROM membre WHERE Mail = '$email'";
        $resultat = $this->pdo->query($sql);
        if ($resultat->fetch()) {
            throw new Exception("Email déjà utilisé");
        }

        $sql = "INSERT INTO membre (Nom, Prenom, Mail) VALUES ('$nom', '$prenom', '$email')";
        $this->pdo->query($sql);
        $id_membre = $this->pdo->lastInsertId();

        foreach ($cours as $cours_id) {
            $sql = "INSERT INTO reservation (IDC, Identifiant) VALUES ($cours_id, $id_membre)";
            $this->pdo->query($sql);
        }

        return $id_membre;
    }

    public function genererCode($nom, $prenom) {
        $sql = "SELECT * FROM membre WHERE Nom = '$nom' AND Prenom = '$prenom'";
        $resultat = $this->pdo->query($sql);
        $membre = $resultat->fetch();

        if (!$membre) {
            throw new Exception("Membre non trouvé");
        }

        $code = rand(10000000, 99999999);
        
        $sql = "UPDATE membre SET Code = '$code' WHERE Identifiant = " . $membre['Identifiant'];
        $this->pdo->query($sql);

        return $code;
    }

    public function ajouterMembre($nom, $prenom, $email) {
        $sql = "SELECT * FROM membre WHERE Mail = '$email'";
        $resultat = $this->pdo->query($sql);
        if ($resultat->fetch()) {
            throw new Exception("Email déjà utilisé");
        }

        $sql = "INSERT INTO membre (Nom, Prenom, Mail,A_Regler) VALUES ('$nom', '$prenom', '$email',0)";
        $this->pdo->query($sql);
        return $this->pdo->lastInsertId();
    }

    public function getMembre($id) {
        $sql = "SELECT * FROM membre WHERE Identifiant = :id";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMembres() {
        $sql = "SELECT * FROM membre ORDER BY Nom, Prenom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMembresParCours($cours_id) {
        $sql = "SELECT m.Identifiant, m.Nom, m.Prenom, m.Mail, m.A_Regler as Validation
                FROM membre m 
                INNER JOIN reservation r ON m.Identifiant = r.Identifiant 
                WHERE r.IDC = :cours_id 
                ORDER BY m.Nom, m.Prenom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cours_id' => $cours_id]);
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