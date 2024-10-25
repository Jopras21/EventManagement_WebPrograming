<?php
require_once('db-user.php');

$email = $_POST['email'];
$otp = $_POST['otp'];

$sql = "SELECT * FROM user
        WHERE email = ? AND otp = ?";
$stmt = $dbu->prepare($sql);
$stmt->execute([$email, $otp]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_user_regist.css">
    <title>Reset Password</title>
</head>

<body>
    <?php if ($row): ?>
        <div class="reset-password-container">
            <div class="reset-password-form">
                <h2>Reset Your Password</h2>
                <form action="update_process.php" method="POST">
                    <label for="password">New Password:</label>
                    <input type="password" name="password" required>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <button type="submit">Reset Password</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="reset-password-container">
            <div class="error-message">
                <p>Invalid OTP. Please try again.</p>
                <a href="forgot_password.php">Forgot Password</a>
            </div>
        </div>

        </div>
    <?php endif; ?>
</body>

</html>