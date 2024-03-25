<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (!isset($_POST['email'], $_POST['recallDetails'])) {
    echo json_encode(['error' => 'Missing data']);
    exit();
}

$email = $_POST['email'];
$recallDetails = $_POST['recallDetails'];

$mail = new PHPMailer(true);

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'hp548@njit.edu';
    $mail->Password = 'mkfh blnh ohoo jfbk'; 
    $mail->Port = 587;

    $mail->setFrom('hp548@njit.edu', 'Recall Notification System');
    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject = 'New Vehicle Recall Notification';
    $mail->Body    = $recallDetails;
    $mail->AltBody = strip_tags($recallDetails);

    $mail->send();
    echo json_encode(['success' => 'Email sent']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
?>
