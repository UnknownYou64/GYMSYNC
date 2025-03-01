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
        SELECT c.IDC,c.Jour,c.Heure,c.Nature,c.Place,c.Professeur,(c.Place - IFNULL(COUNT(r.IDC), 0)) AS places_restantes
        FROM cours c
        LEFT JOIN reservation r ON c.IDC = r.idC
        GROUP BY c.IDC, c.Jour, c.Heure, c.Nature, c.Place, c.Professeur
        HAVING places_restantes > 0
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
    $coursDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $cours_selectionnes = isset($_POST['cours']) ? $_POST['cours'] : [];

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($cours_selectionnes)) {
        try {
            
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
                
                $sql_cours = "SELECT Jour, Heure FROM cours WHERE IDC = :id_cours";
                $stmt_cours = $pdo->prepare($sql_cours);
                $stmt_cours->execute([':id_cours' => $cours_id]);
                $cours_info[] = $stmt_cours->fetch(PDO::FETCH_ASSOC);
            }

            // envoie du mail
            require_once 'mail.php';

            $message = "Inscription réussie ! Un e-mail de confirmation vous a été envoyé.";
            $messageType = 'success';
        } catch (PDOException $e) {
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
                            <?php if (!empty($coursDisponibles)) { ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sélection</th>
                                                <th>Jour</th>
                                                <th>Heure</th>
                                                <th>Nature</th>
                                                <th>Professeur</th>
                                                <th>Places restantes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($coursDisponibles as $cours) { ?>
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="cours[]" 
                                                               value="<?= $cours['IDC'] ?>" 
                                                               id="cours_<?= $cours['IDC'] ?>">
                                                    </td>
                                                    <td><?= $cours['Jour'] ?></td>
                                                    <td><?= date('H:i', strtotime($cours['Heure'])) ?></td>
                                                    <td><?= $cours['Nature'] ?></td>
                                                    <td><?= $cours['Professeur'] ?></td>
                                                    <td><?= $cours['places_restantes'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <p class="alert alert-info">Aucun cours disponible</p>
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

