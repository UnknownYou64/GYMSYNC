<?php
//mdp : admin
//email : admin@gmail.com

//



session_start();
require_once __DIR__ . '/dao/MembreDao.php';

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $membreDao = new MembreDao();
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        $resultat = $membreDao->verifierConnexion($email, $password);

        if ($resultat) {
            $_SESSION['role'] = $resultat['role'];
            $_SESSION['email'] = $resultat['email'];
            if (isset($resultat['id'])) {
                $_SESSION['id'] = $resultat['id'];
            }
            
            if ($resultat['role'] === 'admin') {
                header('Location: Administrateur.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $message = 'Email ou mot de passe incorrect.';
            $messageType = 'danger';
        }
    } catch (Exception $e) {
        $message = "Une erreur est survenue lors de la connexion: " . $e->getMessage();
        $messageType = 'danger';
        
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - GYMSYNC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </nav>

    <div class="my-5" id="login-container">
        <?php if ($message){ ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php }?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Connexion</h2>
                        <form method="POST" action="Login.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Se connecter</button>
                            </div>
                            <div class="mt-4 text-center text-muted">
                                <p class="mb-2" style="color: #ffd700">Pour vous créer un compte :</p>
                                <p style="color: #ffd700">Veuillez contacter l'administrateur par email à :<br>
                            <strong style="color: black">gymavenirbizanos@free.fr</strong></p>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    
</body>
    <footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 GYMSYNC - Tous droits réservés</p>
    </footer>
</html>
