<?php
require_once('db-user.php');

$email = $_POST['email'];
$otp = $_POST['otp'];

$sql = "SELECT * FROM user
        WHERE email = ? AND otp = ?";
$stmt = $dbu->prepare($sql);
$stmt->execute([$email, $otp]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo   '<form action="update_process.php" method="POST">
                <label for="password">New Password:</label>
                <input type="password" name="password" required>
                <input type="hidden" name="email" value="'.$email.'">
                <button type="submit">Reset Password</button>
            </form>';
} else {
    echo "Invalid OTP. Please try again";
    echo "$email, $otp";
    echo '<a href="forgot_password.php">Forgot Password</a>';
}
