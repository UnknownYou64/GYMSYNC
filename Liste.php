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
$message = '';

// Traitement de l'ajout d'un cours
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['role'] === "admin") {
    try {
        $jour = $_POST['jour'];
        $heure = $_POST['heure'];
        $nature = $_POST['nature'];
        $places = $_POST['places'];
        $prof = $_POST['prof'];

        $coursDao->ajouterCours($jour, $heure, $nature, $places, $prof);
        $message = "Cours ajouté avec succès";
        
        // Recharger la liste des cours
        $cours = $coursDao->recupererTousLesCours();
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
} else {
    // Récupérer la liste des cours normalement
    try {
        $cours = $coursDao->recupererTousLesCours();
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
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
<?php
    include 'NavBar.php';
?>

    <header class="bg-dark text-white text-center py-3">
        <h1>Liste des Cours Disponibles</h1>
    </header>

    <?php if ($message): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

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

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin"): ?>
        <div class="container mt-5">
            <div class="card">
                <div class="card-header">
                    <h3>Ajouter un nouveau cours</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-2">
                                <select name="jour" class="form-control" required>
                                    <option value="">Jour</option>
                                    <option value="Lundi">Lundi</option>
                                    <option value="Mardi">Mardi</option>
                                    <option value="Mercredi">Mercredi</option>
                                    <option value="Jeudi">Jeudi</option>
                                    <option value="Vendredi">Vendredi</option>
                                    <option value="Samedi">Samedi</option>
                                    <option value="Dimanche">Dimanche</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="time" name="heure" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="nature" class="form-control" placeholder="Type de cours" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="places" class="form-control" placeholder="Places" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="prof" class="form-control" placeholder="Professeur" required>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-success">Ajouter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 GYMSYNC</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
