<?php

class Connexiondb {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $dbname = 'gymsync';
    private $username = 'root';
    private $password = 'root';

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion : " . $e->getMessage());
        }
    }

    
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

   
    public function getConnection() {
        return $this->connection;
    }
}


//envoyer au serveur

// scp inscription.php root@10.3.16.11:/var/www/.
//remplacer inscription.php par le nom du fichier que vous voulez envoyer