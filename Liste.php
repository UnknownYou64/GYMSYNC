<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/dao/CoursDao.php';

// Créer l'objet coursDao
$coursDao = new CoursDao();
$cours = [];
$erreur = null;

// Récupérer la liste des cours
try {
    $cours = $coursDao->recupererTousLesCours();
} catch (Exception $e) {
    $erreur = $e->getMessage();
}

// Liste des jours de la semaine
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Cours</title>
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="inscription.php">Inscription</a></li>
                    <li class="nav-item"><a class="nav-link active" href="Liste.php">Liste des Cours</a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin") { ?>
                        <li class="nav-item me-3"><a href="Administrateur.php" class="nav-link">Admin</a></li>
                    <?php } ?>
                    <?php if (isset($_SESSION['role'])) { ?>
                        <a href="logout.php" class="btn btn-danger">Déconnexion</a>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="bg-dark text-white text-center py-3">
        <h1>Liste des Cours Disponibles</h1>
    </header>

    <div class="container my-4">
        <?php if (isset($erreur)): ?>
            <div class="alert alert-danger"><?= $erreur ?></div>
        <?php else: ?>
            <div class="row">
                <?php 
                // Pour chaque jour, récupérer ses cours
                foreach ($jours as $jour):
                    $coursDuJour = [];
                    // Parcourir tous les cours pour trouver ceux du jour
                    foreach ($cours as $c) {
                        if ($c['Jour'] === $jour) {
                            $coursDuJour[] = $c;
                        }
                    }
                    
                    // Si il y a des cours ce jour
                    if (!empty($coursDuJour)):
                ?>
                    <div class="col-12">
                        <h3 class="mt-4"><?= $jour ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-fixed">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 15%">Heure</th>
                                        <th style="width: 25%">Cours</th>
                                        <th style="width: 15%">Places totales</th>
                                        <th style="width: 25%">Statut</th>
                                        <th style="width: 20%">Professeur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($coursDuJour as $c): ?>
                                        <tr class="<?= $c['places_restantes'] <= 0 ? 'table-secondary' : '' ?>">
                                            <td style="width: 15%"><?= date('H:i', strtotime($c['Heure'])) ?></td>
                                            <td style="width: 25%"><?= $c['Nature'] ?></td>
                                            <td style="width: 15%"><?= $c['Place'] ?></td>
                                            <td style="width: 25%">
                                                <?php if ($c['places_restantes'] <= 0): ?>
                                                    <span class="badge bg-danger">Complet</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">
                                                        <?= $c['places_restantes'] ?> places disponibles
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="width: 20%"><?= $c['Professeur'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php 
                    endif;
                endforeach;
                ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 GYMSYNC</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
