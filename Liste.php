<?php
require_once 'Connexion.php';

try {
    // Requête SQL corrigée
    $sql = "
        SELECT c.Date, c.Place, c.Professeur, 
        (c.Place - IFNULL(COUNT(r.IDC), 0)) AS places_restantes
        FROM cours c
        LEFT JOIN reservation r ON c.IDC = r.idC
        GROUP BY c.IDC, c.Date, c.Place, c.Professeur
        ORDER BY c.Date ASC
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inscription.php">Inscription</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="Liste.php">Liste des Cours</a>
                    </li>
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
                        <th class="w-20">Date</th>
                        <th class="w-25">Nombre de Places</th>
                        <th class="w-25">Places restantes</th>
                        <th class="w-30">Professeur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cours)): ?>
                        <?php foreach ($cours as $coursItem): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($coursItem['Date'])) ?></td>
                                <td><?= htmlspecialchars($coursItem['Place']) ?></td>
                                <td>
                                    <?php
                                   
                                    if ($coursItem['places_restantes'] == 0) {
                                        echo "Complet"; 
                                    } else {
                                        echo htmlspecialchars($coursItem['places_restantes']); 
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($coursItem['Professeur']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Aucun cours disponible.</td>
                        </tr>
                    <?php endif; ?>
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
