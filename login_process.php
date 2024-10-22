<?php
session_start();
require_once('db-user.php');

// ambil data dari login form
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

// periksa data user
$sql = "SELECT * FROM user 
        WHERE username = ? AND email = ?";
$stmt = $dbu->prepare($sql);
$stmt->execute([$username, $email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    header('location: index.php?error=not_found');
    exit;
} else {
    if(!password_verify($password, $row['password'])){
        setcookie("email", $email, time() + (5), "/");
        setcookie("username", $username, time() + (5), "/");
        header('location: index.php?error=wrong_password');
        exit;
    } else {
        $_SESSION['email'] = $row['email'];
        $_SESSION['username'] = $row['username'];
        header('location: event_browsing.php');
        exit;
    }
}
?>
