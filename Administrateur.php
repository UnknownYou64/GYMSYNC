<?php
session_start();

if (isset($_GET['download_doc']) && $_SESSION['role'] === "admin") {
    $fichier = 'Doc_Technique_GYMSYNYC.pdf';
    $chemin = __DIR__ . '/' . $fichier;

    if (file_exists($chemin)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($fichier) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($chemin));
        readfile($chemin);
        exit;
    } else {
        die("Fichier introuvable.");
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header('Location: index.php');
    exit;
}
require_once 'config/GestionConnexion.php';

require_once 'dao/BaseDonneeDao.php';
require_once 'dao/CoursDao.php';
require_once 'dao/MembreDao.php';
require_once 'dao/HistoriqueDao.php';
require_once 'dao/ExportDao.php';
require_once 'dao/ActualiteDao.php';

// Initialisation des DAO
$coursDao = new CoursDao();
$membreDao = new MembreDao();
$historiqueDao = new HistoriqueDao();
$message = '';
$messageType = '';

$historiques = $historiqueDao->recupererhistorique();


if (isset($_GET['cours_id'])) {
    try {
        $membres = $membreDao->getMembresParCours($_GET['cours_id']);
        header('Content-Type: application/json');
        echo json_encode($membres);
        exit;
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    

    if (isset($_POST['ajout_membre_avec_code'])) {
        try {
            $nom = $_POST['nom_member'];
            $prenom = $_POST['prenom_member'];
            $mail = $_POST['mail_member'];
            
            // Ajouter le membre
            $membreId = $membreDao->ajouterMembre($nom, $prenom, $mail);
            
            // Générer le code
            $code = $membreDao->genererCode($nom, $prenom);
            
            // Ajouter à l'historique
            $historiqueDao->insererhistorique("Ajout du membre et génération de code pour : $nom $prenom");
            
            $message = "Membre ajouté avec succès. Code généré : $code";
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
            
            $historiqueInfos = $historiqueDao->recupHistMembreCours($membre_id, $cours_id);
            $coursDao->supprimerMemberDuCours($membre_id, $cours_id);

            if (!empty($historiqueInfos)) {
                $info = $historiqueInfos[0];
                $nom = $info['Nom'];
                $prenom = $info['Prenom'];
                $nature = $info['Nature'];
                $jour = $info['Jour'];
                $heure = $info['Heure'];
    
                $messageHist = "Suppression du membre : $prenom $nom du cours $nature du $jour à $heure";
            }
    
            $historiqueDao->insererhistorique($messageHist);
            $historiques = $historiqueDao->recupererhistorique();
            
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

            $historiqueInfos = $historiqueDao->recupHistoCours($cours_id);

            $coursDao->supprimerToutesReservations($cours_id);
            
            $coursDao->supprimerCours($cours_id);

            if (!empty($historiqueInfos)) {
                $info = $historiqueInfos[0];
                $nature = $info['Nature'];
                $jour = $info['Jour'];
                $heure = $info['Heure'];
    
                $messageHist = "Suppression du cours : $nature du $jour à $heure";
            }
    
            $historiqueDao->insererhistorique($messageHist);
            $historiques = $historiqueDao->recupererhistorique();
            
            $message = "Le cours a été supprimé avec succès.";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['valider_paiements'])) {
        try {
            if (!empty($_POST['membres'])) {
                foreach ($_POST['membres'] as $membre_id) {
                    // Mettre à jour la validation du paiement
                    $membreDao->basculerValidation($membre_id);
                    
                    // Récupérer les informations du membre
                    $membre = $membreDao->getMembre($membre_id);
    
                    // Créer le message pour l'historique
                    $messageHist = "Paiement validé pour : " . $membre['Nom'] . " " . $membre['Prenom'];
    
                    // Enregistrer l'action dans l'historique
                    $historiqueDao->insererhistorique($messageHist);
                    $historiques = $historiqueDao->recupererhistorique();
                    }
    
                $message = "Les paiements sélectionnés ont été validés avec succès.";
                $messageType = 'success';
                } else {
                $message = "Veuillez sélectionner au moins un membre.";
                $messageType = 'warning';
                }
            } catch (Exception $e) {
            $message = "Erreur lors de la validation : " . $e->getMessage();
            $messageType = 'danger';
            }
        }

    if (isset($_POST['export_csv'])) {
        try {
            $exportDao = new ExportDao();
            $cours_id = $_POST['cours_id'];
            
            // Ajouter à l'historique avant l'export
            $cours = $coursDao->getCours($cours_id);
            $historiqueDao->insererhistorique("Export CSV du cours : {$cours['Nature']} du {$cours['Jour']}");
            
            $exportDao->exporterCoursCSV($cours_id);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['export_tous_cours'])) {
        try {
            $exportDao = new ExportDao();
            $historiqueDao->insererhistorique("Export CSV de tous les cours");
            $exportDao->exporterTousLesCoursCSV();
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['ajouter_actualite'])) {
        try {
            $texte = $_POST['nouveau_texte'];
            $couleur = $_POST['nouvelle_couleur'];
            $gras = isset($_POST['nouveau_gras']) ? 1 : 0;
            
            $actualiteDao = new ActualiteDao();
          
            $actualites = $actualiteDao->getAllActualites();
            $ordre = count($actualites) + 1;
            
            $actualiteDao->ajouterActualite($texte, $couleur, $gras, $ordre);
            $historiqueDao->insererhistorique("Ajout d'une nouvelle actualité");
            
            $message = "L'actualité a été ajoutée avec succès.";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['sauvegarder_actualites'])) {
        $actualiteDao = new ActualiteDao();
        try {
            foreach ($_POST['actualite'] as $id => $data) {

                $actualiteDao->updateActualite(
                    $id,
                    $data['texte'],
                    $data['couleur'],
                    isset($data['gras']) ? 1 : 0
                );
            }
            $historiqueDao->insererhistorique("Modification des actualités");
            $message = "Les actualités ont été mises à jour avec succès.";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }

    if (isset($_POST['supprimer_actualite'])) {
        try {
            $id = $_POST['supprimer_actualite'];
            $actualiteDao = new ActualiteDao();
            $actualiteDao->supprimerActualite($id);
            
            $historiqueDao->insererhistorique("Suppression d'une actualité");
            
            $message = "L'actualité a été supprimée avec succès.";
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
    <link rel="stylesheet" href="css/administrateur.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php
    include 'NavBar.php';
?>

<header class="bg-dark text-center ">
    <h1>Espace de Gestion</h1>
</header>

<div class="container my-4" id="admin">


    <?php if ($message){ ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    <?php } ?>



    <div class="row g-3 justify-content-center">
        <div class="row g-3">
        <div class="col-12 col-md-6">
            
            <div class="card p-3 shadow-sm">
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
                    <button type="submit" name="ajout_membre_avec_code" class="btn btn-success w-100">
                        Enregistrer le membre et générer le code
                    </button>
                </form>
            </div>
        </div>

        
        
        <div class="col-12 col-md-6">
            
        
            <div class="card p-3 shadow-sm">
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
                            <option value="">Choisir un membre</option>
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



            
        <div class="col-12 col-md-6">
            <div class="card p-3 shadow-sm mb-4">
            <h3 class="text-center">Modifier Actualités</h3>
            <form method="POST" action="Administrateur.php" class="mb-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="nouveau_texte" class="form-label">Nouvelle Actualité</label>
                            <input type="text" class="form-control" id="nouveau_texte" name="nouveau_texte" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nouvelle_couleur" class="form-label">Couleur</label>
                            <input type="color" id="nouvelle_couleur" class="ms-4" name="nouvelle_couleur" value="#000000">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Style </label>
                                <input type="checkbox" class="form-check-input ms-4" id="nouveau_gras" name="nouveau_gras">
                                <label class="form-check-label ms-2" for="nouveau_gras">Gras</label>
                            </div>
                    </div>
                </div>
                <button type="submit" name="ajouter_actualite" class="btn btn-success w-100">Ajouter l'actualité</button>
            </form>
            <hr>
            <form method="POST" action="Administrateur.php">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th style="min-width: 200px;">Texte</th>
                                <th style="width: 80px;">Couleur</th>
                                <th style="width: 60px;">Gras</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $actualiteDao = new ActualiteDao();
                        $actualites = $actualiteDao->getAllActualites();
                        foreach ($actualites as $actualite) {
                        ?>
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="actualite[<?= $actualite['id'] ?>][texte]" 
                                           value="<?= $actualite['texte'] ?>">
                                </td>
                                <td class="text-center">
                                    <input type="color" class="form-control form-control-color w-100" 
                                           name="actualite[<?= $actualite['id'] ?>][couleur]" 
                                           value="<?= $actualite['couleur'] ?? '#000000' ?>">
                                </td>
                                <td class="text-center align-middle">
                                    <input type="checkbox" class="form-check-input" 
                                           name="actualite[<?= $actualite['id'] ?>][gras]" 
                                           <?= $actualite['gras'] ? 'checked' : '' ?>>
                                </td>
                                <td class="text-center">
                                    <button type="submit" class="btn btn-danger btn-sm w-100" 
                                            name="supprimer_actualite" value="<?= $actualite['id'] ?>">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-grid gap-2 mt-3">
                <button type="submit" name="sauvegarder_actualites" class="btn btn-primary">Sauvegarder les modifications</button>
                </div>
            </form>
            </div>
        </div>
        

            

        <div class="col-12 col-md-6">
            <div class="card p-3 shadow-sm mb-4">
                <h3 class="text-center">Liste des Membres à Valider</h3>
                <form method="POST" action="Administrateur.php">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $membres = $membreDao->getMembresNonPaye(); 
                                foreach ($membres as $membre){
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="membres[]" value="<?= $membre['Identifiant'] ?>" 
                                            class="form-check-input membre-checkbox">
                                    </td>
                                    <td><?= $membre['Nom'] ?></td>
                                    <td><?= $membre['Prenom'] ?></td>
                                    <td><span class="badge bg-warning">Non Payer</span></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" name="valider_paiements" class="btn btn-success">
                            Valider les paiements sélectionnés
                        </button>
                    </div>
                </form>
            </div>
        





        
            <div class="card p-3 shadow-sm mb-4">
                <h3 class="text-center">Exporter les Données</h3>
                <form method="POST" action="Administrateur.php">
                    <div class="mb-3">
                        <label class="form-label">Sélectionner un cours pour l'export</label>
                        <select name="cours_id" class="form-control">
                            <option value="">Choisir un cours</option>
                            <?php 
                            foreach ($cours as $c) {
                                echo "<option value='" . $c['IDC'] . "'>" 
                                    . $c['Jour'] . " à " 
                                    . date('H:i', strtotime($c['Heure'])) . " - " 
                                    . $c['Nature'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="export_csv" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Exporter le cours sélectionné
                        </button>
                        <button type="submit" name="export_tous_cours" class="btn btn-primary">
                            <i class="fas fa-file-excel"></i> Exporter tous les cours
                        </button>
                    </div>
                </form>
            </div>
    </div>




    
    

    <div class="row justify-content-center mt-4">
        <div class="col-12">
            <div class="card p-4 shadow mb-4">
                <h3 class="text-center">Historique</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered fs-5">
                        <thead class="table-dark">
                            <tr>
                                <th>Action</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($historiques, 0, 10) as $historique){ ?>
                                <tr>
                                    <td><?= htmlspecialchars($historique['Action']) ?></td>
                                    <td><?= htmlspecialchars($historique['DateAction']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>                            


    
</div>



    
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card p-4 shadow mb-4 w-100 text-center">
                    <h3 class="text-center">Assistance</h3>
                    
                    <a href="Administrateur.php?download_doc=1" class="btn btn-primary mt-3">
									Télécharger le document technique
						</a>
                </div>
            </div>
        </div>
    
</div>







<script>
document.getElementById('cours_select').addEventListener('change', function() {
    const cours_id = this.value;
    const membre_select = document.getElementById('membre_select');
    
    if (cours_id) {
        fetch(`Administrateur.php?cours_id=${cours_id}`)
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau');
                return response.json();
            })
            .then(membres => {
                membre_select.innerHTML = '<option value="">Choisir un membre</option>';
                membres.forEach(membre => {
                    membre_select.innerHTML += `<option value="${membre.Identifiant}">
                        ${membre.Nom} ${membre.Prenom}
                    </option>`;
                });
                membre_select.disabled = false;
            })
            .catch(error => {
                console.error('Erreur:', error);
                membre_select.innerHTML = '<option value="">Erreur de chargement</option>';
                membre_select.disabled = true;
            });
    } else {
        membre_select.innerHTML = '<option value="">Choisir d\'abord un cours</option>';
        membre_select.disabled = true;
    }
});


document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.getElementsByClassName('membre-checkbox');
    for(let checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
});
</script>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 GYMSYNC - Tous droits réservés</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

