<?php
session_start();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/accueil.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
<!-- Navbar -->
<?php
    include 'NavBar.php';
?>

<header class="text-center bg-dark">
    <h1>Bienvenue sur le site de l'association de gymnastique de Bizanos.</h1>
</header>

<div class="container my-4" id="accueil">
    <div>
        
        <h2 class="col-md-12 bg-secondary text-white p-3 mb-4">Actualités</h2>
        <?php
        require_once __DIR__ . '/dao/ActualiteDao.php';
        $actualiteDao = new ActualiteDao();
        $actualites = $actualiteDao->getAllActualites();

        echo '<div class="row row-cols-1 row-cols-md-3 g-4 mb-4">';
        foreach ($actualites as $actualite) {
            $style = '';
            if (!empty($actualite['couleur'])) {
                $style .= "color: {$actualite['couleur']};";
            }
            if (!empty($actualite['gras'])) {
                $style .= "font-weight: bold;";
            }
            
            echo '<div class="col">';
            echo '<div class="card shadow-sm">'; 
            echo '<div class="card-body p-4">';         
            echo '<div class="d-flex align-items-start">'; 
            echo '<i class="fas fa-bell me-3 text-warning" style="font-size: 2rem;"></i>'; 
            echo "<p class='card-text' style='font-size: 1.2rem; $style'>" . $actualite['texte'] . "</p>";
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        ?>
        <br>
        
        <h2 class="col-md-12 bg-secondary text-white p-3 mb-4">Nos tarifs:</h2> 

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Tarifs</th>
                        <th scope="col">1 Cours</th>
                        <th scope="col">2 Cours</th>
                        <th scope="col">3 Cours</th>
                        <th scope="col">4 Cours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once __DIR__ . '/dao/BaseDonneeDao.php'; 
                    require_once __DIR__ . '/dao/TarifDao.php'; 
                    
                    $tarif = new TarifDao();
                    $allTarifs = $tarif->getAllTarifs();
                    $tarifsParCategorie = [];
                    
                    foreach ($allTarifs as $uneTarif) {
                        $categorie = $uneTarif['categorie'];
                        $prix = $uneTarif['prix'];
                        if (!isset($tarifsParCategorie[$categorie])) {
                            $tarifsParCategorie[$categorie] = [];
                        }
                        $tarifsParCategorie[$categorie][] = $prix;
                    }
                    
                    foreach ($tarifsParCategorie as $categorie => $tarifs) {
                        echo "<tr>";
                        echo "<th scope='row'>" . htmlspecialchars($categorie) . "</th>";
                        foreach ($tarifs as $prix) {
                            echo "<td class='align-middle'><strong>" . $prix . "€</strong></td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <br>
        <h2 class="col-md-12 bg-secondary text-white p-3 mb-4">Nous situer</h2>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-map-marker-alt text-secondary me-2"></i>
                            Adresse
                        </h5>
                        <p class="card-text">
                            Espace Daniel Balavoine<br>
                            Avenue de l'Europe<br>
                            64320 Bizanos
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-envelope text-secondary me-2"></i>
                            Contact
                        </h5>
                        <p class="card-text">
                            Email: gymavenirbizanos@free.fr
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body p-0">
            <div class="ratio ratio-4x3">
                <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2904.185641370534!2d-0.35410538471574815!3d43.28943387913549!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd56491e11e42a2b%3A0xc1c69fea539e3f82!2sEspace+Daniel+Balavoine!5e0!3m2!1sfr!2sfr!4v1501675843131" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            </div>
        </div>
        <div class="film-tape">
            <img src="Images/FT.png" class="film-tape-bg">
            <div class="photos">
                <img src="Images/pilates1.jpg" alt="Photo 1" class="photo">
                <img src="Images/pilates2.jpg" alt="Photo 2" class="photo">
                <img src="Images/pilates3.jpg" alt="Photo 3" class="photo">
                <img src="Images/pilates4.jpg" alt="Photo 4" class="photo">
                <img src="Images/pilates5.jpg" alt="Photo 5" class="photo">
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
