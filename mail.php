<?php

// Inclusion de l'autoloader de Composer
require 'vendor/autoload.php';

// Utilisation des classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    $mail->setFrom('gymsync64@gmail.com', 'Gymsync');
    $mail->addAddress($email, $nom . ' ' . $prenom);
    $mail->addReplyTo('gymsync64@gmail.com', 'Gymsync');

    // Contenu de l'email
    $mail->isHTML(true);
    $mail->Subject = 'Confirmation d\'inscription à vos cours';

    // Création du contenu de l'email
    $body = "<h1>Bonjour $prenom,</h1>";
    $body .= "<p>Vous êtes bien inscrit(e) aux cours suivants :</p>";
    $body .= "<ul>";
    foreach ($cours_info as $cours) {
        $body .= "<li><strong>" . date('d/m/Y', strtotime($cours['Date'])) . "</strong></li>";
    }
    $body .= "</ul>";
    $body .= "<p>Lieu : Espace Daniel Balavoine<br>
              Avenue de l'Europe<br>
              64320 Bizanos</p>";
    $body .= "<p>La confirmation de votre inscription se fera lorsque vous aurez réglé un chèque à Mme Mundubeltz</p>";
    $body .= "<br><p>Cordialement,<br>L'équipe Gymsync</p>";

    $mail->Body = $body;

    // Envoi de l'email
    $mail->send();
    
} catch (Exception $e) {
    echo "L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}";
}
?>

