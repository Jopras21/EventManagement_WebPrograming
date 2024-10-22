<?php
require_once('db-user.php');
require 'PHPMailer/PHPMailerAutoload.php';

$sql = "SELECT * FROM user 
        WHERE email = ?";
$stmt = $dbu->prepare($sql);
$stmt->execute([$email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row){
    
}

?>