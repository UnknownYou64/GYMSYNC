<?php
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['PHP_SELF'] === "/Administrateur.php" && $_SESSION['role'] !== "admin") {
    header('Location: index.php');
    exit;
}

?>

<?php
require_once 'Connexion.php';

try {
    $sql = "
        SELECT 
            c.IDC,
            c.Jour,
            c.Heure,
            c.Place,
            c.Nature,
            c.Professeur,
            (c.Place - IFNULL(COUNT(r.IDC), 0)) AS places_restantes
        FROM cours c
        LEFT JOIN reservation r ON c.IDC = r.idC
        GROUP BY c.IDC, c.Jour, c.Heure, c.Place, c.Nature, c.Professeur
        ORDER BY 
            CASE 
                WHEN c.Jour = 'Lundi' THEN 1
                WHEN c.Jour = 'Mardi' THEN 2
                WHEN c.Jour = 'Mercredi' THEN 3
                WHEN c.Jour = 'Jeudi' THEN 4
                WHEN c.Jour = 'Vendredi' THEN 5
                WHEN c.Jour = 'Samedi' THEN 6
                WHEN c.Jour = 'Dimanche' THEN 7
            END,
            c.Heure ASC
    ";

    $stmt = $pdo->query($sql);
    $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
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
                <li class="nav-item me-3"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item me-3"><a class="nav-link" href="inscription.php">Inscription</a></li>
                <li class="nav-item me-3"><a class="nav-link active" href="Liste.php">Liste des Cours</a></li>
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
    <h1>Liste des Cours</h1>
</header>

<div class="container my-4">
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th class="w-15">Jour</th>
                    <th class="w-15">Heure</th>
                    <th class="w-20">Nature du cours</th>
                    <th class="w-15">Places totales</th>
                    <th class="w-15">Places restantes</th>
                    <th class="w-20">Professeur</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cours)) { ?>
                    <?php foreach ($cours as $coursItem) { ?>
                        <tr>
                            <td><?= $coursItem['Jour'] ?></td>
                            <td><?= date('H:i', strtotime($coursItem['Heure'])) ?></td>
                            <td><?= $coursItem['Nature'] ?></td>
                            <td><?= $coursItem['Place'] ?></td>
                            <td>
                                <?php
                                if ($coursItem['places_restantes'] == 0) {
                                    echo "Complet";
                                } else {
                                    echo $coursItem['places_restantes'];
                                }
                                ?>
                            </td>
                            <td><?= $coursItem['Professeur'] ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun cours disponible.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 GYMSYNC</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
