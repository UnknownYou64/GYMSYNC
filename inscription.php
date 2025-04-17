<<<<<<< HEAD
<?php
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/dao/TarifDao.php';
require_once __DIR__ . '/dao/MembreDao.php';
require_once __DIR__ . '/dao/CoursDao.php';

$tarifDao = new TarifDao();
$membreDao = new MembreDao();
$coursDao = new CoursDao();

try {
    $coursDisponibles = $coursDao->recupererCoursDisponibles();
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cours_selectionnes = isset($_POST['cours']) ? $_POST['cours'] : [];
    $type_adherent = isset($_POST['type_adherent']) ? $_POST['type_adherent'] : '';

    if (!empty($cours_selectionnes)) {
        try {


            //recuperer id du membre connecter
            if (!isset($_SESSION['id'])) {
                throw new Exception("Vous devez être connecté pour vous inscrire aux cours.");
            }
            $membre_id = $_SESSION['id'];


            // Inscription aux cours

            $nombre_cours = count($cours_selectionnes);
            if ($nombre_cours > 4) {
                throw new Exception("Vous ne pouvez sélectionner que 4 cours maximum.");
            }
            $tarif = $tarifDao->getTarif($type_adherent, $nombre_cours);
            if (!$tarif) {
                throw new Exception("Aucun tarif trouvé pour votre sélection.");
            }
            $membreDao->majTarif($membre_id, $tarif['IDT']);
            $coursDao->inscrireAuxCours($membre_id, $cours_selectionnes);
            $membre = $membreDao->getMembre($membre_id);
            $email = $membre['Mail'];
            $nom = $membre['Nom'];
            $prenom = $membre['Prenom'];
            $cours_info = $coursDao->getCoursInfo($cours_selectionnes);
            



            // Envoi de mail et afficher message alerte

            require_once 'mail.php';
            $message = "Inscription réussie ! Le Tarif à payer est de : " . $tarif['prix'] . "€.<br>Vous allez recevoir un mail avec les détails de la réservation.";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
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
    <link rel="stylesheet" href="css/inscription.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'NavBar.php'; ?>

    <header class="header-inscription text-center">
        <h1>Formulaire d'inscription</h1>
    </header>

    <div class="container my-4">
        <?php if ($message){?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="carte-cours">
                    <h3 class="titre-selection text-center">Sélection des cours</h3>
                    <form method="POST" action="inscription.php">
                        <div class="mb-3">
                            <label class="form-label">Type d'adhérent</label>
                            <select name="type_adherent" class="form-control" required>
                                <option value="">Choisissez votre catégorie</option>
                                <option value="adulte">Adulte</option>
                                <option value="couple">Couple</option>
                                <option value="etudiant">Étudiant</option>
                            </select>
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
                        <button type="submit" class="btn btn-inscription w-100">S'inscrire aux cours</button>
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
