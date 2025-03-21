<?php
require_once __DIR__ . '/dao/MembreDao.php';

if (isset($_GET['cours_id'])) {
    $membreDao = new MembreDao();
    $membres = $membreDao->getMembresParCours($_GET['cours_id']);
    header('Content-Type: application/json');
    echo json_encode($membres);
}