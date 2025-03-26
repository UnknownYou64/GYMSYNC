<?php
// Récupérer le nom du fichier actuel
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">GYMSYNC</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item me-3">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" 
                       href="index.php">Accueil</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link <?php echo ($current_page == 'inscription.php') ? 'active' : ''; ?>" 
                       href="inscription.php">Inscription</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link <?php echo ($current_page == 'Liste.php') ? 'active' : ''; ?>" 
                       href="Liste.php">Liste des Cours</a>
                </li>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin") { ?>
                    <li class="nav-item me-3">
                        <a class="nav-link <?php echo ($current_page == 'Administrateur.php') ? 'active' : ''; ?>" 
                           href="Administrateur.php">Admin</a>
                    </li>
                <?php } ?>
                
                <?php if (isset($_SESSION['role'])) { ?>
                    <a href="logout.php" class="btn btn-danger">Déconnexion</a>
                <?php } else { ?>
                    <li class="nav-item me-3">
                        <a class="nav-link <?php echo ($current_page == 'Login.php') ? 'active' : ''; ?>" 
                           href="Login.php">Se connecter</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>