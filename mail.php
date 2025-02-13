<?php

// Inclusion de l'autoloader de Composer
require 'vendor/autoload.php';  // Assure-toi que ce chemin est correct selon l'emplacement du fichier

// Utilisation des classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);  // Crée une nouvelle instance PHPMailer

try {
    // Configuration du serveur SMTP
    $mail->isSMTP();  // On utilise SMTP
    $mail->Host = 'smtp.gmail.com';  // Hôte du serveur SMTP (ici Gmail)
    $mail->SMTPAuth = true;  // Authentification SMTP activée
    $mail->Username = 'gymsync64@gmail.com';  // Ton adresse Gmail
    $mail->Password = 'Gym64sync!@';  // Ton mot de passe (ou mot de passe spécifique d'application)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Sécurisation de la connexion
    $mail->Port = 587;  // Port SMTP

    $mail->SMTPDebug = 2;  // Affiche les détails du processus SMTP
    $mail->Debugoutput = 'html';  // Format de sortie du débogage


    // Définition des informations de l'email
    $mail->setFrom('gymsync64@gmail.com', 'Gymsync');
    $mail->addAddress('william.lotz64@gmail.com', 'LOTZ');  // Destinataire
    $mail->addReplyTo('gymsync64@gmail.com', 'Gymsync');  // Adresse pour les réponses

    // Contenu de l'email
    $mail->isHTML(true);  // Email au format HTML
    $mail->Subject = 'Test de PHPMailer';
    $mail->Body    = '<h1>Ceci est un test d\'email envoyé avec PHPMailer</h1>';

    // Envoi de l'email
    $mail->send();
    echo 'L\'email a été envoyé avec succès.';
} catch (Exception $e) {
    echo "L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}";
}
?>
