<?php

require_once __DIR__ . '/BaseDonneeDao.php';
require_once __DIR__ . '/CoursDao.php';

class MembreDao extends BaseDonneeDao {
    // Variables simples pour admin
    public $admin_email = "admin@gmail.com";
    public $admin_password = "admin";

    public function __construct() {
        parent::__construct('membre');
    }

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
}