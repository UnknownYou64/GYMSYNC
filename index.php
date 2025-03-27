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
    

</head>
<body>
<!-- Navbar -->
<?php
    include 'NavBar.php';
?>

<header class="bg-dark text-white text-center py-3">
    <h1>Accueil</h1>
</header>


<div class="container my-4">
    <div class="row">
                <!--

    ici le code pour les informations necessaire a la presentation du site pour la page d'acceuil 
    voici le site ou prendre les information : http://gymavenirbizanos.free.fr/index.html             
    on doit retrouver les informations suivantes :
    - les horaires d'ouverture
    - les cours proposés
    - les tarifs
    - le lieu
    - les contacts
    - les actualités
    - les photos ( optionnel ) a voir avec la professeur
                
                
                
        Soit vous garder le code ci dessous soit vous le supprimer et vous le refaite a votre maniere

        

    
        <div class="col-md-4 bg-light p-3">c1</div>
        <div class="col-md-4 bg-secondary text-white p-3">c2</div>
        <div class="col-md-4 bg-dark text-white p-3">c3</div>
    -->

    
    </div>
        
        <h2 class="col-md-4 bg-secondary text-white p-3">Actualités:</h2>
        
        <h3>Pas de cours pendant les vacances de Noël</h3>
        <h3>Arrêt des cours le vendredi 20 décembre 2024</h3>
        <h3>Reprise des cours le lundi 6 janvier 2025</h3>
        <h3>Les membres du bureau vous souhaitent à toutes et à tous de très belles fêtes de fin d'année.</h3>
        <h3>Refonte du site web</h3>
        <br>
        
        <h2 class="col-md-4 bg-secondary text-white p-3">Nos tarifs:</h2> 
        <br>

        
        <!--<div class="row">
        <table style="font-size:130%">
            <th>Tarifs</th><th>1 Cours</th><th>2 Cours</th><th>3 Cours</th><th>4 Cours</th>
            <tr><td>Adulte</td><td>136€ </td><td>166€ </td><td>186€ </td><td>206€ </td></tr>
      <tr><td>Couple</td><td>260€ </td><td>308€ </td><td>340€ </td><td>355€ </td>
      <tr><td>Etudiant</td><td>90€ </td><td>110€ </td><td>130€ </td><td>140€ </td>
        </table>
        </div>   --> 
        

        <div class="row">
        <table style="font-size:130%">
            <th>Tarifs</th><th>1 Cours</th><th>2 Cours</th><th>3 Cours</th><th>4 Cours</th>
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
        echo "<tr><td>" . $categorie . "</td>";
        
        foreach ($tarifs as $prix) {
            echo "<td>" . $prix . "€</td>";
        }
        
        echo "</tr>";
    }
    
    
    ?>
        </table>
        </div>    
        
        
        
        <br>
        <h2 class="col-md-4 bg-secondary text-white p-3">Nous situer:</h2>
        <br>

        <h5>Espace Daniel Balavoine Avenue de l'Europe 64320 Bizanos</h5>
        <div>
				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2904.185641370534!2d-0.35410538471574815!3d43.28943387913549!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd56491e11e42a2b%3A0xc1c69fea539e3f82!2sEspace+Daniel+Balavoine!5e0!3m2!1sfr!2sfr!4v1501675843131" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
        </div>
<br>
        <h2 class="col-md-4 bg-secondary text-white p-3">Nous contacter:</h2>
        <h5> Mail: gymavenirbizanos@free.fr </h5>
        <br>
        </div>

        
    </div>
</div>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 GYMSYNC</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>