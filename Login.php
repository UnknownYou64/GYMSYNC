<?php
session_start();
require_once 'Connexion.php'; 


$admin_email = "admin@gmail.com";
$admin_password = "admin";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    
    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['role'] = "admin";
        $_SESSION['email'] = $email;
        header('Location: Administrateur.php');
        exit;
    } else {
        
        $sql = "SELECT * FROM membre WHERE Mail = :email AND Code IS NOT NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $membre = $stmt->fetch();

        if ($membre) {
            $_SESSION['role'] = "membre";
            $_SESSION['email'] = $membre['Mail'];
            header('Location: index.php'); 
            exit;
        } else {
            echo "<script>alert('Connexion échouée. Vérifiez vos informations.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow">
                    <h3 class="text-center">Connexion</h3>
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Votre e-mail" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
