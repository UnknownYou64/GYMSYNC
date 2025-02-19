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

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $cours_selectionnes = isset($_POST['cours']) ? $_POST['cours'] : [];

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($cours_selectionnes)) {
        try {
            $pdo->beginTransaction();

            $sql = "INSERT INTO membre (Nom, Prenom, Mail) VALUES (:nom, :prenom, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email
            ]);
            
            $membre_id = $pdo->lastInsertId();

            $cours_info = [];
            foreach ($cours_selectionnes as $cours_id) {
                $sql_reservation = "INSERT INTO reservation (IDC, Identifiant) VALUES (:id_cours, :membre_id)";
                $stmt_reservation = $pdo->prepare($sql_reservation);
                $stmt_reservation->execute([
                    ':id_cours' => $cours_id,
                    ':membre_id' => $membre_id
                ]);

                // Récupérer les informations du cours
                $sql_cours = "SELECT Date FROM cours WHERE IDC = :id_cours";
                $stmt_cours = $pdo->prepare($sql_cours);
                $stmt_cours->execute([':id_cours' => $cours_id]);
                $cours_info[] = $stmt_cours->fetch(PDO::FETCH_ASSOC);
            }

            $pdo->commit();

            // Envoie du mail
            require_once 'mail.php';

            $message = "Inscription réussie ! Un e-mail de confirmation vous a été envoyé.";
            $messageType = 'success';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Erreur lors de l'inscription : " . $e->getMessage();
            $messageType = 'danger';
        }
    } else {
        $message = "Veuillez remplir tous les champs et sélectionner au moins un cours.";
        $messageType = 'warning';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>
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
                <li class="nav-item me-3"><a class="nav-link active" href="inscription.php">Inscription</a></li>
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
        <h1>Formulaire d'inscription</h1>
    </header>

    <div class="container my-4">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

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
                            <label class="form-label">Sélectionner les cours (1 à 4)</label>
                            <?php if (!empty($coursDisponibles)) {
                                foreach ($coursDisponibles as $cours) { ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="cours[]" value="<?= $cours['IDC'] ?>" id="cours_<?= $cours['IDC'] ?>">
                                        <label class="form-check-label" for="cours_<?= $cours['IDC'] ?>">
                                            <?= date('d/m/Y', strtotime($cours['Date'])) ?> - Places restantes: <?= $cours['places_restantes'] ?>
                                        </label>
                                    </div>
                                <?php }
                            } else { ?>
                                <p>Aucun cours disponible</p>
                            <?php } ?>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 GYMSYNC</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type=checkbox][name="cours[]"]');
            const maxAllowed = 4;

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const checkedCheckboxes = document.querySelectorAll('input[type=checkbox][name="cours[]"]:checked');
                    
                    if (checkedCheckboxes.length > maxAllowed) {
                        this.checked = false;
                        alert('Vous ne pouvez sélectionner que 4 cours maximum.');
                    }
                });
            });
        });
    </script>
</body>
</html>

