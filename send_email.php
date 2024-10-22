<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once('db-user.php');
require 'vendor/autoload.php';

$email = $_POST['email'];
$_SESSION['email'] = $email;

$sql = "SELECT * FROM user WHERE email = ?";
$stmt = $dbu->prepare($sql);
$stmt->execute([$email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row){
    $otp = rand(100000, 999999);
    
    $sql = "UPDATE user SET otp = ? WHERE email = ?";
    $stmt = $dbu->prepare($sql);
    $stmt->execute([$otp, $email]);

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'abigailvaniapra@gmail.com';
    $mail->Password = 'imcs kwma liez rslu';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('abigailvaniapra@gmail.com', 'Reset Password'); 
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Request to Reset Password';
    $mail->Body = "The OTP for your reset password request is <b>$otp</b>. This OTP is valid for 10 minutes.";

    if($mail->send()) {
        header('location: enter_otp.php');
    } else {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
} else {
    header('location: forgot_password.php?error=no_account');
}
?>