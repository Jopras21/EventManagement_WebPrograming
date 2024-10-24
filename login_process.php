<?php
session_start();
require_once('db-user.php');

$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

// checking
$sql = "SELECT * FROM user WHERE username = ? AND email = ?";
$stmt = $dbu->prepare($sql);
$stmt->execute([$username, $email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('location: index.php?error=not_found');
    exit();
} else {
    if (!password_verify($password, $row['password'])) {
        // Jika password salah, set cookie singkat 
        setcookie("email", $email, time() + (5), "/");
        setcookie("username", $username, time() + (5), "/");
        header('location: index.php?error=wrong_password');
        exit();
    } else {

        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];  
        
        if ($row['role'] === 'admin') {
            header('location: admin_dashboard.php');  
        } else {
            header('location: event_browsing.php');  
        }
        exit();
    }
}
?>
