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
    $mail->Password = 'bqoc ddnl wvbv mevn';  // Ton mot de passe (ou mot de passe spécifique d'application)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Sécurisation de la connexion
    $mail->Port = 587;  // Port SMTP

   
    // Définition des informations de l'email
    $mail->setFrom('gymsync64@gmail.com', 'Gymsync');
    $mail->addAddress($email, $nom . ' ' . $prenom);  // Destinataire = celui qui s'inscrit
    $mail->addReplyTo('gymsync64@gmail.com', 'Gymsync');  // Adresse pour les réponses

    // Contenu de l'email
    $mail->isHTML(true);  // Email au format HTML
    $mail->Subject = 'Confirmation d\'inscription à votre cours';
    $mail->Body    = "
        <h1>Bonjour $prenom,</h1>
        <p>Vous êtes bien inscrit(e) au cours du <strong>" . date('d/m/Y', strtotime($cours['Date'])) . "</strong>.</p>
        <p>Lieu : Espace Daniel Balavoine<br>
                  Avenue de l'Europe<br>
                  64320 Bizanos</p>
        <p>Nous avons hâte de vous voir !</p>
        <br>
        <p>Cordialement,<br>L'équipe Gymsync</p>";


    // Envoi de l'email
    $mail->send();
    echo 'L\'email a été envoyé avec succès.';
} catch (Exception $e) {
    echo "L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}";
}
?>
