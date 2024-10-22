<?php
require_once('db-user.php');

$password = $_POST['password'];
$email = $_POST['email'];

$newpass = password_hash($password, PASSWORD_BCRYPT);

$sql = "UPDATE user
        SET password = ?, otp = NULL
        WHERE email = ?";
$stmt = $dbu->prepare($sql);
$stmt->execute([$newpass, $email]);
?>