<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header('Location: index.php');
    exit;
}

require_once 'Connexion.php';
require_once 'dao/BaseDonneeDao.php';
require_once 'dao/CoursDao.php';
require_once 'dao/MembreDao.php';

// Initialisation des DAO
$coursDao = new CoursDao();
$membreDao = new MembreDao();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generate_code'])) {
        try {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $code = $membreDao->genererCode($nom, $prenom);
            
            $message = "Code généré : $code pour $nom $prenom";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['add_member'])) {
        try {
            $nom = $_POST['nom_member'];
            $prenom = $_POST['prenom_member'];
            $mail = $_POST['mail_member'];
            
            $membreId = $membreDao->ajouterMembre($nom, $prenom, $mail);
            
            $message = "Membre ajouté avec succès.";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">GYMSYNC</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item me-3"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item me-3"><a class="nav-link" href="inscription.php">Inscription</a></li>
                <li class="nav-item me-3"><a class="nav-link" href="Liste.php">Liste des Cours</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin") { ?>
                    <li class="nav-item me-3">
                        <a href="Administrateur.php" class="nav-link">Admin</a>
                    </li>
                <?php } ?>
                <?php if (isset($_SESSION['role'])) { ?>
                    <li class="nav-item"><a href="logout.php" class="btn btn-danger">Déconnexion</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<header class="bg-dark text-white text-center py-3">
    <h1>Espace Administrateur</h1>
</header>




<!-- 

Dans la page administrateur je veux mettre des instructions a suivre pour chaque fonctionnalité 
je veux les mettre a droite et a gauche de la page 
dire ce que sa modifie et ce que sa ajoute 



et en bas de page afficher un historique des actions effectuées par l'administrateur (petits historique sans boutons revennir a l'etat precedent)


a la fin il faut aussi un espace pour en cas de probleme, envoyer vers le cahier technique ou envoyer un sms/whatsapp a un numero de telephone (0786448613)




-->

<div class="container my-4">
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow mb-4">
                <h3 class="text-center">Code Generateur</h3>
                <form method="POST" action="Administrateur.php">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <button type="submit" name="generate_code" class="btn btn-primary w-100">Générer Code</button>
                </form>
            </div>

            <div class="card p-4 shadow mt-4">
                <h3 class="text-center">Ajouter un Membre</h3>
                <form method="POST" action="Administrateur.php">
                    <div class="mb-3">
                        <label for="nom_member" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom_member" name="nom_member" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom_member" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom_member" name="prenom_member" required>
                    </div>
                    <div class="mb-3">
                        <label for="mail_member" class="form-label">Email</label>
                        <input type="email" class="form-control" id="mail_member" name="mail_member" required>
                    </div>
                    <button type="submit" name="add_member" class="btn btn-primary w-100">Ajouter le Membre</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 shadow mb-4">
            <h3 class="text-center">historique</h3>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 shadow mb-4">
            <h3 class="text-center">Problème</h3>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 GYMSYNC</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

