<?php
require_once('db-user.php');

// ambil data
$name = $_POST['name'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// periksa data user
$sql = "SELECT * FROM user 
        WHERE username = ? OR email = ?";
$stmt = $dbu->prepare($sql);
$stmt->execute([$username, $email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
        setcookie("name", $name, time() + (5), "/");
        header('location: user_registration_authentication.php?error=used');
        exit;
}

// encrypting password
$en_pass = password_hash($password, PASSWORD_BCRYPT);

// SQL query
$sql = "INSERT INTO user (name, username, email, password)
        VALUES(?, ?, ?, ?)";

// execute query
$result = $dbu->prepare($sql);
$result->execute([$name, $username, $email, $en_pass]);

header('location: event_browsing.php');
