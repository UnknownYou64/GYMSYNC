<?php

// Inclusion de l'autoloader de Composer
require 'vendor/autoload.php';

// Utilisation des classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Construire le message avec les informations des cours
$message_cours = "<h3>Vos cours réservés :</h3><ul>";
foreach ($cours_info as $cours) {
    $message_cours .= sprintf(
        "<li>%s à %s</li>",
        $cours['Jour'],
        date('H:i', strtotime($cours['Heure']))
    );
}
$message_cours .= "</ul>";

$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'gymsync64@gmail.com';
    $mail->Password = 'bqoc ddnl wvbv mevn';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Définition des informations de l'email
    $mail->setFrom('gymsync64@gmail.com', 'GYMSYNC');
    $mail->addAddress($email, $nom . ' ' . $prenom);
    $mail->addReplyTo('gymsync64@gmail.com', 'Gymsync');

    // Contenu de l'email
    $mail->isHTML(true);
    $mail->Subject = 'Confirmation d\'inscription aux cours - GYMSYNC';

    // Création du contenu de l'email
    $mail->Body = "
        <h2>Confirmation d'inscription</h2>
        <p>Bonjour $prenom $nom,</p>
        <p>Votre inscription aux cours a bien été enregistrée.</p>
        $message_cours
        <p>Merci de votre confiance !</p>
        <p>L'équipe GYMSYNC</p>
    ";

    // Envoi de l'email
    $mail->send();
    
} catch (Exception $e) {
    // Gérer l'erreur silencieusement ou la logger
    error_log("Erreur d'envoi d'email : {$mail->ErrorInfo}");
}
?>

