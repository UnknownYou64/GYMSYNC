<?php

// Inclusion de l'autoloader de Composer
require 'vendor/autoload.php';

// Utilisation des classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Récupération du tarif depuis la base de données
$pdo = new PDO("mysql:host=localhost;dbname=gymsync", "root", "root");
$stmt = $pdo->prepare("
    SELECT t.prix 
    FROM membre m 
    JOIN tarifs t ON m.tarif_id = t.IDT 
    WHERE m.Identifiant = :id
");
$stmt->execute(['id' => $membre_id]); // Assurez-vous d'avoir $membre_id disponible
$tarif = $stmt->fetch(PDO::FETCH_ASSOC);
$montant_total = $tarif['prix'] ?? 'À déterminer'; // Valeur par défaut si pas de tarif trouvé

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

    // Ajout du logo et création du contenu de l'email
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: black;'>
            
            <h2 style='color: #333;'>Confirmation d'inscription</h2>
            <p>Bonjour $prenom $nom,</p>
            <p>Nous vous confirmons votre inscription aux cours suivants :</p>
            $message_cours
            
            <div style='background-color: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 5px;'>
                <h3 style='color: #333; margin-top: 0;'>Informations de paiement</h3>
                <p><strong>Montant annuel à régler :</strong> {$montant_total} €</p>
                <p><strong>Moyens de paiement acceptés :</strong></p>
                <ul>
                    <li>Chèque à l'ordre de : <strong>Association GYMSYNC</strong></li>
                    <li>Virement bancaire (RIB sur demande)</li>
                    <li>Espèces (uniquement sur place)</li>
                </ul>
                <p><strong>Adresse pour l'envoi du règlement :</strong><br>
                    Association GYMSYNC<br>
                    123 rue du Sport<br>
                    64000 PAU
                </p>
            </div>
            <div>
            <p style='color: #666;'><em>Merci de régler dans un délai de 15 jours.</em></p>
            
            <p>Pour toute question, notre équipe reste à votre disposition :<br>
            📧 gymsync64@gmail.com<br>
            📞 07.86.44.86.13</p>
            
            <p>Cordialement,</p>
            <div style='margin-top: 20px;'>
                <strong>L'équipe GYMSYNC</strong><br>
                <small>Association sportive agréée</small>
            </div>
            </div>
        </div>
    ";

    // Envoi de l'email
    $mail->send();
    
} catch (Exception $e) {
    // Gérer l'erreur silencieusement ou la logger
    error_log("Erreur d'envoi d'email : {$mail->ErrorInfo}");
}
?>

