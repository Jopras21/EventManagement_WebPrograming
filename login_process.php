<?php
session_start();
require_once('db-user.php');

// Ambil data dari login form
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

// Periksa data user
$sql = "SELECT * FROM user WHERE username = ? AND email = ?";
$stmt = $dbu->prepare($sql);
$stmt->execute([$username, $email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('location: index.php?error=not_found');
    exit;
} else {
    if (!password_verify($password, $row['password'])) {
        // Jika password salah, set cookie singkat (5 detik)
        setcookie("email", $email, time() + (5), "/");
        setcookie("username", $username, time() + (5), "/");
        header('location: index.php?error=wrong_password');
        exit;
    } else {
        // Jika login sukses, simpan session
        $_SESSION['email'] = $row['email'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];  // Simpan peran pengguna
        
        // Arahkan berdasarkan peran (role)
        if ($row['role'] === 'admin') {
            header('location: event_management.php');  // Admin diarahkan ke event management
        } else {
            header('location: event_browsing.php');  // User diarahkan ke event browsing
        }
        exit;
    }
}
?>
