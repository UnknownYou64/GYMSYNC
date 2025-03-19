<?php
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/dao/MembreDao.php';
require_once __DIR__ . '/dao/CoursDao.php';

// Initialisation des DAO
$membreDao = new MembreDao();
$coursDao = new CoursDao();

// Récupération des cours disponibles
try {
    $coursDisponibles = $coursDao->recupererCoursDisponibles();
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}

$message = '';
$messageType = '';

// Remplacer la partie du code qui récupère l'ID du membre
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cours_selectionnes = isset($_POST['cours']) ? $_POST['cours'] : [];

    if (!empty($cours_selectionnes)) {
        try {
            // Récupération de l'ID du membre depuis la session
            if (!isset($_SESSION['id'])) {
                throw new Exception("Vous devez être connecté pour vous inscrire aux cours.");
            }
            $membre_id = $_SESSION['id'];
            
            // Vérifier le nombre de cours sélectionnés
            if (count($cours_selectionnes) > 4) {
                throw new Exception("Vous ne pouvez sélectionner que 4 cours maximum.");
            }

            // Inscrire aux cours sélectionnés
            $coursDao->inscrireAuxCours($membre_id, $cours_selectionnes);
            
            // Récupérer les informations des cours pour l'email
            $cours_info = $coursDao->getCoursInfo($cours_selectionnes);
            
            require_once 'mail.php';

            $message = "Inscription aux cours réussie ! Un e-mail de confirmation vous a été envoyé.";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur lors de l'inscription aux cours : " . $e->getMessage();
            $messageType = 'danger';
        }
    } else {
        $message = "Veuillez sélectionner au moins un cours.";
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
                    <h3 class="text-center">Sélection des cours</h3>
                    <form method="POST" action="inscription.php">
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
                        <button type="submit" class="btn btn-primary w-100">S'inscrire aux cours</button>
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

