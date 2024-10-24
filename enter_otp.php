<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>
    <link rel="stylesheet" href="style_user_regist.css">
</head>

<body>
    <?php
    session_start();

    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    ?>
    <div class="enter-otp-container">
        <div class="enter-otp-section">
            <div class="enter-otp-notes">
                <h1>Enter OTP</h1>
            </div>
            <div class="enter-otp-form">
                <form action="reset_password.php" method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="number" name="otp" required>
                    <button type="submit">Submit OTP</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>