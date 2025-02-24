<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Mon Site</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item me-3">
                    <a class="nav-link active" href="index.php">Accueil</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link" href="inscription.php">Inscription</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link" href="Liste.php">Liste des Cours</a>
                </li>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin") { ?>
                    <li class="nav-item me-3">
                        <a href="Administrateur.php" class="nav-link">Admin</a>
                    </li>
                <?php } ?>
                
                <?php if (isset($_SESSION['role'])) { ?>
                    <a href="logout.php" class="btn btn-danger">Déconnexion</a>
                <?php } else { ?>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="Login.php">Se connecter</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<header class="bg-dark text-white text-center py-3">
    <h1>Accueil</h1>
</header>

<div class="container my-4">
    <div class="row">
        <div class="col-md-4 bg-light p-3">c1</div>
        <div class="col-md-4 bg-secondary text-white p-3">c2</div>
        <div class="col-md-4 bg-dark text-white p-3">c3</div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 GYMSYNC</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
