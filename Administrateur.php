<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header('Location: index.php');
    exit;
}

require_once 'Connexion.php';
require_once 'dao/BaseDonneeDao.php';
require_once 'dao/CoursDao.php';
require_once 'dao/MembreDao.php';
require_once 'dao/historiqueDao.php';
// Initialisation des DAO
$coursDao = new CoursDao();
$membreDao = new MembreDao();
$message = '';
$messageType = '';
$historiqueDao = new historiqueDao();
$historiques = $historiqueDao->recupererhistorique();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generer_code'])) {
        try {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $code = $membreDao->genererCode($nom, $prenom);
            $message = "Code généré : $code pour $nom $prenom";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['ajout_membre'])) {
        try {
            $nom = $_POST['nom_member'];
            $prenom = $_POST['prenom_member'];
            $mail = $_POST['mail_member'];
            
            $membreId = $membreDao->ajouterMembre($nom, $prenom, $mail);
            
            $message = "Membre ajouté avec succès.";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['suppr_membre_cours'])) {
        try {
            $membre_id = $_POST['membre_id'];
            $cours_id = $_POST['cours_id'];
            
            $coursDao->supprimerMemberDuCours($membre_id, $cours_id);
            
            $message = "Membre retiré du cours avec succès.";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['suppr_cours'])) {
        try {
            $cours_id = $_POST['cours_id'];
            
            $coursDao->supprimerToutesReservations($cours_id);
            
            $coursDao->supprimerCours($cours_id);
            
            $message = "Le cours a été supprimé avec succès.";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
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

<?php
    include 'NavBar.php';
?>

<header class="bg-dark text-white text-center py-3">
    <h1>Espace de Gestion</h1>
</header>

<div class="container my-4">
    <!-- Messages d'alerte en français -->
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Générateur de code -->
            <div class="card p-4 shadow mb-4">
                <h3 class="text-center">Générer un Code d'Accès</h3>
                <form method="POST" action="Administrateur.php">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom du membre</label>
                        <input type="text" class="form-control" id="nom" name="nom" placeholder="Saisir le nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom du membre</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Saisir le prénom" required>
                    </div>
                    <button type="submit" name="generer_code" class="btn btn-primary w-100">Générer le code</button>
                </form>
            </div>

            <!-- Ajout de membre -->
            <div class="card p-4 shadow mb-4">
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
                        <label for="mail_member" class="form-label">Adresse e-mail</label>
                        <input type="email" class="form-control" id="mail_member" name="mail_member" required>
                    </div>
                    <button type="submit" name="ajout_membre" class="btn btn-success w-100">Enregistrer le membre</button>
                </form>
            </div>

            <!-- Gestion des cours -->
            <div class="card p-4 shadow mb-4">
                <h3 class="text-center">Gestion des Cours</h3>
                <form method="POST" action="Administrateur.php">
                    <div class="mb-3">
                        <label class="form-label">Sélectionner un cours</label>
                        <select name="cours_id" id="cours_select" class="form-control" required>
                            <option value="">Choisir un cours</option>
                            <?php 
                            $cours = $coursDao->recupererTousLesCours();
                            foreach ($cours as $c) {
                                echo "<option value='" . $c['IDC'] . "'>" 
                                    . $c['Jour'] . " à " 
                                    . date('H:i', strtotime($c['Heure'])) . " - " 
                                    . $c['Nature'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sélectionner un membre</label>
                        <select name="membre_id" id="membre_select" class="form-control" disabled>
                            
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="suppr_membre_cours" class="btn btn-warning">
                            Désinscrire le membre
                        </button>
                        <button type="submit" name="suppr_cours" class="btn btn-danger">
                            Supprimer ce cours
                        </button>
                    </div>
                </form>
            </div>



        
        </div>
    </div>



    <div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card p-4 shadow mb-4">
            <h3 class="text-center">Historique</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Action</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historiques as $historique): ?>
                            <tr>
                                <td><?= htmlspecialchars($historique['Action']) ?></td>
                                <td><?= htmlspecialchars($historique['DateAction']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4 shadow mb-4">
                <h3 class="text-center">Assistance</h3>
            </div>
        </div>
    </div>
</div>







<script>
document.getElementById('cours_select').addEventListener('change', function() {
    const cours_id = this.value;
    const membre_select = document.getElementById('membre_select');
    if (cours_id) {
        // Faire une requête AJAX pour obtenir les membres du cours
        fetch(`get_membres_cours.php?cours_id=${cours_id}`)
            .then(response => response.json())
            .then(membres => {
                membre_select.innerHTML = '<option value="">Choisir un membre</option>';
                membres.forEach(membre => {
                    membre_select.innerHTML += `<option value="${membre.Identifiant}">
                        ${membre.Nom} ${membre.Prenom}
                    </option>`;
                });
                membre_select.disabled = false;
            });
    } else {
        membre_select.innerHTML = '<option value="">Choisir d\'abord un cours</option>';
        membre_select.disabled = true;
    }
});
</script>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 GYMSYNC - Tous droits réservés</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

