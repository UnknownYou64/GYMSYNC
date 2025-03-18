<?php
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/dao/CoursDao.php';

try {
    $coursDao = new CoursDao();
    $cours = $coursDao->recupererTousLesCours();
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
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
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Jour</th>
                            <th>Heure</th>
                            <th>Nature du cours</th>
                            <th>Places totales</th>
                            <th>Places restantes</th>
                            <th>Professeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cours)): ?>
                            <?php foreach ($cours as $coursItem): ?>
                                <tr>
                                    <td><?= $coursItem['Jour'] ?></td>
                                    <td><?= date('H:i', strtotime($coursItem['Heure'])) ?></td>
                                    <td><?= $coursItem['Nature'] ?></td>
                                    <td><?= $coursItem['Place'] ?></td>
                                    <td>
                                        <?php if ($coursDao->estComplet($coursItem['IDC'])): ?>
                                            <span class="badge bg-danger">Complet</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <?= $coursItem['places_restantes'] ?> places
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $coursItem['Professeur'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Aucun cours disponible.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 GYMSYNC</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
