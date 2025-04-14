<?php

require_once __DIR__ . '/BaseDonneeDao.php';
require_once __DIR__ . '/CoursDao.php';
require_once __DIR__ . '/MembreDao.php';

class ExportDao extends BaseDonneeDao {
    private $coursDao;
    private $membreDao;

    public function __construct() {
        parent::__construct('cours');
        $this->coursDao = new CoursDao();
        $this->membreDao = new MembreDao();
    }

    public function exporterCoursCSV($cours_id) {
        $cours = $this->coursDao->getCours($cours_id);
        $membres = $this->membreDao->getMembresParCours($cours_id);

        if (!$cours) {
            throw new Exception("Cours non trouvé");
        }

        $filename = 'Cours_' . preg_replace('/[^A-Za-z0-9]/', '_', $cours['Nature']) 
                 . '_' . $cours['Jour'] 
                 . '_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // En-tête avec mise en forme
        fputcsv($output, [], ';');
        fputcsv($output, ['**************'], ';');
        fputcsv($output, [''], ';');
        fputcsv($output, ['              COURS DU ' . strtoupper($cours['Jour']) . '              '], ';');
        fputcsv($output, [''], ';');
        fputcsv($output, ['*************'], ';');
        fputcsv($output, [], ';');

        // Informations du cours
        fputcsv($output, ['Nature', 'Heure', 'Places totales', 'Professeur'], ';');
        fputcsv($output, [
            $cours['Nature'],
            date('H:i', strtotime($cours['Heure'])),
            $cours['Place'],
            $cours['Professeur']
        ], ';');

        // Ligne vide de séparation
        fputcsv($output, [], ';');

        // Liste des membres
        fputcsv($output, ['LISTE DES MEMBRES INSCRITS'], ';');
        fputcsv($output, ['Nom', 'Prénom', 'Email', 'Statut Paiement'], ';');
        
        foreach ($membres as $membre) {
            fputcsv($output, [
                $membre['Nom'] ?? '',
                $membre['Prenom'] ?? '',
                $membre['Mail'] ?? '',
                isset($membre['Validation']) && $membre['Validation'] ? 'Payé' : 'Non payé'
            ], ';');
        }

        // Lignes vides de fin
        fputcsv($output, [], ';');
        fputcsv($output, [], ';');

        fclose($output);
        exit;
    }

    public function exporterTousLesCoursCSV() {
        $cours = $this->coursDao->recupererTousLesCours();
        
        
        $coursParJour = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        foreach ($cours as $unCours) {
            $coursParJour[$unCours['Jour']][] = $unCours;
        }

        $filename = 'Planning_Cours_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        
        foreach ($jours as $jour) {
            if (isset($coursParJour[$jour])) {
                fputcsv($output, [], ';');
                fputcsv($output, ['**************'], ';');
                fputcsv($output, [''], ';');
                fputcsv($output, ['              COURS DU ' . strtoupper($jour) . ' '], ';');
                fputcsv($output, [''], ';');
                fputcsv($output, ['**************'], ';');
                fputcsv($output, [], ';');

                foreach ($coursParJour[$jour] as $unCours) {
                    
                    
                    
                    
                    fputcsv($output, ['Nature', 'Heure', 'Places totales', 'Professeur'], ';');
                    fputcsv($output, [
                        $unCours['Nature'],
                        date('H:i', strtotime($unCours['Heure'])),
                        $unCours['Place'],
                        $unCours['Professeur']
                    ], ';');

                    
                    fputcsv($output, ['LISTE DES MEMBRES INSCRITS'], ';');
                    fputcsv($output, ['Nom', 'Prénom', 'Email', 'Statut Paiement'], ';');
                    
                    $membres = $this->membreDao->getMembresParCours($unCours['IDC']);
                    
                    foreach ($membres as $membre) {
                        fputcsv($output, [
                            $membre['Nom'] ?? '',
                            $membre['Prenom'] ?? '',
                            $membre['Mail'] ?? '',
                            isset($membre['Validation']) && $membre['Validation'] ? 'Payé' : 'Non payé'
                        ], ';');
                    }

                    fputcsv($output, [], ';');
                    fputcsv($output, [], ';');
                }
            }
        }

        fclose($output);
        exit;
    }
}