<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire avec Bootstrap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
    require_once 'Connexion.php';

    try {
        $sql = "
            SELECT c.IDC, c.Date, (c.Place - IFNULL(COUNT(r.IDC), 0)) AS places_restantes
            FROM cours c
            LEFT JOIN reservation r ON c.IDC = r.idC
            GROUP BY c.IDC, c.Date, c.Place
            HAVING places_restantes > 0
            ORDER BY c.Date ASC
        ";

        $stmt = $pdo->query($sql);
        $coursDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
    ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Mon Site</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-3">
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link active" href="inscription.php">Inscription</a>
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
                        <li class="nav-item me-3">
                            <a href="logout.php" class="btn btn-danger">Déconnexion</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="bg-dark text-white text-center py-3">
        <h1>Formulaire d'inscription</h1>
    </header>

    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow">
                    <h3 class="text-center">Remplissez le formulaire</h3>
                    <form method="POST" action="inscription.php">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_cours" class="form-label">Sélectionner un cours</label>
                            <select class="form-select" id="date_cours" name="date_cours" required>
                                <option value="">Sélectionnez une date</option>
                                <?php if (!empty($coursDisponibles)) {
                                    foreach ($coursDisponibles as $cours) { ?>
                                        <option value="<?= $cours['IDC'] ?>">
                                            <?= date('d/m/Y', strtotime($cours['Date'])) ?> - Places restantes: <?= $cours['places_restantes'] ?>
                                        </option>
                                <?php }} else { ?>
                                    <option disabled>Aucun cours disponible</option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $date_cours = (int)$_POST['date_cours'];

        if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($date_cours)) {
            try {
                $sql = "INSERT INTO membre (Nom, Prenom, Mail) VALUES (:nom, :prenom, :email)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email
                ]);
        
                $sql_reservation = "INSERT INTO reservation (IDC, Identifiant) VALUES (:id_cours, LAST_INSERT_ID())";
                $stmt_reservation = $pdo->prepare($sql_reservation);
                $stmt_reservation->execute([':id_cours' => $date_cours]);
        

                //envoie du mail
                require_once 'mail.php';
        
                echo "<script>alert('Inscription réussie ! Un e-mail de confirmation vous a été envoyé.'); window.location.href='Liste.php';</script>";
            } catch (PDOException $e) {
                echo "<script>alert('Erreur lors de l\'inscription : " . $e->getMessage() . "');</script>";
            }
        }
    }


  
    ?>





    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 GYMSYNC</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
