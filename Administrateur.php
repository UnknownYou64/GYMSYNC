<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header('Location: index.php');
    exit;
}

require_once 'Connexion.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generate_code'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $code = substr(bin2hex(random_bytes(4)), 0, 8); 

        try {
            $sql = "SELECT * FROM membre WHERE Nom = :nom AND Prenom = :prenom";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([ ':nom' => $nom, ':prenom' => $prenom ]);
            $membre = $stmt->fetch();

            if ($membre) {
                $updateSql = "UPDATE membre SET Code = :code WHERE Identifiant = :id";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([ ':code' => $code, ':id' => $membre['Identifiant'] ]);
                $message = "Code généré : $code pour $nom $prenom";
                $messageType = 'success';
            } else {
                $message = "Membre introuvable.";
                $messageType = 'danger';
            }
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['add_course'])) {
        $jour = $_POST['jour'];
        $heure = $_POST['heure'];
        $nature = $_POST['nature'];
        $places = $_POST['places']; 
        $professor = $_POST['professor'];

        try {
            $sql = "INSERT INTO cours (Jour, Heure, Nature, Place, Professeur) VALUES (:jour, :heure, :nature, :places, :professor)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':jour' => $jour,
                ':heure' => $heure,
                ':nature' => $nature,
                ':places' => $places,
                ':professor' => $professor
            ]);
            $message = "Cours ajouté avec succès.";
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = "Erreur lors de l'ajout du cours : " . $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['add_member'])) {
        $nom = $_POST['nom_member'];
        $prenom = $_POST['prenom_member'];
        $mail = $_POST['mail_member'];
        
        try {
            $checkSql = "SELECT * FROM membre WHERE Mail = :mail";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([':mail' => $mail]);
            
            if ($checkStmt->rowCount() > 0) {
                $message = "Un membre avec cette adresse email existe déjà.";
                $messageType = 'danger';
            } else {
                $sql = "INSERT INTO membre (Nom, Prenom, Mail) VALUES (:nom, :prenom, :mail)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':mail' => $mail
                ]);
                $message = "Membre ajouté avec succès.";
                $messageType = 'success';
            }
        } catch (PDOException $e) {
            $message = "Erreur lors de l'ajout du membre : " . $e->getMessage();
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Administrateur</title>
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
    <h1>Espace Administrateur</h1>
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
            <div class="card p-4 shadow mb-4">
                <h3 class="text-center">Code Generateur</h3>
                <form method="POST" action="Administrateur.php">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <button type="submit" name="generate_code" class="btn btn-primary w-100">Générer Code</button>
                </form>
            </div>

            <div class="card p-4 shadow">
                <h3 class="text-center">Ajouter un Cours</h3>
                <form method="POST" action="Administrateur.php">
                    <div class="mb-3">
                        <label for="jour" class="form-label">Jour</label>
                        <select class="form-control" id="jour" name="jour" required>
                            <option value="">Sélectionner un jour</option>
                            <option value="Lundi">Lundi</option>
                            <option value="Mardi">Mardi</option>
                            <option value="Mercredi">Mercredi</option>
                            <option value="Jeudi">Jeudi</option>
                            <option value="Vendredi">Vendredi</option>
                            <option value="Samedi">Samedi</option>
                            <option value="Dimanche">Dimanche</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="heure" class="form-label">Heure</label>
                        <input type="time" class="form-control" id="heure" name="heure" required>
                    </div>
                    <div class="mb-3">
                        <label for="nature" class="form-label">Nature du cours</label>
                        <input type="text" class="form-control" id="nature" name="nature" placeholder="Ex: Yoga, Pilates, etc." required>
                    </div>
                    <div class="mb-3">
                        <label for="places" class="form-label">Nombre de Places</label>
                        <input type="number" class="form-control" id="places" name="places" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="professor" class="form-label">Professeur</label>
                        <input type="text" class="form-control" id="professor" name="professor" required>
                    </div>
                    <button type="submit" name="add_course" class="btn btn-primary w-100">Ajouter le Cours</button>
                </form>
            </div>

            <div class="card p-4 shadow mt-4">
                <h3 class="text-center">Ajouter un Membre</h3>
                <form method="POST" action="Administrateur.php">
                    <div class="mb-3">
                        <label for="nom_member" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom_member" name="nom_member" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom_member" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom_member" name="prenom_member" required>
                    </div>
                    <div class="mb-3">
                        <label for="mail_member" class="form-label">Email</label>
                        <input type="email" class="form-control" id="mail_member" name="mail_member" required>
                    </div>
                    <button type="submit" name="add_member" class="btn btn-primary w-100">Ajouter le Membre</button>
                </form>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 GYMSYNC</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

