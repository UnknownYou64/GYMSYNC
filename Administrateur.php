<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header('Location: index.php');
    exit;
}



require_once 'Connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);

    $code = substr(bin2hex(random_bytes(4)), 0, 8); 

    try {
        $sql = "SELECT * FROM membre WHERE Nom = :nom AND Prenom = :prenom";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom
        ]);

        $membre = $stmt->fetch();

        if ($membre) {
            $updateSql = "UPDATE membre SET Code = :code WHERE Identifiant = :id";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([
                ':code' => $code,
                ':id' => $membre['Identifiant']
            ]);

            echo "<script>alert('Code généré : $code pour $nom $prenom');</script>";
        } else {
            echo "<script>alert('Membre introuvable.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erreur : " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Générer un Code d'Accès</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin - GYMSYNC</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inscription.php">Inscription</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Liste.php">Liste des Cours</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- En-tête -->
    <header class="bg-dark text-white text-center py-3">
        <h1>Générer un Code d'Accès</h1>
    </header>

    <!-- Formulaire de recherche du membre -->
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow">
                    <h3 class="text-center">Recherche Membre</h3>
                    <form method="POST" action="Administrateur.php">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Générer Code</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 GYMSYNC</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
